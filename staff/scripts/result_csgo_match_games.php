#!/usr/bin/php
<?php
error_reporting(E_ALL);
date_default_timezone_set('UTC');
use Sunra\PhpSimple\HtmlDomParser;
use Dota2Api\Api;

require __DIR__.'/../bootstrap/autoload.php';

echo date("d-m-Y H:i:s")." - ".$argv[0]." process started\n";

// BOOTSTRAP
$dotenv = new Dotenv\Dotenv(__DIR__."/../");
$dotenv->load();

$db = new PDO(sprintf('mysql:host=%s;dbname=%s;port=%d;charset=utf8mb4', getenv('DB_HOST'), getenv('DB_DATABASE'), getenv('DB_PORT')), getenv('DB_USERNAME'), getenv('DB_PASSWORD'));
// END BOOTSTRAP

echo date("d-m-Y H:i:s")." - bootstrapped\n";

$query = $db->query("SELECT
        `mg`.`id`,
        `mg`.`dummy_match_id`,
        `mg`.`winner`,
        `dm`.`opponent1`,
        `dm`.`opponent2`
        FROM `match_games` AS `mg`
        LEFT JOIN `dummy_matches` AS `dm`
        ON `dm`.`id` = `mg`.`dummy_match_id`
        WHERE `dm`.`game_id` = 3
        AND `mg`.`match_id` = 0
        AND `mg`.`winner` IS NOT NULL
        and `mg`.`updated_at` < DATE_SUB(NOW(), INTERVAL 5 HOUR)
        LIMIT 200");

$matches = $query->fetchAll(PDO::FETCH_OBJ);
//echo date("d-m-Y H:i:s")." - fetched match_games\n";
if (null != $matches) {
    echo date("d-m-Y H:i:s")." - we have ".count($matches)." match_games\n";
    foreach ($matches as $k => $match) {
        if ($match->winner == $match->opponent1) {
            $db->query('UPDATE `match_games` SET `opponent1_score` = 1, `opponent2_score` = 0, `updated_at` = NOW() WHERE `id` = '.$match->id);
            echo date("d-m-Y H:i:s")." - winner is opponent1\n";
        }
        else {
            $db->query('UPDATE `match_games` SET `opponent2_score` = 1, `opponent1_score` = 0, `updated_at` = NOW() WHERE `id` = '.$match->id);
            echo date("d-m-Y H:i:s")." - winner is opponent2\n";
        }
    }

    echo date("d-m-Y H:i:s")." - done\n";
}
else {
    echo date("Y-m-d H:i:s")." No matches found\n";
    print_r($db->errorInfo()); echo "\n";
}