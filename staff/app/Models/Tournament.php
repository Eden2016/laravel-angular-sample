<?php
namespace App;

use App\Models\Streams;
use Fish\Logger\Logger;
use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class Tournament extends Model
{
    use Logger;
    const STATUS_UPCOMING = 0;
    const STATUS_LIVE = 1;
    const STATUS_CANCELED = 2;
    const STATUS_COMPLETED = 3;

    const CURR_EUR = 0;
    const CURR_USD = 1;
    const CURR_RMB = 2;
    const CURR_SEK = 3;
    const CURR_GBP = 4;
    const CURR_TWD = 5;
    const CURR_HKD = 6;
    const CURR_BRL = 7;
    const CURR_KRW = 8;
    const CURR_RUB = 9;
    const CURR_JPY = 10;
    const CURR_ARS = 11;
    const CURR_AUD = 12;
    const CURR_TRY = 13;
    const CURR_SGD = 14;
    const CURR_MYR = 15;
    const CURR_THB = 16;
    const CURR_VND = 17;
    const CURR_INR = 18;
    const CURR_PHP = 19;
    const CURR_DKK = 20;
    const CURR_PLN = 21;
    const CURR_NOK = 22;



    const DISTRO_FIXED = 0;
    const DISTRO_PERCENTAGE = 1;

    protected $table = 'tournaments';

    protected $casts = [
        'ignored_streams'    => 'array',
        'maps_ids'           => 'array',
        'prize_distribution' => 'array',
        'notable_teams'      => 'array',
    ];

    protected $fillable = [
        'game_id',
        'event_id',
        'league_id',
        'name',
        'location',
        'start',
        'end',
        'season',
        'prize',
        'currency',
        'prize_distribution',
        'prize_dist_type',
        'description',
        'url',
        'status',
        'hidden',
        'active',
        'maps_ids',
        'notable_teams',
    ];

    protected $guarded = [
        'id'
    ];
    protected $appends = ['is_done'];

    public static function listCurrencies()
    {
        return array(
            self::CURR_EUR => 'EUR',
            self::CURR_USD => 'USD',
            self::CURR_RMB => 'RMB',
            self::CURR_SEK => 'SEK',
            self::CURR_SEK => 'SEK',
            self::CURR_GBP => 'GBP',
            self::CURR_TWD => 'TWD',
            self::CURR_HKD => 'HKD',
            self::CURR_BRL => 'BRL',
            self::CURR_KRW => 'KRW',
            self::CURR_RUB => 'RUB',
            self::CURR_JPY => 'JPY',
            self::CURR_ARS => 'ARS',
            self::CURR_AUD => 'AUD',
            self::CURR_TRY => 'TRY',
            self::CURR_SGD => 'SGD',
            self::CURR_MYR => 'MYR',
            self::CURR_THB => 'THB',
            self::CURR_VND => 'VND',
            self::CURR_INR => 'INR',
            self::CURR_PHP => 'PHP',
            self::CURR_DKK => 'DKK',
            self::CURR_PLN => 'PLN',
            self::CURR_NOK => 'NOK',
        );
    }

    public static function listDistributions()
    {
        return array(
            self::DISTRO_FIXED => "Fixed Amount",
            self::DISTRO_PERCENTAGE => "Percentage"
        );
    }

    public static function listCountries()
    {
        $countries = DB::select("SELECT * FROM countries ORDER BY countryName ASC");

        $retData = array();
        foreach ($countries as $country) {
            $retData[$country->id] = $country->countryName;
        }

        return $retData;
    }

    public static function listGames()
    {
        $games = DB::select("SELECT * FROM games ORDER BY id ASC");

        $retData = array();
        foreach ($games as $game) {
            $retData[$game->id] = $game->name;
        }

        return $retData;
    }

    public static function listRegions()
    {
        return array(
            "SEA",
            "Europe",
            "North America",
            "South America",
            "CIS",
            "Asia",
            "Africa",
            "Australia"
        );
    }

    public function getLinkAttribute()
    {
        return route('tournament.view', ['tournamentId' => $this->id]);
    }

    public function event()
    {
        return $this->belongsTo('App\Event');
    }

    public function stages()
    {
        return $this->hasMany('App\Stage');
    }

    public function getIsDoneAttribute()
    {
        if (!$this->stages->count()) {
            return false;
        }
        $not_resulted_matches = Tournament::select(\DB::raw('count(dummy_matches.id) as not_resulted'))
            ->leftJoin('stages', 'stages.tournament_id', '=', 'tournaments.id')
            ->leftJoin('stage_formats', 'stage_formats.stage_id', '=', 'stages.id')
            ->leftJoin('stage_rounds', 'stage_rounds.stage_format_id', '=', 'stage_formats.id')
            ->leftJoin('dummy_matches', 'dummy_matches.round_id', '=', 'stage_rounds.id')
            ->whereNull('dummy_matches.winner')
            ->where('stages.hidden', 0)
            ->where('stage_formats.hidden', 0)
            ->where('stage_rounds.hidden', 0)
            ->where('dummy_matches.is_tie', 0)
            ->where('dummy_matches.done', 0)
            ->where('dummy_matches.hidden', 0)
            ->where('tournaments.id', $this->id)
            ->first()
            ->not_resulted;
        if ($not_resulted_matches > 0) {
            return false;
        }
        return true;
    }

    public function getMapsAttribute()
    {
        return Maps::whereIn('id', $this->maps_ids)->get();
    }

    public function league()
    {
        return $this->hasOne('App\League', 'leagueid', 'league_id');
    }

    public function game()
    {
        return $this->hasOne('App\Game', 'id', 'game_id');
    }

    public function getNotableAttribute()
    {
        return TeamAccount::whereIn('id', $this->notable_teams)->get();
    }

    public function getTeamsAttribute()
    {
        try {
            $dummy_matches = $this->stages->pluck('stageFormats')->flatten()->pluck('rounds')->flatten()->pluck('dummyMatches')->flatten();
            $teams = $dummy_matches->pluck('opponent1')->unique()->values()->merge(
                $dummy_matches->pluck('opponent2')->unique()->values()
            )->unique();
            return TeamAccount::whereIn('id', $teams)->get();
        } catch (\Exception $e) {
            return [];
        }
    }

    public function streams()
    {
        return $this->belongsToMany(Streams::class, 'tournaments_streams', 'tournaments_id', 'streams_id');
    }

    public function getAllStreamsIdsAttribute()
    {
        return $this->all_streams->pluck('id')->toArray();
    }

    public function getAllStreamsAttribute()
    {
        $streams = new Collection();

        /**
         * Connect streams from tournament event
         */
        if ($this->event->streams) {
            $streams->push($this->event->streams->pluck('id')->toArray());
        }

        /**
         * Add streams directly connected to tournament
         */
        if ($this->streams) {
            $streams->push($this->streams->pluck('id')->toArray());
        }

        /**
         * Flatten streams collection
         */
        $streams = $streams->flatten();


        /**
         * Finally reject streams that are ignored in tournament
         */
        if ($this->ignored_streams) {
            $streams = $streams->reject(function ($item) {
                return in_array($item, $this->ignored_streams);
            });
        }

        return Streams::whereIn('id', $streams->toArray())->get();
    }

    public function getTournamentStatusAttribute() 
    {
        $now = time();

        if(strtotime($this->start) > $now) {
            $return = self::STATUS_UPCOMING;
        }

        if(strtotime($this->start) < $now && strtotime($this->end) >= $now) {
            $return = self::STATUS_LIVE;
        }

        if(strtotime($this->end) < $now) {
            $return = self::STATUS_COMPLETED;
        }

        return $return;
    }

    public function getScoreboardAttribute()
    {
        return Cache::remember('tournament.scoreboard_' . $this->id, 60 * 24 * 30, function () {
            $resulted_matches = new Collection();
            $matches = \App\StageFormat::leftJoin('stages', 'stages.id', '=', 'stage_formats.stage_id')
                ->with('rounds.dummyMatches.opponent1_details', 'rounds.dummyMatches.opponent2_details',
                    'rounds.dummyMatches.matchGames')
                ->where('stages.tournament_id', $this->id)
                ->get()
                ->pluck('rounds')->flatten()
                ->pluck('dummyMatches')->flatten()->sortBy('start');
            foreach ($matches as $m) {
                if ($m->winner !== null || $m->is_tie || $m->is_forfeited) {
                    $resulted_matches->push($m);
                }
            }
            $teams = $matches->pluck('opponent1_details')->merge($matches->pluck('opponent2_details'))->unique();
            $teams->map(function ($team, $key) use ($resulted_matches) {
                $team_resulted_matches = $resulted_matches->where('opponent1',
                    $team->id)->merge($resulted_matches->where('opponent2', $team->id));
                $team->wins = $team_resulted_matches->where('winner', $team->id)->count();
                $team->draws = $team_resulted_matches->where('is_tie', 1)->count();
                $team->points = 0;
                foreach ($team_resulted_matches as $m) {
                    $stage_format = $m->stageRound->stageFormat;
                    $win_points = $stage_format->points_per_win ? $stage_format->points_per_win : 2;
                    $draw_points = $stage_format->points_per_draw ? $stage_format->points_per_draw : 1;
                    if ($m->winner == $team->id) {
                        $team->points += (int)$win_points;
                    } elseif ($m->is_tie == 1) {
                        $team->points += (int)$draw_points;
                    }
                }
                return $team;
            });
            $teams = $teams->sortByDesc('points');

            return $teams;
        });
    }
}


