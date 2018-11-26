<?php
namespace App\Services;

use App\Models\DummyMatch;
use App\Models\Game;
use App\Models\Individual;
use App\Models\LolChampionPick;
use Cache;
use DB;
use App\Models\Slot;
use App\Models\MatchLineup;

class PlayerStatsServices
{
    public static function listDotaPlayers($game=1, $limit=25, $offset=0, $order="RAND()")
    {
        $players = null;
        $gameString = "";
        if ($game)
            $gameString = "AND i.game_id = ".$game;

        //Fetch all live and upcoming matches
        $matches = DB::select(DB::raw(sprintf("SELECT
                                    DISTINCT(i.id) AS player_id
                                FROM dummy_matches AS dm
                                LEFT OUTER JOIN player_teams AS pt ON pt.team_id = dm.opponent1 OR pt.team_id = dm.opponent2
                                LEFT OUTER JOIN individuals AS i ON i.id = pt.individual_id
                                WHERE
                                    dm.game_id = %d
                                    AND pt.end_date IS NULL
                                    AND (dm.start > NOW() OR (dm.start < now() AND dm.winner IS NULL AND dm.is_tie = 0))", $game)));

        if (count($matches) > 0) {
            $playerIds = [];
            foreach ($matches as $match) {
                if ($match->player_id > 0)
                    $playerIds[] = $match->player_id;
            }

            $players = DB::select(DB::raw(sprintf("SELECT
                            i.id,
                            i.nickname,
                            i.first_name,
                            i.last_name,
                            i.bio,
                            i.date_of_birth,
                            i.avatar,
                            i.earnings,
                            c.countryName,
                            ta.name AS team_name,
                            ta.logo AS team_logo,
                            pt.start_date AS team_start,
                            u.account_id,
                            (SELECT COUNT(s.id) FROM slots AS s WHERE s.account_id = u.account_id) AS games_num,
                            (SELECT
                                COUNT(mf.match_id)
                            FROM slots AS sf
                            LEFT JOIN matches AS mf
                                ON mf.match_id = sf.match_id
                            WHERE sf.account_id = u.account_id
                                AND ((sf.player_slot < 10 AND mf.radiant_win = 0) OR (sf.player_slot > 10 AND mf.radiant_win = 1))
                            ) AS lost_games
                        FROM individuals AS i
                        LEFT JOIN countries AS c ON c.id = i.nationality
                        LEFT JOIN player_teams AS pt ON pt.individual_id = i.id
                        LEFT JOIN team_accounts AS ta ON ta.id = pt.team_id
                        LEFT JOIN users AS u ON u.steamid = i.steam_id
                        WHERE pt.end_date is NULL
                        AND u.account_id IS NOT NULL
                        AND ta.name IS NOT NULL
                        AND i.id IN (%s)
                        %s
                        ORDER BY %s
                        LIMIT %d,%d", implode(",", $playerIds), $gameString, $order, $offset, $limit)));

            if (count($players)) {
                foreach ($players as $player) {
                    $player->previous_teams = self::playerTeams($player->id);
                    $player->id = MainServices::maskId($player->id);
                }
            }
        }

        return $players;
    }

    public static function playerTeams($individualId)
    {
        $playerTeams = DB::select(DB::raw(sprintf("SELECT
                                ta.id,
                                ta.name,
                                ta.logo,
                                pt.start_date,
                                pt.end_date
                            FROM player_teams AS pt
                            LEFT JOIN team_accounts AS ta ON ta.id = pt.team_id
                            WHERE pt.individual_id = %d
                            AND pt.end_date IS NOT NULL
                            GROUP BY pt.team_id
                            HAVING MAX(pt.end_date)", $individualId)));

        if (count($playerTeams)) {
            foreach ($playerTeams as $team) {
                $team->id = MainServices::maskId($team->id);
            }
        }

        return $playerTeams;
    }

    public static function listPlayers($game=1, $limit=25, $offset=0, $order="RAND()")
    {
        $players = null;
        $gameString = "";
        if ($game)
            $gameString = "AND i.game_id = ".$game;

        //Fetch all live and upcoming matches
        $matches = DB::select(DB::raw(sprintf("SELECT
                                    DISTINCT(i.id) AS player_id
                                FROM dummy_matches AS dm
                                LEFT OUTER JOIN player_teams AS pt ON pt.team_id = dm.opponent1 OR pt.team_id = dm.opponent2
                                LEFT OUTER JOIN individuals AS i ON i.id = pt.individual_id
                                WHERE
                                    dm.game_id = %d
                                    AND pt.end_date IS NULL
                                    AND (dm.start > NOW() OR (dm.start < now() AND dm.winner IS NULL AND dm.is_tie = 0))", $game)));

        if (count($matches) > 0) {
            $playerIds = [];
            foreach ($matches as $match) {
                if ($match->player_id > 0)
                    $playerIds[] = $match->player_id;
            }

            if (count($playerIds) > 50)
                $playerIds = array_slice($playerIds, 50);

            $players = DB::select(DB::raw("SELECT
                            i.id,
                            i.nickname,
                            i.first_name,
                            i.last_name,
                            i.bio,
                            i.date_of_birth,
                            i.avatar,
                            i.earnings,
                            c.countryName,
                            ta.name AS team_name,
                            ta.logo AS team_logo,
                            pt.start_date AS team_start,
                            (SELECT
                                count(mg.id)
                            FROM match_games AS mg
                            INNER JOIN dummy_matches AS dm ON dm.id = mg.dummy_match_id
                            LEFT JOIN match_lineups AS ml ON ml.match_game_id = mg.id
                            WHERE (ml.team_id = dm.opponent1 OR ml.team_id = dm.opponent2)
                            AND ml.individual_id =  `i`.`id`
                            ) AS games_num,
                            (
                            SELECT
                                count(mg.id)
                            FROM match_games AS mg
                            INNER JOIN dummy_matches AS dm ON dm.id = mg.dummy_match_id
                            LEFT JOIN match_lineups AS ml ON ml.match_game_id = mg.id
                            WHERE mg.winner IS NOT NULL
                            AND
                                ((ml.team_id = dm.opponent1 AND mg.winner <> dm.opponent1) OR (ml.team_id = dm.opponent2 AND mg.winner <> dm.opponent2))
                            AND ml.individual_id =  `i`.`id`
                            ) AS lost_games
                        FROM individuals AS i
                        LEFT JOIN countries AS c ON c.id = i.nationality
                        LEFT JOIN player_teams AS pt ON pt.individual_id = i.id
                        LEFT JOIN team_accounts AS ta ON ta.id = pt.team_id
                        WHERE pt.end_date is NULL
                        AND ta.name IS NOT NULL
                        AND i.id IN (".implode(",", $playerIds).")
                        ".$gameString."
                        ORDER BY ".$order));

            if (count($players)) {
                foreach ($players as $player) {
                    $player->previous_teams = self::playerTeams($player->id);
                    $player->id = MainServices::maskId($player->id);
                }
            }
        }

        return $players;
    }

    public static function getMostPlayedHeroes($steamId, $timeFrame=false)
    {
        $player = \App\Models\Account::where('steamid', $steamId)->first();

        if (null !== $player) {
            if ($timeFrame) {
                $time = strtotime(sprintf('-%d days', $timeFrame));

                $slots = Slot::leftJoin('matches', 'slots.match_id', '=', 'matches.match_id')
                    ->where('account_id', $player->account_id)
                    ->where('matches.start_time', '>', date("Y-m-d", $time))
                    ->get();
            } else {
                $slots = Slot::where('account_id', $player->account_id)->get();
            }

            $wins = 0;
            $loses = 0;
            $hero_wins = [];
            $hero_loses = [];
            $teams = [];
            foreach ($slots as $s) {
                if ($s->player_slot < 10) { //is radiant
                    if ($s->match->radiant_win == 1) {
                        $wins++;
                        $hero_wins[] = $s->hero_id;
                    } else {
                        $loses++;
                        $hero_loses[] = $s->hero_id;
                    }
                    $teams[] = $s->match->radiant_team_id;
                } else { //then is dire
                    if ($s->match->radiant_win == 0) {
                        $wins++;
                        $hero_wins[] = $s->hero_id;
                    } else {
                        $loses++;
                        $hero_loses[] = $s->hero_id;
                    }
                    $teams[] = $s->match->dire_team_id;
                }
            }

            $heroes = array_count_values($slots->pluck('hero_id')->toArray());
            $hero_wins = array_count_values($hero_wins);
            $hero_loses = array_count_values($hero_loses);

            arsort($hero_wins);
            arsort($hero_loses);

            $stats = new \stdClass();

            if ($hero_wins) {
                $stats->hero_wins = $hero_wins;
                $stats->most_hero_wins = array_keys($hero_wins)[0];
            }
            if ($hero_loses) {
                $stats->hero_loses = $hero_loses;
                $stats->most_hero_loses = array_keys($hero_loses)[0];
            }

            $stats->slots = $slots;
            $stats->most_played_hero = array_search(max($heroes), $heroes);
            $stats->hero_games = $heroes;
            $stats->lastest_played_hero = $slots->last()->hero_id;
            $stats->heroes = array_unique(array_merge(array_keys($hero_wins), array_keys($hero_loses)));

            $hero_wins_percents = [];
            foreach ($hero_wins as $hero_id => $wins) {
                $hero_wins_percents[$hero_id] = ($wins * $slots->where('hero_id', $hero_id)->count()) / 100;
            }
            arsort($hero_wins_percents);

            $hero_loses_percents = [];
            foreach ($hero_loses as $hero_id => $loses) {
                $hero_loses_percents[$hero_id] = ($loses * $slots->where('hero_id', $hero_id)->count()) / 100;
            }
            arsort($hero_loses_percents);

            if ($hero_wins_percents) {
                $stats->hero_win_percents = $hero_wins_percents;
                $stats->best_hero = array_keys($hero_wins_percents)[0];
            }
            if ($hero_loses_percents) {
                $stats->worst_hero = array_keys($hero_loses_percents)[0];
                $stats->hero_lose_percents = $hero_loses_percents;
            }
        } else {
            return false;
        }

        return $stats;
    }

    public static function getMostPlayedChampions($playerId, $timeFrame = false)
    {
        $champions = DB::select(DB::raw("SELECT
                count(mg.id) AS total_games,
                lc.id AS champion_id,
                lc.name AS champion_name,
                mg.opponent1_members,
                mg.opponent2_members,
                mg.winner,
                mg.is_tie,
                dm.opponent1,
                dm.opponent2,
                SUM(CASE WHEN (mg.opponent1_members LIKE CONCAT('%\"', mg.opponent1_members, '\"%') AND mg.winner = dm.opponent1) OR (mg.opponent2_members LIKE CONCAT('%\"', `lcp`.`player_id`, '\"%') AND mg.winner = dm.opponent2) THEN 1 ELSE 0 END) AS wins
            FROM lol_champion_picks AS lcp
            LEFT JOIN lol_champions AS lc ON lc.id = lcp.champion_id
            LEFT JOIN match_games AS mg ON mg.id = lcp.match_game_id
            LEFT JOIN dummy_matches AS dm ON dm.id = mg.dummy_match_id
            WHERE lcp.player_id = ".$playerId."
            GROUP BY lcp.champion_id"));

        if (count($champions)) {
            usort($champions, function($a, $b) {
                return $a->total_games < $b->total_games;
            });
            $champions = array_values($champions);
        }

        return $champions;
    }

    public static function getOnlyMostPlayedHeroes($steamId, $timeFrame=false)
    {
        $heroes = DB::select(DB::raw(sprintf("SELECT
                            s.hero_id,
                            count(s.id) AS games_played,
                            (SELECT
                                COUNT(mf.match_id)
                            FROM slots AS sf
                            LEFT JOIN matches AS mf
                                ON mf.match_id = sf.match_id
                            WHERE sf.account_id = s.account_id
                                AND sf.hero_id = s.hero_id
                                AND ((sf.player_slot < 10 AND mf.radiant_win = 0) OR (sf.player_slot > 10 AND mf.radiant_win = 1))
                            ) as hero_losses
                        FROM slots AS s
                        LEFT JOIN matches AS m ON m.match_id = s.match_id
                        LEFT JOIN users AS u ON u.account_id = s.account_id
                        WHERE u.steamid = %d
                        GROUP BY hero_id", $steamId)));

        if (count($heroes)) {
            usort($heroes, function($a, $b) {
                return $a->games_played < $b->games_played;
            });
        }

        return $heroes;
    }

    public static function getWinLoseDota2($steamId, $slots=null)
    {
        if (!$slots)
            $player = \App\Models\Account::where('steamid', $steamId)->first();
        else
            $player = null;

        if (null !== $player || $slots !== null) {
            if (!$slots)
                $slots = Slot::where('account_id', $player->account_id)->get();

            $wins = 0;
            $loses = 0;

            foreach ($slots as $s) {
                if ($s->player_slot < 10) { //is radiant
                    if ($s->match->radiant_win == 1) {
                        $wins++;
                    } else {
                        $loses++;
                    }
                } else { //then is dire
                    if ($s->match->radiant_win == 0) {
                        $wins++;
                    } else {
                        $loses++;
                    }
                }
            }

            return [
                'won'  => $wins,
                'lost' => $loses
            ];
        } else {
            return [
                'won'  => 0,
                'lost' => 0
            ];
        }
    }

    public static function getWinLose($playerId)
    {
        $matches = DB::select(DB::raw(sprintf("SELECT
                        SUM(CASE WHEN (mg.winner = ml.team_id) THEN 1 ELSE 0 END) AS won,
                        SUM(CASE WHEN (mg.winner <> ml.team_id && mg.winner IS NOT NULL) THEN 1 ELSE 0 END) AS lost
                    FROM match_lineups AS ml
                    JOIN match_games AS mg ON mg.id = ml.match_game_id
                    JOIN dummy_matches AS dm ON dm.id = mg.dummy_match_id
                    WHERE individual_id = %d
                    GROUP BY ml.individual_id", $playerId)));

        if ($matches) {
            return [
                'won'  => (int)$matches[0]->won,
                'lost' => (int)$matches[0]->lost
            ];
        }
        else {
            return [
                'won'  => 0,
                'lost' => 0
            ];
        }
    }

    public static function getLastMatches($playerId, $game="dota2")
    {
        $matches = DB::select(DB::raw("
                SELECT
                    taa.id AS opponent1_id,
                    taa.name AS opponent1_name,
                    tab.id AS opponent2_id,
                    tab.name AS opponent2_name,
                    mg.winner AS mg_winner,
                    mg.is_tie AS mg_tie,
                    dm.winner,
                    dm.is_tie,
                    mg.opponent1_members,
                    mg.opponent2_members,
                    dm.start,
                    m.name AS map_name
                FROM match_games AS mg
                INNER JOIN dummy_matches AS dm ON dm.id = mg.dummy_match_id
                INNER JOIN team_accounts AS taa ON taa.id = dm.opponent1
                INNER JOIN team_accounts AS tab ON tab.id = dm.opponent2
                LEFT JOIN maps AS m ON m.id = mg.map_id
                WHERE mg.opponent1_members LIKE '%\"".$playerId."\"%' OR mg.opponent2_members LIKE '%\"".$playerId."\"%'
                ORDER BY dm.start DESC
                LIMIT 5
            "));

        if (count($matches)) {
            $retData = [];
            foreach ($matches AS $k=> $match) {
                $retData[$k] = new \stdClass();

                $retData[$k]->opponent1_members = $match->opponent1_members;
                $retData[$k]->opponent2_members = $match->opponent2_members;
                $retData[$k]->winner = MainServices::maskId($match->mg_winner);
                $retData[$k]->is_tie = $match->mg_tie;

                $retData[$k]->map = new \stdClass();
                $retData[$k]->map->name = $match->map_name;

                $retData[$k]->match = new \stdClass();
                $retData[$k]->match->winner = MainServices::maskId($match->winner);
                $retData[$k]->match->is_tie = $match->is_tie;
                $retData[$k]->match->start = $match->start;

                $retData[$k]->match->opponent1_details = new \stdClass();
                $retData[$k]->match->opponent1_details->id = MainServices::maskId($match->opponent1_id);
                $retData[$k]->match->opponent1_details->name = $match->opponent1_name;

                $retData[$k]->match->opponent2_details = new \stdClass();
                $retData[$k]->match->opponent2_details->id = MainServices::maskId($match->opponent2_id);
                $retData[$k]->match->opponent2_details->name = $match->opponent2_name;
            }

            return $retData;
        } else {
            return [];
        }
    }

    public static function getLastMatchesLol($playerId)
    {
        $matches = DB::select(DB::raw("
                SELECT
                    taa.id AS opponent1_id,
                    taa.name AS opponent1_name,
                    tab.id AS opponent2_id,
                    tab.name AS opponent2_name,
                    mg.winner AS mg_winner,
                    mg.is_tie AS mg_tie,
                    dm.winner,
                    dm.is_tie,
                    mg.opponent1_members,
                    mg.opponent2_members,
                    dm.start,
                    lc.name AS champion_name,
                    lc.slug_name AS champion_slug_name,
                    lc.id AS champion_id
                FROM match_games AS mg
                INNER JOIN dummy_matches AS dm ON dm.id = mg.dummy_match_id
                INNER JOIN team_accounts AS taa ON taa.id = dm.opponent1
                INNER JOIN team_accounts AS tab ON tab.id = dm.opponent2
                LEFT JOIN lol_champion_picks AS lcp ON lcp.match_game_id = mg.id AND lcp.player_id = ".$playerId."
                LEFT JOIN lol_champions AS lc ON lc.id = lcp.champion_id
                WHERE mg.opponent1_members LIKE '%\"".$playerId."\"%' OR mg.opponent2_members LIKE '%\"".$playerId."\"%'
                ORDER BY dm.start DESC
                LIMIT 5
            "));

        if (count($matches)) {
            $retData = [];
            foreach ($matches AS $k=> $match) {
                $retData[$k] = new \stdClass();

                $retData[$k]->opponent1_members = $match->opponent1_members;
                $retData[$k]->opponent2_members = $match->opponent2_members;
                $retData[$k]->winner = MainServices::maskId($match->mg_winner);
                $retData[$k]->is_tie = $match->mg_tie;

                $retData[$k]->champion = new \stdClass();
                $retData[$k]->champion->id = $match->champion_id;
                $retData[$k]->champion->name = $match->champion_name;
                $retData[$k]->champion->slug_name = $match->champion_slug_name;

                $retData[$k]->match = new \stdClass();
                $retData[$k]->match->winner = MainServices::maskId($match->winner);
                $retData[$k]->match->is_tie = $match->is_tie;
                $retData[$k]->match->start = $match->start;

                $retData[$k]->match->opponent1_details = new \stdClass();
                $retData[$k]->match->opponent1_details->id = MainServices::maskId($match->opponent1_id);
                $retData[$k]->match->opponent1_details->name = $match->opponent1_name;

                $retData[$k]->match->opponent2_details = new \stdClass();
                $retData[$k]->match->opponent2_details->id = MainServices::maskId($match->opponent2_id);
                $retData[$k]->match->opponent2_details->name = $match->opponent2_name;
            }

            return $retData;
        } else {
            return [];
        }
    }

    public static function getMatchStats($playerId, $start=false, $end=false)
    {
        if ($start) {
            $matchStats = DB::select(DB::raw("SELECT
                    count(mg.id) AS total_games,
                    SUM(CASE WHEN (mg.opponent1_members LIKE CONCAT('%\"', ".$playerId.", '\"%') AND mg.winner = dm.opponent2) OR (mg.opponent2_members LIKE CONCAT('%\"', ".$playerId.", '\"%') AND mg.winner = dm.opponent1) THEN 1 ELSE 0 END) AS lost
                    FROM match_games AS mg
                    INNER JOIN dummy_matches AS dm ON dm.id = mg.dummy_match_id
                    WHERE (mg.opponent1_members LIKE '%\"".$playerId."\"%' OR mg.opponent2_members LIKE '%\"".$playerId."\"%')
                    AND dm.start BETWEEN '".$start."' AND '".$end."'"));
        }
        else {
            $matchStats = DB::select(DB::raw("SELECT
                    count(mg.id) AS total_games,
                    SUM(CASE WHEN (mg.opponent1_members LIKE CONCAT('%\"', ".$playerId.", '\"%') AND mg.winner = dm.opponent2) OR (mg.opponent2_members LIKE CONCAT('%\"', ".$playerId.", '\"%') AND mg.winner = dm.opponent1) THEN 1 ELSE 0 END) AS lost
                    FROM match_games AS mg
                    INNER JOIN dummy_matches AS dm ON dm.id = mg.dummy_match_id
                    WHERE (mg.opponent1_members LIKE '%\"".$playerId."\"%' OR mg.opponent2_members LIKE '%\"".$playerId."\"%')"));
        }

        return $matchStats;
    }
}