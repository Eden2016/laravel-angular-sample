<?php
namespace App\Http\Controllers;

use PDO;
use App\Services;
use App\User;
use App\Http\Controllers\Controller;
use Dota2Api\Api;
use DB;
use Sunra\PhpSimple\HtmlDomParser;

class CronController extends Controller
{
	/**
	 * @var S3ClientObject
	 **/
	protected $_s3;

	/**
	 * @var string
	 **/
	protected $_bucket;

	public function __construct() {
		//Initialize DOTA2 Web API
		Api::init(getenv('STEAM_API_KEY_TEST'), array(getenv('DB_HOST'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'), getenv('DB_DATABASE'), ''));

		//Initialize Amazon S3 SDK
		$this->_s3 = \AWS::createClient('s3');
		$this->_bucket = getenv('BUCKET_NAME');
	}

	public function getHeroes() {
		$heroesMapper = new \Dota2Api\Mappers\HeroesMapper();
		$heroes = $heroesMapper->load();

		if (count($heroes) > 0) {
			foreach ($heroes as $hero) {
				echo $hero['localized_name']."<br />";
			}
		}
	}

	public function getMatches($leagueId=false) {
		if ($leagueId)
			$leagueMapper = new \Dota2Api\Mappers\LeagueMapper($leagueId); // set league id (can be get via leagues_mapper)
		else
			$leagueMapper = new \Dota2Api\Mappers\LeagueMapper();

		$games = $leagueMapper->load();

		$liveMatches = array();
		$liveMatchIds = array();

		if (is_array($games) && count($games) > 0) {
			foreach ($games as $game) {
				if ($game->get('league_id') == 4122) //Disable FACEIT League
					continue;

				$league = DB::select('select name from leagues where leagueid = ?', array($game->get('league_id')));

				if (count($league) > 0)
					$leagueName = $league[0]->name;
				else
					$leagueName = "";

				$liveMatches['live_games'][$game->get('match_id')] = array(
						"league_id"			=> $game->get('league_id'),
						"league_name"		=> $leagueName,
						"radiant" 			=> $game->get('radiant_name') != "" ? $game->get('radiant_name') : "Radiant",
						"dire"				=> $game->get('dire_name') != "" ? $game->get('dire_name') : "Dire",
						"started_at"		=> (time() - $game->get('duration')),
						"game_number"		=> $game->get('game_number') ? (int)$game->get('game_number') : 1,
						"series_id"			=> (int)$game->get('series_id'),
						"series_type"		=> (int)$game->get('series_type'),
						"stage"				=> (string)$game->get('stage_name'),
						"radiant_series_wins" => (int)$game->get('radiant_series_wins'),
						"dire_series_wins"	=> (int)$game->get('dire_series_wins'),
						"duration"			=> (int)$game->get('duration'),
						"started"			=> $game->get('duration') == 0 ? false : true
					);

				$liveMatchIds[] = $game->get('match_id');

				//Add match details to file on S3
				$direPlayers = array();
				$radiantPlayers = array();

				foreach (array_merge($game->get("dire_team"), $game->get('radiant_team')) as $player) {
					if ($player['team'] == 0 && isset($game->get('radiant_scoreboard')['players'])) {
						foreach ($game->get('radiant_scoreboard')['players']->player as $score) {
							if ($score->account_id == $player['account_id']) {
								$radiantPlayers[] = array(
										"name" 				=> $player['name'],
										"account_id" 		=> $player['account_id'],
										"hero_id" 			=> (int)$score->hero_id,
										"kills" 			=> (int)$score->kills,
										"deaths" 			=> (int)$score->death,
										"assists" 			=> (int)$score->assissts,
										"last_hits" 		=> (int)$score->last_hits,
										"denies" 			=> (int)$score->denies,
										"gold" 				=> (int)$score->gold,
										"level" 			=> (int)$score->level,
										"gold_per_min" 		=> (int)$score->gold_per_min,
										"xp_per_min" 		=> (int)$score->xp_per_min,
										"ultimate_state" 	=> (int)$score->ultimate_state,
										"ultimate_cooldown"	=> (int)$score->ultimate_cooldown,
										"item0" 			=> (int)$score->item0,
										"item1" 			=> (int)$score->item1,
										"item2" 			=> (int)$score->item2,
										"item3" 			=> (int)$score->item3,
										"item4" 			=> (int)$score->item4,
										"item5" 			=> (int)$score->item5,
										"respawn_timer" 	=> (int)$score->respawn_timer,
										"position_x" 		=> (float)$score->position_x,
										"position_y" 		=> (float)$score->position_y,
										"net_worth" 		=> (int)$score->net_worth
									);
							}
						}
					} else if ($player['team'] == 1 && isset($game->get('dire_scoreboard')['players'])) {
						foreach ($game->get('dire_scoreboard')['players']->player as $score) {
							if ($score->account_id == $player['account_id']) {
								$direPlayers[] = array(
										"name" 				=> $player['name'],
										"account_id" 		=> $player['account_id'],
										"hero_id" 			=> (int)$score->hero_id,
										"kills" 			=> (int)$score->kills,
										"deaths" 			=> (int)$score->death,
										"assists" 			=> (int)$score->assissts,
										"last_hits" 		=> (int)$score->last_hits,
										"denies" 			=> (int)$score->denies,
										"gold" 				=> (int)$score->gold,
										"level" 			=> (int)$score->level,
										"gold_per_min" 		=> (int)$score->gold_per_min,
										"xp_per_min" 		=> (int)$score->xp_per_min,
										"ultimate_state" 	=> (int)$score->ultimate_state,
										"ultimate_cooldown"	=> (int)$score->ultimate_cooldown,
										"item0" 			=> (int)$score->item0,
										"item1" 			=> (int)$score->item1,
										"item2" 			=> (int)$score->item2,
										"item3" 			=> (int)$score->item3,
										"item4" 			=> (int)$score->item4,
										"item5" 			=> (int)$score->item5,
										"respawn_timer" 	=> (int)$score->respawn_timer,
										"position_x" 		=> (float)$score->position_x,
										"position_y" 		=> (float)$score->position_y,
										"net_worth" 		=> (int)$score->net_worth
									);
							}
						}
					}
				}


				$liveMatch = array(
						"match_id"			=> $game->get('match_id'),
						"league_id"			=> $game->get('league_id'),
						"league_name"		=> $leagueName,
						"radiant" 			=> $game->get('radiant_name') != "" ? $game->get('radiant_name') : "Radiant",
						"dire"				=> $game->get('dire_name') != "" ? $game->get('dire_name') : "Dire",
						"started_at"		=> (time() - $game->get('duration')),
						"stage"				=> (string)$game->get('stage_name'),
						"game_number"		=> $game->get('game_number') ? (int)$game->get('game_number') : 1,
						"series_id"			=> (int)$game->get('series_id'),
						"series_type"		=> (int)$game->get('series_type'),
						"radiant_series_wins" => (int)$game->get('radiant_series_wins'),
						"dire_series_wins"	=> (int)$game->get('dire_series_wins'),
						"duration"			=> (int)$game->get('duration'),
						"is_finished"		=> false,
						"dire_picks"		=> isset($game->get('dire_scoreboard')['picks']) ? $game->get('dire_scoreboard')['picks'] : [],
						"dire_bans"			=> isset($game->get('dire_scoreboard')['bans']) ? $game->get('dire_scoreboard')['bans'] : [],
						"radiant_picks"		=> isset($game->get('radiant_scoreboard')['picks']) ? $game->get('radiant_scoreboard')['picks'] : [],
						"radiant_bans"		=> isset($game->get('radiant_scoreboard')['bans']) ? $game->get('radiant_scoreboard')['bans'] : [],
						"dire_players"		=> $direPlayers,
						"radiant_players"	=> $radiantPlayers,
						"tower_status_radiant" 		=> isset($game->get('radiant_scoreboard')['tower_state']) ? $game->get('radiant_scoreboard')['tower_state'] : 0,
						"tower_status_dire" 		=> isset($game->get('dire_scoreboard')['tower_state']) ? $game->get('dire_scoreboard')['tower_state'] : 0,
						"barracks_status_radiant"	=> isset($game->get('radiant_scoreboard')['barracks_state']) ? $game->get('radiant_scoreboard')['barracks_state'] : 0,
						"barracks_status_dire" 		=> isset($game->get('dire_scoreboard')['barracks_state']) ? $game->get('dire_scoreboard')['barracks_state'] : 0,
						"score_radiant" 			=> isset($game->get('radiant_scoreboard')['score']) ? $game->get('radiant_scoreboard')['score'] : 0,
						"score_dire" 				=> isset($game->get('dire_scoreboard')['score']) ? $game->get('dire_scoreboard')['score'] : 0
					);

				$this->_putObject(
					sprintf("matches/%d/%d.json", $game->get('match_id'), (int)$game->get('duration')),
					json_encode($liveMatch));

				$this->_putObject(
					sprintf("matches/%d/%d.json", $game->get('match_id'), $game->get('match_id')),
					json_encode($liveMatch));

				$match = DB::select('SELECT match_id FROM live_matches WHERE match_id = ?', array($game->get('match_id')));

				if (count($match) < 1) {
					DB::insert('insert into live_matches (match_id, league_id, radiant, dire, stage, series_type, game_number, series_id, started_at, finished_at, is_finished) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array(
							$game->get('match_id'),
							$liveMatch['league_id'],
							$liveMatch['radiant'],
							$liveMatch['dire'],
							$liveMatch['stage'],
							$liveMatch['series_type'],
							$liveMatch['game_number'],
							$liveMatch['series_id'],
							$liveMatch['started_at'],
							0,
							0
						)
					);
				}

				$now = time();

				$this->_addMatchHistory($liveMatch['match_id'], (int)$game->get('duration'), array_merge($radiantPlayers, $direPlayers), $now);
				$this->_addMatchStatusHistory($liveMatch['match_id'], $liveMatch, $now);
			}

			//Put live matches object to games.json file on S3
			$this->_putObject("games.json", json_encode($liveMatches));
		}

		//Finish finished matches
		$this->_finishMatches($liveMatchIds);
	}

	public function saveBacklogMatch() {
		$matchGames = \App\MatchGame::where('match_id', '>', '0')->where('is_crawled', '=', '0')->take(5)->get();

		if (null !== $matchGames) {
			foreach ($matchGames as $mg) {
				if ($this->saveMatch($mg->match_id)) {
					$mg->is_crawled = 1;
					$mg->save();
				}
			}
		}
	}

	private function _putObject($fileName, $contents, $contentType="application/json") {
		$this->_s3->putObject([
		    'ACL'			=> 'public-read',
		    'Bucket'		=> $this->_bucket, // REQUIRED
		    'Key'			=> $fileName, // REQUIRED
		    'Body'			=> $contents,
		    'ContentType'	=> $contentType,
		]);

		return $this->_s3->getWaiter('ObjectExists', array(
			    'Bucket' => $this->_bucket,
			    'Key'    => $fileName
		    ));
	}

	private function _getObject($key) {
		$object = $this->_s3->getObject([
		    'Bucket' => $this->_bucket, // REQUIRED
		    'Key' => $key,
		]);

		if ($object) {
			return $object['Body'];
		} else {
			return false;
		}
	}

	/**
	 *
	 *
	 **/
	public function getLiveMatches($limit) {
		$matchesMapperWeb = new \Dota2Api\Mappers\MatchesMapperWeb();
		$matchesMapperWeb->setMatchesRequested($limit);
		$matchesShortInfo = $matchesMapperWeb->load();

		foreach ($matchesShortInfo as $key=>$matchShortInfo) {
		    $matchMapper = new \Dota2Api\Mappers\MatchMapperWeb($key);
		    $match = $matchMapper->load();

		    if (in_array($match->get('lobby_type'), array(0, 2, 5))) {
			    $mm = new \Dota2Api\Mappers\MatchMapperDb();
			    $mm->save($match);
			}
		}

		echo "saved";
	}

	public function getAllMatches() {
		$leagueMapper = new \Dota2Api\Mappers\LeaguesMapperDb();
		$leagues = $leagueMapper->load();

		foreach ($leagues as $league) {
			$leagueMapper = new \Dota2Api\Mappers\LeagueMapper($leagueId); // set league id (can be get via leagues_mapper)
			$games = $leagueMapper->load();

			foreach ($games as $game) {
				print_r($game);
			}
		}
	}

	public function saveMatch($matchId) {
		$mm = new \Dota2Api\Mappers\MatchMapperWeb($matchId);
		$match = $mm->load();

		if (null !== $match) {
			$saver = new \Dota2Api\Mappers\MatchMapperDb();
			$saver->save($match);
		}

		return true;
	}

	public function getLeagues() {
		$leaguesMapperWeb = new \Dota2Api\Mappers\LeaguesMapperWeb();
		$leagues = $leaguesMapperWeb->load();

		return $leagues;
	}

	public function saveLeagues() {
		$saver = new \Dota2Api\Mappers\LeaguesMapperDb();

		foreach ($this->getLeagues() as $league) {
			$saver->save($league);
		}

		echo "da";
	}

	private function _finishMatches(array $liveMatchIds) {
		$time = time();
		$matches = DB::select('SELECT * FROM live_matches WHERE finished_at = 0 AND is_finished = 0');

		if (count($matches) > 0) {
			$finishMatches = array();

			foreach ($matches as $match) {
				if (count($liveMatchIds) < 1 || !in_array($match->match_id, $liveMatchIds)) {
					$finishMatches[] = $match->match_id;
					$this->saveMatch($match->match_id);
				}
			}

			if (count($finishMatches) > 0) {
				DB::update('update live_matches set finished_at = ?, is_finished = 1 WHERE match_id IN ('.implode(", ", $finishMatches).')', array($time));

				foreach ($finishMatches as $matchId) {
					$rawContents = $this->_getObject(sprintf('matches/%d/%d.json', $matchId, $matchId));
					$contents = json_decode($rawContents, true);

					$contents['is_finished'] = true;
					$contents['finished_at'] = $time;

					$this->_putObject(sprintf('matches/%d/%d.json', $matchId, $matchId), json_encode($contents));

					$players = array_merge($contents['dire_players'], $contents['radiant_players']);
					if (count($players)) {
						foreach ($players as $k=>$player) {
							$accountId = (int)$player['account_id'];

							//Refresh the statistics of each player that played in this match and store them into the cache until
							//they play another match
							$this->dispatch(new \App\Jobs\SinglePlayerStatsRefresh($accountId));
						}
					}
				}
			}

			return true;
		} else {
			return false;
		}
	}

	public function backlog() {
		$str = $this->_request("http://www.dotabuff.com/esports/matches");
		$dom = HtmlDomParser::str_get_html($str);

		$elems = $dom->find("a");

		$foundIds = array();
		foreach ($elems as $key => $a) {
			if (preg_match( "/\/matches\/([0-9]+)/", $a->href, $matches))
				$foundIds[] = $matches[1];
		}

		if (count($foundIds) > 0) {
			foreach ($foundIds as $matchId) {

			}
		}
	}


	public function toutouFetchEvents()
    {
        $factory = new \App\Factories\ToutouMatchFactory();
        $factory->fetchEvents();

        return response()->json([
        		"success" => true
        	]);
    }

	/**
	*
	* End of TouTou Integration Section
	*
	 */

	private function _addMatchHistory($matchId, $duration, $playerData, $now) {
		if ($duration == 0)
			return true;

		DB::beginTransaction();

		foreach ($playerData as $key => $player) {
			DB::insert('insert into match_history (match_id, player_id, time, duration, hero_id, kills, deaths, assists, level, gold, gold_per_minute, xp_per_minute, denies, item0, item1, item2, item3, item4, item5, pos_x, pos_y, net_worth) values
				 (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array(
				 	$matchId,
				 	$player['account_id'],
				 	$now,
				 	$duration,
				 	$player['hero_id'],
				 	$player['kills'],
				 	$player['deaths'],
				 	$player['assists'],
				 	$player['level'],
				 	$player['gold'],
				 	$player['gold_per_min'],
				 	$player['xp_per_min'],
				 	$player['denies'],
				 	$player['item0'],
				 	$player['item1'],
				 	$player['item2'],
				 	$player['item3'],
				 	$player['item4'],
				 	$player['item5'],
				 	$player['position_x'],
				 	$player['position_y'],
				 	$player['net_worth']
				 ));
		}

		DB::commit();

		return true;
	}

	private function _addMatchStatusHistory($matchId, $data, $now) {
		DB::insert('INSERT INTO `match_status_history` (match_id, time, duration, tower_status_radiant, tower_status_dire, barracks_status_radiant, barracks_status_dire, score_radiant, score_dire) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)', array(
			$data['match_id'],
			$now,
			$data['duration'],
			$data['tower_status_radiant'],
			$data['tower_status_dire'],
			$data['barracks_status_radiant'],
			$data['barracks_status_dire'],
			$data['score_radiant'],
			$data['score_dire']
		));

		return true;
	}

	private function _getLeaguesIds() {
		$leaguesMapperWeb = new \Dota2Api\Mappers\LeaguesMapperWeb();
		$leagues = $leaguesMapperWeb->load();

		$ids = array();
		foreach($leagues as $id=>$league) {
		    $ids[] = $id;
		}

		 return $ids;
	}

	private function _request($url) {
		$curl_handle=curl_init();
		curl_setopt($curl_handle, CURLOPT_URL, $url);
		curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curl_handle, CURLOPT_TIMEOUT, 10);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36');
		$query = curl_exec($curl_handle);
		curl_close($curl_handle);

		return $query;
	}
}