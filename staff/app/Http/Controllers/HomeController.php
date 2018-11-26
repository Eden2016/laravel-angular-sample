<?php
namespace App\Http\Controllers;

use App\DummyMatch;
use App\Events\Event;
use App\Game;
use App\Models\Streams;
use Illuminate\Support\Collection;
use PDO;
use stdClass;
use Illuminate\Http\Request;
use App\User;
use App\Http\Controllers\Controller;
use Dota2Api\Api;
use DB;
use View;
use Log;
use Cache;
use Mail;

class HomeController extends Controller
{
    /**
     * @var S3ClientObject
     **/
    protected $_s3;
    /**
     * @var string
     **/
    protected $_bucket;
    private $_doCache = true;

    public function __construct() {
        //Initialize DOTA2 Web API
        Api::init(getenv('STEAM_API_KEY_TEST'), array(getenv('DB_HOST'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'), getenv('DB_DATABASE'), ''));

        if (getenv('APP_ENV') === 'development')
            $this->_doCache = false;

        //Initialize Amazon S3 SDK
        $this->_s3 = \AWS::createClient('s3');
        $this->_bucket = getenv('BUCKET_NAME');
    }

    public function test(Request $request) {
        return print_r($request->currentGame, true);
        $mm = new \Dota2Api\Mappers\MatchMapperWeb(2185912764);
        $match = $mm->load();

        print_r($match);
    }

    public function liveMatches(Request $request)
    {
        $matchesRaw = $this->_getObject('games.json');
        $matches = json_decode($matchesRaw);

        if ($matches && isset($matches->live_games)) {
            $data['matches'] = $matches->live_games;
        }

        if ($this->_doCache)
            $cache = Cache::get('live_matches');
        else
            $cache = null;

        if (null === $cache) {
            $recentResult = \App\LiveMatch::where('is_finished', '1')->take(10)->orderBy('finished_at', 'DESC')->get(); //DB::select("SELECT * FROM live_matches WHERE is_finished = 1 ORDER BY finished_at DESC LIMIT 10");

            if (count($recentResult) > 0) {
                $data['recentMatches'] = array();
                foreach ($recentResult as $k=>$recent) {
                        $league = $recent->league;

                        $match = new stdClass;
                        $match->match_id = $recent->match_id;
                        $match->league_id = $recent->league_id;
                        $match->league_name = is_object($league) ? $league->name : "";
                        $match->radiant = $recent->radiant;
                        $match->dire = $recent->dire;
                        $match->series_type = $recent->series_type;
                        $match->game_number = $recent->game_number;
                        $match->stage = $recent->stage;
                        $match->finished_at = $recent->finished_at;

                        $data['recentMatches'][] = $match;
                }
            }
        } else {
            $data['recentMatches'][] = json_decode($cache);
        }

        $data['homeActiveMenu'] = true;

        $game = $request->currentGame;

        $data['dummies']['completed'] = \App\DummyMatch::whereNotNull('winner')
            ->whereNotNull('dummy_matches.start')
            ->select('dummy_matches.*')
            ->orWhere('is_tie', 1)
            ->orWhere('is_forfeited', 1)
            ->with('stageRound.stageFormat.stage.tournament')
            ->with('opponent1_details')
            ->with('opponent2_details')
            ->with('game')
            ->orderBy('dummy_matches.start', 'desc')
            ->where('dummy_matches.hidden', 0)
            ->where('stage_rounds.hidden', 0)
            ->where('stage_formats.hidden', 0)
            ->where('stages.hidden', 0)
            ->leftJoin('stage_rounds', 'dummy_matches.round_id', '=', 'stage_rounds.id')
            ->leftJoin('stage_formats', 'stage_rounds.stage_format_id', '=', 'stage_formats.id')
            ->leftJoin('stages', 'stage_formats.stage_id', '=', 'stages.id')
            ->take(50);

        $data['dummies']['upcoming'] = \App\DummyMatch::where('dummy_matches.start', '>',
            \Carbon\Carbon::now()->toDateTimeString())
            ->whereNotNull('dummy_matches.start')
            ->select('dummy_matches.*')
            ->with('stageRound.stageFormat.stage.tournament')
            ->with('opponent1_details')
            ->with('opponent2_details')
            ->with('game')
            ->orderBy('dummy_matches.start', 'asc')
            ->where('dummy_matches.hidden', 0)
            ->where('stage_rounds.hidden', 0)
            ->where('stage_formats.hidden', 0)
            ->where('stages.hidden', 0)
            ->leftJoin('stage_rounds', 'dummy_matches.round_id', '=', 'stage_rounds.id')
            ->leftJoin('stage_formats', 'stage_rounds.stage_format_id', '=', 'stage_formats.id')
            ->leftJoin('stages', 'stage_formats.stage_id', '=', 'stages.id')
            ->take(50);

        $data['dummies']['live'] = \App\DummyMatch::where('dummy_matches.start', '<=', \Carbon\Carbon::now()->toDateTimeString())
            ->whereNotNull('dummy_matches.start')
            ->select('dummy_matches.*')
            ->whereNull('winner')
            ->where('is_tie', 0)
            ->where('is_forfeited', 0)
            ->with('stageRound.stageFormat.stage.tournament')
            ->with('opponent1_details')
            ->with('opponent2_details')
            ->with('game')
            ->orderBy('dummy_matches.start', 'asc')
            ->where('dummy_matches.hidden', 0)
            ->where('stage_rounds.hidden', 0)
            ->where('stage_formats.hidden', 0)
            ->where('stages.hidden', 0)
            ->leftJoin('stage_rounds', 'dummy_matches.round_id', '=', 'stage_rounds.id')
            ->leftJoin('stage_formats', 'stage_rounds.stage_format_id', '=', 'stage_formats.id')
            ->leftJoin('stages', 'stage_formats.stage_id', '=', 'stages.id')
            ->take(50);
        $data['dummies']['tba'] = \App\DummyMatch::whereNull('dummy_matches.start')
            ->select('dummy_matches.*')
            ->whereNull('winner')
            ->where('is_tie', 0)
            ->where('is_forfeited', 0)
            ->with('stageRound.stageFormat.stage.tournament')
            ->with('opponent1_details')
            ->with('opponent2_details')
            ->with('game')
            ->orderBy('dummy_matches.start', 'asc')
            ->where('dummy_matches.hidden', 0)
            ->where('stage_rounds.hidden', 0)
            ->where('stage_formats.hidden', 0)
            ->where('stages.hidden', 0)
            ->leftJoin('stage_rounds', 'dummy_matches.round_id', '=', 'stage_rounds.id')
            ->leftJoin('stage_formats', 'stage_rounds.stage_format_id', '=', 'stage_formats.id')
            ->leftJoin('stages', 'stage_formats.stage_id', '=', 'stages.id')
            ->take(50);

        if ($game) {
            $data['dummies']['completed']->where('game_id', $game->id);
            $data['dummies']['upcoming']->where('game_id', $game->id);
            $data['dummies']['live']->where('game_id', $game->id);
            $data['dummies']['tba']->where('game_id', $game->id);
        }

        $data['dummies']['completed'] = $data['dummies']['completed']->get();
        $data['dummies']['upcoming'] = $data['dummies']['upcoming']->get();
        $data['dummies']['live'] = $data['dummies']['live']->get();
        $data['dummies']['tba'] = $data['dummies']['tba']->get();

        return view('live_matches', $data);
    }

    private function _getObject($key)
    {
        try {
            $object = $this->_s3->getObject([
                'Bucket' => $this->_bucket, // REQUIRED
                'Key' => $key,
            ]);
        } catch (\Aws\S3\Exception\S3Exception $e) {
           \Log::error($e->getMessage());
            return false;
        }

        if ($object) {
            return $object['Body'];
        } else {
            return false;
        }
    }

    public function contact() {
        $name = \Input::get('name');
        $mail = \Input::get('mail');
        $phone = \Input::get('phone');
        $text = \Input::get('text');
        $gResponse = \Input::get('recaptchaResponse');

        $concatText = $text."\n\r\tName: ".$name."\n\r\tE-mail: ".$mail."\n\r\tPhone: ".$phone;

        $secret = env('G_RECAPTCHA_SECRET');
        $recaptcha = new \ReCaptcha\ReCaptcha($secret);
        $resp = $recaptcha->verify($gResponse, \Request::ip());
        if ($resp->isSuccess()) {
            Mail::raw($concatText, function ($message) use ($name, $mail) {
                $message->from("noreply@esportsconstruct.com");
                $message->replyTo($mail, $name);
                $message->to("hello@esportsconstruct.com");
                $message->subject("Contact Form");
            });

            $retData = array(
                    "status" => "success"
                );
        } else {
            $errors = $resp->getErrorCodes();

            $retData = array(
                    "status" => "error",
                    "message" => "Couldn't verify your reCaptcha input! "
                );
        }

        return response()->json($retData);
    }

    public function landingPage(){

        return View::make('landing_page');
    }

    public function dump($matchId, $duration=false) {
        if (!$duration || $duration === 0)
            $data = $this->_getObject('matches/'.$matchId.'/'.$matchId.'.json');
        else
            $data = $this->_getObject('matches/'.$matchId.'/'.$duration.'.json');

        if ($data) {
            $array = json_decode($data, true);

            $stage = is_array($array['stage']) ? "" : $array['stage'];

            $csvData = "League ID,League Name,Radiant,Dire,Started At,Stage\n";
            $csvData .= $array['league_id'].",".$array['league_name'].",".$array['radiant'].",".$array['dire'].",".date("d-m-Y H:i", $array['started_at']).",".$stage."\n";
            $csvData .= "\n";
            $csvData .= "Player Name,Player ID,Team,Hero,Kills,Deaths,Assissts,Last Hits,Denies,Gold,Level,Gold Per Minute,XP Per Minute,Item 1,Item 2,Item 3,Item 4,Item 5,Item 6\n";

            if (count($array['radiant_players']) > 0) {
                foreach ($array['radiant_players'] as $radiant) {
                    $csvData .= $radiant['name'].",".$radiant['account_id'].",Radiant,".$radiant['hero_id'].",".$radiant['kills'].",".$radiant['deaths'].",".$radiant['assists'].",".$radiant['last_hits'].",".$radiant['denies'].",".$radiant['gold'].",".$radiant['level'].",".$radiant['gold_per_min'].",".$radiant['xp_per_min'].",".$radiant['item0'].",".$radiant['item1'].",".$radiant['item2'].",".$radiant['item3'].",".$radiant['item4'].",".$radiant['item5']."\n";
                }
            }

            if (count($array['dire_players']) > 0) {
                foreach ($array['dire_players'] as $dire) {
                    $csvData .= $dire['name'].",".$dire['account_id'].",Dire,".$dire['hero_id'].",".$dire['kills'].",".$dire['deaths'].",".$dire['assists'].",".$dire['last_hits'].",".$dire['denies'].",".$dire['gold'].",".$dire['level'].",".$dire['gold_per_min'].",".$dire['xp_per_min'].",".$dire['item0'].",".$dire['item1'].",".$dire['item2'].",".$dire['item3'].",".$dire['item4'].",".$dire['item5']."\n";
                }
            }

            $this->_putObject('csvs/'.$matchId.'-data.csv', $csvData, 'application/octet-stream');

            return redirect('https://s3.eu-central-1.amazonaws.com/esportslytics/csvs/'.$matchId.'-data.csv');
        } else {
            return redirect(groute('/'));
        }
    }

    private function _putObject($fileName, $contents, $contentType="application/json") {
        $this->_s3->putObject([
            'ACL'           => 'public-read',
            'Bucket'        => $this->_bucket, // REQUIRED
            'Key'           => $fileName, // REQUIRED
            'Body'          => $contents,
            'ContentType'   => $contentType,
        ]);

        return $this->_s3->getWaiter('ObjectExists', array(
                'Bucket' => $this->_bucket,
                'Key'    => $fileName
            ));
    }
}