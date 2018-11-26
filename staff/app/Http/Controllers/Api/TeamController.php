<?php
namespace App\Http\Controllers\Api;

use App\Team;
use App\TeamAccount;
use App\StageFormat;
use PDO;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Response;
use Input;
use Cache;
use Log;

class TeamController extends Controller
{
	/**
	 *
	 * @var bool
	 *
	 */
	private $_doCache = true;

	/**
	 *
	 * @var int
	 *
	 */
	private $_cacheTime;

	public function __construct() {

		$this->_cacheTime = 60 * 24; // 1 day
	}

	/**
	 *
	 *
	 */
	public function index($game) {
		$teams = \App\TeamAccount::where('active', 1);

		if (Input::has('created'))
			$teams->where('created', '>=', Input::get('created'));

		if (Input::has('region'))
			$teams->where('region', Input::get('region'));

		if (Input::has('location'))
			$teams->where('location', Input::get('location'));

		if (Input::has('name'))
			$teams->where('name', 'LIKE', '%'.Input::get('name').'%');

		$teams = $teams->get();
		return response()->json($teams);
	}

	public function create($game) {

	}

	public function store($game) {

	}

	public function show($game, $id) {
		$gameId = \App\Services\GameServices::getGameId($game);

		$team = \App\TeamAccount::find($id);
		if ($team) {
			$matches = \App\DummyMatch::where('opponent1', $team->id)->orWhere('opponent2', $team->id);

			$matchesFiltered = array();
			foreach ($matches as $match) {
				$tournament = $match->round->stageFormat->stage->tournament;

				if ($tournament->game_id == $gameId) {
					$matchesFiltered[] = $match;
				}
			}

			if ($this->_doCache)
				$cache = Cache::get(sprintf('api_team_stats_%d', $team->id));
			else
				$cache = null;

			if (null === $cache) {
				$retData['team'] = $team;

				//Get Current Roster
				$currentRoster = \App\PlayerTeam::where('team_id', $team->id)->whereNull('end_date')->get();

				if (count($currentRoster) > 0) {
					foreach ($currentRoster as $roster) {
						$player = $roster->player;

						$retData['stats']['current_roster'][$player->id] = $player;
					}
				} else {
					$retData['stats']['current_roster'] = [];
				}

				//Get Recent Roster
				$recentRoster = \App\PlayerTeam::where('team_id', $team->id)->whereNotNull('end_date')->get();

				if (count($recentRoster) > 0) {
					foreach ($recentRoster as $roster) {
						$player = $roster->player;

						$retData['stats']['recent_roster'][$player->id] = $player;
					}
				} else {
					$retData['stats']['recent_roster'] = [];
				}

				//Get Win Streak
				$retData['stats']['winStreak'] = 0;
				$retData['stats']['loseStreak'] = 0;
				foreach ($matchesFiltered as $match) {
					if ($match->winner == $teamID) {
						if ($retData['stats']['loseStreak'] > 0)
							break;

						$retData['stats']['winStreak']++;
					} else {
						if ($retData['stats']['winStreak'] > 0)
							break;

						$retData['stats']['loseStreak']++;
					}
				}

				$retData = json_encode($retData);

				if ($this->_doCache)
					Cache::put(sprintf('api_team_stats_%d', $id), $retData, $this->_cacheTime);
			} else {
				$retData = $cache;
			}
		} else {
			$retData = array(
					"success" => false,
					"message" => "No team with that ID found!"
				);
		}

		return response($retData)
            ->header('Content-Type', 'application/json');
	}

	public function edit($game, $id) {

	}

	public function update($game, $id) {

	}

	public function destroy($game, $id) {

	}

    public function members(Request $request)
    {
        $roster = TeamAccount::find($request->get('id'))->roster;

        if ($request->get('sfId')) {
        	$start = StageFormat::find($request->get('sfId'))->stage->start;

        	$roster = $roster->filter(function($p) use ($start) {
	            if(!$p->pivot->is_coach && ($p->pivot->end_date == null || strtotime($p->pivot->end_date) > strtotime($start)))
	                return true;

	            return false;
	        });
        }
        else {
        	$roster = $roster->filter(function($p){
	            if(!$p->pivot->is_coach && $p->pivot->end_date == null)
	                return true;

	            return false;
	        });
        }

        return $roster;
    }
}