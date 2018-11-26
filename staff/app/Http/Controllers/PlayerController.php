<?php
namespace App\Http\Controllers;

use App\Account;
use App\Individual;
use App\MatchGame;
use App\Slot;
use Datatables;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use View;
use Validator;
use Input;
use Cache;
use Dota2Api\Api;
use App\Services\CdnServices;

class PlayerController extends Controller
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

	public function __construct()
    {
		//Initialize DOTA2 Web API
		Api::init(getenv('STEAM_API_KEY_TEST'), array(getenv('DB_HOST'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'), getenv('DB_DATABASE'), ''));

		if (getenv('APP_ENV') === 'development')
			$this->_doCache = false;

		//Initialize Amazon S3 SDK
		$this->_s3 = \AWS::createClient('s3');
		$this->_bucket = getenv('BUCKET_NAME');
	}

	public function listPlayers(Request $request)
    {

        $data['user_prefilled'] = $request->get("username", '');
		$data['playersActiveMenu'] = true;

		return view('player/list', $data);
	}

    public function dataTableQuery(Request $request)
    {
        return Datatables::eloquent(\App\Individual::query())
            ->filter(function ($query) use ($request) {
                if($request->get('nickname')) {
                    $query->where('nickname', 'like', "%{$request->get('nickname')}%");
                }
                if($request->get('steamid')) {
                    $query->where('steam_id', '=', intval($request->get('steamid')));
                }
                if($request->has('uneditted')) {
                    $query->whereNull('bio');
                }
            })
            ->editColumn('steam_id', function(\App\Individual $player) {
                return '' . $player->steam_id;
            })
            ->make(true);
    }
	public function showDota($accountId)
    {
		$player = \App\Account::where('account_id', '=', $accountId)->first();

		if (null === $player) {
			App::abort(404);
		}

		//If we don't have the player name yet, get it
		if ($player->personaname == null) {
			$playersMapperWeb = new \Dota2Api\Mappers\PlayersMapperWeb();
			$playersInfo = $playersMapperWeb->addId($player->steamid)->load();

			if ($playersInfo) {
			    $player->personaname = $playersInfo[$player->steamid]->get('personaname');
			    $player->avatar = $playersInfo[$player->steamid]->get('avatarful');
			    $player->profileurl = $playersInfo[$player->steamid]->get('profileurl');

			    $player->save();
			}
		}

		$data['player'] = $player;

		if ($this->_doCache)
			$cache = Cache::get(sprintf('player_stats_%d', $data['player']->account_id));
		else
			$cache = null;

		if (null === $cache) {
			$data['slots'] = $player->slots;

			$data['stats']['alltime']['wins'] = 0;
			$data['stats']['alltime']['kills'] = 0;
			$data['stats']['alltime']['deaths'] = 0;
			$data['stats']['alltime']['assists'] = 0;
			$data['stats']['alltime']['gold_pm'] = 0;
			$data['stats']['alltime']['total_gold'] = 0;
			$data['stats']['alltime']['last_hits'] = 0;
			$data['stats']['alltime']['denies'] = 0;
			$data['stats']['alltime']['xp_pm'] = 0;
			$data['stats']['alltime']['level'] = 0;

			$data['stats']['month']['wins'] = 0;
			$data['stats']['month']['kills'] = 0;
			$data['stats']['month']['deaths'] = 0;
			$data['stats']['month']['assists'] = 0;
			$data['stats']['month']['gold_pm'] = 0;
			$data['stats']['month']['total_gold'] = 0;
			$data['stats']['month']['last_hits'] = 0;
			$data['stats']['month']['denies'] = 0;
			$data['stats']['month']['xp_pm'] = 0;
			$data['stats']['month']['level'] = 0;

			$data['stats']['quarter']['wins'] = 0;
			$data['stats']['quarter']['kills'] = 0;
			$data['stats']['quarter']['deaths'] = 0;
			$data['stats']['quarter']['assists'] = 0;
			$data['stats']['quarter']['gold_pm'] = 0;
			$data['stats']['quarter']['total_gold'] = 0;
			$data['stats']['quarter']['last_hits'] = 0;
			$data['stats']['quarter']['denies'] = 0;
			$data['stats']['quarter']['xp_pm'] = 0;
			$data['stats']['quarter']['level'] = 0;

			$data['stats']['year']['wins'] = 0;
			$data['stats']['year']['kills'] = 0;
			$data['stats']['year']['deaths'] = 0;
			$data['stats']['year']['assists'] = 0;
			$data['stats']['year']['gold_pm'] = 0;
			$data['stats']['year']['total_gold'] = 0;
			$data['stats']['year']['last_hits'] = 0;
			$data['stats']['year']['denies'] = 0;
			$data['stats']['year']['xp_pm'] = 0;
			$data['stats']['year']['level'] = 0;

			$month = 0;
			$quarter = 0;
			$year = 0;

			$iterations = 0;

			$monthHeroes = array();
			$quarterHeroes = array();
			$yearHeroes = array();

			foreach ($data['slots'] as $slot) {
				$match = $slot->match;
				$liveMatch = $match->liveMatch;

				//Get recent tournaments
				if (null != $liveMatch && $liveMatch->is_finished) {
					$league = $liveMatch->league;
					
					if (null != $league) {
						$data['stats']['recent_tournaments'][] = array(
								'id' => $league->leagueid,
								'name' => $league->name
							);
					}
				}

				//Get current tournaments
				if (null!== $liveMatch && !$liveMatch->is_finished && $iterations < 10) {
					$league = $liveMatch->league;
					
					$data['stats']['current_tournaments'][] = array(
							'id' => $league->leagueid,
							'name' => $league->name
						);
				}

				//1 Month stats
				if (strtotime($match->start_time) > time() - 86400 * 30) {
					$month++;

					if ($match->radiant_win && in_array($slot->player_slot, array(0, 1, 2, 3, 4)))
						$data['stats']['month']['wins']++;
					else if (!$match->radiant_win && in_array($slot->player_slot, array(128, 129, 130, 131, 132)))
						$data['stats']['month']['wins']++;

					$monthHeroes[] = $slot->hero_id;

					$data['stats']['month']['kills'] += $slot->kills;
					$data['stats']['month']['deaths'] += $slot->deaths;
					$data['stats']['month']['assists'] += $slot->assists;

					$data['stats']['month']['gold_pm'] += $slot->gold_per_min;

					$data['stats']['month']['total_gold'] += $slot->gold;
					$data['stats']['month']['last_hits'] += $slot->last_hits;
					$data['stats']['month']['denies'] += $slot->denies;
					$data['stats']['month']['xp_pm'] += $slot->xp_per_min;
					$data['stats']['month']['level'] += $slot->level;
				}

				//3 Month stats
				if (strtotime($match->start_time) > time() - 86400 * 90) {
					$quarter++;

					if ($match->radiant_win && in_array($slot->player_slot, \App\Slot::RADIANT_ARRAY))
						$data['stats']['quarter']['wins']++;
					else if (!$match->radiant_win && in_array($slot->player_slot, \App\Slot::DIRE_ARRAY))
						$data['stats']['quarter']['wins']++;

					$quarterHeroes[] = $slot->hero_id;

					$data['stats']['quarter']['kills'] += $slot->kills;
					$data['stats']['quarter']['deaths'] += $slot->deaths;
					$data['stats']['quarter']['assists'] += $slot->assists;

					$data['stats']['quarter']['gold_pm'] += $slot->gold_per_min;

					$data['stats']['quarter']['total_gold'] += $slot->gold;
					$data['stats']['quarter']['last_hits'] += $slot->last_hits;
					$data['stats']['quarter']['denies'] += $slot->denies;
					$data['stats']['quarter']['xp_pm'] += $slot->xp_per_min;
					$data['stats']['quarter']['level'] += $slot->level;
				}

				//12 Month stats
				if (strtotime($match->start_time) > time() - 86400 * 360) {
					$year++;

					if ($match->radiant_win && in_array($slot->player_slot, \App\Slot::RADIANT_ARRAY))
						$data['stats']['year']['wins']++;
					else if (!$match->radiant_win && in_array($slot->player_slot, \App\Slot::DIRE_ARRAY))
						$data['stats']['year']['wins']++;

					$yearHeroes[] = $slot->hero_id;

					$data['stats']['year']['kills'] += $slot->kills;
					$data['stats']['year']['deaths'] += $slot->deaths;
					$data['stats']['year']['assists'] += $slot->assists;

					$data['stats']['year']['gold_pm'] += $slot->gold_per_min;

					$data['stats']['year']['total_gold'] += $slot->gold;
					$data['stats']['year']['last_hits'] += $slot->last_hits;
					$data['stats']['year']['denies'] += $slot->denies;
					$data['stats']['year']['xp_pm'] += $slot->xp_per_min;
					$data['stats']['year']['level'] += $slot->level;

					if (in_array($slot->player_slot, \App\Slot::RADIANT_ARRAY)) {
						$data['stats']['recent_teams'][] = array(
								'team' => $match->radiant_name,
								'team_id' => $match->radiant_team_id
							);
					} else if (in_array($slot->player_slot, \App\Slot::DIRE_ARRAY)) {
						$data['stats']['recent_teams'][] = array(
								'team' => $match->dire_name,
								'team_id' => $match->dire_team_id
							);
					}
				}

				//All time stats
				if ($match->radiant_win && in_array($slot->player_slot, \App\Slot::RADIANT_ARRAY))
					$data['stats']['alltime']['wins']++;
				else if (!$match->radiant_win && in_array($slot->player_slot, \App\Slot::DIRE_ARRAY))
					$data['stats']['alltime']['wins']++;

				$data['stats']['alltime']['kills'] += $slot->kills;
				$data['stats']['alltime']['deaths'] += $slot->deaths;
				$data['stats']['alltime']['assists'] += $slot->assists;

				$data['stats']['alltime']['gold_pm'] += $slot->gold_per_min;

				$data['stats']['alltime']['total_gold'] += $slot->gold;
				$data['stats']['alltime']['last_hits'] += $slot->last_hits;
				$data['stats']['alltime']['denies'] += $slot->denies;
				$data['stats']['alltime']['xp_pm'] += $slot->xp_per_min;
				$data['stats']['alltime']['level'] += $slot->level;

				$iterations++;
			}

			//Get Win Streak
			$data['stats']['winStreak'] = 0;
			$data['stats']['loseStreak'] = 0;
			foreach ($data['slots'] as $slot) {
				$match = $slot->match;

				if (in_array($slot->player_slot, \App\Slot::RADIANT_ARRAY))
					$team = \App\Slot::TEAM_RADIANT;
				else
					$team = \App\Slot::TEAM_DIRE;


				if (($match->radiant_win == 1 && $team == \App\Slot::TEAM_RADIANT) || ($match->radiant_win == 0 && $team == \App\Slot::TEAM_DIRE)) {
					if ($data['stats']['loseStreak'] > 0)
						break;

					$data['stats']['winStreak']++;
				} else {
					if ($data['stats']['winStreak'] > 0)
						break;

					$data['stats']['loseStreak']++;
				}
			}

			$numSlots = count($data['slots']) ? : 1;
			$data['stats']['alltime']['num'] = count($data['slots']);
			$data['stats']['alltime']['win_pc'] = ($data['stats']['alltime']['wins'] * 100) / count($data['slots']);
			$data['stats']['alltime']['gold_pm'] /= count($data['slots']);
			$data['stats']['alltime']['total_gold'] /= count($data['slots']);
			$data['stats']['alltime']['last_hits'] /= count($data['slots']);
			$data['stats']['alltime']['denies'] /= count($data['slots']);
			$data['stats']['alltime']['xp_pm'] /= count($data['slots']);
			$data['stats']['alltime']['level'] /= count($data['slots']);
			$data['stats']['alltime']['hero'] = $this->_mode($data['slots']);
			$data['stats']['alltime']['kda'] = sprintf('%d/%d/%d', $data['stats']['alltime']['kills'], $data['stats']['alltime']['deaths'], $data['stats']['alltime']['assists']);

			$month = $month ? : 1;
			$data['stats']['month']['num'] = $month;
			$data['stats']['month']['win_pc'] = ($data['stats']['month']['wins'] * 100) / $month;
			$data['stats']['month']['gold_pm'] /= $month;
			$data['stats']['month']['total_gold'] /= $month;
			$data['stats']['month']['last_hits'] /= $month;
			$data['stats']['month']['denies'] /= $month;
			$data['stats']['month']['xp_pm'] /= $month;
			$data['stats']['month']['level'] /= $month;
			$data['stats']['month']['kda'] = sprintf('%d/%d/%d', $data['stats']['month']['kills'], $data['stats']['month']['deaths'], $data['stats']['month']['assists']);

			$monthHeroes = array_count_values($monthHeroes);
			if (is_array($monthHeroes) && count($monthHeroes) > 0) { 
				$data['stats']['month']['hero'] = array_search(max($monthHeroes), $monthHeroes);
			} else {
				$data['stats']['month']['hero'] = 0;
			}

			$quarter = $quarter ? : 1;
			$data['stats']['quarter']['num'] = $quarter;
			$data['stats']['quarter']['win_pc'] = ($data['stats']['quarter']['wins'] * 100) / $quarter;
			$data['stats']['quarter']['gold_pm'] /= $quarter;
			$data['stats']['quarter']['total_gold'] /= $quarter;
			$data['stats']['quarter']['last_hits'] /= $quarter;
			$data['stats']['quarter']['denies'] /= $quarter;
			$data['stats']['quarter']['xp_pm'] /= $quarter;
			$data['stats']['quarter']['level'] /= $quarter;
			$data['stats']['quarter']['kda'] = sprintf('%d/%d/%d', $data['stats']['quarter']['kills'], $data['stats']['quarter']['deaths'], $data['stats']['quarter']['assists']);

			$quarterHeroes = array_count_values($quarterHeroes);
			if (is_array($quarterHeroes) && count($quarterHeroes) > 0) {
				$data['stats']['quarter']['hero'] = array_search(max($quarterHeroes), $quarterHeroes);
			} else {
				$data['stats']['quarter']['hero'] = 0;
			}

			$year = $year ? : 1;
			$data['stats']['year']['num'] = $year;
			$data['stats']['year']['win_pc'] = ($data['stats']['year']['wins'] * 100) / $year;
			$data['stats']['year']['gold_pm'] /= $year;
			$data['stats']['year']['total_gold'] /= $year;
			$data['stats']['year']['last_hits'] /= $year;
			$data['stats']['year']['denies'] /= $year;
			$data['stats']['year']['xp_pm'] /= $year;
			$data['stats']['year']['level'] /= $year;
			$data['stats']['year']['kda'] = sprintf('%d/%d/%d', $data['stats']['year']['kills'], $data['stats']['year']['deaths'], $data['stats']['year']['assists']);

			$yearHeroes = array_count_values($yearHeroes);
			if (is_array($yearHeroes) && count($yearHeroes) > 0) {
				$data['stats']['year']['hero'] = array_search(max($yearHeroes), $yearHeroes);
			} else {
				$data['stats']['year']['hero'] = 0;
			}

			$lastMatch = $data['slots']->first()->match;
			$lastSlot = $data['slots']->first();

			if (in_array($lastSlot->player_slot, \App\Slot::RADIANT_ARRAY)) {
				$data['stats']['current_team'] = $lastMatch->radiant_name;
				$data['stats']['current_team_id'] = $lastMatch->radiant_team_id;
			} else if (in_array($lastSlot->player_slot, \App\Slot::DIRE_ARRAY)) {
				$data['stats']['current_team'] = $lastMatch->dire_name;
				$data['stats']['current_team_id'] = $lastMatch->dire_team_id;
			} else {
				$data['stats']['current_team'] = "No Team";
				$data['stats']['current_team_id'] = 1;
			}

			$data['stats']['recent_teams'] = $this->_uniqueTeams($data['stats']['recent_teams'], $data['stats']['current_team_id']);

			if (isset($data['stats']['current_tournaments']))
				$data['stats']['current_tournaments'] = $this->_uniqueTournaments($data['stats']['current_tournaments']);

			if (isset($data['stats']['recent_tournaments']))
				$data['stats']['recent_tournaments'] = $this->_uniqueTournaments($data['stats']['recent_tournaments']);

			if ($this->_doCache)
				Cache::put(sprintf('player_stats_%d', $data['player']->account_id), json_encode($data['stats']), 60 * 24);
		} else {
			$data['stats'] = json_decode($cache, true);
		}

		//Map heroe IDs to hero names
		$heroesRaw = $this->_getObject('heroes.json');
		$heroes = json_decode($heroesRaw);

		$data['heroMap'] = array();
		foreach ($heroes->heroes as $hero) {
			$data['heroMap'][$hero->id] = $hero->localized_name;
		}
		$data['heroMap'][0] = "No hero";

		return view('player/showDota', $data);
	}

    private function _mode($slots)
    {
        if (count($slots) == 0) {
            return null;
        }

        $modeMap = array();
        $maxEl = 0;
        $maxCount = 0;

        foreach ($slots as $k => $slot) {
            if (!isset($modeMap[$slot->hero_id])) {
                $modeMap[$slot->hero_id] = 1;
            } else {
                $modeMap[$slot->hero_id]++;
            }

            if ($modeMap[$slot->hero_id] > $maxCount) {
                $maxEl = $slot->hero_id;
                $maxCount = $modeMap[$slot->hero_id];
            }
        }

        return $maxEl;
    }

    private function _uniqueTeams(array $teams, $currentTeam)
    {
        $uniqueTeams = array();
        $teamsBuffer = array();

        foreach ($teams as $k => $team) {
            if (!in_array($team['team_id'], $teamsBuffer) && $team['team_id'] !== $currentTeam) {
                $uniqueTeams[] = array(
                    'team' => $team['team'],
                    'team_id' => $team['team_id']
                );

                $teamsBuffer[] = $team['team_id'];
            }
        }

        return $uniqueTeams;
    }

    private function _uniqueTournaments(array $tournaments)
    {
        $uniqueTournaments = array();
        $tournamentsBuffer = array();

        foreach ($tournaments as $k => $tournament) {
            if (!in_array($tournament['id'], $tournamentsBuffer)) {
                $uniqueTournaments[] = array(
                    'name' => $tournament['name'],
                    'id' => $tournament['id']
                );

                $tournamentsBuffer[] = $tournament['id'];
            }
        }

        return $uniqueTournaments;
    }

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

	public function show(Request $request, $playerId) {
		$playerId = intval($playerId);

		$data['player'] = \App\Individual::find($playerId);
		$data['his_matches'] = \App\Individual::find($playerId)->matches->sortByDesc('id')->take(5);

		if (null === $data['player']) {
			\App::abort(404);
		}
		$data['stats'] = $data['player']->stats;

		$data['teams'] = \App\PlayerTeam::where('individual_id', $data['player']->id)
										->with('team')
										->get();
        $data['heroes'] = collect(\GuzzleHttp\json_decode(file_get_contents(storage_path('data/heroes.json'))));

        if($request->currentGameSlug == 'overwatch') {
            $data['ow_heroes'] = \App\Models\Overwatch\OverwatchHero::all();
        }

		$data['playersActiveMenu'] = true;
		return view('player/show', $data);
	}

	public function create(Request $request) {
		$data['playersActiveMenu'] = true;
        if($request->currentGameSlug == 'overwatch') {
            $data['ow_heroes'] = \App\Models\Overwatch\OverwatchHero::all();
        }
		return view('player/create', $data);
	}

	public function edit(Request $request, $playerId) {
		$playerId = intval($playerId);
		$data['player'] = \App\Individual::find($playerId);
		$steamIds = \App\IndividualSteam::where('individual_id', $playerId)->get();

		if (count($steamIds) > 0) {
			$data['steamIds'] = array();
			foreach ($steamIds as $id) {
				$data['steamIds'][] = $id->steam_id;
			}

			$data['steamIds'] = implode(", ", $data['steamIds']);
		} else {
			$data['steamIds'] = $data['player']->steam_id;
		}

		if (null == $data['player'])
			\App::abort(404);

        if($request->currentGameSlug == 'overwatch') {
            $data['ow_heroes'] = \App\Models\Overwatch\OverwatchHero::all();
        }

		$data['playersActiveMenu'] = true;
		return view('player/edit', $data);
	}

	public function store(Request $request) {
        $request->merge(array_map('trim', $request->except(['player_role', 'player_race','ow_sign_heroes', 'ow_role'])));
		$validator = Validator::make($request->all(), [
            'nickname' => 'required|max:50',
            'first_name' => 'max:255',
            'last_name' => 'max:255',
            'twitch' => 'max:100',
            'facebook' => 'max:100',
            'twitter' => 'max:100',
            'location' => 'numeric',
            'nationality' => 'numeric',
            'earnings' => 'numeric',
	        'date_of_birth' => 'date',
	        'game' => 'numeric',
	        'active' => 'numeric'
        ]);

        if ($validator->fails()) {
            return redirect(groute('player.create'))
                        ->withErrors($validator)
                        ->withInput();
        }

		if (Input::has('id')) {
        	$playerId = intval(Input::get('id'));
        	$player = \App\Individual::find($playerId);
        } else {
	        $player = new \App\Individual();
	    }

	    $steamIds = explode(", ", Input::get('steamid'));
	    if (Input::has('steamid') && Input::get('steamid') !== "") {
	    	$mainSteam = $steamIds[0];
	    } else {
	    	$mainSteam = null;
	    }

	    $player->steam_id = $mainSteam;
	    $player->game_id = Input::has('game') ? Input::get('game') : 1;
	    $player->nickname = Input::has('nickname') ? Input::get('nickname') : null;
	    $player->historical_handles = Input::has('historical_handles') ? Input::get('historical_handles') : null;
	    $player->first_name = Input::has('first_name') ? Input::get('first_name') : null;
	    $player->last_name = Input::has('last_name') ? Input::get('last_name') : null;
	    $player->date_of_birth = Input::has('date_of_birth') ? Input::get('date_of_birth') : null;
	    $player->nationality = Input::has('nationality') ? Input::get('nationality') : null;
	    $player->location = Input::has('location') ? Input::get('location') : null;
	    $player->bio = Input::has('biography') ? Input::get('biography') : null;
	    $player->twitter = Input::has('twitter') ? Input::get('twitter') : null;
	    $player->facebook = Input::has('facebook') ? Input::get('facebook') : null;
	    $player->source = Input::has('source') ? Input::get('source') : "";
	    $player->twitch = Input::has('twitch') ? Input::get('twitch') : null;
	    $player->active = Input::has('active') ? Input::get('active') : 1;
        $player->earnings = Input::get('earnings', 0);
        $player->player_role = Input::get('player_role', []);
        $player->ow_sign_heroes = implode(',', $request->get('ow_sign_heroes', []));
        $player->ow_roles = implode(',', $request->get('ow_role', []));

        if(request()->has('remove_image')){
            $player->avatar = null;
        }
        if(request()->hasFile('file')){
            if(request()->file('file')->move(public_path('uploads'), request()->file('file')->getClientOriginalName())){
                $player->avatar = request()->file('file')->getClientOriginalName();

                CdnServices::uploadImage($player->avatar);
            }
        }

        $player->save();

        //Drop all previous records for that player's steam ids
        \App\IndividualSteam::where("individual_id", $player->id)->delete();

        //Generate new records depending on the input from staff
        foreach ($steamIds as $id) {
        	$individualSteam = new \App\IndividualSteam();

        	$individualSteam->individual_id = $player->id;
        	$individualSteam->steam_id = $id;

        	$individualSteam->save();
        }

        //Drop all races on edit/create
        \App\Models\Sc2Race::where('individual_id', $player->id)->delete();
        //Generate new records depending on the input from staff
        if (is_array($request->input('player_race'))) {
        	foreach ($request->input('player_race') as $race) {
	        	$r = new \App\Models\Sc2Race();
	        	$r->individual_id = $player->id;
	        	$r->race = $race;
	        	$r->save();
	        }
        }
        return redirect(groute('player.show', ['playerId' => $player->id]));
	}

	public function addRoster() {
		$playerId = intval(Input::get('playerId'));
		$teamId = intval(Input::get('teamId'));
		$startDate = Input::get('start');
		$endDate = Input::get('end');
		$isCoach = Input::get('coach');
        $is_sub = Input::get('is_sub', 0);
        $is_standin = Input::get('is_standin', 0);
        $is_manager = Input::get('is_manager', 0);

		$roster = new \App\PlayerTeam();

		$roster->individual_id = $playerId;
		$roster->team_id = $teamId;
		$roster->start_date = $startDate;
		$roster->is_coach = $isCoach;
        $roster->is_sub = $is_sub;
        $roster->is_standin = $is_standin;
        $roster->is_manager = $is_manager;

		if ($endDate == "")
			$roster->end_date = null;
		else
			$roster->end_date = $endDate;

		$roster->save();

		$retData = array(
				"status" => "success"
			);

		return response()->json($retData);
	}

	public function editRoster() {
		$rosterId = intval(Input::get('rosterId'));
		$teamId = intval(Input::get('teamId'));
		$startDate = Input::get('start');
		$endDate = Input::get('end');
		$isCoach = Input::get('coach');
        $is_sub = Input::get('is_sub', 0);
        $is_standin = Input::get('is_standin', 0);
        $is_manager = Input::get('is_manager', 0);

		$roster = \App\PlayerTeam::find($rosterId);

		if (null !== $roster) {
			$roster->team_id = $teamId;
			$roster->start_date = $startDate;
			$roster->is_coach = $isCoach;
            $roster->is_sub = $is_sub;
            $roster->is_standin = $is_standin;
            $roster->is_manager = $is_manager;

			if ($endDate == "")
				$roster->end_date = null;
			else
				$roster->end_date = $endDate;

			$roster->save();

			$retData = array(
					"status" => "success"
				);
		} else {
			$retData = array(
					"status" => "error",
					"message" => "No such roster history found!"
				);
		}

		return response()->json($retData);
	}

	public function removeRoster() {
		$rosterId = intval(Input::get('rosterId'));

		$roster = \App\PlayerTeam::find($rosterId);

		if (null !== $roster) {
			$roster->delete();

			$retData = array(
					"status" => "success"
				);
		} else {
			$retData = array(
					"status" => "error",
					"message" => "No such roster history found!"
				);
		}

		return response()->json($retData);
	}

	public function getRosterHistory($rosterId) {
		$rosterId = intval($rosterId);

		try {
			$roster = \App\PlayerTeam::where('id', $rosterId)->with('team')->firstOrFail();

			$retData = array(
					"status" => "success",
					"roster" => $roster
				);
		} catch (Illuminate\Database\Eloquent\ModelNotFoundException $e) {
			$retData = array(
					"status" => "error",
					"message" => "No such roster history found!"
				);
		}

		return response()->json($retData);
	}

	public function getApiPlayers(Request $request){

	    $players = new Account();

        $players = $players->select('users.*')
                        ->leftJoin('slots', 'users.account_id', '=', 'slots.account_id')
                        ->leftJoin('match_games', 'slots.match_id', '=', 'match_games.match_id')
                        ->where('match_games.match_id', '>', 0)
                        ->groupBy('users.account_id')
                        ->orderBy('slots.id', 'desc');

        if($request->has('q')){
            $players = $players->where(function($q) use ($request){
                $q->where('users.personaname', 'like', '%'.$request->get('q').'%')
                    ->orWhere('users.steamid', $request->get('q'));
            });
        }

	    $data['players'] = $players->paginate($request->get('perPage', 10));

        $data['playersActiveMenu'] = true;

        return view('player.api_list', $data);
    }

    /**
     * Soft deletes an individual
     *
     * @param  int $playerId
     *
     * @return \Illuminate\Http\Redirect
     */
    public function remove($playerId)
    {
    	$individual = \App\Individual::find($playerId);

    	if (null !== $individual)
    	{
    		$individual->hidden = 1;
    		$individual->save();
    	}

    	return redirect(groute('players.list'));
    }

    /**
     * Fetches a player by his name
     *
     * @param string $name
     *
     * @return Illuminate\Http\Response
     */
    public function getPlayerByName(Request $request)
    {
    	$players = Individual::where('nickname', 'LIKE', '%'.$request->input('name').'%')->get();

		if (count($players)) {
			$return = array(
					"status" => "success",
					"players" => $players
				);
		} else {
			$return = array(
					"status" => "error",
					"message" => "No teams found"
				);
		}

		return response()->json($return);
    }

    public function refreshStats($steamId)
    {
    	$this->dispatch(new \App\Jobs\SinglePlayerStatsRefresh(false, $steamId));

    	return response()->json(
    			array(
    					"status" => "success"
    				)
    		);
    }
}