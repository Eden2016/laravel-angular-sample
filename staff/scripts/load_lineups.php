#!/usr/bin/php
<?php
error_reporting(E_ALL);
date_default_timezone_set('UTC');
use Sunra\PhpSimple\HtmlDomParser;
use Dota2Api\Api;

require __DIR__.'/../bootstrap/autoload.php';

function fetchMatchGames($db) {
    $query = $db->query("SELECT
                count(`mg`.`id`) AS num
            FROM `match_games` AS `mg`
            WHERE `mg`.`updated_at` < DATE_SUB(NOW(), INTERVAL 1 day)");
    $opa = $query->fetchAll(PDO::FETCH_OBJ);

    echo $opa[0]->num." match games left unchecked. ".ceil($opa[0]->num / 300)." more iterations to go.\n\n";
    $query = $db->query("SELECT
                `mg`.`id`,
                `dm`.`opponent1`,
                `dm`.`opponent2`,
                `mg`.`opponent1_members`,
                `mg`.`opponent2_members`
            FROM `match_games` AS `mg`
            LEFT JOIN `dummy_matches` AS `dm` ON `dm`.`id` = `mg`.`dummy_match_id`
            WHERE `mg`.`updated_at` < DATE_SUB(NOW(), INTERVAL 1 day)
            LIMIT 300");

    $matches = $query->fetchAll(PDO::FETCH_OBJ);

    return $matches;
}

function placeholders($text, $count=0, $separator=","){
    $result = array();
    if($count > 0){
        for($x=0; $x<$count; $x++){
            $result[] = $text;
        }
    }

    return implode($separator, $result);
}

function toArray($string) {
    $string = str_replace("[", "", $string);
    $string = str_replace("]", "", $string);
    $string = str_replace("\"", "", $string);

    return explode(",", $string);
}

echo date("d-m-Y H:i:s")." - ".$argv[0]." process started\n";

// BOOTSTRAP
$dotenv = new Dotenv\Dotenv(__DIR__."/../");
$dotenv->load();

$db = new PDO(sprintf('mysql:host=%s;dbname=%s;port=%d;charset=utf8mb4', getenv('DB_HOST'), getenv('DB_DATABASE'), getenv('DB_PORT')), getenv('DB_USERNAME'), getenv('DB_PASSWORD'));
// END BOOTSTRAP

echo date("d-m-Y H:i:s")." - bootstrapped\n";

$matches = fetchMatchGames($db);
echo count($matches)." is the match count\n";
while ($matches != null && count($matches) > 0) {
    echo "Fetching lineups...\n";
    $db->beginTransaction();

    $insert_values = [];
    $question_marks = [];
    foreach ($matches as $match) {
        if ($match->opponent1_members != null && $match->opponent1_members != "[]" && $match->opponent1_members != "") {
            $members = toArray($match->opponent1_members);
            $members = array_unique($members);

            foreach ($members as $member) {
                if ($member != "null" && $member != null && $member != "") {
                    /*$question_marks[] = '('  . placeholders('?', 3) . ')';
                    $insert_values = array_merge($insert_values, [
                            $match->id,
                            $member,
                            $match->opponent1
                        ]);*/

                    $db->query(sprintf("INSERT INTO `match_lineups` (`match_game_id`, `individual_id`, `team_id`) VALUES (%d, %d, %d)", $match->id, (int)$member, $match->opponent1));
                }
            }
        }

        if ($match->opponent2_members != null && $match->opponent2_members != "[]" && $match->opponent2_members != "") {
            $members = toArray($match->opponent2_members);
            $members = array_unique($members);

            foreach ($members as $member) {
                if ($member != "null" && $member != null && $member != "") {
                    /*$question_marks[] = '('  . placeholders('?', 3) . ')';
                    $insert_values = array_merge($insert_values, [
                            $match->id,
                            $member,
                            $match->opponent2
                        ]);*/

                    $db->query(sprintf("INSERT INTO `match_lineups` (`match_game_id`, `individual_id`, `team_id`) VALUES (%d, %d, %d)", $match->id, (int)$member, $match->opponent2));
                }
            }
        }

        $db->query("UPDATE match_games SET updated_at = NOW() WHERE id = ".$match->id);
    }

    if (count($insert_values)) {
        //$insert = $db->query("INSERT INTO `match_lineups` (`match_game_id`, `individual_id`, `team_id`) VALUES " . implode(',', $question_marks));
    }

    $db->commit();


    $matches = fetchMatchGames($db);
}