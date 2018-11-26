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

function updateInfo($players, $db, $apikey) {
	$playersInfo = _request(sprintf('http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=%s&steamids=%s', $apikey, implode(",", $players)));

	$playersInfo = json_decode($playersInfo);
	if ($playersInfo) {
		foreach ($playersInfo->response->players as $player) {
			$query = $db->query('SELECT id FROM `individuals` WHERE `steam_id` = '.$player->steamid);
			$hasPlayer = $query->fetch(PDO::FETCH_OBJ);

			if (null !== $hasPlayer) {
				echo date("Y-m-d H:i:s")." Adding record for player ".$player->steamid."\n";

				if (isset($player->realname) && $player->realname != "") {
					$rname = explode(" ", $player->realname);

					if (count($rname) > 1) {
						list($firstName, $lastName) = $rname;
					} else {
						$firstName = $player->realname;
						$lastName = "";
					}
				} else {
					$firstName = "";
					$lastName = "";
				}

				$db->exec("INSERT INTO `individuals` (`steam_id`, `nickname`, `first_name`, `last_name`, `avatar_url`) VALUES (".$player->steamid.", '".$player->personaname."', '".$firstName."', '".$lastName."', '".$player->avatarfull."')");
			}
		}
	} else {
		echo date("Y-m-d H:i:s")." Problems with decoding Steam Web API data.\n";
	}
}

echo date("d-m-Y H:i:s")." - ".$argv[0]." process started\n";

// BOOTSTRAP
$dotenv = new Dotenv\Dotenv(__DIR__."/../");
$dotenv->load();

$db = new PDO(sprintf('mysql:host=127.0.0.1;dbname=%s;port=%d;charset=utf8mb4', getenv('DB_DATABASE'), getenv('DB_PORT')), getenv('DB_USERNAME'), getenv('DB_PASSWORD'));

Api::init(getenv('STEAM_API_KEY_TEST'), array('127.0.0.1', getenv('DB_USERNAME'), getenv('DB_PASSWORD'), getenv('DB_DATABASE'), ''));

if (isset($argv[1]))
	$apikey = $argv[1];
else
	$apikey = getenv('STEAM_API_KEY_TEST');
// END BOOTSTRAP

echo date("d-m-Y H:i:s")." - bootstrapped\n";

$query = $db->query('SELECT distinct `users`.`steamid` FROM `slots` left join `match_games` on `slots`.`match_id` = `match_games`.`match_id` LEFT JOIN `users` on `users`.`account_id` = `slots`.`account_id` where `match_games`.`match_id` > 0');
$players = $query->fetchAll(PDO::FETCH_OBJ);

if (null != $players) {

	$playersUpdate = array();
	foreach ($players as $k => $player) {
		$playersUpdate[] = $player->steamid;

		if ($k%20 == 0) {
			updateInfo($playersUpdate, $db, $apikey);
			$playersUpdate = array();
		}
	}
} else {
	echo date("Y-m-d H:i:s")." No players found\n";
	print_r($db->errorInfo()); echo "\n";
}