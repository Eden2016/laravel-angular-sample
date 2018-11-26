<?php
namespace App\Services;

use App\Game;
use App\Models\Dota2\Dota2ChampionBan;
use App\Models\Dota2\Dota2ChampionPick;
use Illuminate\Http\Request;
use App\Models\MatchStandin;
use App\Models\MatchLineup;
use Log;
use DB;

class MatchGameServices
{
    /**
     * Creates a new match game
     *
     * @param Illuminate\Http\Request $request
     *
     * @return \App\MatchGame|bool
     */
    public static function create(Request $request)
    {
        $matchGame = new \App\MatchGame();

        $matchGame->is_crawled          = 0;
        $matchGame->dummy_match_id      = (int)$request->input('match');
        $matchGame->match_id            = $request->input('steam');
        $matchGame->opponent1_score     = (int)$request->input('opp1score');
        $matchGame->opponent2_score     = (int)$request->input('opp2score');
        $matchGame->number              = $request->input('gameNum');
        $matchGame->start               = $request->input('startDate', '2010-10-10 00:00:00');
        $matchGame->opponent1_members   = $request->input('opponent1_members', []);
        $matchGame->opponent2_members   = $request->input('opponent2_members', []);
        $matchGame->map_id              = $request->input('map_id');
        $matchGame->rounds_data         = $request->input('rounds', []);
        $matchGame->streams             = $request->input('streams', []);
        $matchGame->is_tie = $request->input('is_tie', false);
        $matchGame->winner = (int)$request->input('winner', null);
        $matchGame->radiant_team        = $request->input('radiant_team');
        $matchGame->walkover = $request->input('walkover', false);

        $startTime = 0;
        if ($matchGame->start != "") {
            list($date, $time) = explode(" ", $matchGame->start);
            list($year, $month, $day) = explode("-", $date);
            list($hour, $minute, $second) = explode(":", $time);

            $startTime = mktime($hour, $minute, $second, $month, $day, $year);
        }

        if ($matchGame->opponent1_score > 0 || $matchGame->opponent2_score > 0)
            $matchGame->status = \App\MatchGame::STATUS_FINISHED;
        else if (time() > $startTime)
            $matchGame->status = \App\MatchGame::STATUS_LIVE;
        else
            $matchGame->status = \App\MatchGame::STATUS_UPCOMING;

        $matchGame->save();

        $dm = $matchGame->match;
        self::addLineup($request->input('opponent1_members', []), $dm->opponent1, $matchGame->id);
        self::addLineup($request->input('opponent2_members', []), $dm->opponent2, $matchGame->id);

        return $matchGame;
    }

    /**
     * Adds lineups to an intermediate table for many to many relationship
     *
     * @param array $members
     * @param int   $teamId
     * @param int   $mgId
     * @return  bool
     */
    public static function addLineup($members, $teamId, $mgId)
    {
        MatchLineup::where('match_game_id', $mgId)->where('team_id', $teamId)->where('is_standin', 0)->delete();

        $data = [];
        if (is_array($members) && count($members)) {
            foreach ($members as $player) {
                if ($player > 0) {
                    $data[] = [
                        'match_game_id' => $mgId,
                        'individual_id' => $player,
                        'team_id'       => $teamId,
                        'is_standin'    => 0,
                    ];
                }
            }
        }

        if (count($data)) {
            MatchLineup::insert($data);
        }
    }

    /**
     * Edits a match game
     *
     * @param Illuminate\Http\Request $request
     * @param App\MatchGame $matchGame
     *
     * @return \App\MatchGame|bool
     */
    public static function edit(Request $request, \App\MatchGame $matchGame)
    {
        $matchGame->dummy_match_id      = (int)$request->input('match');
        $matchGame->match_id            = $request->input('steam');
        $matchGame->opponent1_score     = (int)$request->input('opp1score');
        $matchGame->opponent2_score     = (int)$request->input('opp2score');
        $matchGame->number              = $request->input('gameNum');
        $matchGame->start               = $request->input('startDate', '2010-10-10 00:00:00');
        $matchGame->opponent1_members   = $request->input('opponent1_members', []);
        $matchGame->opponent2_members   = $request->input('opponent2_members', []);
        $matchGame->map_id              = $request->input('map_id');
        $matchGame->rounds_data         = $request->input('rounds', []);
        $matchGame->streams             = $request->input('streams', []);
        $matchGame->is_tie = $request->input('is_tie', false);
        $matchGame->winner = (int)$request->input('winner', null);
        $matchGame->radiant_team        = $request->input('radiant_team');
        $matchGame->walkover = $request->has('walkover');

        if ($matchGame->start != "") {
            list($date, $time) = explode(" ", $matchGame->start);
            list($year, $month, $day) = explode("-", $date);
            list($hour, $minute) = explode(":", $time);

            $startTime = mktime($hour, $minute, 0, $month, $day, $year);
        }

        if ($matchGame->opponent1_score > 0 || $matchGame->opponent2_score > 0)
            $matchGame->status = \App\MatchGame::STATUS_FINISHED;
        else if (time() > $startTime)
            $matchGame->status = \App\MatchGame::STATUS_LIVE;
        else
            $matchGame->status = \App\MatchGame::STATUS_UPCOMING;

        $matchGame->save();

        $dm = $matchGame->match;
        self::addLineup($request->input('opponent1_members', []), $dm->opponent1, $matchGame->id);
        self::addLineup($request->input('opponent2_members', []), $dm->opponent2, $matchGame->id);

        return $matchGame;
    }

    /**
     * Adds/edits standins for a given match
     *
     * @param Illuminate\Http\Request $request
     *
     * @return void
     */
    public static function addStandins(Request $request, $mgId)
    {
        $opponent1Standins = $request->input('opponent1_standins') != "" ? explode(",", $request->input('opponent1_standins')) : [];
        $opponent2Standins = $request->input('opponent2_standins') != "" ? explode(",", $request->input('opponent2_standins')) : [];

        MatchStandin::where('match_game_id', $mgId)->delete();
        MatchLineup::where('match_game_id', $mgId)->where('is_standin', 1)->delete();

        $data = [];
        foreach ($opponent1Standins as $standin) {
            $data[] = [
                    'individual_id' => $standin,
                    'match_game_id' => $mgId,
                    'team_id' => $request->input('opponent1_id')
                ];

            $dataLineup[] = [
                    'match_game_id' => $mgId,
                    'individual_id' => $standin,
                    'team_id' => $request->input('opponent1_id'),
                    'is_standin' => 1
                ];
        }

        foreach ($opponent2Standins as $standin) {
            $data[] = [
                    'individual_id' => $standin,
                    'match_game_id' => $mgId,
                    'team_id' => $request->input('opponent2_id')
                ];

            $dataLineup[] = [
                    'match_game_id' => $mgId,
                    'individual_id' => $standin,
                    'team_id' => $request->input('opponent2_id'),
                    'is_standin' => 1
                ];
        }

        if (count($data))
            MatchStandin::insert($data);

        if (count($dataLineup))
            MatchLineup::insert($dataLineup);
    }

    /**
     * Gets standins for a given match game and team
     *
     * @param int $mgId
     * @param int $teamId
     *
     * @return array|bool
     */
    public static function getStandins($mgId, $teamId)
    {
        $standins = MatchStandin::where('match_game_id', $mgId)->where('team_id', $teamId)->get();

        $dataSet = [];
        if (count($standins)) {
            foreach ($standins as $standin) {
                $dataSet[] = array(
                        "text" => $standin->player->nickname,
                        "id" => $standin->player->id
                    );
            }
        }

        return $dataSet;
    }

    /**
     *
     * @param  Illuminate\Http\Request $request
     * @param  \App\MatchGame  $mg
     * @return bool
     */
    public static function championPicks(Request $request, $mg)
    {
        $limit = 5;
        if ($request->currentGameSlug == 'lol') {
            \App\Models\LolChampionPick::where('match_game_id', $mg->id)->delete();
        } elseif ($request->currentGameSlug == 'dota2') {
            Dota2ChampionPick::where('match_game_id', $mg->id)->delete();
            $limit = 6;
        }

        $opponent1_members = $request->input('opponent1_members', []);
        $opponent1_picks = $request->input('opponent1_picks', []);

        $opponent2_members = $request->input('opponent2_members', []);
        $opponent2_picks = $request->input('opponent2_picks', []);

        for ($i = 0; $i < $limit; $i++) {
            if (isset($opponent1_picks[$i]) && $opponent1_members[$i] && $opponent1_picks[$i] != '' && $opponent1_members[$i] != '') {
                if ($request->currentGameSlug == 'lol') {
                    $opponent1_pick = new \App\Models\LolChampionPick();
                } else {
                    $opponent1_pick = new Dota2ChampionPick();
                }
                $opponent1_pick->match_game_id = $mg->id;
                $opponent1_pick->player_id = $opponent1_members[$i];
                $opponent1_pick->champion_id = $opponent1_picks[$i];
                $opponent1_pick->save();
            }

            if (isset($opponent2_picks[$i]) && $opponent2_members[$i] && $opponent2_picks[$i] != '' && $opponent1_members[$i] != '') {
                if ($request->currentGameSlug == 'lol') {
                    $opponent2_pick = new \App\Models\LolChampionPick();
                } else {
                    $opponent2_pick = new Dota2ChampionPick();
                }
                $opponent2_pick->match_game_id = $mg->id;
                $opponent2_pick->player_id = $opponent2_members[$i];
                $opponent2_pick->champion_id = $opponent2_picks[$i];
                $opponent2_pick->save();
            }
        }

        return true;
    }

    /**
     *
     * @param  Illuminate\Http\Request $request
     * @param  \App\MatchGame  $mg
     * @return bool
     */
    public static function championBans(Request $request, $mg)
    {
        if ($request->currentGameSlug == 'lol') {
            \App\Models\LolChampionBan::where('match_game_id', $mg->id)->delete();
        } elseif ($request->currentGameSlug == 'dota2') {
            Dota2ChampionBan::where('match_game_id', $mg->id)->delete();
        }

        if (count($request->input('opponent1_bans'))) {
            foreach ($request->input('opponent1_bans') as $champ_ban) {
                if ($request->currentGameSlug == 'lol') {
                    $ban = new \App\Models\LolChampionBan();
                } else {
                    $ban = new Dota2ChampionBan();
                }
                $ban->match_game_id = $mg->id;
                $ban->team_id = $request->input('opponent1_id');
                $ban->champion_id = $champ_ban;
                $ban->save();
            }
        }

        if (count($request->input('opponent2_bans'))) {
            foreach ($request->input('opponent2_bans') as $champ_ban) {
                if ($request->currentGameSlug == 'lol') {
                    $ban = new \App\Models\LolChampionBan();
                } else {
                    $ban = new Dota2ChampionBan();
                }
                $ban->match_game_id = $mg->id;
                $ban->team_id = $request->input('opponent2_id');
                $ban->champion_id = $champ_ban;
                $ban->save();
            }
        }

        return true;
    }

    public static function resultMatchGame($matchGame)
    {
        $matchInfo = DB::select(DB::raw("SELECT * FROM matches WHERE match_id = ".$matchGame->match_id));

        if (null == $matchInfo) {
            return false;
        }

        $opponents = DB::select(DB::raw("SELECT
                    *,
                    taa.name AS taname,
                    taa.team_id AS taid,
                    tab.name AS tbname,
                    tab.team_id AS tbid
                FROM dummy_matches AS dm
                LEFT JOIN team_accounts as taa ON taa.id = dm.opponent1
                LEFT JOIN team_accounts AS tab ON tab.id = dm.opponent2
                WHERE dm.id = ".$matchGame->dummy_match_id));

        if (null != $opponents) {
            //Map opponents to their respectful Steam Team (if they haven't been mapped manually already)
            $opponent1id = $opponents->taid;
            if (!$opponent1id) {
                $opponent1id = self::mapOpponent($opponents->taname);
            }
            $opponent2id = $opponents->tbid;
            if (!$opponent2id) {
                $opponent2id = self::mapOpponent($opponents->tbname);
            }

            //If no match after mapping -> unresult match game and continue
            if (!$opponent1id || !$opponent2id) {
                DB::statement('UPDATE `match_games` SET `opponent1_score` = 0, `opponent2_score` = 0, winner = null, `updated_at` = NOW() WHERE `id` = '.$matchGame->id);
                return false;
            }

            if (null !== $matchInfo) {
                $winner = self::calculateWinner($matchInfo, $opponent1id, $opponent2id);

                if ($winner === 1) {
                    DB::statement('UPDATE `match_games` SET `opponent1_score` = 1, `opponent2_score` = 0, `winner` = '.$matchGame->opponent1.', `updated_at` = NOW() WHERE `id` = '.$matchGame->id);
                }
                else {
                    DB::statement('UPDATE `match_games` SET `opponent2_score` = 1, `opponent1_score` = 0, `winner` = '.$matchGame->opponent2.', `updated_at` = NOW() WHERE `id` = '.$matchGame->id);
                }

                return true;
            }
        }
        else {
            return false;
        }
    }

    public static function mapOpponent($name)
    {
        $team = DB::select(DB::raw(sprintf("SELECT * FROM teams WHERE name = '%s'", $name)));

        if (null != $team)
            return $team->id;
        else
            return false;
    }

    public static function calculateWinner($matchInfo, $opp1Id, $opp2Id)
    {
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
}