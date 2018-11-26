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

function savePlayerName($db, $playerIds, $ip=false, $apiKey=false) {
	$requestURL = sprintf('http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=%s&language=en_us&steamids=%s', $apiKey, implode(",", $playerIds)); 
	$playerData = _request($requestURL, $ip);
	$players = json_decode($playerData);

	if ($players) {
		$db->beginTransaction();

		try {
			foreach ($players->response->players as $player) {
				echo date("d-m-Y H:i:s")." - Saving player with name ".$player->personaname."\n";

				$stmnt = $db->prepare("UPDATE `users` SET `personaname` = ?, `profileurl` = ?, `avatar` = ? WHERE `steamid` = ?");
				$stmnt->execute(array(
						$player->personaname,
						$player->profileurl,
						$player->avatar,
						$player->steamid
					));
			}

			$db->commit();
		} catch (PDOException $e) {
		    // roll back transaction
		    $db->rollback();
		}
	} else {
		echo date("d-m-Y H:i:s")." - Invalid JSON\n";
		echo $requestURL."\n";
		print_r($players);
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

if (isset($argv[3]))
	$iterations = $argv[3];
else
	$iterations = 100;

if (isset($argv[4]))
	$limit = $argv[4];
else
	$limit = 20;

while ($iterations > 0) {
	echo date("d-m-Y H:i:s")." - Locking tables\n";
	$db->exec('LOCK TABLES `users` WRITE;');
	$query = $db->query('SELECT steamid FROM `users` WHERE `personaname` IS NULL LIMIT '.$limit);
	$players = $query->fetchAll(PDO::FETCH_OBJ);

	if (null != $players) {

		$playerIds = array();
		foreach ($players as $k => $player) {
			$playerIds[] = $player->steamid;
		}

		$db->exec('UNLOCK TABLES');
		echo date("d-m-Y H:i:s")." - tables unlocked\n";

		savePlayerName($db, $playerIds, $ip, $apikey);
	} else {
		$db->exec('UNLOCK TABLES');
		echo date("Y-m-d H:i:s")." No players found\n";
		print_r($db->errorInfo()); echo "\n";
	}

	$iterations--;
}