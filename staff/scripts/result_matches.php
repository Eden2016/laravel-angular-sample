#!/usr/bin/php
<?php
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
                        `id`,
                        `opponent1`,
                        `opponent2`
                        FROM `dummy_matches`
                        WHERE
                            (`start` < now() OR `start` IS NULL)
                        AND `winner` IS NULL
                        AND `is_tie` = 0
                        AND `is_forfeited` = 0");
$matches = $query->fetchAll(PDO::FETCH_OBJ);

if (null != $matches) {
    echo "We have ".count($matches)." unresulted matches\nResulting...";
    foreach ($matches as $k => $match) {
        $query = $db->query('SELECT `opponent1_score`, `opponent2_score`, `winner` FROM `match_games` WHERE `dummy_match_id` = '.$match->id);
        $matchGames = $query->fetchAll(PDO::FETCH_OBJ);

        if (count($matchGames)) {
            $opp1score = 0;
            $opp2score = 0;

            $continue = false;
            foreach ($matchGames as $mg) {
                if ($mg->opponent1_score > $mg->opponent2_score || $mg->winner == $match->opponent1)
                    $opp1score++;
                else if ($mg->opponent1_score < $mg->opponent2_score || $mg->winner == $match->opponent2)
                    $opp2score++;
                else
                    $continue = true;
            }

            if ($continue)
                continue;

            if ($opp1score > $opp2score)
                $db->query('UPDATE `dummy_matches` SET winner = '.$match->opponent1.' WHERE `id` = '.$match->id);
            else if ($opp1score < $opp2score)
                $db->query('UPDATE `dummy_matches` SET winner = '.$match->opponent2.' WHERE `id` = '.$match->id);
            else
                $db->query('UPDATE `dummy_matches` SET is_tie = 1 WHERE `id` = '.$match->id);
        }
    }

    echo date("d-m-Y H:i:s")." - done\n";
}
else {
    echo date("Y-m-d H:i:s")." No matches found\n";
    print_r($db->errorInfo()); echo "\n";
}