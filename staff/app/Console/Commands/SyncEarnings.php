<?php

namespace App\Console\Commands;

use App\Individual;
use App\TeamAccount;
use App\Tournament;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SyncEarnings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:earnings 
    {--u|user : Specify user}
    {--t|team : Specify team}
    {--only-users=n : Parse only users}
    {--only-teams=n : Parse only teams}
    {--interval=30 : Interval in days for prize check. Will not apply if 0 (zero)}
    {--limit=5000 : Limit count}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync players earnings if no team or user specified - all will be synced.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $user = $this->option('user');
        $team = $this->option('team');
        $interval = $this->option('interval');
        $limit = $this->option('limit');


        $users = null;
        $teams = null;
        if ($this->option('only-teams') == 'n') {
            $users = new Individual();
            if ($user) {
                $users = $users->where('id', $user);
            }
            if ($interval > 0) {
                $users = $users->where(function ($q) use ($interval) {
                    $q->where('last_prizes_check', '<', Carbon::now()->subDays($interval));
                    $q->orWhere('last_prizes_check', null);
                });
            }
            if ($limit > 0) {
                $users = $users->limit($limit);
            }
            $users = $users->whereNotNull('created_at')->get();
        }

        if ($this->option('only-users') == 'n') {
            $teams = new TeamAccount();
            if ($team) {
                $teams = $teams->where('id', $team);
            }
            if ($interval > 0) {
                $teams = $teams->where(function ($q) use ($interval) {
                    $q->where('last_prizes_check', '<', Carbon::now()->subDays($interval));
                    $q->orWhere('last_prizes_check', null);
                });
            }
            if ($limit > 0) {
                $teams = $teams->limit($limit);
            }
            $teams = $teams->whereNotNull('created_at')->get();
        }
        if ($users) {
            $this->info('===== Checking users =====');
            $progress = $this->output->createProgressBar(count($users));
            foreach ($users as $user) {
                $progress->advance(1);
                /**
                 * Player has no teams at all
                 */
                if (!count($user->teams)) {
                    continue;
                }
                /**
                 * Get tournaments created after user creation in the system,
                 * they are not completed and no prizes described
                 */
                $tournaments = $user->tournaments->reject(function ($t) use ($user) {
                    if ($t->created_at < $user->created_at || !$t->is_done || !count($t->prize_distribution)) {
                        return true;
                    }
                    /**
                     * Check if user already has log into database for that tournament
                     */
                    $logs = DB::table('prize_distribution_log')
                        ->where('type_of', 'App\Individual')
                        ->where('action_id', $user->id)
                        ->where('tournament_id', $t->id)
                        ->count();
                    if ($logs) {
                        return true;
                    }
                    return false;
                });
                /**
                 * If no tournaments there is no new prizes
                 */
                if (!$tournaments->count()) {
                    continue;
                }

                /**
                 * Reject tournaments in which user is not part of the team due tournament period
                 */
                $tournaments = $tournaments->filter(function ($t) use ($user) {


                    $user_teams = $user->teams->filter(function ($r) use ($t) {
                        if (!$r->pivot) {
                            return false;
                        }
                        return strtotime($r->pivot->start_date) >= strtotime($t->start)
                        && strtotime($r->pivot->end_date) <= strtotime($t->end);
                    });
                    foreach ($user_teams as $user_team) {
                        if (in_array($user_team->id, $t->teams->pluck('id')->toArray())) {
                            return true;
                        }
                    }
                    return false;
                });
                if (!$tournaments->count()) {
                    continue;
                }

                /**
                 * Reject tournaments where user team does not go into first n places
                 */
                $tournaments = $tournaments->filter(function ($t) use ($user) {
                    $places_with_prizes = count($t->prize_distribution);
                    $i = 1;
                    foreach ($t->scoreboard as $team) {
                        /**
                         * Stop cycle if no more places with prizes
                         */
                        if ($i > $places_with_prizes) {
                            break;
                        }

                        /**
                         * Check if team is in player teams
                         */
                        if (in_array($team->id, $user->teams->pluck('id')->toArray())) {
                            return true;
                        }
                        $i++;
                    }
                    return false;
                });

                /**
                 * We have tournaments in which user is active and his team is in tournaments prize place
                 * Time to get and calculate prize
                 */
                $total_user_prize = 0;
                foreach ($tournaments as $tournament) {
                    $places_with_prizes = count($tournament->prize_distribution);
                    $i = 1;
                    foreach ($tournament->scoreboard as $team) {
                        /**
                         * Stop cycle if no more places with prizes
                         */
                        if ($i > $places_with_prizes) {
                            break;
                        }
                        if (in_array($team->id, $user->teams->pluck('id')->toArray())) {
                            DB::table('prize_distribution_log')->insert([
                                'type_of' => 'App\Individual',
                                'action_id' => $user->id,
                                'tournament_id' => $tournament->id,
                                'sum' => (int)$tournament->prize_distribution[$i] / count($team->roster)
                            ]);
                            $total_user_prize += (int)$tournament->prize_distribution[$i] / count($team->roster);
                        }

                        $i++;
                    }
                }
                $user->earnings = $user->earnings + $total_user_prize;
                $user->save();

            }
            Individual::whereIn('id', $users->pluck('id'))->update(['last_prizes_check' => Carbon::now()]);
            $progress->finish();
            $this->info('===== End of users =====');
        }
        if ($teams) {
            $this->info('===== Checking teams =====');
            $progress = $this->output->createProgressBar(count($teams));
            foreach ($teams as $team) {
                $progress->advance(1);
                /**
                 * reject tournaments created before team creation
                 * and check for existing prize distribution
                 */
                $tournaments = $team->tournaments->reject(function ($t) use ($team) {
                    if ($t->created_at < $team->created_at || !$t->is_done || !count($t->prize_distribution)) {
                        return true;
                    }
                    /**
                     * Check if team already has log into database for that tournament
                     */
                    $logs = DB::table('prize_distribution_log')
                        ->where('type_of', 'App\TeamAccount')
                        ->where('action_id', $team->id)
                        ->where('tournament_id', $t->id)
                        ->count();
                    if ($logs) {
                        return true;
                    }
                    return false;
                });
                /**
                 * If no tournaments there is no new prizes
                 */
                if (!$tournaments->count()) {
                    continue;
                }
                $total_team_prize = 0;
                foreach ($tournaments as $tournament) {
                    $places_with_prizes = count($tournament->prize_distribution);
                    $i = 1;
                    foreach ($tournament->scoreboard as $scoreboard_team) {
                        /**
                         * Stop cycle if no more places with prizes
                         */
                        if ($i > $places_with_prizes) {
                            break;
                        }
                        if ($scoreboard_team->id == $team->id) {
                            DB::table('prize_distribution_log')->insert([
                                'type_of' => 'App\TeamAccount',
                                'action_id' => $team->id,
                                'tournament_id' => $tournament->id,
                                'sum' => (int)$tournament->prize_distribution[$i]
                            ]);
                            $total_team_prize += (int)$tournament->prize_distribution[$i];
                            continue;
                        }
                        $i++;
                    }
                }
                $team->total_earnings = $team->total_earnings + $total_team_prize;
                $team->save();

            }
            TeamAccount::whereIn('id', $teams->pluck('id'))->update(['last_prizes_check' => Carbon::now()]);
            $progress->finish();
            $this->info('===== End of teams =====');
        }
        $this->info('Done. Exiting.');
    }
}
