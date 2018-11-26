<?php
namespace App\Http\Controllers;

use App\Game;
use App\Individual;
use App\MatchGame;
use App\Models\MatchesStreams;
use App\Http\Controllers\Controller;
use App\OpponentPrefill;
use App\StageFormat;
use App\ToutouMatch;
use App\Models\OddStreams;

use Datatables;
use Dota2Api\Api;
use DB;
use View;
use Validator;
use Input;
use Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Cache;

class MatchController extends Controller
{
    /**
     * @var S3ClientObject
     **/
    protected $_s3;

    /**
     * @var string
     **/
    protected $_bucket;

    /**
     * @var string
     **/
    protected $_game;

    public function __construct()
    {
        //Initialize DOTA2 Web API
        Api::init(getenv('STEAM_API_KEY_TEST'),
            array(getenv('DB_HOST'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'), getenv('DB_DATABASE'), ''));

        //Initialize Amazon S3 SDK
        $this->_s3 = \AWS::createClient('s3');
        $this->_bucket = getenv('BUCKET_NAME');

        $this->_game = request()->currentGameSlug;
    }

    /**
     * Show list of dummy matches
     *
     * @return Illuminate\View\View
     */
    public function listDummyMatches()
    {
        $data['game'] = \App\Game::where('slug', $this->_game)->firstOrFail();
        $data['patches'] = $data['game']->patches;

        $data['completed'] = \App\DummyMatch::whereNotNull('winner')
            ->orWhere('is_tie', 1)
            ->orWhere('is_forfeited', 1)
            ->with('stageRound.stageFormat.stage.tournament')
            ->with('opponent1_details')
            ->with('opponent2_details');

        $data['upcoming'] = \App\DummyMatch::where('start', '>', \Carbon\Carbon::now()->toDateTimeString())
            ->orWhereNull('start')
            ->with('stageRound.stageFormat.stage.tournament')
            ->with('opponent1_details')
            ->with('opponent2_details');

        $data['live'] = \App\DummyMatch::where('start', '<=', \Carbon\Carbon::now()->toDateTimeString())
            ->whereNull('winner')
            ->where('is_tie', 0)
            ->where('is_forfeited', 0)
            ->with('stageRound.stageFormat.stage.tournament')
            ->with('opponent1_details')
            ->with('opponent2_details');

        if ($data['game']) {
            /*$data['completed']->where('game', $data['game']->id);
            $data['upcoming']->where('game', $data['game']->id);
            $data['live']->where('game', $data['game']->id);*/
        }

        $data['completed'] = $data['completed']->get();
        $data['upcoming'] = $data['upcoming']->get();
        $data['live'] = $data['live']->get();

        $data['matchesActiveMenu'] = true;

        return view('match/listdummies', $data);
    }

    public function listCsgoMatches()
    {
        $data['matchesActiveMenu'] = true;
        return view('match/general_matches', $data);
    }

    public function listDotaMatches(){
        $data['matchesActiveMenu'] = true;
        return view('match/general_matches', $data);
    }

    public function listGeneralMatches()
    {
        $data['matchesActiveMenu'] = true;
        return view('match/general_matches', $data);
    }

    public function dataTableQuery(Request $request)
    {
        $model = \App\DummyMatch::query();
        return Datatables::eloquent($model->select('dummy_matches.*')
            ->where('dummy_matches.hidden', 0)
            ->where('stage_rounds.hidden', 0)
            ->where('stage_formats.hidden', 0)
            ->where('stages.hidden', 0)
            ->where(function ($q) {
                $q->where('opponent1', '!=', 34);
                $q->where('opponent1', '!=', 35);
            })
            ->leftJoin('stage_rounds', 'dummy_matches.round_id', '=', 'stage_rounds.id')
            ->leftJoin('stage_formats', 'stage_rounds.stage_format_id', '=', 'stage_formats.id')
            ->leftJoin('stages', 'stage_formats.stage_id', '=', 'stages.id'))
            ->filter(function ($query) use ($request) {
                switch($request->get('match_type', 'all')) {
                    case 'completed':
                        $query->whereNotNull('dummy_matches.start')
                            ->whereNotNull('winner')
                            ->orWhere('is_tie', 1)
                            ->orWhere('is_forfeited', 1);
                        break;
                    case 'upcoming':
                        $query->where('dummy_matches.start', '>',
                                \Carbon\Carbon::now()->toDateTimeString())
                            ->whereNotNull('dummy_matches.start');
                        break;
                    case 'live':
                        $query->where('dummy_matches.start', '<=', \Carbon\Carbon::now()->toDateTimeString())
                            ->whereNotNull('dummy_matches.start')
                            ->whereNull('winner')
                            ->where('is_tie', 0)
                            ->where('is_forfeited', 0);
                        break;
                    case 'tba':
                        $query->whereNull('dummy_matches.start')
                            ->whereNull('winner')
                            ->where('is_tie', 0)
                            ->where('is_forfeited', 0);
                        break;
                    default:
                        break;
                }
                if($request->get('game_id')) {
                    $query->where('dummy_matches.game_id', $request->get('game_id'));
                }
            })
            ->addColumn('tournament', function(\App\DummyMatch $match) {
                return sprintf('<a href="%s">%s</a>',
                    groute('tournament.view', \App\Game::allCached($match->game_id)->slug, ['tournamentId' => $match->tournament->id]),
                    $match->tournament->name);
            })
            ->editColumn('start', function(\App\DummyMatch $match) {
                return date_convert($match->start, 'UTC', Auth::user()->timezone, 'Y-m-d H:i:s', '<b>H:i</b> Y-m-d');
            })
            ->addColumn('versus', function(\App\DummyMatch $match) {
                return sprintf('<a href="%s">vs</a>', groute('match.view', \App\Game::allCached($match->game_id)->slug, [
                    'tournamentId' => $match->tournament->id,
                    'stageId' => $match->stageRound->stageFormat->stage->id,
                    'sfId' => $match->stageRound->stageFormat->id,
                    'matchId' => $match->id
                ]));
            })
            ->addColumn('opponent1_detail', function(\App\DummyMatch $match) {
                return sprintf('<a href="%s">%s</a>',
                    groute('team.show',\App\Game::allCached($match->game_id)->slug, ['teamId' => $match->opponent1]),
                    $match->opponent1_details->name);
            })
            ->addColumn('opponent2_detail', function(\App\DummyMatch $match) {
                return sprintf('<a href="%s">%s</a>',
                    groute('team.show', \App\Game::allCached($match->game_id)->slug, ['teamId' => $match->opponent2]),
                    $match->opponent2_details->name);
            })
            ->addColumn('game', function(\App\DummyMatch $match) {
                return \App\Game::allCached($match->game_id)->name;
            })
            ->make(true);

    }
    /**
     * Show list of Steam API matches with a given state
     *
     * @param string $state
     *
     * @return Illuminate\View\View
     */
    public function listMatches($state = 'completed')
    {
        $data['game'] = \App\Game::where('slug', $this->_game)->firstOrFail();
        $data['patches'] = $data['game']->patches;
        $data['matchesActiveMenu'] = true;

        switch ($state) {
            case 'completed':
                if (!Input::has("team")) {
                    $playersNum = \App\LiveMatch::where('is_finished', 1);
                } else {
                    $playersNum = 1;
                }
                break;
            case 'upcoming':
                if (!Input::has("team")) {
                    $playersNum = \App\DummyMatch::where('status', \App\DummyMatch::STATUS_UPCOMING);
                } else {
                    $playersNum = 1;
                }
                break;
            case 'live':
                if (!Input::has("team")) {
                    $playersNum = \App\LiveMatch::where('is_finished', 0)->where('started_at', '<', time());
                } else {
                    $playersNum = 1;
                }
                break;
        }

        if (Input::has('patch')) {
            $patch = \App\Patch::find(Input::get('patch'));

            if (null !== $patch && is_object($playersNum)) {
                $playersNum = $playersNum->where('started_at', '>', strtotime($patch->date));
            }
        }

        if (is_object($playersNum)) {
            $playersNum = $playersNum->count();
        }

        $page = Input::has('page') ? Input::get('page') : 1;
        $perPage = Input::has('perPage') ? Input::get('perPage') : 30;
        $pagesNum = floor($playersNum / $perPage);

        $skipNum = ($page * $perPage) - $perPage;

        switch ($state) {
            case 'completed':
                $data['completed'] = true;

                if (!Input::has("team")) {
                    $data['matches'] = \App\LiveMatch::orderBy('started_at', 'DESC')->where('is_finished',
                        1)->skip($skipNum)->take($perPage);
                } else {
                    if (Input::get("type") == 1) {
                        $data['matches'] = \App\LiveMatch::orderBy('started_at', 'DESC')->where('is_finished',
                            1)->where('radiant', 'LIKE', '%' . Input::get('team') . '%')->orWhere('dire', 'LIKE',
                            '%' . Input::get('team') . '%');
                    } else {
                        if (Input::get("type") == 2) {
                            $data['matches'] = array();

                            $leagues = \App\League::where('name', 'LIKE', '%' . Input::get("team") . '%')->get();

                            if ($leagues) {
                                $matches = array();
                                foreach ($leagues as $league) {
                                    $matches = $league->matches;

                                    if (count($matches) > 0) {
                                        foreach ($league->matches as $match) {
                                            $data['matches'][] = $match;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                break;
            case 'upcoming':
                $data['upcoming'] = true;

                if (!Input::has("team")) {
                    $data['matches'] = \App\DummyMatch::orderBy('start', 'ASC')->where('status',
                        \App\DummyMatch::STATUS_UPCOMING)->skip($skipNum)->take($perPage);
                } else {

                }
                break;
            case 'live':
                $data['live'] = true;

                if (!Input::has("team")) {
                    $data['matches'] = \App\LiveMatch::orderBy('started_at', 'DESC')->where('is_finished',
                        0)->where('started_at', '<', time())->skip($skipNum)->take($perPage);
                } else {
                    $data['matches'] = \App\LiveMatch::orderBy('started_at', 'DESC')->where('is_finished',
                        0)->where('started_at', '<', time())->where('radiant', 'LIKE',
                        '%' . Input::get('team') . '%')->orWhere('dire', 'LIKE', '%' . Input::get('team') . '%');
                }
                break;
        }

        if (isset($data['matches']) && !is_array($data['matches'])) {
            if (Input::has('patch')) {
                $patch = \App\Patch::find(Input::get('patch'));

                if (null !== $patch) {
                    $data['matches'] = $data['matches']->where('started_at', '>', strtotime($patch->date));
                }
            }

            $data['matches'] = $data['matches']->get();

            if (Input::has('patch')) {
                $data['selectedPatch'] = Input::get('patch');
            }
        }

        if (Input::get("date") != "") {
            list($day, $month, $year) = explode("-", Input::get("date"));

            $timeStart = mktime(0, 0, 0, $month, $day, $year);
            $timeEnd = mktime(23, 59, 59, $month, $day, $year);

            foreach ($data['matches'] as $k => $match) {
                if ($match->started_at < $timeStart || $match->started_at > $timeEnd) {
                    unset($data['matches'][$k]);
                }
            }
        }

        $data['pagesNum'] = $pagesNum;
        $data['currentPage'] = $page;
        $data['startPage'] = $page > 5 ? $page - 5 : 1;
        $data['endPage'] = $page + 5 > $pagesNum ? $pagesNum : $page + 5;

        $data['matchListActiveMenu'] = true;

        return view('match/list', $data);

    }

    /**
     * Show Steam API match data
     *
     * @param int $matchId
     *
     * @return Illuminate\View\View
     */
    public function showMatch($matchId)
    {
        $match = \App\DummyMatch::where('id', $matchId)
            ->with('stageRound.stageFormat.stage.tournament.game')
            ->with('matchGames')
//            ->with('opponent1_details')
//            ->with('opponent2_details')
            ->with('opponent1_details.roster')
            ->with('opponent2_details.roster')
            ->get();


        if (count($match) > 0) {
            $data['match'] = $match[0];
            $opponent1 = $data['match']->opponent1;
            $opponent2 = $data['match']->opponent2;
            if (!$data['match']->opponent1_members) {
                try {
                    $data['match']->opponent1_members = OpponentPrefill::where('stage_format_id',
                        $data['match']->stageRound->stage_format_id)
                        ->where('opponent_id', $data['match']->opponent1)->first()->team_members;
                } catch (\Exception $e) {
                    $data['match']->opponent1_members = [];
                }
            }
            if (!$data['match']->opponent2_members) {
                try {
                    $data['match']->opponent2_members = OpponentPrefill::where('stage_format_id',
                        $data['match']->stageRound->stage_format_id)
                        ->where('opponent_id', $data['match']->opponent2)->first()->team_members;
                } catch (\Exception $e) {
                    $data['match']->opponent2_members = [];
                }
            }

            if ($data['match']->opponent1_members) {
                $roster_ids = $data['match']->opponent1_details->roster->pluck('id')->toArray();
                foreach ($data['match']->opponent1_members as $member) {
                    if (!in_array($member, $roster_ids)) {
                        $data['match']->opponent1_details->roster->push(Individual::find($member));
                    }
                }
            }

            if ($data['match']->opponent2_members) {
                $roster_ids = $data['match']->opponent2_details->roster->pluck('id')->toArray();
                foreach ($data['match']->opponent2_members as $member) {
                    if (!in_array($member, $roster_ids)) {
                        $data['match']->opponent2_details->roster->push(Individual::find($member));
                    }
                }
            }


            $opponent1score = null;
            $opponent2score = null;
            if (count($data['match']->matchGames) > 0) {
                $opponent1score = 0;
                $opponent2score = 0;
                foreach ($data['match']->matchGames as $mg) {
                    $opponent1score += $mg->opponent1_score;
                    $opponent2score += $mg->opponent2_score;
                }
            }

            $data['match']['opponent1_score'] = $opponent1score;
            $data['match']['opponent2_score'] = $opponent2score;

            $data['pastEncounters'] = \App\DummyMatch::where('hidden', 0)
                ->where(function ($query) use ($opponent1, $opponent2) {
                    $query->where('opponent1', $opponent1);
                    $query->where('opponent2', $opponent2);
                })
                ->orWhere(function ($query) use ($opponent1, $opponent2) {
                    $query->where('opponent2', $opponent1);
                    $query->where('opponent1', $opponent2);
                })
                ->with('matchGames')
                ->get();

            $data['matchesActiveMenu'] = true;
            if (request()->wantsJson()) {
                return response()->json($data);
            }
            return View::make('match/match', $data)
                ->nest('header_view', 'default/header')
                ->nest('footer_view', 'default/footer');
        } else {
            $matchRaw = $this->_getObject('matches/' . $matchId . '/' . $matchId . '.json');
            $nos3 = false;

            if ($matchRaw) {
                $match = json_decode($matchRaw);
            } else {
                $match = null;
            }

            if (null !== $match && $match->started_at && $match->started_at < time()) {
                $data['match'] = $match;
                $nos3 = false;
            } else {
                $match = \App\Match::find($matchId);

                if (null !== $match) {
                    $nos3 = true;
                    $data['match'] = $match;
                    $data['slots'] = $match->slots;
                    //dd($data['slots']);
                }
            }

            //Get historycal data for the match
            $result = $this->_s3->listObjects([
                'Bucket' => $this->_bucket, // REQUIRED
                'Marker' => 'matches/' . $matchId . '/',
            ]);

            $allMinutes = array();
            foreach ($result['Contents'] as $file) {
                list($mainDir, $matchIdDir, $minute) = explode("/", $file['Key']);

                if ($matchIdDir == $matchId) {
                    if ($minute != "0.json" && $minute != $matchIdDir . ".json") {
                        $duration = explode(".", $minute)[0];

                        $allMinutes[$duration] = array(
                            'match_id' => $matchId,
                            'duration' => $duration
                        );
                    }
                }
            }

            if (count($allMinutes) > 0) {
                ksort($allMinutes);
                $data['byTheMinute'] = $allMinutes;
            }

            //Map heroe IDs to hero names
            $heroesRaw = $this->_getObject('heroes.json');
            $heroes = json_decode($heroesRaw);

            $data['heroMap'] = array();
            foreach ($heroes->heroes as $hero) {
                $data['heroMap'][$hero->id] = $hero->localized_name;
            }

            //Map Item IDs to Item names
            $itemResult = DB::select('SELECT id, localized_name AS name FROM `items`');

            $data['itemMap'] = array();
            if ($itemResult && count($itemResult) > 0) {
                foreach ($itemResult as $item) {
                    $data['itemMap'][$item->id] = $item->name;
                }
            }

            if (request()->wantsJson()) {
                return response()->json($data);
            }

            if (!$nos3) {
                //return view('match/match', $data);
                return view('match/matchapi', $data);
            } else {
                return view('match/matchnos3', $data);

            }
        }
    }

    /**
     * Gets objects from the S3 storage
     *
     * @param string $key
     *
     * @return string|bool
     */
    private function _getObject($key)
    {
        try {
            $object = $this->_s3->getObject([
                'Bucket' => $this->_bucket, // REQUIRED
                'Key' => $key,
            ]);
        } catch (\Aws\S3\Exception\S3Exception $e) {
            Log::error($e->getMEssage());
            return false;
        }

        if ($object) {
            return $object['Body'];
        } else {
            return false;
        }
    }

    /**
     *
     *
     * @return Illuminate\View\View
     */
    public function showSingleMatch()
    {
        return view('match/match');
    }

    /**
     * Show Steam API match data at a given minute of its playthroiugh
     *
     * @param int $matchId
     * @param int $matchDuration
     *
     * @return Illuminate\View\View
     */
    public function showHistoryMatch($matchId, $matchDuration)
    {
        $matchRaw = $this->_getObject('matches/' . $matchId . '/' . $matchDuration . '.json');

        if ($matchRaw) {
            $match = json_decode($matchRaw);
        } else {
            $match = null;
        }

        if (null !== $match && $match->started_at && $match->started_at < time()) {
            $data['match'] = $match;
        }

        //Get historycal data for the match
        $result = $this->_s3->listObjects([
            'Bucket' => $this->_bucket, // REQUIRED
            'Marker' => 'matches/' . $matchId . '/',
        ]);

        $allMinutes = array();
        foreach ($result['Contents'] as $file) {
            list($mainDir, $matchIdDir, $minute) = explode("/", $file['Key']);

            if ($matchIdDir == $matchId) {
                if ($minute != "0.json" && $minute != $matchIdDir . ".json") {
                    $duration = explode(".", $minute)[0];

                    $allMinutes[$duration] = array(
                        'match_id' => $matchId,
                        'duration' => $duration
                    );
                }
            }
        }

        if (count($allMinutes) > 0) {
            ksort($allMinutes);
            $data['byTheMinute'] = $allMinutes;
        }

        $data['duration'] = $matchDuration;

        //Map heroe IDs to hero names
        $heroesRaw = $this->_getObject('heroes.json');
        $heroes = json_decode($heroesRaw);

        $data['heroMap'] = array();
        foreach ($heroes->heroes as $hero) {
            $data['heroMap'][$hero->id] = $hero->localized_name;
        }

        //Map Item IDs to Item names
        $itemResult = DB::select('SELECT id, localized_name AS name FROM `items`');

        $data['itemMap'] = array();
        if ($itemResult && count($itemResult) > 0) {
            foreach ($itemResult as $item) {
                $data['itemMap'][$item->id] = $item->name;
            }
        }

        //return view('match/match', $data);
        return view('match/match', $data);
    }

    /**
     * Edit form for a dummy match
     *
     * @param int $tournamentId
     * @param int $stageId
     * @param int $sfId
     * @param int $matchId
     *
     */
    public function edit($tournamentId, $stageId, $sfId, $matchId)
    {
        $data['match'] = \App\DummyMatch::find($matchId);

        if (null == $data['match']) {
            $data['errorMessage'] = "No such match found";
        } else {
            $data['matchGames'] = \App\MatchGame::where('dummy_match_id', '=', $matchId)->get();
        }

        $data['round'] = \App\StageRound::find($data['match']->round_id);
        $data['sf'] = \App\StageFormat::find($data['round']->stage_format_id);
        $data['stage'] = \App\Stage::find($data['sf']->stage_id);
        if (request()->wantsJson()) {
            return response()->json($data);
        }

        return view('match/edit', $data);
    }

    /**
     * Adds/Edits Dummy matches
     *
     * @param Request $request
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'opponent2' => 'required|max:100',
            'opponent1' => 'required|max:100'
        ]);

        $dmId = intval(Input::get('id'));
        $tournamentId = intval(Input::get('tournament'));
        $stageId = intval(Input::get('stage'));
        $sfId = intval(Input::get('sf'));
        $roundId = intval(Input::get('round'));

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $dm = \App\DummyMatch::find($dmId);

        //$dm->match_id = Input::get('match_id');
        $dm->opponent1 = Input::get('opponent1');
        $dm->opponent2 = Input::get('opponent2');

        if (request()->has('streams')) {
            MatchesStreams::where('matches_id', $dm->id)->delete();
            foreach (request()->get('streams') as $stream) {
                MatchesStreams::insert([
                    'matches_id' => $dm->id,
                    'streams_id' => $stream
                ]);
                if (($key = array_search($stream, (array)$dm->ignored_streams)) !== false) {
                    $ignored_streams = $dm->ignored_streams;
                    unset($ignored_streams[$key]);
                    $dm->ignored_streams = array_unique($ignored_streams);
                }
            }
        }

        if (Input::has('is_tie')) {
            $dm->is_tie = Input::get('is_tie', 0);
        }

        if (Input::has('winner') && Input::get('winner') != "" && Input::get('winner') > 0) {
            $dm->winner = intval(Input::get('winner'));
        }

        if (Input::get('start') != '') {
            $dm->start = date_convert(Input::get('start'), Auth::user()->timezone, 'UTC', 'Y-m-d H:i', 'Y-m-d H:i:s');
        } else {
            $dm->start = null;
        }
        if (Input::has('map_id')) {
            $dm->map_id = Input::get('map_id');
        }

        $dm->save();

        //Map the match to a TouTou Match if possible
        if (Input::has('toutou_match') && Input::get('toutou_match') > 0) {
            $ttMatch = \App\ToutouMatch::find((int)Input::get('toutou_match'));

            if (null !== $ttMatch) {
                $ttMatch->dummy_match = $dm->id;
                $ttMatch->automatic_assigment = 0;

                $ttMatch->save();
            }
        }

        //Clear match cache for API
        if ($dm) {
            if ($dm->game_id > 0) {
                $game = Game::find($dm->game_id);
            }
            else {
                $tournament = $dm->stageRound->stageFormat->stage->tournament;
                $game = Game::find($tournament->game_id);
            }

            Cache::forget(sprintf("match.data.%s.%d", $game->slug, $dm->id));
        }

        return redirect()->back();
    }

    /**
     * AJAX method for storing dummy match info
     *
     * @return string
     */
    public function storeMatch(Request $request)
    {
        $matchId = intval(Input::get('id'));

        $dm = \App\DummyMatch::where('id', $matchId)->with('stageRound')->first();

        $dm->opponent1 = Input::get('opponent1', $dm->opponent1);
        $dm->opponent2 = Input::get('opponent2', $dm->opponent2);
        $dm->position = $request->input('position', 0);

        if (Input::has('winner') && Input::get('winner') != "" && Input::get('winner') > 0) {
            $dm->winner = intval(Input::get('winner'));

            /*
                Move winner from this match to the next round
             */
            $nextRoundPos = round($dm->position / 2);
            $nextRoundNum = $dm->stageRound->number + 1;
            $nextRound = \App\StageRound::where('stage_format_id', $dm->stageRound->stage_format_id)->where('number', $nextRoundNum)->where('type', $dm->stageRound->type)->with('dummyMatches')->first();

            if (null !== $nextRound) {
                //If there is a next round, find the match we need to seed
                //then add the opponent to it
                if ($dm->stageRound->type == \App\StageRound::ROUND_TYPE_UPPER_BRACKET) {
                    $match = null;
                    if (count($nextRound->dummyMatches)) {
                        foreach ($nextRound->dummyMatches as $dmatch) {
                            if ($dmatch->position == $nextRoundPos)
                                $match = $dmatch;
                        }
                    }


                    if ($match !== null) {
                        if ($dm->position % 2 == 0) {
                            if ($match->opponent2 == 34 || $match->opponent2 == 35)
                                $match->opponent2 = $request->input('winner');
                        }
                        else {
                            if ($match->opponent1 == 34 || $match->opponent1 == 35)
                                $match->opponent1 = $request->input('winner');
                        }

                        $match->save();
                    }
                }
                else if ($dm->stageRound->type == \App\StageRound::ROUND_TYPE_LOWER_BRACKET) {
                    if ($nextRound->number % 2 != 0) {
                        $match = null;
                        if (count($nextRound->dummyMatches)) {
                            foreach ($nextRound->dummyMatches as $dmatch) {
                                if ($dmatch->position == $nextRoundPos)
                                    $match = $dmatch;
                            }
                        }

                        if ($match) {
                            if ($dm->position % 2 == 0) {
                                if ($match->opponent2 == 34 || $match->opponent2 == 35)
                                    $match->opponent2 = $request->input('winner');
                            }
                            else {
                                if ($match->opponent1 == 34 || $match->opponent1 == 35)
                                    $match->opponent1 = $request->input('winner');
                            }

                            $match->save();
                        }
                    }
                    else {
                        $match = null;
                        if (count($nextRound->dummyMatches)) {
                            foreach ($nextRound->dummyMatches as $dmatch) {
                                if ($dmatch->position == $dm->position)
                                    $match = $dmatch;
                            }
                        }

                        if ($match) {
                            if ($match->opponent2 == 34 || $match->opponent2 == 35) {
                                $match->opponent2 = $request->input('winner');
                                $match->save();
                            }
                        }
                    }
                }
            }
            else {
                //If there is no next round, look for a round of type ROUND_TYPE_FINAL
                //and seed the opponent in there
                //Do nothing if there is no final round, or this was already a final round
                if ($dm->stageRound->type != \App\StageRound::ROUND_TYPE_FINAL) {
                    $nextRound = \App\StageRound::where('stage_format_id', $dm->stageRound->stage_format_id)->where('type', \App\StageRound::ROUND_TYPE_FINAL)->with('dummyMatches')->first();
                    if (null !== $nextRound) {
                        $match = $nextRound->dummyMatches->first();
                        if ($dm->stageRound->type == \App\StageRound::ROUND_TYPE_UPPER_BRACKET) {
                            if ($match->opponent1 == 34 || $match->opponent1 == 35) {
                                $match->opponent1 = $request->input('winner');
                                $match->save();
                            }
                        }
                        else {
                            if ($match->opponent2 == 34 || $match->opponent2 == 35) {
                                $match->opponent2 = $request->input('winner');
                                $match->save();
                            }
                        }
                    }
                }
            }

            /*
                Move the loser of this match to lower bracket (if there is such)
             */
            //Check if this is already lower bracket -> then do nothing
            if ($dm->stageRound->type === \App\StageRound::ROUND_TYPE_UPPER_BRACKET) {
                $lowerRoundNum = ($dm->stageRound->number * 2) - 2;
                if ($lowerRoundNum === 0)
                    $lowerRoundNum = 1;

                $nextRound = \App\StageRound::where('stage_format_id', $dm->stageRound->stage_format_id)->where('number', $lowerRoundNum)->where('type', \App\StageRound::ROUND_TYPE_LOWER_BRACKET)->with('dummyMatches')->first();

                if (null !== $nextRound) {
                    //If there is a round in the lower bracket, find the match we need to seed
                    //then add the opponent to it
                    $match = null;
                    if (count($nextRound->dummyMatches)) {
                        foreach ($nextRound->dummyMatches as $dmatch) {
                            if ($nextRound->number != 1 && $dmatch->position == $dm->position) {
                                $match = $dmatch;
                            }
                            else if ($nextRound->number == 1 && $dmatch->position == round($dm->position / 2)) {
                                $match = $dmatch;
                            }
                        }
                    }

                    //Seed the loser from the upper bracket into the home team slot
                    //Only if the opponent is one of the placeholders (Opponent 1 and Opponent 2)
                    if ($match) {
                        if ($nextRound->number != 1) {
                            if ($match->opponent1 == 34 || $match->opponent1 == 35) {
                                $match->opponent1 = $dm->opponent1 == $request->input('winner') ? $dm->opponent2 : $dm->opponent1;
                                $match->save();
                            }
                        }
                        else {
                            if ($dm->position % 2 == 0) {
                                if ($match->opponent2 == 34 || $match->opponent2 == 35)
                                    $match->opponent2 = $dm->opponent1 == $request->input('winner') ? $dm->opponent2 : $dm->opponent1;
                            }
                            else {
                                if ($match->opponent1 == 34 || $match->opponent1 == 35)
                                    $match->opponent1 = $dm->opponent1 == $request->input('winner') ? $dm->opponent2 : $dm->opponent1;
                            }
                            $match->save();
                        }
                    }
                }
            }

        }

        if (Input::get('start') != '') {
            $dm->start = date_convert(Input::get('start'), Auth::user()->timezone, 'UTC', 'Y-m-d H:i', 'Y-m-d H:i:s');
        }

        if (Input::has('is_tie')) {
            $dm->is_tie = Input::get('is_tie', 0);
        }

        if (Input::has('is_forfeited')) {
            $dm->is_forfeited = Input::get('is_forfeited', 0);
        }
        $dm->disqualified_team = Input::get('disqualified_team', null);

        if (Input::has('map_id')) {
            $dm->map_id = Input::get('map_id');
        }
        $dm->save();

        //Map the match to a TouTou Match if possible
        if (Input::has('ttMatch') && Input::get('ttMatch') > 0) {
            $ttMatch = \App\ToutouMatch::find((int)Input::get('ttMatch'));

            if (null !== $ttMatch) {
                $ttMatch->dummy_match = $dm->id;
                $ttMatch->automatic_assigment = 0;

                $ttMatch->save();
            }
        }

        //Clear match cache for API
        if ($dm) {
            if ($dm->game_id > 0) {
                $game = Game::find($dm->game_id);
            }
            else {
                $tournament = $dm->stageRound->stageFormat->stage->tournament;
                $game = Game::find($tournament->game_id);
            }

            Cache::forget(sprintf("match.data.%s.%d", $game->slug, $dm->id));
        }

        return json_encode(array(
            "status" => "success"
        ));
    }

    /**
     * Displays a DotA2 Map with players and towers on it
     *
     * @param int $matchId
     *
     * @return Illuminate\View\View
     */
    public function showMap($matchId)
    {
        $data['test'] = true;

        $matchMapperWeb = new \Dota2Api\Mappers\MatchMapperWeb($matchId);
        $match = $matchMapperWeb->load();

        $data['match'] = \App\Match::find($matchId);

        if (null != $data['match']) {
            $data['tower_status_radiant'] = sprintf('%011b', $data['match']->tower_status_radiant);
            $data['tower_status_dire'] = sprintf('%011b', $data['match']->tower_status_dire);

            $data['barracks_status_radiant'] = sprintf('%06b', $data['match']->barracks_status_radiant);
            $data['barracks_status_dire'] = sprintf('%06b', $data['match']->barracks_status_dire);

            $matchHistory = DB::select('SELECT `mh`.`player_id`, `mh`.`hero_id`, `mh`.`kills`, `mh`.`pos_x`, `mh`.`pos_y`, `s`.`player_slot` AS `slot`, `u`.`personaname` AS `name` FROM `match_history` AS `mh` LEFT JOIN `slots` AS `s` ON `mh`.`match_id` = `s`.`match_id` AND `mh`.`player_id` = `s`.`account_id` LEFT JOIN `users` AS `u` ON `mh`.`player_id` = `u`.`account_id` WHERE `mh`.`match_id` = ' . $matchId . ' GROUP BY `mh`.`player_id`');

            $data['players'] = $matchHistory;

            $data['froms3'] = false;
        } else {
            $data['match'] = $this->_getObject('matches/' . $matchId . '/' . $matchId . '.json');
            $data['match'] = json_decode($data['match']);

            $status = isset($data['match']->tower_status_radiant) ? $data['match']->tower_status_radiant : 0;
            $data['tower_status_radiant'] = sprintf('%011b', $status);

            $status = isset($data['match']->tower_status_dire) ? $data['match']->tower_status_dire : 0;
            $data['tower_status_dire'] = sprintf('%011b', $status);

            $status = isset($data['match']->barracks_status_radiant) ? $data['match']->barracks_status_radiant : 0;
            $data['barracks_status_radiant'] = sprintf('%06b', $status);

            $status = isset($data['match']->barracks_status_dire) ? $data['match']->barracks_status_dire : 0;
            $data['barracks_status_dire'] = sprintf('%06b', $status);

            $data['dire_players'] = $data['match']->dire_players;
            $data['radiant_players'] = $data['match']->radiant_players;

            $data['froms3'] = true;
        }

        $data['radiant_tower_positions'] = array(
            array(77, 9), // t4 top
            array(80, 12), // t4 bot

            array(83, 23), // t3 bot
            array(85, 45), // t2 bot
            array(82, 79), // t1 bot

            array(71, 19), // t3 mid
            array(65, 26), // t2 mid
            array(55, 35), // t1 mid

            array(66, 7),  // t3 top
            array(53, 8),  // t2 top
            array(37, 8)  // t1 top
        );
        $data['radiant_barracks_positions'] = array(
            array(81, 19), // BOT RANGED
            array(86, 20), // BOT MELEE
            array(72, 15), // MID RANGED
            array(75, 18), // MID MELEE
            array(70, 4), // TOP RANGED
            array(70, 10) // TOP MELEE
        );

        $data['dire_tower_positions'] = array(
            array(14, 80), // t4 top
            array(17, 84), // t4 bot

            array(28, 86), // t3 bot
            array(45, 85), // t2 bot
            array(58, 85), // t1 bot

            array(24, 73), // t3 mid
            array(32, 65), // t2 mid
            array(41, 55), // t1 mid

            array(11, 69),  // t3 top
            array(11, 50),  // t2 top
            array(11, 17)  // t1 top
        );
        $data['dire_barracks_positions'] = array(
            array(25, 90), // BOT RANGED
            array(24, 84), // BOT MELEE
            array(20, 74), // MID RANGED
            array(23, 78), // MID MELEE
            array(8, 73), // TOP RANGED
            array(13, 73) // TOP MELEE
        );

        //Map heroe IDs to hero names
        $heroesRaw = $this->_getObject('heroes.json');
        $heroes = json_decode($heroesRaw);

        $data['heroMap'] = array();
        foreach ($heroes->heroes as $hero) {
            $data['heroMap'][$hero->id] = $hero->localized_name;
        }

        return view('match/map', $data);
    }

    /**
     * Deletes a dummy match
     *
     * @param int $matchId
     *
     * @return string
     */
    public function removeDummyMatch($matchId)
    {
        $dummyMatch = \App\DummyMatch::find($matchId);

        if ($dummyMatch) {
            $dummyMatch->delete();
        }

        return json_encode(array(
            "success" => true
        ));
    }

    /**
     * Gets info for a given dummy match
     *
     * @param int $matchId
     *
     * @return string
     */
    public function getDummyMatchInfo($matchId)
    {
        $dummyMatch = \App\DummyMatch::find($matchId);

        if ($dummyMatch) {
            $opponent1 = $dummyMatch->opponent1_details;
            $opponent2 = $dummyMatch->opponent2_details;
            $winnername = $dummyMatch->getWinner;

            if ($winnername) {
                $winner = $winnername->name;
            } else {
                $winner = "";
            }

            $return = array(
                "status" => "success",
                "match" => array(
                    "id" => $dummyMatch->id,
                    "opponent1name" => $opponent1->name,
                    "opponent1id" => $opponent1->id,
                    "opponent2name" => $opponent2->name,
                    "opponent2id" => $opponent2->id,
                    "winnername" => $winner,
                    "winner" => $dummyMatch->winner,
                    "is_tie" => $dummyMatch->is_tie,
                    "is_forfeited" => $dummyMatch->forfeited,
                    "start" => $dummyMatch->start ? date_convert($dummyMatch->start, 'UTC', Auth::user()->timezone, 'Y-m-d H:i:s', 'Y-m-d H:i') : '',
                    "map_id" => $dummyMatch->map_id,
                    "position" => $dummyMatch->position
                )
            );
        } else {
            $return = array(
                "status" => "error",
                "message" => "No match found!"
            );
        }

        return json_encode($return);
    }

    /**
     * Marks a dummy match as done
     *
     * @return Illuminate\Http\Response
     */
    public function markDone()
    {
        $matchId = intval(Input::get('id'));
        $dummyMatch = \App\DummyMatch::find($matchId);

        if ($dummyMatch) {
            $dummyMatch->done = 1;
            $dummyMatch->save();

            //$this->_markTournamentAsDone($dummyMatch->id);

            $retData = array(
                "status" => "success"
            );
        } else {
            $retData = array(
                "status" => "fail",
                "message" => "No match found"
            );
        }

        return response()->json($retData);
    }

    /**
     * Changes dummy match opponent
     *
     * @return Illuminate\Http\Response
     */
    public function changeOpponent()
    {
        $matchId = (int)Input::get('match');
        $opponentId = (int)Input::get('opponent');
        $side = (int)Input::get('side');

        $match = \App\DummyMatch::find($matchId);

        if (null !== $match) {
            if ($side == 1) {
                $match->opponent1 = $opponentId;
            } else {
                $match->opponent2 = $opponentId;
            }

            $match->save();

            $retData = array(
                "status" => "success"
            );
        } else {
            $retData = array(
                "status" => "error",
                "message" => "No match found!"
            );
        }

        return response()->json($retData);
    }

    /**
     * Gets drafts for a given dummy match
     *
     * @param int $matchId
     *
     * @return Illuminate\Http\Response
     */
    public function getDrafts($matchId)
    {
        $draft = \App\Models\MatchDraft::where('dummy_match_id', $matchId)->first();

        if (null !== $draft) {
            $retData = array(
                "status" => "success",
                "data" => $draft
            );
        }
        else {
            $retData = array(
                "status" => "error",
                "message" => "No draft found."
            );
        }

        return response()->json($retData);
    }

    /**
     * Saves drafts for a given dummy match
     *
     * @param Illuminate\Http\Request $request
     *
     * @return Illuminate\Http\Response
     */
    public function saveDrafts(Request $request)
    {
        $draft = \App\Models\MatchDraft::where('dummy_match_id', $request->input('match_id'))->first();

        if (null === $draft) {
            $draft = \App\Services\DraftServices::create(
                intval($request->input('match_id')),
                $request->input('draft')
            );

            if ($draft) {
                $retData = array(
                    "status" => "success",
                    "data" => $draft,
                    "requested_draft" => $request->input('draft')
                );
            }
            else {
                $retData = array(
                    "status" => "error",
                    "message" => "There was an error while creating the draft.",
                    "match_id" => $request->input('match_id'),
                    "draft" => $request->input('draft')
                );
            }
        }
        else {
            $draft = \App\Services\DraftServices::edit(
                $draft,
                $request->input('match_id'),
                $request->input('draft')
            );

            if ($draft) {
                $retData = array(
                    "status" => "success",
                    "data" => $draft
                );
            }
            else {
                $retData = array(
                    "status" => "error",
                    "message" => "There was an error while editing the draft."
                );
            }
        }

        return response()->json($retData);
    }

    /**
     * Move match up in the bracket
     * @param  int $matchId
     * @return Illuminate\Http\Redirect
     */
    public function moveUp($matchId)
    {
        $match = \App\DummyMatch::find($matchId);

        if (null !== $match) {
            $prevMatch = \App\DummyMatch::where('position', $match->position - 1)->where('round_id', $match->round_id)->first();

            if (null !== $prevMatch) {
                $prevMatch->position = $match->position;
                $prevMatch->save();
            }

            $match->position = $match->position - 1;
            $match->save();
        }

        return redirect()->back();
    }

    /**
     * Move match down in the bracket
     * @param  int $matchId
     * @return Illuminate\Http\Redirect
     */
    public function moveDown($matchId)
    {
        $match = \App\DummyMatch::find($matchId);

        if (null !== $match) {
            $nextMatch = \App\DummyMatch::where('position', $match->position + 1)->where('round_id', $match->round_id)->first();

            if (null !== $nextMatch) {
                $nextMatch->position = $match->position;
                $nextMatch->save();
            }

            $match->position = $match->position + 1;
            $match->save();
        }

        return redirect()->back();
    }

    /**
     * Returns all Toutou matches
     */
    public function toutouMatches()
    {
        $data['matches'] = \App\ToutouMatch::where('active', 1)->get();

        return view('match/toutoumatches', $data);
    }

    public function singleToutouMatch($id)
    {
        $match = ToutouMatch::find($id);
        $streams = OddStreams::where('event_id', $id)->where('client_id', 1)->with('stream')->get();

        $streamData = [];
        if (count($streams)) {
            foreach ($streams as $stream) {
                $streamData[] = [
                    "id" => $stream->stream_id,
                    "text" => $stream->stream->title . " (".$stream->stream->lang.")"
                ];
            }
        }

        if (!$match) {
            abort(404);
        }

        $retData = [
            "event" => $match,
            "streams" => $streamData
        ];

        return response()->json($retData);
    }

    public function postSingleToutouMatch($id)
    {
        $match = ToutouMatch::find($id);
        if (!$match) {
            abort(404);
        }
        try {
            $match->update(request()->only(['dummy_match', 'game_id', 'game_number', 'automatic_assigment']));

            if (request()->has('streams')) {
                $streams = explode(",", request()->input('streams'));

                OddStreams::where('event_id', request()->input('id'))->where('client_id', 1)->delete();
                foreach ($streams as $stream) {
                    $oddStream = new OddStreams();

                    $oddStream->event_id = request()->input('id');
                    $oddStream->stream_id = $stream;
                    $oddStream->client_id = 1;

                    $oddStream->save();
                }
            }

            return response()->json('Match saved', 200);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    /**
     * Puts objects into the S3 storage
     *
     * @param string $fileName
     * @param string $contents
     * @param string $contentType
     *
     * @return Aws\Waiter
     */
    private function _putObject($fileName, $contents, $contentType = "application/json")
    {
        $this->_s3->putObject([
            'ACL' => 'public-read',
            'Bucket' => $this->_bucket, // REQUIRED
            'Key' => $fileName, // REQUIRED
            'Body' => $contents,
            'ContentType' => $contentType,
        ]);

        return $this->_s3->getWaiter('ObjectExists', array(
            'Bucket' => $this->_bucket,
            'Key' => $fileName
        ));
    }

}
