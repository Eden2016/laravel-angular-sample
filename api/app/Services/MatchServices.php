<?php
namespace App\Services;

use App\Models\Individual;
use DB;
use Cache;
use Log;
use Session;

class MatchServices
{
    /**
     * @param  int $id
     * @return array|null
     */
    public static function getMatchInfo($id)
    {
        $matchInfo = DB::select(DB::raw("SELECT
                        dm.winner AS winner,
                        t.id  AS tournament_id,
                        t.name AS tournament_name,
                        g.name AS game_name,
                        g.slug AS game_slug,
                        g.id AS game_id,
                        `taa`.`name` AS opponent1name,
                        `taa`.`id` AS opponent1id,
                        `tab`.`name` AS opponent2name,
                        `tab`.`id` AS opponent2id,
                        taac.countryName AS opponent1country,
                        tabc.countryName AS opponent2country,
                        dm.opponent1_members,
                        dm.opponent2_members,
                        dm.is_tie,
                        sr.stage_format_id,
                        sf.type
                    FROM `dummy_matches` AS `dm`
                    LEFT JOIN `team_accounts` AS `taa` ON `dm`.`opponent1` = `taa`.`id`
                    LEFT JOIN `countries` AS `taac` ON taa.location = taac.id
                    LEFT JOIN `team_accounts` AS `tab` ON `dm`.`opponent2` = `tab`.`id`
                    LEFT JOIN `countries` AS `tabc` ON tab.location = tabc.id
                    LEFT JOIN stage_rounds as sr ON sr.id = dm.round_id
                    LEFT JOIN stage_formats as sf ON sf.id = sr.stage_format_id
                    LEFT JOIN stages as s ON s.id = sf.stage_id
                    LEFT JOIN tournaments as t ON t.id = s.tournament_id
                    LEFT JOIN games as g ON g.id = t.game_id
                    WHERE `dm`.`id` = ".$id));

        if ($matchInfo) {
            foreach($matchInfo as $match) {
                $match->winner = MainServices::maskId($match->winner);
                $match->opponent1id = MainServices::maskId($match->opponent1id);
                $match->opponent2id = MainServices::maskId($match->opponent2id);
            }
        }

        return $matchInfo;
    }

    /**
     * @param  int $id
     * @return array|null
     */
    public static function getMatchStreams($id)
    {
        $streams = DB::select(DB::raw("
            SELECT
                s.*
            FROM matches_streams as ms
            LEFT JOIN streams as s ON s.id = ms.streams_id
            WHERE ms.matches_id = ".$id));

        if (count($streams)) {
            foreach ($streams as $stream) {
                if ($stream->embed_code) {
                    $stream->embed = $stream->embed_code;
                }
                else {
                    $stream->embed = $stream->code;
                }
            }
        }

        return $streams;
    }

    /**
     * @param  int $matchId
     * @param  int $opponent1
     * @param  int $opponent2
     * @param  int $num
     * @return array|null
     */
    public static function getLastMatchups($matchId, $opponent1, $opponent2, $game="dota2", $num=5)
    {
        $matchUps = DB::select(DB::raw(sprintf("SELECT
            dm.id AS match_id,
            dm.winner AS winner,
            dm.start AS start_date,
            dm.is_tie,
            t.name AS tournament_name,
            t.id AS tournament_id,
            g.name AS game_name,
            g.id AS game_id,
            `taa`.`name` AS opponent1name,
            `taa`.`id` AS opponent1id,
            `tab`.`name` AS opponent2name,
            `tab`.`id` AS opponent2id,
            taac.countryName AS opponent1country,
            tabc.countryName AS opponent2country,
            (SELECT COUNT(mg.id) FROM match_games AS mg WHERE mg.dummy_match_id = dm.id) AS match_games_number
        FROM `dummy_matches` AS `dm`
        LEFT JOIN `team_accounts` AS `taa` ON `dm`.`opponent1` = `taa`.`id`
        LEFT JOIN `countries` AS `taac` ON taa.location = taac.id
        LEFT JOIN `team_accounts` AS `tab` ON `dm`.`opponent2` = `tab`.`id`
        LEFT JOIN `countries` AS `tabc` ON tab.location = tabc.id
        LEFT JOIN stage_rounds as sr ON sr.id = dm.round_id
        LEFT JOIN stage_formats as sf ON sf.id = sr.stage_format_id
        LEFT JOIN stages as s ON s.id = sf.stage_id
        LEFT JOIN tournaments as t ON t.id = s.tournament_id
        LEFT JOIN games as g ON g.id = t.game_id
        WHERE dm.id <> %d
        AND (dm.start < NOW() OR dm.start IS NULL)
        AND (dm.winner IS NOT NULL OR dm.is_tie <> 0)
        AND ((dm.opponent1 = %d AND dm.opponent2 = %d)
        OR (dm.opponent1 = %d AND dm.opponent2 = %d))
        ORDER BY dm.start DESC
        LIMIT %d", $matchId, $opponent1, $opponent2, $opponent2, $opponent1, $num)));

        if (count($matchUps)) {
            foreach ($matchUps as $match) {
                $match->match_games = self::getMatchGames($match->match_id, $game);
                $match->score = self::getMatchScore($match->match_games, $match->opponent1id, $match->opponent2id);

                $match->match_id = MainServices::maskId($match->match_id);
                $match->winner = MainServices::maskId($match->winner);
                $match->tournament_id = MainServices::maskId($match->tournament_id);
                $match->opponent1id = MainServices::maskId($match->opponent1id);
                $match->opponent2id = MainServices::maskId($match->opponent2id);
            }
        }

        return $matchUps;
    }

    /**
     * Gets the final score according to match games
     * @param  array $matchGames
     * @param  int $opponent1
     * @param  int $opponent2
     * @return \stdClass
     */
    public static function getMatchScore($matchGames, $opponent1, $opponent2)
    {
        $opponent1 = MainServices::maskId($opponent1);
        $opponent2 = MainServices::maskId($opponent2);

        $scores = new \stdClass();
        $scores->opp1score = 0;
        $scores->opp2score = 0;

        if ($matchGames && count($matchGames) > 0) {
            foreach ($matchGames as $mg) {
                if ($mg->winner) {
                    if ($mg->winner == $opponent1)
                        $scores->opp1score++;
                    else if ($mg->winner == $opponent2)
                        $scores->opp2score++;
                }
                else {
                    $scores->opp1score += $mg->opponent1_score;
                    $scores->opp2score += $mg->opponent2_score;
                }
            }
        }

        return $scores;
    }

    /**
     * @param  int $id
     * @return array|null
     */
    public static function getMatchGames($id, $game = "dota2")
    {
        $matchGames = DB::select(DB::raw("SELECT
            mg.id,
            mg.match_id,
            mg.opponent1_score,
            mg.opponent2_score,
            mg.number,
            mg.opponent1_members,
            mg.opponent2_members,
            mg.rounds_data,
            mg.is_tie,
            mg.winner,
            dm.opponent1,
            dm.opponent2,
            m.id AS map_id,
            m.name AS map_name,
            m.image AS map_image
        FROM `match_games` as mg
        JOIN `dummy_matches` AS `dm` ON `dm`.`id` = `mg`.`dummy_match_id`
        LEFT JOIN maps as m ON m.id = mg.map_id
        WHERE mg.dummy_match_id = " . $id));

        if (count($matchGames)) {
            foreach ($matchGames as $mg) {
                $mg->winner = MainServices::maskId($mg->winner);
                //$mg->opponent1_members = json_decode($mg->opponent1_members);
                //$mg->opponent2_members = json_decode($mg->opponent2_members);
                $mg->opponent1_players = self::getLineups($mg->opponent1, $mg->opponent1_members, $mg->id);//Individual::whereIn('id', (array)$mg->opponent1_members)->get();
                $mg->opponent2_players = self::getLineups($mg->opponent2, $mg->opponent2_members, $mg->id);//Individual::whereIn('id', (array)$mg->opponent2_members)->get();

                if ($game == "dota2") {
                    if ($mg->match_id) {
                        $mg->slots = self::getSlots($mg->match_id);
                    }
                }

                if ($game == "csgo") {
                    if ($mg->rounds_data != "[]" && $mg->rounds_data != null) {
                        $mg->rounds_data = array_map(function ($rd) {
                            if (isset($rd->ct))
                                $rd->ct = MainServices::maskId($rd->ct);

                            return $rd;
                        }, json_decode($mg->rounds_data));
                    }
                    else {
                        $mg->rounds_data = [];
                    }
                }

                if ($game == "lol") {
                    $mg->picks = self::getLolPicks($mg->id);
                }
            }
        }

        return $matchGames;
    }

    /**
     * Get slots info for a given match id
     *
     * @param  int $match_id
     * @return array|null
     */
    public static function getSlots($match_id)
    {
        if (null != $match_id) {
            $slots = DB::select(DB::raw("SELECT
                    s.match_id,
                    s.hero_id,
                    s.player_slot,
                    u.personaname AS nickname,
                    u.steamid,
                    u.account_id,
                    m.duration
                FROM `slots` AS `s`
                LEFT JOIN users AS u ON u.account_id = s.account_id
                LEFT JOIN matches AS m ON m.match_id = s.match_id
                WHERE s.match_id = " . $match_id));

            return $slots;
        } else {
            return null;
        }
    }

    /**
     * @param  int $team
     * @param  int $num
     * @return array|null
     */
    public static function getLastMatches($team, $game="dota2", $num=5)
    {
        $matches = DB::select(DB::raw(sprintf("SELECT
            dm.id AS match_id,
            dm.winner AS winner,
            dm.start AS start_date,
            dm.is_tie,
            `taa`.`name` AS opponent1name,
            `taa`.`id` AS opponent1id,
            `tab`.`name` AS opponent2name,
            `tab`.`id` AS opponent2id,
            taac.countryName AS opponent1country,
            tabc.countryName AS opponent2country,
            (SELECT COUNT(mg.id) FROM match_games AS mg WHERE mg.dummy_match_id = dm.id) AS match_games_number
        FROM `dummy_matches` AS `dm`
        LEFT JOIN `team_accounts` AS `taa` ON `dm`.`opponent1` = `taa`.`id`
        LEFT JOIN `countries` AS `taac` ON taa.location = taac.id
        LEFT JOIN `team_accounts` AS `tab` ON `dm`.`opponent2` = `tab`.`id`
        LEFT JOIN `countries` AS `tabc` ON tab.location = tabc.id
        WHERE (dm.opponent1 = %d OR dm.opponent2 = %d)
        AND dm.start < NOW()
        AND (dm.winner IS NOT NULL OR dm.is_tie <> 0)
        ORDER BY dm.start DESC
        LIMIT %d", $team, $team, $num)));

        if (count($matches)) {
            foreach ($matches as $match) {
                $match->match_games = self::getMatchGames($match->match_id, $game);
                $match->score = self::getMatchScore($match->match_games, $match->opponent1id, $match->opponent2id);

                //$match->match_id = MainServices::maskId($match->match_id);
                $match->winner = MainServices::maskId($match->winner);
                $match->opponent1id = MainServices::maskId($match->opponent1id);
                $match->opponent2id = MainServices::maskId($match->opponent2id);
            }
        }

        return $matches;
    }

    /**
     * @param  int $team
     * @param  int $weeks
     * @return array|null
     */
    public static function getTeamPerformance($team, $months=6)
    {
        $performance = DB::select(DB::raw(sprintf("SELECT
                            dm.winner AS winner,
                            dm.start AS start_date,
                            dm.is_tie
                        FROM `dummy_matches` AS `dm`
                        WHERE dm.winner is not null
                        AND dm.start > DATE_SUB(NOW(), interval %d month)
                         AND (dm.opponent1 = %d OR dm.opponent2 = %d)
                        ORDER BY dm.start DESC", $months, $team, $team)));

        $result = [];
        if (count($performance)) {
            foreach ($performance as $match) {
                $date = new \DateTime($match->start_date);
                $month = $date->format("n");
                $year = date('Y', strtotime($match->start_date));
                if (!array_key_exists($month.$year, $result)) {
                    $result[$month.$year] = [
                        'wins' => 0,
                        'loses' => 0,
                        'draws' => 0,
                        'month' => (int)$month,
                        'year' => $year,
                        'timestamp' => strtotime($match->start_date)
                    ];
                }
                if ($match->winner == $team) {
                    $result[$month.$year]['wins']++;
                }
                elseif ($match->is_tie) {
                    $result[$month.$year]['draws']++;
                }
                else {
                    $result[$month.$year]['loses']++;
                }
            }
        }

        //prefill missed out months, so we make it 6 in total
        for ($i = $months-1; $i >= 0; $i--) {
            if ($i > 0)
                $time = strtotime("-".$i." months");
            else
                $time = time();

            $year = date("Y", $time);
            $month = date("n", $time);

            if (!array_key_exists($month.$year, $result)) {
                $result[$month.$year] = [
                    'wins' => 0,
                    'loses' => 0,
                    'draws' => 0,
                    'month' => (int)$month,
                    'year' => $year,
                    'timestamp' => $time
                ];
            }
        }

        if (count($result)) {
            usort($result, function ($a, $b) {
                if ($a['timestamp'] ==  $b['timestamp'])
                    return 0;

                return ($a['timestamp'] < $b['timestamp']) ? -1 : 1;
            });
        }

        //Make array keys incrementing numbers starting from 0, so javascript doesn't shit it's pants
        $result = array_values($result);

        return $result;
    }

    /**
     * @param  int $matchId
     * @return array|null
     */
    public static function getDota2MatchGames($matchId)
    {
        $matchGames = DB::select(DB::raw("SELECT
                            mg.id,
                            mg.match_id,
                            mg.number,
                            mg.opponent1_score,
                            mg.opponent2_score,
                            mg.winner,
                            mg.opponent1_members AS mg_opp1_members,
                            mg.opponent2_members AS mg_opp2_members,
                            dm.opponent1_members AS dm_opp1_members,
                            dm.opponent2_members AS dm_opp2_members,
                            dm.opponent1,
                            dm.opponent2
                        FROM `match_games` AS `mg`
                        LEFT JOIN `dummy_matches` AS `dm` ON `dm`.`id` = mg.dummy_match_id
                        WHERE dm.id = ".$matchId));

        $mgs = [];
        if (count($matchGames)) {
            foreach ($matchGames as $k=>$mg) {
                if (!$mg->match_id) {
                    //continue;
                    $membersOpp1 = $mg->mg_opp1_members;
                    $membersOpp2 = $mg->mg_opp2_members;

                    if ($membersOpp1 == null)
                        $membersOpp1 = $mg->dm_opp1_members;

                    if ($membersOpp2 == null)
                        $membersOpp2 = $mg->dm_opp2_members;

                    $lineupOpp1 = self::getLineups($mg->opponent1, $membersOpp1, $mg->id);
                    $lineupOpp2 = self::getLineups($mg->opponent2, $membersOpp2, $mg->id);
                }
                else {
                    $lineupOpp1 = self::getDota2ApiLineups($mg->match_id, $mg->opponent1);
                    $lineupOpp2 = self::getDota2ApiLineups($mg->match_id, $mg->opponent2);
                }

                $mgs[$k] = array(
                        "match_id" => $mg->match_id,
                        "number" => $mg->number,
                        "opponent1_score" => $mg->opponent1_score,
                        "opponent2_score" => $mg->opponent2_score,
                        "winner" => MainServices::maskId($mg->winner),
                        "opponent1_members" => $lineupOpp1,
                        "opponent2_members" => $lineupOpp2
                    );

                //$mgs[$k]['slots'] = self::getSlots($mg->match_id);
                $mgs[$k]['bans'] = self::getHeroBans($mg->match_id);
            }

            return $mgs;
        }
        else {
            return null;
        }
    }

    /**
     * @param  int $matchId
     * @return array|null
     */
    public static function getMatchGameLineups($matchId, $game='dota2')
    {
        $matchGames = DB::select(DB::raw("SELECT
                            mg.id,
                            mg.match_id,
                            mg.number,
                            mg.opponent1_score,
                            mg.opponent2_score,
                            mg.winner,
                            mg.opponent1_members AS mg_opp1_members,
                            mg.opponent2_members AS mg_opp2_members,
                            dm.opponent1_members AS dm_opp1_members,
                            dm.opponent2_members AS dm_opp2_members,
                            dm.opponent1,
                            dm.opponent2
                        FROM `match_games` AS `mg`
                        LEFT JOIN `dummy_matches` AS `dm` ON `dm`.`id` = mg.dummy_match_id
                        WHERE dm.id = ".$matchId));

        $mgs = [];
        if (count($matchGames)) {
            foreach ($matchGames as $k=>$mg) {
                $membersOpp1 = $mg->mg_opp1_members;
                $membersOpp2 = $mg->mg_opp2_members;

                if ($membersOpp1 == null)
                    $membersOpp1 = $mg->dm_opp1_members;

                if ($membersOpp2 == null)
                    $membersOpp2 = $mg->dm_opp2_members;

                $lineupOpp1 = self::getLineups($mg->opponent1, $membersOpp1, $mg->id);
                $lineupOpp2 = self::getLineups($mg->opponent2, $membersOpp2, $mg->id);

                $mgs[$k] = array(
                        "match_id" => $mg->match_id,
                        "number" => $mg->number,
                        "opponent1_score" => $mg->opponent1_score,
                        "opponent2_score" => $mg->opponent2_score,
                        "winner" => MainServices::maskId($mg->winner),
                        "opponent1_members" => $lineupOpp1,
                        "opponent2_members" => $lineupOpp2
                    );

                if ($game === 'dota2') {
                    $mgs[$k]['slots'] = self::getSlots($mg->match_id);
                    $mgs[$k]['bans'] = self::getHeroBans($mg->match_id);
                }
                else if ($game === 'lol') {
                    $mgs[$k]['picks'] = self::getLolPicks($mg->id);
                    $mgs[$k]['bans'] = self::getLolBans($mg->id);
                }
            }

            return $mgs;
        }
        else {
            return null;
        }
    }

    /**
     * Returns info for players in a lineup
     *
     * @param  int $team
     * @param  string $members
     * @return array|null
     */
    public static function getLineups($team, $members, $mgId = null)
    {
        if (null != $members && $members != '[""]' && $members != '[]') {
            $members = str_replace("[", "", $members);
            $members = str_replace("]", "", $members);
            $members = str_replace("\"", "", $members);

            $membersInfo = DB::select(DB::raw(sprintf("SELECT
                                     id,
                                     steam_id,
                                     nickname,
                                     first_name,
                                     last_name,
                                     avatar,
                                     bio
                                     FROM individuals
                                     WHERE id IN (%s)", $members)));

            if (count($membersInfo)) {
                foreach ($membersInfo as $member) {
                    $member->id = MainServices::maskId($member->id);
                }
            }

            if ($mgId) {
                $standIns = DB::select(DB::raw(sprintf("SELECT
                                    i.id,
                                    i.steam_id,
                                    i.nickname,
                                    i.first_name,
                                    i.last_name,
                                    i.avatar,
                                    i.bio
                                FROM `match_standins` AS `ms`
                                INNER JOIN `individuals` AS `i` ON `i`.`id` = `ms`.`individual_id`
                                WHERE `ms`.`team_id` = %d
                                AND `ms`.`match_game_id` = %d", $team, $mgId)));

                if (count($standIns)) {
                    foreach ($standIns as $member) {
                        $member->id = MainServices::maskId($member->id);
                    }

                    $membersInfo = array_merge($membersInfo, $standIns);
                }
            }

            return $membersInfo;
        }
        else {
            return self::getRoster($team);
        }
    }

    /**
     * Returns roster of a given team
     *
     * @param  int $team
     * @return array|null
     */
    public static function getRoster($team)
    {
        $roster = DB::select(DB::raw("SELECT
                                        i.id,
                                        i.steam_id,
                                        i.nickname,
                                        i.first_name,
                                        i.last_name,
                                        i.avatar,
                                        i.bio
                                    FROM player_teams AS pt
                                    LEFT JOIN individuals as i ON i.id = pt.individual_id
                                    WHERE pt.end_date IS NULL
                                    AND is_coach = 0
                                    AND is_manager = 0
                                    AND pt.team_id = " . $team));

        if (count($roster)) {
            foreach ($roster as $player) {
                $player->id = MainServices::maskId($player->id);
            }
        }

        return $roster;
    }

    /**
     * Get banned heros
     *
     * @param  int $match_id
     * @return array|null
     */
    public static function getHeroBans($match_id)
    {
        if (null != $match_id) {
            $bans = DB::select(DB::raw("SELECT `hero_id`, `team`, `order` FROM `picks_bans` WHERE `is_pick` = 0 AND `match_id` = ".$match_id));

            return $bans;
        }
        else {
            return null;
        }
    }

    /**
     * @param  int $opponent1
     * @param  int $opponent2
     * @return array|null
     */
    public static function matchupsMapBreakdown($opponent1, $opponent2)
    {
        $mapBreakdown = DB::select(DB::raw(sprintf("SELECT
                                m.id,
                                m.name,
                                count(mg.id) AS times_played
                            FROM match_games AS mg
                            LEFT JOIN dummy_matches AS dm ON dm.id = mg.dummy_match_id
                            LEFT JOIN maps AS m ON m.id = mg.map_id
                            WHERE (dm.opponent1 = %d AND opponent2 = %d)
                            OR (dm.opponent1 = %d AND opponent2 = %d)
                            GROUP BY m.id
                            HAVING m.id IS NOT NULL", $opponent1, $opponent2, $opponent2, $opponent1)));

        return $mapBreakdown;
    }

    /**
     * @param  int $teamId
     * @return array|null
     */
    public static function teamMapBreakdown($teamId)
    {
        $mapBreakdown = DB::select(DB::raw(sprintf("SELECT
                                            m.id,
                                            m.name,
                                            count(mg.id) AS times_played,
                                            (
                                            SELECT
                                                count(mgm.`id`)
                                            FROM match_games AS mgm
                                            LEFT JOIN dummy_matches AS dmd ON dmd.id = mgm.dummy_match_id
                                            WHERE mgm.map_id = mg.map_id
                                            AND mgm.winner = %d
                                            ) AS wins,
                                            (
                                            SELECT
                                                count(mgm.`id`)
                                            FROM match_games AS mgm
                                            LEFT JOIN dummy_matches AS dmd ON dmd.id = mgm.dummy_match_id
                                            WHERE mgm.map_id = mg.map_id
                                            AND mgm.is_tie = 1
                                            ) AS ties
                                        FROM match_games AS mg
                                        LEFT JOIN dummy_matches AS dm ON dm.id = mg.dummy_match_id
                                        LEFT JOIN maps AS m ON m.id = mg.map_id
                                        WHERE (dm.opponent1 = %d OR opponent2 = %d)
                                        GROUP BY m.id
                                        HAVING m.id IS NOT NULL", $teamId, $teamId, $teamId)));

        return $mapBreakdown;
    }

    public static function getTournamentInfo($matchId)
    {
        $tournament = DB::select(DB::raw("SELECT
                        t.name,
                        t.id
                    FROM dummy_matches AS dm
                    LEFT JOIN stage_rounds AS sr ON sr.id = dm.round_id
                    LEFT JOIN stage_formats AS sf ON sf.id = sr.stage_format_id
                    LEFT JOIN stages AS s ON s.id = sf.stage_id
                    LEFT JOIN tournaments AS t ON t.id = s.tournament_id
                    WHERE dm.id = ".$matchId));

        return $tournament[0];
    }

    /**
     * Get picks for LoL match games
     * @param  int $mgId
     * @return NULL|Array
     */
    public static function getLolPicks($mgId)
    {
        $picks = DB::select(DB::raw("SELECT
                        lcp.player_id,
                        lcp.champion_id,
                        lc.name AS champion_name
                    FROM lol_champion_picks AS lcp
                    LEFT JOIN lol_champions AS lc ON lc.id = lcp.champion_id
                    WHERE lcp.match_game_id = ".$mgId));

        if ($picks) {
            if (count($picks)) {
                foreach ($picks as $pick) {
                    $pick->player_id = MainServices::maskId($pick->player_id);
                }
            }

            return $picks;
        }
        else {
            return null;
        }
    }

    /**
     * Get bans for LoL match games
     * @param  int $mgId
     * @return NULL|Array
     */
    public static function getLolBans($mgId)
    {
        $bans = DB::select(DB::raw("SELECT
                        lcb.team_id,
                        lcb.champion_id,
                        lc.name AS champion_name
                    FROM lol_champion_bans AS lcb
                    LEFT JOIN lol_champions AS lc ON lc.id = lcb.champion_id
                    WHERE lcb.match_game_id = ".$mgId));

        if ($bans) {
            return $bans;
        }
        else {
            return null;
        }
    }

    /**
     * [getDota2ApiLineups description]
     * @param  int $matchId
     * @param  int $opponent1Id
     * @param  int $opponent2Id
     * @return bool|array
     */
    public static function getDota2ApiLineups($matchId, $opponentId)
    {
        //Get linueps info from steam api data
        //by looking for the match in the matches table
        $lineups = DB::select(DB::raw("SELECT
                s.hero_id,
                s.player_slot,
                m.start_time,
                i.id,
                i.steam_id,
                i.nickname,
                i.first_name,
                i.last_name,
                i.avatar,
                i.bio
            FROM slots AS s
            LEFT JOIN users AS u ON u.account_id = s.account_id
            LEFT JOIN individuals AS i ON i.steam_id = u.steamid
            LEFT JOIN matches AS m ON m.match_id = s.match_id
            WHERE s.match_id = ".$matchId));

        if ($lineups) {
            $lineup = [];
            foreach ($lineups AS $player) {
                if (self::mapPlayerToTeam($player->id, $player->start_time) == $opponentId) {
                    $player->id = MainServices::maskId($player->id);
                    $lineup[] = $player;
                }
            }

            return $lineup;
        }
        else {
            //If we don't have info in the matches table, we should check the match_history table
            //because the match may not have finished yet
            $lineups = DB::select(DB::raw(sprintf("SELECT
                        *
                    FROM
                    (select `id`, `match_id`, `player_id`, `time`, `duration`, `hero_id` from `match_history` where `match_id` = %d order by id desc) as `t1`
                    group by `player_id`", $matchId)));

            if ($lineups) {
                $lineup = [];

                foreach ($lineups as $k=>$player) {
                    $info = DB::select(DB::raw("SELECT
                                    i.id,
                                    i.steam_id,
                                    i.nickname,
                                    i.first_name,
                                    i.last_name,
                                    i.avatar,
                                    i.bio
                                FROM users AS u
                                LEFT JOIN individuals AS i ON i.steam_id = u.steamid
                                WHERE u.account_id = ".$player->player_id));

                    if ($info) {
                        if (self::mapPlayerToTeam($info->id, date('Y-m-d', $player->time)) == $opponentId) {
                            $newInfo = new \stdClass();
                            $newInfo->id = $player->id = MainServices::maskId($info[0]->id);
                            $newInfo->steam_id = steam_id;
                            $newInfo->nickname = $info[0]->nickname;
                            $newInfo->first_name = $info[0]->first_name;
                            $newInfo->last_name = $info[0]->last_name;
                            $newInfo->avatar = $info[0]->avatar;
                            $newInfo->bio = $info[0]->bio;
                            $newInfo->hero_id = $player->hero_id;

                            $lineup[] = $newInfo;
                        }
                    }
                }

                return $lineup;
            }
            else {
                return false;
            }
        }
    }

    /**
     * Maps an individual to a team he played for a given match
     * @param  int $individualId
     * @param  string $matchStartTime
     * @return int
     */
    public static function mapPlayerToTeam($individualId, $matchStartTime)
    {
        $team = DB::select(DB::raw(sprintf("SELECT
                ta.id
            FROM player_teams AS pt
            LEFT JOIN team_accounts AS ta ON ta.id = pt.team_id
            WHERE individual_id = %d
            AND start_date <= '%s'
            AND (end_date >= '%s' OR end_date IS NULL)", $individualId, $matchStartTime, $matchStartTime)));

        if ($team) {
            return $team[0]->id;
        }
        else {
            return 0;
        }
    }
}
