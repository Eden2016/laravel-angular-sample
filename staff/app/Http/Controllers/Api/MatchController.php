<?php
namespace App\Http\Controllers\Api;

use App\DummyMatch;
use App\Models\MatchesStreams;
use PDO;
use Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use View;
use Validator;
use Input;
use Cache;
use Log;
use Dota2Api\Api;
use Session;

class MatchController extends Controller
{
	private $_doCache = true;

	public function index($game) {
		$gameId = \App\Services\GameServices::getGameId($game);

		$skip = Input::has('skip') ? intval(Input::get('skip')) : 0;
		$results = Input::has('results') ? intval(Input::get('results')) : 100;
		$date = Input::has('date') ? Input::get('date') : false;

		if ($results > 100)
			$results = 100;

		$matches = \App\DummyMatch::where('game_id', $gameId)
					->with('opponent1_details')
					->with('opponent2_details');

		if ($results)
			$matches->take($results);

		if ($skip > 0)
			$matches->skip($skip);

		if ($date)
			$matches->where('start', '>=', $start);

		$matches = $matches->get();

		if (count($matches) > 0) {
			$retData = array(
					"status" => "success",
					"result" => $matches
				);
		} else {
			$retData = array(
					"status" => "fail",
					"message" => "No matches found"
				);
		}

		return Response::json($retData);
	}

	public function create($game) {

	}

	public function store($game) {

	}

	public function show($game, $id) {
		try {
			$match = \App\DummyMatch::where('id', $id)
						->with('matchGames')
						->firstOrFail();

			/*if ($game == "dota2")
				$matchData = $this->_fetchDotaMatch($match->match_id);*/

			$retData = array(
					"status" => "success",
					"result" => $match
				);
		} catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
			$retData = array(
					"status" => "fail",
					"message" => "No match found"
				);
		}

		return Response::json($retData);
	}

	public function edit($game, $id) {

	}

	public function update($game, $id) {

	}

	public function destroy($game, $id) {

	}

    public function ignoreStream(Request $request)
    {
        $stream_id = $request->get('id');
        $match = $request->get('match');
        $match = DummyMatch::find($match);
        if (!$match) {
            return response()->json([
                "success" => false,
                "message" => 'Match not found'
            ], 402);
        }

        /**
         * Don't ask why. Some PHP bug disallow $stream_id to be added directly to $match->ignored_streams
         */
        $ignored_streams = (array)$match->ignored_streams;
        $ignored_streams[] = $stream_id;
        try {

            $match->ignored_streams = array_unique($ignored_streams);
            $match->save();
            MatchesStreams::where('matches_id', $match->id)->where('streams_id', $stream_id)->delete();
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ], 500);
        }


        return response()->json($match, 200);
    }

	private function _fetchDotaMatch($matchId) {
		$match = \App\Match::find($matchId);

		$retData = array();
		if ($match) {
			$retData['match']['id'] 				= $match->match_id;
			$retData['match']['radiant_win'] 		= $match->radiant_win;
			$retData['match']['duration'] 			= $match->duration;
			$retData['match']['start'] 				= $match->start_time;
			$retData['match']['radiant_team_id'] 	= $match->radiant_team_id;
			$retData['match']['radiant'] 			= $match->radiant_name;
			$retData['match']['dire_team_id'] 		= $match->dire_team_id;
			$retData['match']['dire_name'] 			= $match->dire_name;


			$league = \App\League::find($match->leagueid);
			if ($league){
				$retData['match']['league_id'] = $match->leagueid;
				$retdata['match']['league_name'] = $league->name;
			}

			$slots = $match->slots;
			if (count($slots) > 0) {
				foreach ($slots as $k=>$slot) {
					$player = $slot->player;

					if (in_array($slot->player_slot, \App\Slot::RADIANT_ARRAY)) {
						$side = 'radiant';
					} else {
						$side = 'dire';
					}

					$retData['match'][$side][$slot->id]['name'] 		= $player->name;
					$retData['match'][$side][$slot->id]['hero_id'] 		= $slot->hero_id;
					$retData['match'][$side][$slot->id]['hero_name'] 	= $slot->hero_name;
					$retData['match'][$side][$slot->id]['level'] 		= $slot->level;
					$retData['match'][$side][$slot->id]['kills'] 		= $slot->kills;
					$retData['match'][$side][$slot->id]['deaths'] 		= $slot->deaths;
					$retData['match'][$side][$slot->id]['assists'] 		= $slot->assists;
					$retData['match'][$side][$slot->id]['gold'] 		= $slot->gold;
					$retData['match'][$side][$slot->id]['last_hits'] 	= $slot->last_hits;
					$retData['match'][$side][$slot->id]['denies'] 		= $slot->denies;
					$retData['match'][$side][$slot->id]['gold_per_min'] = $slot->gold_per_min;
					$retData['match'][$side][$slot->id]['xp_per_min'] 	= $slot->xp_per_min;
					$retData['match'][$side][$slot->id]['gold_spent'] 	= $slot->gold_spent;
					$retData['match'][$side][$slot->id]['hero_damage'] 	= $slot->hero_damage;
					$retData['match'][$side][$slot->id]['tower_damage'] = $slot->tower_damage;
					$retData['match'][$side][$slot->id]['hero_healing'] = $slot->hero_healing;
					$retData['match'][$side][$slot->id]['item_0'] 		= $slot->item_0;
					$retData['match'][$side][$slot->id]['item_1'] 		= $slot->item_1;
					$retData['match'][$side][$slot->id]['item_2'] 		= $slot->item_2;
					$retData['match'][$side][$slot->id]['item_3'] 		= $slot->item_3;
					$retData['match'][$side][$slot->id]['item_4'] 		= $slot->item_4;
					$retData['match'][$side][$slot->id]['item_5'] 		= $slot->item_5;
				}
			}
		} else {
			$retData = false;
		}

		return $retData;
	}
}