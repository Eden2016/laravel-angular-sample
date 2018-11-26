<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Individual;
use App\Account;
use App\Slot;
use Cache;

class SinglePlayerStatsRefresh extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle($accountId, $steamId=false)
    {
        if ($accountId)
            $account = Account::where('account_id', $accountId)->first();
        else
            $account = Account::where('steamid', $steamId)->first();

        if (null !== $account) {
            $player = Individual::where('steam_id', $account->steamid)->first();

            if (null !== $player) {
                if (!$player->player || $player->player == null) {

                    $slots = $player->player->slots;
                    if ($slots) {
                        $stats = new \stdClass();

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

                        $stats->slots = $slots;

                        //Cache player stats
                        Cache::forget(sprintf('player.id_%d.stats', $player->id));
                        Cache::forever(sprintf('player.id_%d.stats', $player->id), json_encode($stats));

                        //Unset heavy loaded variables, so they don't clog the memory
                        unset($stats);
                        unset($player);
                        unset($slots);
                        unset($hero_wins);
                        unset($hero_loses);
                    }
                }
            }
        }
    }
}
