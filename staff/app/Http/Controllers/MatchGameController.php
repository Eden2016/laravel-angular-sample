<?php
namespace App\Http\Controllers;

use App\Game;
use App\Models\Dota2\Dota2ChampionBan;
use App\Models\Dota2\Dota2ChampionPick;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Dota2Api\Api;
use App\Services\MatchGameServices;
use View;
use Validator;
use Input;
use Illuminate\Support\Facades\Auth;
use App\DummyMatch;
use Cache;

class MatchGameController extends Controller
{
    public function __construct()
    {
        //Initialize DOTA2 Web API
        Api::init(getenv('STEAM_API_KEY_TEST'), array(getenv('DB_HOST'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'), getenv('DB_DATABASE'), ''));
    }

    /**
     * Fetch a match game from the database
     *
     * @param int $matchGameId
     *
     * @return string
     */
    public function get(Request $request, $matchGameId)
    {
        $matchGameId = intval($matchGameId);

        $mg = \App\MatchGame::with(['match.opponent1_details', 'match.opponent2_details'])->where('id', $matchGameId)->first();

        if (null !== $mg) {
            $return = array(
                    "success" => true,
                    "match_game" => $mg,
                    "start" => $mg->start && $mg->start != '0000-00-00 00:00:00' ? date_convert($mg->start, 'UTC', Auth::user()->timezone, 'Y-m-d H:i:s', 'Y-m-d H:i') : '',
                    "opponent1_standins" => MatchGameServices::getStandins($mg->id, $mg->match->opponent1),
                    "opponent2_standins" => MatchGameServices::getStandins($mg->id, $mg->match->opponent2),
                );

            if ($request->currentGameSlug == 'lol') {
                $return['picks'] = \App\Models\LolChampionPick::where('match_game_id', $matchGameId)->get();

                $return['opponent1_bans'] = \App\Models\LolChampionBan::where('match_game_id', $matchGameId)->where('team_id', $return['match_game']->match->opponent1)->get()->pluck("champion_id");
                $return['opponent2_bans'] = \App\Models\LolChampionBan::where('match_game_id', $matchGameId)->where('team_id', $return['match_game']->match->opponent2)->get()->pluck("champion_id");
            } elseif ($request->currentGameSlug== 'dota2') {
                $return['picks'] = Dota2ChampionPick::where('match_game_id', $matchGameId)->get();

                $return['opponent1_bans'] = Dota2ChampionBan::where('match_game_id', $matchGameId)->where('team_id',
                    $return['match_game']->match->opponent1)->get()->pluck("champion_id");
                $return['opponent2_bans'] = Dota2ChampionBan::where('match_game_id', $matchGameId)->where('team_id',
                    $return['match_game']->match->opponent2)->get()->pluck("champion_id");
            }
        }
        else {
            $return = array(
                    "error" => true,
                    "message" => "No match game found"
                );
        }

        return response()->json($return);
    }

    /**
     * Adds/edits a match game
     *
     * @param Illuminate\Http\Request $request
     *
     * @return string
     */
	public function store(Request $request)
    {
		if ($request->input('id')) {
        	$mgId      = (int)$request->input('id');
        	$matchGame = \App\MatchGame::find($mgId);

            if (null !== $matchGame) {
                $matchGame = MatchGameServices::edit($request, $matchGame);
            }
            else {
                $matchGame = false;
            }
        }
        else {
	        $matchGame = MatchGameServices::create($request);
	    }

        if ($request->input('match_date') != "" && $request->input('match_date') != null) {
            $dummyMatch = \App\DummyMatch::find($request->input('match'));

            if (null !== $dummyMatch) {
                $dummyMatch->start = date_convert($request->input('match_date'), Auth::user()->timezone, 'UTC', 'Y-m-d H:i', 'Y-m-d H:i:s');
                $dummyMatch->save();
            }
        }

        if ($matchGame && $matchGame->match_id > 0) {
            $hasMatch = \App\Match::where('match_id', $matchGame->match_id)->first();

            if (!$hasMatch) {
                $matchInfo = $this->_saveMatch($matchGame->match_id);

                if ($matchInfo) {
                    MatchGameServices::resultMatchGame($matchGame);
                }
            }
        }

        if ($matchGame) {
            MatchGameServices::addStandins($request, $matchGame->id);

            if ($request->currentGameSlug == 'lol' || $request->currentGameSlug == 'dota2') {
                MatchGameServices::championPicks($request, $matchGame);
                MatchGameServices::championBans($request, $matchGame);
            }

            $return = array(
                    "success" => true,
                    "match_id" => $matchGame->match_id,
                    "request" => $request
                );
        }
        else {
            $return = array(
                    "success" => false,
                    "match_id" => $matchGame->match_id
                );
        }

        //Clear match cache for API
        if ($matchGame) {
            $dm = $matchGame->match;

            if ($dm->game_id > 0) {
                $game = Game::find($dm->game_id);
            }
            else {
                $tournament = $dm->stageRound->stageFormat->stage->tournament;
                $game = Game::find($tournament->game_id);
            }

            Cache::forget(sprintf("match.data.%s.%d", $game->slug, $dm->id));
        }

        return response()->json($return);
	}

    /**
     * Saves DotA2 match info in the database
     *
     * @param int $matchId
     *
     * @return \Dota2Api\Mappers\MatchMapperDb
     */
    private function _saveMatch($matchId)
    {
        $mm = new \Dota2Api\Mappers\MatchMapperWeb($matchId);
        $match = $mm->load();

        if (null !== $match) {
            $saver = new \Dota2Api\Mappers\MatchMapperDb();
            $saver->save($match);
        }

        return $match;
    }

    /**
     * Removes a match game from the database
     *
     * @param int $matchGameId
     *
     * @return Illuminate\Http\Response
     */
    public function remove($matchGameId) {
        $mg = \App\MatchGame::find($matchGameId);

        if (null !== $mg) {
            $mg->delete();

            $retData = array(
                    "status" => "success"
                );
        } else {
            $retData = array(
                    "status" => "error",
                    "message" => "No match game found!"
                );
        }

        return response()->json($retData);
    }
}