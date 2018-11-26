<?php
namespace App\Http\Controllers;

use App\Models\Individual;
use App\Services\MainServices;
use App\Services\PlayerStatsServices;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\Services\GameServices;

class PlayerController extends Controller {

    private $_doCache = true;

    public function index(Request $request, $game) {
        if ($game != "all" && $game)
            $gameId = GameServices::getGameId($game);
        else
            $gameId = false;

        $skip 		= (int) $request->get('skip', 0);
        $results 	= (int) $request->get('results', 25);
        $birth 		= $request->get('birth', false);
        $sort = explode(',',$request->get('order', 'rand(),'));
        if(!$sort[1]) $sort[1] = 'asc';

        if ($results < 1 || $results > 50) $results = 50;

        $players = Cache::get(sprintf("players.list.%s.limit-%d.offset-%d.order-%s-%s", $game, $results, $skip, $sort[0], $sort[1]));

        if (null === $players) {
            if ($game == "dota2") {
                $players = PlayerStatsServices::listDotaPlayers($gameId, $results, $skip, $sort[0]." ".$sort[1]);
            }
            else {
                $players = PlayerStatsServices::listPlayers($gameId, $results, $skip, $sort[0]." ".$sort[1]);
            }

            if (count($players) > 0) {
                Cache::put(
                    sprintf(
                        "players.list.%s.limit-%d.offset-%d.order-%s-%s",
                        $game,
                        $results,
                        $skip,
                        $sort[0],
                        $sort[1]
                    ),
                    $players, 10);
            }
        }

        if (count($players) > 0) {
            $retData = array(
                "status" => "success",
                "result" => $players
            );
        } else {
            $retData = array(
                "status" => "fail",
                "message" => "No players found"
            );
        }

        return response()->json($retData);
    }

    public function show(Request $request, $game, $id) {
        $id = MainServices::unmaskId($id);
        $months = $request->input('performance_time_frame', 6);

        if ($this->_doCache)
            $player = Cache::get(sprintf('player.%s.%d.performance-time-frame-%d', $game, $id, $months));
        else
            $player = null;

        if (null === $player) {
            try {
                $player = Individual::where('id', $id)
                    ->with('playerTeams')
                    ->with('country')
                    ->with('game')
                    ->firstOrFail();
                if (get_game() == 'dota2') {
                    $player->recent_performance = $player->player->slots ? $player->player->slots->take(5) : [];
                    $player->monthly_performance = (array)$player->getMonthlyPerformance($months);
                    $player->top_played_heroes = PlayerStatsServices::getOnlyMostPlayedHeroes($player->steam_id, 180);
                    $player->games = PlayerStatsServices::getWinLoseDota2($player->steam_id, $player->player->slots);
                } elseif (get_game() == 'lol') {
                    $player->recent_performance = PlayerStatsServices::getLastMatchesLol($id);
                    $player->monthly_performance = (array)$player->getMonthlyPerformanceLol($months);
                    $player->most_played_champions =  PlayerStatsServices::getMostPlayedChampions($id);

                    $player->games = PlayerStatsServices::getWinLose($id);
                } elseif (get_game() == 'csgo') {
                    $player->recent_performance = PlayerStatsServices::getLastMatches($id, $game);
                    $player->monthly_performance = (array)$player->getMonthlyPerformanceCs($months);
                    $player->most_played_maps = $player->most_played_maps;

                    $player->games = PlayerStatsServices::getWinLose($id);
                }

                $retData = array(
                    "status" => "success",
                    "result" => $player
                );

                if ($this->_doCache)
                    Cache::put(sprintf('player.%s.%d.performance-time-frame-%d', $game, $id, $months), $player, 60 * 24 * 2);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                $retData = array(
                    "status" => "fail",
                    "message" => "No player found"
                );
            }
        }
        else {
            $retData = array(
                "status" => "success",
                "result" => $player
            );
        }

        return response()->json($retData);
    }

    public function stats(Request $request, $game, $id){
        $id = MainServices::unmaskId($id);
        $games = GameServices::getGamesListed();

        $heroTimeFrame = $request->input('hero_time_frame', 0);
        $months = $request->input('performance_time_frame', 6);

        try {
            $player = Individual::where('id', $id)
                    ->firstOrFail();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $response = array(
                "status" => "fail",
                "message" => "No player found"
            );
            return response()->json($response);
        }
        try{
            if ($player->game_id == $games['dota2']) { //If the game is DotA2, return DotA2 statistics

                $stats = $player->stats;
                $mostPlayedHeroes = Cache::remember('player.'.$id.'.mostPlayedHeroes.'.$heroTimeFrame, 60*24, function() use ($player, $heroTimeFrame){
                            return PlayerStatsServices::getMostPlayedHeroes($player->steam_id, $heroTimeFrame);
                        });

                $allStats = new \stdClass();
                $allStats->stats = $stats;
                $allStats->most_played_heroes = $mostPlayedHeroes;

                $allStats->monthly_performance = $player->getMonthlyPerformance($months);
            }
            else if ($player->game_id == $games['csgo']) { //If the game is CS:GO, return map statistics

                $allStats = new \stdClass();
                $allStats->stats = new \stdClass();
                $allStats->stats->maps = $player->most_played_maps;
                $allStats->stats->matches = PlayerStatsServices::getLastMatches($id, $game);

                $allStats->monthly_performance = $player->getMonthlyPerformanceCs($months);
            }
            else if ($player->game_id == $games['lol']) {
                $allStats = Cache::remember('player.'.$id.'.lol.stats', 60*24, function() use ($id){
                    $allStats = new \stdClass();
                    $allStats->stats = new \stdClass();
                    $allStats->stats->matches = PlayerStatsServices::getLastMatchesLol($id);
                    $allStats->most_played_champions =  PlayerStatsServices::getMostPlayedChampions($id);

                    $allStats->monthly_performance = $player->getMonthlyPerformanceLol($months);

                    $allStats->stats->last_month = PlayerStatsServices::getMatchStats($id, date('Y-m-d H:i:s', strtotime("first day of last month")), date('Y-m-d H:i:s', strtotime("last day of last month")));
                    $allStats->stats->this_month = PlayerStatsServices::getMatchStats($id, date('Y-m-d H:i:s', strtotime("first day of this month")), date('Y-m-d H:i:s', strtotime("last day of this month")));
                    $allStats->stats->all_time = PlayerStatsServices::getMatchStats($id);

                    return $allStats;
                });
            }

            return response()->json($allStats);
        }catch (\Exception $e){
            return response()->json($e->getMessage(), 500);
        }


    }

    public function destroy($game, $id) {
        abort(501, 'Not Implemented');
    }



    public function store(Request $request, $game, $id=null) {
        abort(501, 'Not Implemented');
    }

    public function edit($game, $id) {
        abort(501, 'Not Implemented');
    }

    public function update($game, $id) {
        abort(501, 'Not Implemented');
    }

    public function create($game) {
        abort(501, 'Not Implemented');
    }
}