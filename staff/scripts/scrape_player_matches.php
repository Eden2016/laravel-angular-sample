
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

function saveMatches($playerAccount, $db, $ip, $apiKey) {
	$matchesMapperWeb = new \Dota2Api\Mappers\MatchesMapperWeb();
	$matchesMapperWeb->setAccountId($playerAccount);
	$matchesShortInfo = $matchesMapperWeb->load();

	foreach ($matchesShortInfo as $matchId=>$matchShortInfo) {
	    $matchMapper = new \Dota2Api\Mappers\MatchMapperWeb($matchId, $ip, $apiKey);
	    $match = $matchMapper->load();

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

				echo date("d-m-Y H:i:s")." - Saved match with ID - ".$matchId." for player ".$playerAccount."\n";
			}
		}

		sleep(1);
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


if (isset($argv[1]))
	$ip = $argv[1];
else
	$ip = false;

echo date("d-m-Y H:i:s")." - cURL queries are called through ".$ip."\n";

if (isset($argv[2]))
	$apikey = $argv[2];
else
	$apikey = false;

$lastCheck = time() - 86400 * 5; //5 days

echo date("d-m-Y H:i:s")." - Locking tables\n";
$db->exec('LOCK TABLES `users` WRITE;');
$query = $db->query('SELECT account_id FROM `users` WHERE `last_check` IS NULL OR last_check < '.$lastCheck.'  LIMIT 10');
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
		
		saveMatches($player->account_id, $db, $ip, $apikey);
	}
} else {
	$db->exec('UNLOCK TABLES');
	echo date("Y-m-d H:i:s")." No players found\n";
	print_r($db->errorInfo()); echo "\n";
}