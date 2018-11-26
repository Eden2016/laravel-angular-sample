#!/usr/bin/php
<?php
date_default_timezone_set('UTC');
use Sunra\PhpSimple\HtmlDomParser;
use Dota2Api\Api;

require __DIR__.'/../bootstrap/autoload.php';

function _request($url, $ip = false) {
	$curl_handle=curl_init();
	curl_setopt($curl_handle, CURLOPT_URL, $url);

	if ($ip)
		curl_setopt($curl_handle, CURLOPT_INTERFACE, $ip);

	curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
	curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36');
	$query = curl_exec($curl_handle);
	curl_close($curl_handle);

	return $query;
}

function addLiveMatch($db, $matchId, $data) {
	$selectMatchStatement = $db->prepare('SELECT id FROM `live_matches` WHERE `match_id` = ?');
	$selectMatchStatement->execute(array($matchId));
	$result = $selectMatchStatement->fetch(PDO::FETCH_OBJ);

	if (null == $result) {
		if ($data['leagueid'] != 4122) {
			$insertMatchStatement = $db->prepare('INSERT INTO `live_matches` (match_id, league_id, radiant, dire, stage, series_type, game_number, series_id, started_at, finished_at, is_finished) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
			$insertMatchStatement->execute(
					array(
							$matchId,
							$data['leagueid'],
							$data['radiant'],
							$data['dire'],
							$data['stage'],
							$data['series_type'],
							$data['game_number'],
							$data['series_id'],
							$data['started_at'],
							$data['finished_at'],
							1
						)
				);
		}
	}

	return true;
}

function saveMatch($matchId, $db, $ip, $apiKey) {
	$mm = new \Dota2Api\Mappers\MatchMapperWeb($matchId, $ip, $apiKey);
	$match = $mm->load();

	if (null !== $match) {
		if ($match->get('leagueid') != 4122) {
			$data = array(
					"leagueid" => $match->get('leagueid'),
					"radiant" => $match->get('radiant_name'),
					"dire" => $match->get('dire_name'),
					"stage" => "",
					"started_at" => strtotime($match->get('start_time')),
					"finished_at" => strtotime($match->get('start_time')) + $match->get('duration'),
					"series_type" => (int)$match->get('series_type'),
					"game_number" => $match->get('game_number') ? (int)$match->get('game_number') : 1,
					"series_id" => (int)$match->get('series_id')
				);

			$saver = new \Dota2Api\Mappers\MatchMapperDb();
			$saver->save($match);
			addLiveMatch($db, $matchId, $data);	
		}
	}

	return true;
}

echo date("d-m-Y H:i:s")." - ".$argv[0]." process started\n";

// BOOTSTRAP
$dotenv = new Dotenv\Dotenv(__DIR__."/../");
$dotenv->load();

$db = new PDO(sprintf('mysql:host=127.0.0.1;dbname=%s;port=%d;charset=utf8mb4', getenv('DB_DATABASE'), getenv('DB_PORT')), getenv('DB_USERNAME'), getenv('DB_PASSWORD'));

Api::init(getenv('STEAM_API_KEY_TEST'), array('127.0.0.1', getenv('DB_USERNAME'), getenv('DB_PASSWORD'), getenv('DB_DATABASE'), ''));
// END BOOTSTRAP

echo date("d-m-Y H:i:s")." - bootstrapped\n";

if (!isset($argv[1]) || $argv[1] == "--matchlist") {
	echo date("d-m-Y H:i:s")." - matchlist option called\n";
	$page = 1;
	while ($page < getenv('SCRAPE_MAX_PAGE')) {
		$str = _request("http://www.dotabuff.com/esports/matches?page=".$page);
		$dom = HtmlDomParser::str_get_html($str);

		$elems = $dom->find("a");

		$foundIds = array();
		foreach ($elems as $key => $a) {
			if (preg_match( "/\/matches\/([0-9]+)/", $a->href, $matches))
				$foundIds[] = $matches[1];
		}

		if (count($foundIds) > 0) {
			foreach ($foundIds as $matchId) {
				saveMatch($matchId, $db);
				echo "Match ".$matchId." saved\n";
				sleep(1);
			}
		}

		$page++;
	}
} else if ($argv[1] == "--playerprofiles") {

	echo date("d-m-Y H:i:s")." - playerprofiles option called\n";
	
	if (isset($argv[2]) && $argv[2] == "-p") {
		$playerNum = isset($argv[3]) ? $argv[3] : 20;
	} else {
		$playerNum = 20;
	}

	echo date("d-m-Y H:i:s")." - ".$playerNum." players to be processed\n";

	if (isset($argv[4]))
		$ip = $argv[4];
	else
		$ip = false;

	echo date("d-m-Y H:i:s")." - cURL queries are called through ".$ip."\n";

	if (isset($argv[5]))
		$apikey = $argv[5];
	else
		$apikey = false;

	$lastCheck = time() - 86400 * 30; //One month

	echo date("d-m-Y H:i:s")." - Locking tables\n";
	$db->exec('LOCK TABLES `users` WRITE;');
	$query = $db->query('SELECT account_id FROM `users` WHERE `last_check` IS NULL OR last_check < '.$lastCheck.'  LIMIT '.$playerNum);
	$players = $query->fetchAll(PDO::FETCH_OBJ);

	if (null != $players) {

		$playersUpdate = array();
		foreach ($players as $k => $player) {
			$playersUpdate[] = $player->account_id;
		}

		$db->query('UPDATE `users` SET `last_check` = '.time().' WHERE `account_id` IN ('.implode(", ", $playersUpdate).')');
		$db->exec('UNLOCK TABLES');
		echo date("d-m-Y H:i:s")." - tables unlocked\n";

		foreach ($players as $k => $player) {
			echo date("d-m-Y H:i:s")." - fetching info for player ".$player->account_id."\n";
			$str = _request(sprintf("http://www.dotabuff.com/esports/players/%d/matches", $player->account_id), $ip);
			$dom = HtmlDomParser::str_get_html($str);

			$elems = $dom->find("a");

			foreach ($elems as $key => $a) {
				if (preg_match( "/\?page\=([0-9]+)/", $a->href, $matches)) {
					if (strpos($a->plaintext, "Last") !== null) {
						$maxPages = $matches[1];
					}
				}
			}

			$maxPages = $maxPages ? : 1;

			echo date("d-m-Y H:i:s")." - max pages for player ".$player->account_id." are ".$maxPages."\n";

			$page = 1;
			while ($page <= $maxPages) {
				echo date("d-m-Y H:i:s")." - fetching info from page ".$page." for player ".$player->account_id."\n";

				$str = _request(sprintf("http://www.dotabuff.com/esports/players/%d/matches?page=%d", $player->account_id, $page), $ip);
				$dom = HtmlDomParser::str_get_html($str);

				$elems = $dom->find("a");

				$foundIds = array();
				foreach ($elems as $key => $a) {
					if (preg_match( "/\/matches\/([0-9]+)/", $a->href, $matches))
						$foundIds[] = $matches[1];
				}

				echo date("d-m-Y H:i:s")." - ".count($foundIds)." matches are found on page ".$page." for player ".$player->account_id."\n";
				if (count($foundIds) > 0) {
					foreach ($foundIds as $k => $matchId) {
						saveMatch($matchId, $db, $ip, $apikey);
						echo date("Y-m-d H:i:s")." - Match number ".($k+1)." for playerId ".$player->account_id." with id ".$matchId." saved\n";
						sleep(1);
					}
				}

				$page++;
			}
		}
	} else {
		$db->exec('UNLOCK TABLES');
		print_r($db->errorInfo()); echo "\n";
	}
} else if ($argv[1] == "help" || $argv[1] == "-help" || $argv[1] == "--help") {
	echo "To get matches from the last esports matches page, use --machlist option\n";
	echo "To get matches from players profiles, use --playerprofiles option with specifing: \n";
	echo "\t -p Number of players to be included in the list\n";
}