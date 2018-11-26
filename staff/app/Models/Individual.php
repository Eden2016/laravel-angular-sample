<?php

namespace App;

use Carbon\Carbon;
use Fish\Logger\Logger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Cache;
use App\Scopes\GameSelectorScope;
use App\Scopes\HiddenSelectorScope;

class Individual extends Model
{
    use Logger;
    const SC2_RACES = [
        1 => "Protoss",
        2 => "Terran",
        3 => "Zerg"
    ];

    const OW_ROLES = [
        0 => "Not selected",
        1 => "DPS",
        2 => "Tank",
        3 => "Support",
        4 => "Flex"
    ];

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
        'avatar_url',
        'twitter',
        'facebook',
        'twitch',
        'active',
        'avatar',
        'player_role',
        'hidden',
        'last_prizes_check'
    ];
    protected $appends = ['photo'];
    protected $dates = ['created_at', 'updated_at', 'last_prizes_check'];
    protected $casts = [
        'player_role' => 'array'
    ];
    private $roles = [
        1 => 'AD',
        2 => 'Top',
        3 => 'Mid',
        4 => 'Jungle',
        5 => 'Support'
    ];
    private $date_interval = [];

    public function getLinkAttribute()
    {
        return route('player.show', ['playerId' => $this->id]);
    }

    public function player()
    {
        return $this->hasOne('App\Account', 'steamid', 'steam_id');
    }

    public function getPhotoAttribute()
    {
        return $this->avatar ? url('uploads/' . $this->avatar) : $this->avatar_url;
    }

    public function getRoleAttribute()
    {
        if (!is_array($this->player_role)) {
            $this->player_role = array($this->player_role);
        }

        return $this->player_role ? $this->player_role : [];
    }

    public function getNamedRolesAttribute()
    {
        $roles = [];
        if (is_array($this->role) && count($this->role)) {
            foreach ($this->role as $role_id) {
                $roles[$role_id] = $this->roles[$role_id];
            }
        }
        return $roles;
    }

    public function getTeamsAttribute()
    {
        try {
            return $this->playerTeams()->with('team')->get()->map(function ($item) {
                return $item->team;
            });
        } catch (\Exception $e) {
            return new Collection();
        }
    }

    public function playerTeams()
    {
        return $this->hasMany('App\PlayerTeam', 'individual_id', 'id');
    }

    public function getMatchesAttribute()
    {
        try {
            $matches_ids = $this->select('dummy_matches.id')
                ->leftJoin('player_teams', 'individuals.id', '=', 'player_teams.individual_id')
                ->leftJoin('team_accounts', 'player_teams.team_id', '=', 'team_accounts.id')
                ->leftJoin('dummy_matches', function ($join) {
                    $join->on('team_accounts.id', '=', 'dummy_matches.opponent1')
                        ->orOn('team_accounts.id', '=', 'dummy_matches.opponent2');
                })
                ->where('individuals.id', $this->id)
                ->distinct()
                ->withoutGlobalScope(HiddenSelectorScope::class)
                ->pluck('id');
            return DummyMatch::whereIn('id', $matches_ids)->get();
        } catch (\Exception $e) {
            return new Collection();
        }
    }

    public function country()
    {
        return $this->hasOne('App\Country', 'id', 'nationality');
    }

    public function setDateIntervalAttribute(array $date)
    {
        $this->date_interval = $date;
    }

    public function getDateIntervalAttribute()
    {
        return $this->date_interval;
    }

    public function game()
    {
        return $this->hasOne(Game::class, 'id', 'game_id');
    }

    public function slots()
    {
        return $this->hasManyThrough(Slot::class, Account::class, 'steamid', 'account_id', 'steam_id');
    }

    public function getNextMatchesAttribute()
    {
        try {
            return $this->matches->reject(function ($item) {
                return time() < strtotime($item->start);
            });
        } catch (\Exception $e) {
            return new Collection();
        }
    }

    public function getPreviousMatchesAttribute()
    {
        try {
            return $this->matches->filter(function ($item) {
                return time() > strtotime($item->start);
            });
        } catch (\Exception $e) {
            return new Collection();
        }
    }

    public function getTournamentsAttribute()
    {
        $tournaments_ids = $this->withoutGlobalScope(HiddenSelectorScope::class)->select('tournaments.id')
            ->leftJoin('player_teams', 'individuals.id', '=', 'player_teams.individual_id')
            ->leftJoin('team_accounts', 'player_teams.team_id', '=', 'team_accounts.id')
            ->leftJoin('dummy_matches', function ($join) {
                $join->on('team_accounts.id', '=', 'dummy_matches.opponent1')
                    ->orOn('team_accounts.id', '=', 'dummy_matches.opponent2');
            })
            ->leftJoin('stage_rounds', 'dummy_matches.round_id', '=', 'stage_rounds.id')
            ->leftJoin('stage_formats', 'stage_rounds.stage_format_id', '=', 'stage_formats.id')
            ->leftJoin('stages', 'stage_formats.stage_id', '=', 'stages.id')
            ->leftJoin('tournaments', 'stages.tournament_id', '=', 'tournaments.id')
            ->whereNotNull('tournaments.id')
            ->where('individuals.id', $this->id)
            ->where('tournaments.hidden', 0)
            ->distinct()->pluck('id');
        return Tournament::whereIn('id', $tournaments_ids)->get();
    }

    public function getPreviousTournamentsAttribute()
    {
        try {
            return $this->tournaments->filter(function ($item) {
                return time() > strtotime($item->start);
            });
        } catch (\Exception $e) {
            return new Collection();
        }

    }

    public function getNextTournamentsAttribute()
    {
        try {
            return $this->tournaments->filter(function ($item) {
                return time() < strtotime($item->start);
            });
        } catch (\Exception $e) {
            return new Collection();
        }
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

    public function getStatsAttribute()
    {
        if (Cache::has(sprintf('player.id_%d.stats', $this->id))) {
            return json_decode(Cache::get(sprintf('player.id_%d.stats', $this->id)));
        }

        $date_interval = $this->date_interval;
        $slots = $this->slots->filter(function ($slot) use ($date_interval) {
            if (!$slot->match) {
                return false;
            }
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
        $stats->kill_death_ratio = round($stats->kills / $stats->deaths, 2);
        $stats->assists = round($slots->sum('assists'), 2);
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

//        $stats->matches_played = $slots->pluck('match_id');
        $stats->wins = $wins;
        $stats->loses = $loses;
        $stats->win_rate = ceil(($wins / ($wins + $loses)) * 100);


        if ($hero_wins) {
            $stats->hero_wins = $hero_wins;
            $stats->most_hero_wins = array_keys($hero_wins)[0];
        }
        if ($hero_loses) {
            $stats->hero_loses = $hero_loses;
            $stats->most_hero_loses = array_keys($hero_loses)[0];
        }

        $stats->most_played_hero = array_search(max($heroes), $heroes);
        $stats->lastest_played_hero = $slots->last()->hero_id;

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

//        $stats->recent_matches = $this->previous_matches;
//        $stats->next_matches = $this->next_matches;
        $stats->recent_tournaments = $this->previous_tournaments;
        $stats->next_tournaments = $this->next_tournaments;


        unset($this->player->slots);

        Cache::forever(sprintf('player.id_%d.stats', $this->id), json_encode($stats));
        return $stats;
    }

    public function getSc2RaceAttribute()
    {
        $races = \App\Models\Sc2Race::where('individual_id', $this->id)->get();

        if (count($races) > 0) {
            $ret = [];
            foreach ($races as $race) {
                $ret['ids'][] = $race->race;
                $ret['names'][] = self::SC2_RACES[$race->race];
            }

            return $ret;
        } else {
            return [
                'names' => [],
                'ids' => []
            ];
        }
    }

    protected function bootIfNotBooted()
    {
        parent::boot();
        static::addGlobalScope(new GameSelectorScope());
        static::addGlobalScope(new HiddenSelectorScope());
    }
}
