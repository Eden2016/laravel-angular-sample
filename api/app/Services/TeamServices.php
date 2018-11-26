<?php
namespace App\Services;

use Cache;
use DB;

class TeamServices
{
    public static function getTeams($game=1, $limit=25, $offset=0)
    {
        $gameString = "";
        if ($game)
            $gameString = "AND ta.game_id = ".$game;

        $teams = DB::select(DB::raw(sprintf("SELECT
                            ta.id,
                            ta.game_id,
                            ta.name,
                            ta.description,
                            ta.total_earnings,
                            ta.logo,
                            c.countryName,
                            (
                            SELECT
                                count(dmw.id)
                            FROM dummy_matches AS dmw
                            WHERE dmw.winner = ta.id
                            AND (dmw.opponent1 = ta.id OR dmw.opponent2 = ta.id)
                            ) as total_wins,
                            (
                            SELECT
                                count(dmt.id)
                            FROM dummy_matches AS dmt
                            WHERE is_tie = 1
                            AND (dmt.opponent1 = ta.id OR dmt.opponent2 = ta.id)
                            ) as total_ties,
                            (
                            SELECT
                                count(dml.id)
                            FROM dummy_matches AS dml
                            WHERE dml.winner <> ta.id
                            AND is_tie = 0
                            AND (dml.opponent1 = ta.id OR dml.opponent2 = ta.id)
                            ) as total_loss
                        FROM team_accounts AS ta
                        LEFT JOIN countries AS c ON c.id = ta.location
                        WHERE (
                                SELECT
                                    count(dmtot.id)
                                FROM dummy_matches AS dmtot
                                WHERE
                                    (dmtot.opponent1 = ta.id OR dmtot.opponent2 = ta.id)
                                    AND (dmtot.start > now() OR (dmtot.start < now() AND dmtot.winner IS NULL AND dmtot.is_tie = 0))
                              ) > 0
                        %s
                        AND ta.id <> 34
                        AND ta.id <> 35
                        LIMIT %d,%d", $gameString, $offset, $limit)));

        if (count($teams)) {
            foreach ($teams as $team) {
                $team->id = MainServices::maskId($team->id);
            }
        }

        return $teams;
    }

    public static function getRoster($team, $game="dota2")
    {
        $roster = DB::select(DB::raw("SELECT
                            i.id,
                            i.steam_id,
                            i.nickname,
                            i.first_name,
                            i.last_name,
                            i.bio,
                            i.avatar,
                            c.countryName,
                            pt.is_coach,
                            pt.is_sub,
                            pt.is_standin,
                            pt.is_manager
                        FROM individuals AS i
                        LEFT JOIN player_teams AS pt ON pt.individual_id = i.id
                        LEFT JOIN countries AS c ON c.id = i.nationality
                        WHERE pt.end_date IS NULL
                        AND pt.team_id = ".$team));

        if (count($roster)) {
            foreach ($roster as $player) {
                $player->id = MainServices::maskId($player->id);

                if ($game == "dota2") {
                    $player->most_played_heroes = PlayerStatsServices::getOnlyMostPlayedHeroes($player->steam_id, 180);
                }
            }
        }

        return $roster;
    }

    public static function getTournamentTeams($tournamentId) {
        $teams = DB::select(DB::raw(sprintf("SELECT
                         ta.*
                        FROM team_accounts AS ta
                        LEFT JOIN dummy_matches AS dm ON dm.opponent1 = ta.id OR dm.opponent2 = ta.id
                        LEFT JOIN stage_rounds AS sr ON sr.id = dm.round_id
                        LEFT JOIN stage_formats AS sf ON sf.id = sr.stage_format_id
                        LEFT JOIN stages AS s ON s.id = sf.stage_id
                        LEFT JOIN tournaments AS t ON t.id = s.tournament_id
                        WHERE ta.id <> 34
                        AND ta.id <> 35
                        AND t.id = %d
                        GROUP BY ta.id", $tournamentId)));

        if (count($teams)) {
            foreach ($teams as $team) {
                $team->id = MainServices::maskId($team->id);
            }
        }

        return $teams;
    }
}