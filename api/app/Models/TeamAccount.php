<?php

namespace App\Models;

use App\Services\MainServices;
use Illuminate\Database\Eloquent\Model;
use DB;

class TeamAccount extends Model
{
    protected $table = 'team_accounts';

    protected $guarded = [
        'id'
    ];

    protected $fillable = [
        'team_id',
        'organization_id',
        'game_id',
        'name',
        'slug_name',
        'tag',
        'created',
        'region',
        'location',
        'twitter',
        'facebook',
        'vk',
        'steam',
        'website',
        'active',
        'hidden'
    ];

    protected $appends = [];

    public function team()
    {
        return $this->hasOne('App\Models\Team', 'id', 'team_id');
    }

    public function country()
    {
        return $this->hasOne('App\Models\Country', 'id', 'location');
    }

    public function matchesOpponent1()
    {
        return $this->hasMany('App\Models\DummyMatch', 'opponent1', 'id');
    }

    public function matchesOpponent2()
    {
        return $this->hasMany('App\Models\DummyMatch', 'opponent2', 'id');
    }

    public function game()
    {
        return $this->hasOne(Game::class, 'id', 'game_id');
    }

    public function getMatchesAttribute()
    {

        /**
         * A fix, since if not unset they are passed with every call to team
         */

        $matches = \Cache::remember('team_matches_' . $this->id, 60 * 2, function () {
            return $this->matchesOpponent1->merge($this->matchesOpponent2);
        });
        unset($this->matchesOpponent1, $this->matchesOpponent2);
        return $matches;
    }

    public function roster()
    {
        return $this->belongsToMany(Individual::class, 'player_teams', 'team_id',
            'individual_id')->withPivot(['end_date', 'start_date', 'is_coach']);
    }

    public function getWinsCountAttribute()
    {
        return \Cache::remember('team_wins_count_' . $this->id, 60 * 24 * 7, function () {
            return DummyMatch::whereWinner($this->id)->count();
        });
    }

    public function getLiveMatchesAttribute()
    {
        return \Cache::remember('team_live_matches_' . $this->id, 60 * 12, function () {
            return $this->matches->filter(function ($m) {
                return strtotime($m->start) < time() && $m->winner == null && $m->is_tie == 0;
            });
        });
    }

    public function getUpcomingMatchesAttribute()
    {
        return \Cache::remember('team_upcoming_matches_' . $this->id, 60 * 12, function () {
            return $this->matches->filter(function ($m) {
                return strtotime($m->start) > time();
            });
        });
    }

    public function getLosesCountAttribute()
    {
        $team = $this;
        return DummyMatch::whereNotNull('winner')
            ->where('winner', '!=', $this->id)
            ->where(function ($q) use ($team) {
                $q->where('opponent1', $team->id)
                    ->orWhere('opponent2', $team->id);
            })
            ->count();
    }

    public function getTiesCountAttribute()
    {
        $team = $this;
        return DummyMatch::where('is_tie', 1)
            ->where(function ($q) use ($team) {
                $q->where('opponent1', $team->id)
                    ->orWhere('opponent2', $team->id);
            })
            ->count();
    }

    public function getWinsAttribute()
    {
        return DummyMatch::whereWinner($this->id)->get();
    }

    public function getLosesAttribute()
    {
        $team = $this;
        return DummyMatch::whereNotNull('winner')
            ->where('winner', '!=', $this->id)
            ->where(function ($q) use ($team) {
                $q->where('opponent1', $team->id)
                    ->orWhere('opponent2', $team->id);
            })
            ->get();
    }

    public function getTieAttribute()
    {
        $team = $this;
        return DummyMatch::where('is_tie', 1)
            ->where(function ($q) use ($team) {
                $q->where('opponent1', $team->id)
                    ->orWhere('opponent2', $team->id);
            })
            ->get();
    }

    public function getStreakAttribute()
    {
        return \Cache::remember('team_streak_' . $this->id, 120, function () {
            $matches = DB::select(DB::raw("SELECT * FROM dummy_matches WHERE (opponent1 = ".$this->id." OR opponent2 = ".$this->id.") AND (winner IS NOT NULL OR is_tie = 1) AND start IS NOT NULL AND start < NOW() ORDER BY start DESC"));

            $streak = 0;
            $prev = 0;
            if (count($matches)) {
                foreach ($matches as $match) {
                    if ($prev === 0) {
                        if ($match->winner === $this->id) {
                            $prev = 1;
                            $streak++;
                        }
                        else if ($match->winner != 0 && $match->winner != null) {
                            $prev = -1;
                            $streak--;
                        }
                        else {
                            $streak = 0;
                            break;
                        }
                    }
                    else {
                        if ($prev > 0) {
                            if ($match->winner === $this->id)
                                $streak++;
                            else
                                break;
                        }
                        else {
                            if (($match->winner != 0 || $match->winner != null) && $match->winner !== $this->id)
                                $streak--;
                            else
                                break;
                        }
                    }
                }
            }
            unset($matches);
            return $streak;
        });
    }

    public function getWinStrikeAttribute()
    {
        return \Cache::remember('team_win_strike_' . $this->id, 60 * 24, function () {
            $matches = $this->matches->filter(function ($m) {
                return $m->winner != null;
            });

            $lastWinStrike = 0;
            $strike = 0;
            $prev = null;
            foreach ($matches as $m) {
                if ($m->winner == $this->id) {
                    if ($prev != 'lose') {
                        $lastWinStrike++;
                        if ($lastWinStrike > $strike) {
                            $strike = $lastWinStrike;
                        }
                    }
                    $prev = 'win';
                } else {
                    $prev = 'lose';
                }
            }
            unset($this->matches);
            return $strike;
        });
    }

    public function getLoseStrikeAttribute()
    {
        return \Cache::remember('team_lose_strike_' . $this->id, 60 * 24, function () {
            $matches = $this->matches->filter(function ($m) {
                return $m->winner != null;
            });

            $lastLoseStrike = 0;
            $strike = 0;
            $prev = null;
            foreach ($matches as $m) {
                if ($m->winner == $this->id) {
                    $prev = 'win';
                } else {
                    if ($prev != 'win') {
                        $lastLoseStrike++;
                        if ($lastLoseStrike > $strike) {
                            $strike = $lastLoseStrike;
                        }
                    }
                    $prev = 'lose';
                }
            }
            unset($this->matches);

            return $strike;
        });
    }

    public function getPerformanceAttribute()
    {
        return \Cache::remember(sprintf('team.id_%d.monthly_performance', $this->id), 60 * 24, function () {
            $team = $this;
            $result = [];
            $matches = DummyMatch::whereNotNull('start')
                ->where(function ($q) {
                    $q->whereNotNull('winner')
                        ->orWhere('is_tie', 1);
                })
                ->where(function ($q) use ($team) {
                    $q->where('opponent1', $team->id)
                        ->orWhere('opponent2', $team->id);
                })
                ->get();

            foreach ($matches as $match) {
                $month = date('m', strtotime($match->start));
                $year = date('Y', strtotime($match->start));
                if (!array_key_exists($month, $result)) {
                    $result[$month] = [
                        'wins' => 0,
                        'loses' => 0,
                        'draws' => 0,
                        'year' => $year,
                        'month' => $month,
                        'timestamp' => $match->start
                    ];
                }
                if ($match->winner == $team->id) {
                    $result[$month]['wins']++;
                } elseif ($match->is_tie) {
                    $result[$month]['draws']++;
                } else {
                    $result[$month]['loses']++;
                }


            }

            if (count($result)) {
                usort($result, function ($a, $b) {
                    if ($a['timestamp'] ==  $b['timestamp'])
                        return 0;

                    return ($a['timestamp'] < $b['timestamp']) ? -1 : 1;
                });
            }

            return $result;
        });
    }


}
