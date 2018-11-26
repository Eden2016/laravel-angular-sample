<?php

namespace App;

use App\Scopes\HiddenSelectorScope;
use Fish\Logger\Logger;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\GameSelectorScope;

class TeamAccount extends Model
{
    use Logger;
    protected $table = 'team_accounts';

    protected $guarded = [
        'id'
    ];
    protected $dates = ['created_at', 'updated_at', 'last_prizes_check'];

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
        'hidden',
        'total_earnings',
        'last_prizes_check'
    ];


    public function getLinkAttribute()
    {
        return route('team.show', ['teamId' => $this->id]);
    }

    public function team()
    {
        return $this->hasOne('App\Team', 'id', 'team_id');
    }

    public function country()
    {
        return $this->hasOne('App\Country', 'id', 'location');
    }

    public function matchesOpponent1()
    {
        return $this->hasMany('App\DummyMatch', 'opponent1', 'id');
    }

    public function matchesOpponent2()
    {
        return $this->hasMany('App\DummyMatch', 'opponent2', 'id');
    }

    public function getTournamentsAttribute()
    {
        $tournaments_ids = $this->withoutGlobalScope(HiddenSelectorScope::class)->select('tournaments.id')
            ->leftJoin('dummy_matches', function ($join) {
                $join->on('team_accounts.id', '=', 'dummy_matches.opponent1')
                    ->orOn('team_accounts.id', '=', 'dummy_matches.opponent2');
            })
            ->leftJoin('stage_rounds', 'dummy_matches.round_id', '=', 'stage_rounds.id')
            ->leftJoin('stage_formats', 'stage_rounds.stage_format_id', '=', 'stage_formats.id')
            ->leftJoin('stages', 'stage_formats.stage_id', '=', 'stages.id')
            ->leftJoin('tournaments', 'stages.tournament_id', '=', 'tournaments.id')
            ->whereNotNull('tournaments.id')
            ->where('team_accounts.id', $this->id)
            ->where('tournaments.hidden', 0)
            ->distinct()->pluck('id');
        return Tournament::whereIn('id', $tournaments_ids)->get();
    }

    public function getMatchesAttribute()
    {
        return $this->matchesOpponent1->merge($this->matchesOpponent2);
    }

    public function roster()
    {
        return $this->belongsToMany(Individual::class, 'player_teams', 'team_id',
            'individual_id')->withPivot([
            'end_date',
            'start_date',
            'is_coach',
        ])->withoutGlobalScope(GameSelectorScope::class);
    }

    public function game()
    {
        return $this->hasOne(Game::class, 'id', 'game_id');
    }

    public function getSimpleStatsAttribute()
    {
        $matches = DummyMatch::where('opponent1', $this->id)
            ->orWhere('opponent2', $this->id)
            ->orderBy('start', 'DESC')
            ->get();

        if (!count($matches)) {
            return false;
        }

        try {
            $stats = new \stdClass();

            $stats->wins = 0;
            $stats->losses = 0;
            $stats->draws = 0;
            foreach ($matches as $match) {
                if ($match->is_tie) {
                    $stats->draws++;
                } else {
                    if ($match->winner === $this->id) {
                        $stats->wins++;
                    } else {
                        $stats->losses++;
                    }
                }
            }
            $stats->win_rate = ceil(($stats->wins / ($stats->wins + $stats->losses)) * 100);

            /**
             * Get the current streak of the team
             */
            $stats->streak = 0;
            $streak = 0;
            foreach ($matches as $match) {
                if ($streak === 0) {
                    if ($match->winner === $this->id) {
                        $streak = 1;
                        $stats->streak++;
                    } else {
                        if ($match->winner != 0) {
                            $streak = -1;
                            $stats->streak--;
                        } else {
                            $stats->streak = 0;
                            break;
                        }
                    }
                } else {
                    if ($streak) {
                        if ($match->winner === $this->id) {
                            $stats->streak++;
                        } else {
                            break;
                        }
                    } else {
                        if ($match->winner != 0 && $match->winner !== $this->id) {
                            $stats->streak--;
                        } else {
                            break;
                        }
                    }
                }
            }

            return $stats;
        } catch (\Exception $e) {
            \Log::error("TeamAccount.php - getSimpleStatsAttribute Method - " . $e->getMessage());
            return false;
        }
    }

    protected function bootIfNotBooted()
    {
        parent::boot();
        static::addGlobalScope(new GameSelectorScope());
    }
}
