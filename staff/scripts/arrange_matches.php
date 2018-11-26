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
// BOOTSTRAP
echo date("d-m-Y H:i:s")." - bootstrapped\n";

$i = 0;
while (true) {
    $query = $db->query(sprintf('SELECT id FROM `stage_rounds` ORDER BY `id` DESC LIMIT %d,20', $i*20));
    $rounds = $query->fetchAll(PDO::FETCH_OBJ);

    if (count($rounds)) {
        foreach ($rounds as $round) {
            $query = $db->query('SELECT id, position, start FROM `dummy_matches` WHERE `round_id` = '.$round->id);
            $matches = $query->fetchAll(PDO::FETCH_OBJ);

            if (count($matches)) {
                $matches = collect($matches);
                $sortedMatches = $matches->sortBy('start');

                foreach ($sortedMatches as $k=>$match) {
                    $db->query(sprintf('UPDATE `dummy_matches` SET `position` = %d WHERE `id` = %d', $k+1, $match->id));
                }
            }
            else {
                continue;
            }
        }
        $i++;
    }
    else {
        echo "no rounds\n";
        break;
    }
}

echo date("d-m-Y H:i:s")." - No more rounds... exiting script\n";