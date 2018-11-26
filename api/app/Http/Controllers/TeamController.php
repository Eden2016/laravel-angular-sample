<?php
namespace App\Http\Controllers;

use App\Models\DummyMatch;
use App\Models\MatchGame;
use App\Models\Slot;
use App\Models\TeamAccount;
use App\Models\Tournament;
use App\Services\MainServices;
use App\Services\TeamServices;
use App\Services\GameServices;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class TeamController extends Controller {

    private $_doCache = true;
    private $_cacheTime;

    public function __construct() {
        $this->_cacheTime = 60 * 24; // 1 day
    }

    public function index(Request $request, $game)
    {
        $gameId = GameServices::getGameId($game);

        $limit = $request->get('results', 25);
        $offset = $request->get('skip', 0);
        $sortBy = $request->get('order', '0,');

        if ($this->_doCache && Cache::has(sprintf('api.teams.%s.limit-%d.offset-%d.sort-%s', $game, $limit, $offset, $sortBy))){
            return response(Cache::get(sprintf('api.teams.%s.limit-%d.offset-%d.sort-%s', $game, $limit, $offset, $sortBy)), 200)
                  ->header('Content-Type', 'application/json');
        }

        $teams = TeamServices::getTeams($gameId, $limit, $offset);

        if ($this->_doCache){
            Cache::put(sprintf('api.teams.%s.limit-%d.offset-%d.sort-%s', $game, $limit, $offset, $sortBy), json_encode($teams), 10);
        }

        return response()->json($teams);
    }

    public function show(Request $request, $game, $id)
    {
        $id = MainServices::unmaskId($id);
        $gameId = \App\Services\GameServices::getGameId($game);
        $team = \App\Models\TeamAccount::with('country')->with('game')->find($id);

        $months = $request->input('performance_time_frame', 6);

        if (!$team) {
            $retData = array(
                "success" => false,
                "message" => "No team with that ID found!"
            );
            return response()->json($retData);
        }

        if ($this->_doCache && Cache::has(sprintf('api.team.info.%s.%d', $game, $team->id))){
            return response(Cache::get(sprintf('api.team.info.%s.%d', $game, $team->id)), 200)
                  ->header('Content-Type', 'application/json');
        }
        $team->current_roster = TeamServices::getRoster($id, $game);

        $team->winStreak = $team->win_strike;
        $team->loseStreak = $team->lose_strike;
        $team->monthly_performance = \App\Services\MatchServices::getTeamPerformance($id, $months); //$team->performance;
        $team->last_matches = DummyMatch::with(['opponent1_details', 'opponent2_details'])->where('opponent1',
            $team->id)->orWhere('opponent2',
            $team->id)->orderBy('start', 'desc')->limit(5)->get();

        $team->total_wins = $team->wins_count;
        $team->total_loses = $team->loses_count;
        $team->total_ties = $team->ties_count;
        $team->id = MainServices::maskId($team->id);

        if (count($team->last_matches)) {
            foreach ($team->last_matches as $match) {
                $tournament = \App\Services\MatchServices::getTournamentInfo($match->id);

                if ($tournament) {
                    $match->tournament_name = $tournament->name;
                    $match->tournament_id = MainServices::maskId($tournament->id);
                }

                $match->masked_id = MainServices::maskId($match->id);
                $match->opponent1_details->masked_id = MainServices::maskId($match->opponent1_details->id);
                $match->opponent2_details->masked_id = MainServices::maskId($match->opponent2_details->id);
            }
        }

        if ($this->_doCache){
            Cache::put(sprintf('api.team.info.%s.%d', $game, MainServices::unmaskId($team->id)), json_encode($team), $this->_cacheTime);
        }

        return response()->json($team);
    }

    public function stats($game, $id){
        $id = MainServices::unmaskId($id);
        $team = \App\Models\TeamAccount::find($id);
        if(!$team){
            return ['success' => false, 'message' => 'No team with that ID'];
        }
        $result = Cache::remember('team.'.$id.'.stats', 60*24, function() use ($game, $id, $team){


            $players = \App\Models\PlayerTeam::where('team_id', $team->id)->whereNotNull('end_date')->get();
            if(!count($players)){
                return ['success' => false, 'message' => 'Team has no players'];
            }
            $team->players = $players->map(function($p){
                $p->stats = $p->player->stats;
                return $p;
            });
            $stats = new \stdClass();
            $stats->total_kills = $team->players->sum('stats.kills');
            $stats->total_deaths = $team->players->sum('stats.deaths');
            $stats->total_assists = $team->players->sum('stats.assists');
            $stats->avg_kills = $team->players->sum('stats.avg_kills');
            $stats->avg_deaths = $team->players->sum('stats.avg_deaths');
            $stats->avg_assists = $team->players->sum('stats.avg_assists');
            $hero_wins = [];
            $hero_loses = [];
            $best_heroes = [];
            $worst_heroes = [];
            $hero_plays = [];
            foreach($team->players as $p){
                foreach($p->stats->hero_wins as $hero => $wins){
                    if(array_key_exists($hero, $hero_wins)){
                        $hero_wins[$hero]+=$wins;
                    }else{
                        $hero_wins[$hero] = $wins;
                    }
                    if(array_key_exists($hero, $hero_plays)){
                        $hero_plays[$hero]+=$wins;
                    }else{
                        $hero_plays[$hero] = $wins;
                    }
                }

                foreach($p->stats->hero_loses as $hero => $loses){
                    if(array_key_exists($hero, $hero_loses)){
                        $hero_loses[$hero]+=$loses;
                    }else{
                        $hero_loses[$hero] = $loses;
                    }
                    if(array_key_exists($hero, $hero_plays)){
                        $hero_plays[$hero]+=$loses;
                    }else{
                        $hero_plays[$hero] = $loses;
                    }
                }



            }
            arsort($hero_wins);
            arsort($hero_loses);
            $stats->most_hero_wins = array_keys($hero_wins)[0];
            $stats->most_hero_loses = array_keys($hero_loses)[0];
            $stats->most_played_hero = array_keys($hero_plays)[0];

            $stats->avg_level = round($team->players->avg('stats.avg_level'), 2);
            $stats->avg_denies = round($team->players->avg('stats.avg_denies'), 2);
            $stats->avg_last_hits = round($team->players->avg('stats.last_hits'), 2);
            $stats->avg_gold = round($team->players->avg('stats.avg_gold'), 2);
            $stats->avg_xp_per_minute = round($team->players->avg('stats.avg_xp_per_minute'), 2);

            $tournament_matches = DummyMatch::where('opponent1', $id)
                ->orWhere('opponent2', $id)
                ->with('matchGames')
                ->get();
            if(!count($tournament_matches)){
                return [
                    'overall' => $stats,
                    'tournaments' => null,
                ];
            }
            $match_games = MatchGame::whereIn('dummy_match_id', $tournament_matches->pluck('id'))->with('match')->get();
            $matches_ids = $match_games->pluck('match_id');
            $tournament_matches = $tournament_matches->map(function($t) use ($matches_ids){
                $t->match;
                return $t;
            });

            $slots = Slot::whereIn('match_id', $matches_ids)->with('match')->get();
            if(!count($slots)){
                return [
                    'overall' => $stats,
                    'tournaments' => null,
                ];
            }

            $wins = 0;
            $loses = 0;
            $hero_wins = [];
            $hero_loses = [];
            foreach($slots as $s){
                if(!$s->match) continue;
                if($s->player_slot < 10){ //is radiant
                    if($s->match->radiant_win==1) {
                        $wins++;
                        $hero_wins[] = $s->hero_id;
                    }else{
                        $loses++;
                        $hero_loses[] = $s->hero_id;
                    }
                }else { //then is dire
                    if($s->match->radiant_win==0) {
                        $wins++;
                        $hero_wins[] = $s->hero_id;
                    }else{
                        $loses++;
                        $hero_loses[] = $s->hero_id;
                    }
                }
            }
            $hero_wins = array_count_values($hero_wins);
            $hero_loses = array_count_values($hero_loses);
            arsort($hero_wins);
            arsort($hero_loses);

            /**
             * TODO: check if logic for "best" hero is right
             * Determine best hero based on hero wins of all matches player with that hero
             */
            $hero_wins_percents = [];
            foreach($hero_wins as $hero_id => $wins){
                $hero_wins_percents[$hero_id] = ($wins * $slots->where('hero_id', $hero_id)->count()) / 100;
            }
            arsort($hero_wins_percents);

            $hero_loses_percents = [];
            foreach($hero_loses as $hero_id => $loses){
                $hero_loses_percents[$hero_id] = ($loses * $slots->where('hero_id', $hero_id)->count()) / 100;
            }
            arsort($hero_loses_percents);

            $t_stats = [
                'kills' => $slots->sum('kills'),
                'deaths' => $slots->sum('deaths'),
                'assists' => $slots->sum('assists'),
                'gold' => $slots->sum('gold'),
                'gold_spent' => $slots->sum('gold_spent'),
                'hero_healing' => $slots->sum('hero_healing'),
                'tower_damage' => $slots->sum('tower_damage'),
                'hero_damage' => $slots->sum('hero_damage'),
                'denies' => $slots->sum('denies'),
                'wins' => $tournament_matches->where('winner', $id)->count(),
                'loses' => $tournament_matches->where('winner', '!=', $id)->count(),
                'draws' => $tournament_matches->where('is_tie', 1)->count(),
                'avg' => [
                    'kills' => round($slots->avg('kills'), 2),
                    'deaths' => round($slots->avg('deaths'), 2),
                    'assists' => round($slots->avg('assists'), 2),
                    'level' => round($slots->avg('level'), 2),
                    'gold' => round($slots->avg('gold'), 2),
                    'gold_spent' => round($slots->avg('gold_spent'), 2),
                    'hero_healing' => round($slots->avg('hero_healing'), 2),
                    'tower_damage' => round($slots->avg('tower_damage'), 2),
                    'hero_damage' => round($slots->avg('hero_damage'), 2),
                    'denies' => round($slots->avg('denies'), 2),
                    'gold_per_min' => round($slots->avg('gold_per_min'), 2),
                    'xp_per_min' => round($slots->avg('xp_per_min'), 2),
                    'last_hits' => round($slots->avg('last_hits'), 2),
                ],
                'most_hero_wins' => array_keys($hero_wins)[0],
                'most_hero_loses' => array_keys($hero_loses)[0],
                'best_hero' => array_keys($hero_wins_percents)[0],
                'worst_hero' => array_keys($hero_loses_percents)[0],
            ];
            $heroes = array_count_values($slots->pluck('hero_id')->toArray());
            $t_stats['most_played_hero'] = array_search(max($heroes), $heroes);

            $result = [
              'overall' => $stats,
              'tournaments' => $t_stats
            ];

            return $result;
        });
        return $result;
    }

    public function store(Request $request, $game, $id=null) {
        abort(501, 'Not Implemented');
        /*
        $team = ($id ? TeamAccount::find(MainServices::unmaskId($id)) : new TeamAccount());
        try{
            if($team->id){
                $team->update($request->all());
            }else{
                $team = $team->create($request->all());
            }
        }catch(\Exception $e){
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ],500);
        }

        return response()->json($team, 200);
        */
    }

    public function destroy($game, $id) {
        abort(501, 'Not Implemented');
        /*
        $id = MainServices::unmaskId($id);
        $team = TeamAccount::find($id);

        if(!$team) return response()->json([
            "success" => false,
            "message" => "Team account not found"
        ]);

        try {
            $team->delete();
        }catch(\Exception $e){
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => 'Team account deleted'
        ]);
        */
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