#!/usr/bin/php
<?php
error_reporting(E_ALL);
date_default_timezone_set('UTC');
use Sunra\PhpSimple\HtmlDomParser;
use Dota2Api\Api;

require __DIR__.'/../bootstrap/autoload.php';

function mapOpponent($name, $db) {
    $sql = "SELECT * FROM teams WHERE name = '?'";
    $query = $db->prepare($sql);
    $query->execute(array($name));

    $team = $query->fetch(PDO::FETCH_OBJ);

    if (null != $team)
        return $team->id;
    else
        return false;
}

function calculateWinner($matchInfo, $opp1Id, $opp2Id) {
    if (isset($matchInfo->radiant_win)) {
        $radiantWin = $matchInfo->radiant_win;
        $radiantTeamId = $matchInfo->radiant_team_id;
    }
    else {
        $radiantWin = $matchInfo->get('radiant_win');
        $radiantTeamId = $matchInfo->get('radiant_team_id');
    }

    if ($radiantWin == 1) {
        if ($radiantTeamId == $opp1Id)
            return 1;
        else
            return 2;
    }
    else {
        if ($radiantTeamId == $opp1Id)
            return 2;
        else
            return 1;
    }
}

function saveMatch($matchId) {
    $mm = new \Dota2Api\Mappers\MatchMapperWeb($matchId);
    $matchInfo = $mm->load();

    if (null !== $matchInfo) {
        $saver = new \Dota2Api\Mappers\MatchMapperDb();
        $saver->save($matchInfo);

        echo date("d-m-Y H:i:s")." - Match was not found and was fetched from steam api\n";
    }

    return $matchInfo;
}

echo date("d-m-Y H:i:s")." - ".$argv[0]." process started\n";

// BOOTSTRAP
$dotenv = new Dotenv\Dotenv(__DIR__."/../");
$dotenv->load();

$db = new PDO(sprintf('mysql:host=%s;dbname=%s;port=%d;charset=utf8mb4', getenv('DB_HOST'), getenv('DB_DATABASE'), getenv('DB_PORT')), getenv('DB_USERNAME'), getenv('DB_PASSWORD'));

Api::init(getenv('STEAM_API_KEY_TEST'), array(getenv('DB_HOST'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'), getenv('DB_DATABASE'), ''));

$accuracyRate = 0;
// END BOOTSTRAP

echo date("d-m-Y H:i:s")." - bootstrapped\n";

$query = $db->query("SELECT
            `mg`.`id`,
            `mg`.`dummy_match_id`,
            `mg`.`match_id`,
            `dm`.`opponent1`,
            `dm`.`opponent2`
        FROM `match_games` AS `mg`
        LEFT JOIN `dummy_matches` AS `dm` ON `dm`.`id` = `mg`.`dummy_match_id`
        WHERE `mg`.`match_id` > 0
        AND `mg`.`opponent1_score` = 0
        AND `mg`.`opponent2_score` = 0
        AND (`mg`.`winner` is null Or `mg`.`winner` = 0)
        LIMIT 200");

$matches = $query->fetchAll(PDO::FETCH_OBJ);
//echo date("d-m-Y H:i:s")." - fetched match_games\n";
if (null != $matches) {
//echo date("d-m-Y H:i:s")." - we have ".count($matches)." match_games\n";
    foreach ($matches as $k => $match) {
        echo date("d-m-Y H:i:s")." - match game ".$match->id."\n";

        $matchInfoQuery = $db->query("SELECT * FROM matches WHERE match_id = ".$match->match_id);
        $matchInfo = $matchInfoQuery->fetch(PDO::FETCH_OBJ);

        if (null == $matchInfo) {
            $matchInfo = saveMatch($match->match_id);
        }

        //echo date("d-m-Y H:i:s")." - loaded match info\n";
        $opponentQuery = $db->query("SELECT
                    *,
                    taa.name AS taname,
                    taa.team_id AS taid,
                    tab.name AS tbname,
                    tab.team_id AS tbid
                FROM dummy_matches AS dm
                LEFT JOIN team_accounts as taa ON taa.id = dm.opponent1
                LEFT JOIN team_accounts AS tab ON tab.id = dm.opponent2
                WHERE dm.id = ".$match->dummy_match_id);

        $opponents = $opponentQuery->fetch(PDO::FETCH_OBJ);
        //echo date("d-m-Y H:i:s")." - fetched opponents\n";

        if (null != $opponents) {
            //Map opponents to their respectful Steam Team (if they haven't been mapped manually already)
            $opponent1id = $opponents->taid;
            if (!$opponent1id) {
                $opponent1id = mapOpponent($opponents->taname, $db);
            }
            $opponent2id = $opponents->tbid;
            if (!$opponent2id) {
                $opponent2id = mapOpponent($opponents->tbname, $db);
            }

            //If no match after mapping -> unresult match game and continue
            if (!$opponent1id || !$opponent2id) {
                //$db->query('UPDATE `match_games` SET `opponent1_score` = 0, `opponent2_score` = 0, winner = null, `updated_at` = NOW() WHERE `id` = '.$match->id);

                //echo date("d-m-Y H:i:s")." - no map found\n";
                //echo "Opponent1: ".$opponent1id."\n";
                //echo "Opponent2: ".$opponent2id."\n\n";

                continue;
            }

            if (null !== $matchInfo) {
                $winner = calculateWinner($matchInfo, $opponent1id, $opponent2id);

                if ($winner === 1) {
                    $db->query('UPDATE `match_games` SET `opponent1_score` = 1, `opponent2_score` = 0, `winner` = '.$match->opponent1.', `updated_at` = NOW() WHERE `id` = '.$match->id);
                    echo date("d-m-Y H:i:s")." - winner is opponent1\n";
                }
                else {
                    $db->query('UPDATE `match_games` SET `opponent2_score` = 1, `opponent1_score` = 0, `winner` = '.$match->opponent2.', `updated_at` = NOW() WHERE `id` = '.$match->id);
                    echo date("d-m-Y H:i:s")." - winner is opponent2\n";
                }

                $accuracyRate++;
            }
        }
        else {
            echo date("d-m-Y H:i:s")." - no dummy_match found\n";
        }

        echo "\n";
    }

    echo date("d-m-Y H:i:s")." - done\n";
    echo date("d-m-Y H:i:s")." - Resulted matches = ".$accuracyRate." out of ".count($matches).", which is ".ceil(($accuracyRate / count($matches)) * 100)."%\n";
}
else {
    echo date("Y-m-d H:i:s")." No matches found\n";
    print_r($db->errorInfo()); echo "\n";
}