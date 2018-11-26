<?php
namespace App\Services;

use DB;
use Cache;
use Log;

class TournamentServices
{
    public static function getStageInfo($tournamentId)
    {
        $stages = DB::select(DB::raw("SELECT id, name FROM `stages` WHERE `hidden` = 0 AND `tournament_id` = ".$tournamentId));

        if ($stages) {
            $data = [];

            foreach ($stages as $stage) {
                $data[] = [
                    'id' => $stage->id,
                    'name' => $stage->name,
                    'stage_formats' => self::getStageFormats($stage->id)
                ];
            }

            return $data;
        }
        else {
            return false;
        }
    }

    public static function getStageFormats($stageId)
    {
        $sfs = DB::select(DB::raw("SELECT id, name, type FROM `stage_formats` WHERE `hidden` = 0 AND `stage_id` = ".$stageId));

        if ($sfs) {
            $data = [];

            foreach ($sfs as $k=>$sf) {
                $data[$k] = [
                    'id' => $sf->id,
                    'name' => $sf->name,
                    'type' => $sf->type
                ];

                if ($sf->type === \App\Models\StageFormat::TYPE_ROUND_ROBIN) {
                    $data[$k]['group_data'] = self::getGroupData($sf->id);
                    $data[$k]['bracket_data'] = null;
                }
                else {
                    $data[$k]['teams'] = null;
                    $data[$k]['bracket_data'] = self::getBracketData($sf->id);
                }
            }

            return $data;
        }
        else {
            return null;
        }
    }

    public static function getRounds($sfId)
    {
        $rounds = DB::select(DB::raw("SELECT `id`, `number`, `type` FROM `stage_rounds` WHERE `hidden` = 0 AND stage_format_id = ".$sfId." ORDER BY `number`, `type` ASC"));

        if ($rounds) {
            $data = [];

            foreach ($rounds as $k=>$round) {
                $data[$k] = [
                    'id' => $round->id,
                    'number' => $round->number,
                    'type' => $round->type,
                    'matches' => self::getMatches($round->id)
                ];
            }

            return $data;
        }
        else {
            return null;
        }
    }

    public static function getGroupData($sfId)
    {
        $teams = DB::select(DB::raw("SELECT
                            ta.name,
                            ta.logo,
                            ta.id
                        FROM stage_rounds AS sr
                        LEFT JOIN dummy_matches AS dm ON sr.id = dm.round_id
                        LEFT JOIN team_accounts AS ta ON dm.opponent1 = ta.id OR dm.opponent2 = ta.id
                        WHERE sr.hidden = 0
                        AND sr.stage_format_id = ".$sfId));

        $matches = DB::select(DB::raw("SELECT
                            dm.opponent1,
                            dm.opponent2,
                            dm.winner,
                            dm.is_tie,
                            dm.id,
                            dm.start AS start_date,
                            taa.name AS home_team,
                            taa.logo AS home_team_logo,
                            tab.name AS away_team,
                            tab.logo AS away_team_logo,
                            sf.points_per_win,
                            sf.points_per_draw
                        FROM stage_formats AS sf
                        LEFT JOIN stage_rounds AS sr ON sf.id = sr.stage_format_id
                        LEFT JOIN dummy_matches AS dm ON sr.id = dm.round_id
                        INNER JOIN team_accounts AS taa ON taa.id = dm.opponent1
                        INNER JOIN team_accounts AS tab ON tab.id = dm.opponent2
                        WHERE sf.hidden = 0
                        AND sr.hidden = 0
                        AND dm.hidden = 0
                        AND sf.id = ".$sfId));

        if ($teams) {
            if ($matches) {
                $matches = collect($matches);

                $matches = $matches->map(function($match, $key) {
                    $opp1score = 0;
                    $opp2score = 0;

                    if ($match->winner > 0 || $match->is_tie == 1) {
                        $match_games = DB::select(DB::raw("SELECT opponent1_score, opponent2_score, winner FROM `match_games` WHERE dummy_match_id = ".$match->id));

                        if ($match_games) {
                            foreach ($match_games AS $mg) {
                                if ($mg->opponent1_score == 1 || $mg->winner == $match->opponent1)
                                    $opp1score++;

                                if ($mg->opponent2_score == 1 || $mg->winner == $match->opponent2)
                                    $opp2score++;
                            }
                        }
                    }

                    $match->home_team_score = $opp1score;
                    $match->away_team_score = $opp2score;

                    return $match;
                });

                $ppw = $matches->first()->points_per_win;
                $ppd = $matches->first()->points_per_draw;
            }
            else {
                $matches = collect([]);
                $ppw = 0;
                $ppd = 0;
            }

            $teams = collect($teams)->unique();
            $teams = $teams->map(function ($team, $key) use ($matches, $ppw, $ppd) {
                $team->matches = $matches->where('opponent1',
                    $team->id)->merge($matches->where('opponent2', $team->id));
                $team->total_matches = $team->matches->count();
                $team->wins = $team->matches->where('winner', $team->id)->count();

                $team->loses = $team->matches->filter(function ($m) use ($team) {
                    return $m->winner != $team->id && !$m->is_tie;
                })->count();

                $team->draws = $team->matches->where('is_tie', 1)->count();
                $team->points = (int)($team->wins * $ppw) + ($team->draws * $ppd);

                unset($team->matches);

                return $team;
            });

            $teamData = [];
            foreach ($teams as $team) {
                $team->id = MainServices::maskId($team->id);
                $teamData[] = $team;
            }

            return [
                'teams' => $teamData,
                'schedule' => $matches->toArray()
                ];
        }
        else {
            return null;
        }
    }

    public static function getMatches($roundId)
    {
        $matches = DB::select(DB::raw("SELECT
                    dm.id,
                    dm.opponent1,
                    dm.opponent2,
                    dm.position,
                    taa.name AS home_team,
                    taa.logo AS home_team_logo,
                    tab.name AS away_team,
                    tab.logo AS away_team_logo
                    FROM dummy_matches AS dm
                    INNER JOIN team_accounts AS taa ON taa.id = dm.opponent1
                    INNER JOIN team_accounts AS tab ON tab.id = dm.opponent2
                    WHERE dm.hidden = 0
                    AND dm.round_id = ".$roundId." ORDER BY dm.position ASC"));

        if ($matches) {
            $data = [];
            foreach ($matches as $match) {
                $match_games = DB::select(DB::raw("SELECT opponent1_score, opponent2_score, winner FROM `match_games` WHERE dummy_match_id = ".$match->id));

                $opp1score = 0;
                $opp2score = 0;
                if ($match_games) {
                    foreach ($match_games AS $mg) {
                        if ($mg->opponent1_score == 1 || $mg->winner == $match->opponent1)
                            $opp1score++;

                        if ($mg->opponent2_score == 1 || $mg->winner == $match->opponent2)
                            $opp2score++;
                    }
                }

                $data[] = [
                    "id" => $match->id,
                    "home_team" => $match->home_team,
                    "away_team" => $match->away_team,
                    "home_team_score" => $opp1score,
                    "away_team_score" => $opp2score,
                    "home_team_logo" => $match->home_team_logo,
                    "away_team_logo" => $match->away_team_logo,
                    "position" => $match->position
                ];
            }

            return $data;
        }
        else {
            return null;
        }
    }

    public static function getBracketData($sfId)
    {
        $sf = \App\Models\StageFormat::where('id', $sfId)
            ->with('rounds.dummyMatches.opponent1_details.country', 'rounds.dummyMatches.opponent2_details.country',
                'rounds.dummyMatches.matchGames')
            ->first();

        if (null !== $sf) {
            $bracket = [
                //Upper Bracket
                [],
                //Lower Bracket
                [],
                //Final Bracket
                [],
                //Decider
                []
            ];

            foreach ($sf->rounds as $round) {
                switch ($round->type) {
                    case \App\Models\StageRound::ROUND_TYPE_UPPER_BRACKET:
                        //push match array into upper bracket array
                        array_push($bracket[0], self::populateBracketMatches($round));
                        break;
                    case \App\Models\StageRound::ROUND_TYPE_LOWER_BRACKET:
                        //push match array into lower bracket array
                        array_push($bracket[1], self::populateBracketMatches($round));
                        break;
                    case \App\Models\StageRound::ROUND_TYPE_FINAL:
                        //push match array into final bracket array
                        array_push($bracket[2], self::populateBracketMatches($round));
                        break;
                    case \App\Models\StageRound::ROUND_TYPE_THIRD_PLACE_PLAYOFF:
                        //push match array into decider bracket array
                        array_push($bracket[3], self::populateBracketMatches($round));
                        break;
                }
            }
        }
        else {
            $bracket = null;
        }

        return $bracket;
    }

    /**
     * Generate array with matches for a given round
     * @param  \App\StageRound $round
     * @return array
     */
    public static function populateBracketMatches(\App\Models\StageRound $round)
    {
        //order matches by the position attribute
        $sortedMatches = $round->dummyMatches->sortBy('position');

        //add matches to match array
        $matches = [];
        foreach ($sortedMatches as $match) {
            $opp1Score = 0;
            $opp2Score = 0;
            foreach ($match->matchGames as $mg) {
                if ($mg->winner == $match->opponent1 || $mg->opponent1_score > 0)
                    $opp1Score++;

                if ($mg->winner == $match->opponent2 || $mg->opponent2_score > 0)
                    $opp2Score++;
            }

            array_push($matches, [
                    [
                        $match->opponent1_details->id,
                        $match->opponent1_details->name,
                        $opp1Score,
                        $match->id,
                        $match->opponent1_details->country->countryName
                    ],
                    [
                        $match->opponent2_details->id,
                        $match->opponent2_details->name,
                        $opp2Score,
                        $match->id,
                        $match->opponent2_details->country->countryName
                    ]
                ]);
        }

        return $matches;
    }
}