<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Services\MainServices;
use App\Models\MatchGame;
use Cache;
use Illuminate\Http\Request;

class Individual extends Model
{
    protected $table = 'individuals';
    protected $guarded = [
        'id'
    ];
    protected $fillable = [
        'steam_id',
        'game_id',
        'nickname',
        'slug_nick',
        'first_name',
        'last_name',
        'date_of_birth',
        'nationality',
        'location',
        'bio',
        'twitter',
        'facebook',
        'twitch',
        'active',
    ];
    protected $appends = ['total_matches', 'win_rate'];
    private $date_interval = [];

    public function player()
    {
        return $this->hasOne('App\Models\Account', 'steamid', 'steam_id');
    }

    public function playerTeams()
    {
        return $this->hasMany('App\Models\PlayerTeam', 'individual_id', 'id')->orderBy('start_date',
            'desc')->with('team');
    }

    public function getCurrentTeamAttribute()
    {
        if (!count($this->playerTeams)) {
            return null;
        }
        return $this->playerTeams->first();
    }

    public function country()
    {
        return $this->hasOne('App\Models\Country', 'id', 'nationality');
    }

    public function game()
    {
        return $this->hasOne('App\Models\Game', 'id', 'game_id');
    }

    public function setDateIntervalAttribute(array $date)
    {
        $this->date_interval = $date;
    }

    public function getDateIntervalAttribute()
    {
        return $this->date_interval;
    }

    public function getTotalMatchesAttribute()
    {
        if (!$this->player) {
            return 0;
        }
        return count($this->player->slots);
    }

    public function getMatchesAttribute()
    {
        $player_teams = $this->playerTeams->pluck('team.id')->unique();
        $matches = DummyMatch::whereIn('opponent1', $player_teams)->orWhereIn('opponent2', $player_teams)->get();
        return $matches;
    }

    public function getUpcomingMatchesAttribute()
    {
        return $this->matches->filter(function ($m) {
            return strtotime($m->start) > time();
        });
    }

    public function getLiveMatchesAttribute()
    {
        return $this->matches->filter(function ($m) {
            return strtotime($m->start) < time() && $m->winner == null && $m->is_tie == 0;
        });
    }

    public function getWinRateAttribute()
    {
        if (!$this->player) {
            return 0;
        }
        if (!count($this->player->slots)) {
            return 0;
        }
        try {
            $slots = $this->player->slots
                ->map(function ($s) {
                    $s->match;
                    return $s;
                });
            $wins = 0;
            $loses = 0;
            foreach ($slots as $s) {
                if (!$s->match) {
                    continue;
                }
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
            unset($this->player->slots);
            return ceil(($wins / ($wins + $loses)) * 100);
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function getHeroStatsAttribute()
    {
        if (!$this->player) {
            return 0;
        }
        if (!count($this->player->slots)) {
            return 0;
        }
        try {

            $slots = $this->player->slots
                ->map(function ($s) {
                    $s->match;
                    return $s;
                });
            $hero_wins = [];
            $hero_loses = [];
            foreach ($slots as $s) {
                if (!$s->match) {
                    continue;
                }
                if ($s->player_slot < 10) { //is radiant
                    if ($s->match->radiant_win == 1) {
                        $hero_wins[] = $s->hero_id;
                    } else {
                        $hero_loses[] = $s->hero_id;
                    }
                    $teams[] = $s->match->radiant_team_id;
                } else { //then is dire
                    if ($s->match->radiant_win == 0) {
                        $hero_wins[] = $s->hero_id;
                    } else {
                        $hero_loses[] = $s->hero_id;
                    }
                    $teams[] = $s->match->dire_team_id;
                }
            }
            unset($this->player->slots);
            $hero_wins = array_count_values($hero_wins);
            $hero_loses = array_count_values($hero_loses);
            arsort($hero_wins);
            arsort($hero_loses);
            return [
                'wins' => $hero_wins,
                'loses' => $hero_loses
            ];
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function getStatsAttribute()
    {
        if (Cache::has(sprintf('player.id_%d.stats', $this->id))) {
            return Cache::get(sprintf('player.id_%d.stats', $this->id));
        }

        $date_interval = $this->date_interval;
        if (!count($this->player->slots)) {
            return [
                'success' => false,
                'message' => 'Player has no slots.'
            ];
        }
        try {
            $slots = $this->player->slots
                ->map(function ($s) {
                    $s->match;
                    return $s;
                })->filter(function ($slot) use ($date_interval) {
                    if ($date_interval) {
                        return $slot->match->start_time > $date_interval[0] && $slot->match->start_time < $date_interval[1];
                    }
                    return true;
                });
            if (!count($slots)) {
                return null;
            }


            $stats = new \stdClass();

            if ($this->date_interval) {
                $stats->date_interval = $this->date_interval;
            }

            $wins = 0;
            $loses = 0;
            $hero_wins = [];
            $hero_loses = [];
            $teams = [];
            foreach ($slots as $s) {
                if (!$s->match) {
                    continue;
                }
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
            $hero_wins = array_count_values($hero_wins);
            $hero_loses = array_count_values($hero_loses);
            arsort($hero_wins);
            arsort($hero_loses);


            $stats->deaths = $slots->sum('deaths');
            $stats->kills = $slots->sum('kills');
            $stats->assists = round($slots->sum('assists'), 2);
            $stats->kill_death_ratio = round($stats->kills + $stats->assists / $stats->deaths, 2);

            $stats->avg_kills = round($slots->avg('kills'), 2);
            $stats->avg_deaths = round($slots->avg('deaths'), 2);
            $stats->avg_assists = round($slots->avg('assists'), 2);

            $stats->avg_level = round($slots->avg('level'));

            $stats->avg_denies = round($slots->avg('denies'));
            $stats->avg_gold_per_minute = round($slots->avg('gold_per_min'), 2);
            $stats->avg_xp_per_minute = round($slots->avg('xp_per_min'));
            $stats->games_played = $slots->count();
            $heroes = array_count_values($slots->pluck('hero_id')->toArray());
            $stats->last_hits = $slots->sum('last_hits'); // sum?

            $stats->avg_gold = round($slots->avg('gold'), 2);

            $stats->wins = $wins;
            $stats->loses = $loses;


            if ($hero_wins) {
                $stats->hero_wins = $hero_wins;
                $stats->most_hero_wins = array_keys($hero_wins)[0];
            }
            if ($hero_loses) {
                $stats->hero_loses = $hero_loses;
                $stats->most_hero_loses = array_keys($hero_loses)[0];
            }

            $stats->most_played_hero = array_search(max($heroes), $heroes);
            $stats->hero_games = $heroes;
            $stats->lastest_played_hero = $slots->last()->hero_id;
            $stats->heroes = array_unique(array_merge(array_keys($hero_wins), array_keys($hero_loses)));

            /**
             * TODO: check if logic for "best" hero is right
             * Determine best hero based on hero wins of all matches player with that hero
             */
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
            $stats->slots = $this->player->slots;

            Cache::forever(sprintf('player.id_%d.stats', $this->id), $stats);

            return $stats;
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     *
     *
     * @return array
     */
    public function getMostPlayedMapsAttribute()
    {
        if (Cache::has(sprintf('player.id_%d.csgo.most_maps', $this->id)))
            return Cache::get(sprintf('player.id_%d.csgo.most_maps', $this->id));

        $maps = DB::table('match_games')
                            ->select(
                                DB::raw('maps.id, maps.name, count(match_games.map_id) AS total_games')
                            )
                            ->join('maps', 'maps.id', '=', 'match_games.map_id')
                            ->where('opponent1_members', 'LIKE', '%"'.$this->id.'"%')
                            ->orWhere('opponent2_members', 'LIKE', '%"'.$this->id.'"%')
                            ->groupBy('map_id')
                            ->get();

        if (count($maps)) {
            $stats = new \stdClass();

            usort($maps, function ($a, $b) {
                return $a->total_games < $b->total_games;
            });

            $stats->most_played_map = isset($maps[0]) ? $maps[0] : new \stdClass();
            $stats->maps = $maps;

            Cache::put(sprintf('player.id_%d.csgo.most_maps', $this->id), $stats,
                2880); //2 days in the cache
            return $stats;
        }
        else {
            return [
                'success' => false,
                'message' => 'No map info found.'
            ];
        }
    }

    /**
     * Returns last x number of matches that a given player has played
     *
     * @return array|Illuminate\Database\Eloquent\Collection
     */
    public function getLastMatchesAttribute()
    {
        /*if (Cache::has(sprintf('player.id_%d.csgo.last_matches', $this->id)))
            return Cache::get(sprintf('player.id_%d.csgo.last_matches', $this->id));*/

        $matches = MatchGame::select(DB::raw("match_games.*"))
                            ->where('match_games.opponent1_members', 'LIKE', '%"'.$this->id.'"%')
                            ->orWhere('match_games.opponent2_members', 'LIKE', '%"'.$this->id.'"%')
                            ->join('dummy_matches', 'dummy_matches.id', '=', 'match_games.dummy_match_id')
                            ->orderBy('dummy_matches.start', 'DESC')
                            ->with('map', 'match.opponent1_details', 'match.opponent2_details')
                            ->take(5)
                            ->get();

        if (count($matches)) {
            //Cache::put(sprintf('player.id_%d.csgo.last_matches', $this->id), $matches, 2880); //2 days in the cache
            $matches = $matches->map(function($match){
                $match->dummy_match_id = MainServices::maskId($match->dummy_match_id);
                $match->match->id = MainServices::maskId($match->match->id);

                $match->winner = MainServices::maskId($match->winner);

                $match->match->opponent1 = MainServices::maskId($match->match->opponent1);
                $match->match->opponent2 = MainServices::maskId($match->match->opponent2);

                $match->match->opponent1_details->id = MainServices::maskId($match->match->opponent1_details->id);
                $match->match->opponent2_details->id = MainServices::maskId($match->match->opponent2_details->id);

                return $match;
            });

            return $matches;
        }
        else {
            return [
                'success' => false,
                'message' => 'No matches info found.'
            ];
        }
    }

    public function getMostPlayedHeroesAttribute()
    {
        if (!$this->player) {
            return [];
        }
        $result = Slot::select(DB::raw('count(id) as plays, hero_id'))
            ->where('account_id', $this->player->account_id)
            ->groupBy('hero_id')
            ->orderBy('plays', 'desc')
            ->limit(5)
            ->get();
        return $result;
    }

    public function getMonthlyPerformance($months = 6)
    {
        if (Cache::has(sprintf('player.id_%d.monthly_performance.months_%d', $this->id, $months))) {
            return Cache::get(sprintf('player.id_%d.monthly_performance.months_%d', $this->id, $months));
        }

        if (!$this->player) {
            return [];
        }
        $slots = $this->player->slots;
        if (!count($slots)) {
            return [];
        }
        $result = [];
        foreach ($slots as $s) {
            if (!$s->match) {
                continue;
            }
            $month = date('n', strtotime($s->match->start_time));
            $year = date('Y', strtotime($s->match->start_time));
            if (!array_key_exists($month.$year, $result)) {
                $result[$month.$year] = [
                    'wins' => 0,
                    'loses' => 0,
                    'year' => $year,
                    'month' => (int)$month,
                    'timestamp' => strtotime($s->match->start_time)
                ];
            }
            if ($s->player_slot < 10) { //is radiant
                if ($s->match->radiant_win == 1) {
                    $result[$month.$year]['wins']++;
                } else {
                    $result[$month.$year]['loses']++;
                }
            } else { //then is dire
                if ($s->match->radiant_win == 0) {
                    $result[$month.$year]['wins']++;
                } else {
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

        Cache::put(sprintf('player.id_%d.monthly_performance.months_%d', $this->id, $months), $result, 60 * 24 * 2);

        return $result;
    }

    public function getMonthlyPerformanceCs($months = 6)
    {
        if (Cache::has(sprintf('player.csgo.id_%d.monthly_performance.months_%d', $this->id, $months))) {
            return Cache::get(sprintf('player.csgo.id_%d.monthly_performance.months_%d', $this->id, $months));
        }

        $performance = DB::select(DB::raw("SELECT
                            mg.winner AS winner,
                            dm.start AS start_date,
                            mg.is_tie,
                            mg.opponent1_members,
                            mg.opponent2_members,
                            dm.opponent1,
                            dm.opponent2
                        FROM `match_games` AS `mg`
                        INNER JOIN `dummy_matches` AS `dm` ON `dm`.`id` = `mg`.`dummy_match_id`
                        WHERE `mg`.`winner` is not null
                        AND `dm`.`start` > DATE_SUB(NOW(), interval ".$months." month)
                        AND (mg.opponent1_members LIKE '%\"".$this->id."\"%' OR mg.opponent2_members LIKE '%\"".$this->id."\"%')
                        ORDER BY `dm`.`start` DESC"));

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
                if ($match->is_tie) {
                    $result[$month.$year]['draws']++;
                    continue;
                }

                if (strstr($match->opponent1_members, '"'.$this->id.'"')) {
                    if ($match->winner == $match->opponent1) {
                        $result[$month.$year]['wins']++;
                    }
                    else {
                        $result[$month.$year]['loses']++;
                    }
                }
                else {
                    if ($match->winner == $match->opponent2) {
                        $result[$month.$year]['wins']++;
                    }
                    else {
                        $result[$month.$year]['loses']++;
                    }
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

        Cache::put(sprintf('player.csgo.id_%d.monthly_performance.months_%d', $this->id, $months), $result, 60 * 24 * 2);

        return $result;
    }

    public function getMonthlyPerformanceLol($months = 6)
    {
        if (Cache::has(sprintf('player.lol.id_%d.monthly_performance.months_%d', $this->id, $months))) {
            return Cache::get(sprintf('player.lol.id_%d.monthly_performance.months_%d', $this->id, $months));
        }

        $performance = DB::select(DB::raw("SELECT
                            mg.winner AS winner,
                            dm.start AS start_date,
                            mg.is_tie,
                            mg.opponent1_members,
                            mg.opponent2_members,
                            dm.opponent1,
                            dm.opponent2
                        FROM `match_games` AS `mg`
                        INNER JOIN `dummy_matches` AS `dm` ON `dm`.`id` = `mg`.`dummy_match_id`
                        WHERE `mg`.`winner` is not null
                        AND `dm`.`start` > DATE_SUB(NOW(), interval ".$months." month)
                        AND (mg.opponent1_members LIKE '%\"".$this->id."\"%' OR mg.opponent2_members LIKE '%\"".$this->id."\"%')
                        ORDER BY `dm`.`start` DESC"));

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
                if ($match->is_tie) {
                    $result[$month.$year]['draws']++;
                    continue;
                }

                if (strstr($match->opponent1_members, '"'.$this->id.'"')) {
                    if ($match->winner == $match->opponent1) {
                        $result[$month.$year]['wins']++;
                    }
                    else {
                        $result[$month.$year]['loses']++;
                    }
                }
                else {
                    if ($match->winner == $match->opponent2) {
                        $result[$month.$year]['wins']++;
                    }
                    else {
                        $result[$month.$year]['loses']++;
                    }
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

        Cache::put(sprintf('player.lol.id_%d.monthly_performance.months_%d', $this->id, $months), $result, 60 * 24 * 2);

        return $result;
    }
}
