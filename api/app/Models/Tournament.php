<?php

namespace App\Models;

use App\Services\MainServices;
use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Support\Collection;

class Tournament extends Model
{
    const STATUS_UPCOMING = 0;
    const STATUS_LIVE = 1;
    const STATUS_CANCELED = 2;
    const STATUS_COMPLETED = 3;

    const CURR_EUR = 0;
    const CURR_USD = 1;
    const CURR_RMB = 2;

    const DISTRO_FIXED = 0;
    const DISTRO_PERCENTAGE = 1;

    protected $table = 'tournaments';

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
        'ignored_streams',
        'logo',
        'maps_ids',
        'toutou_logo'
    ];

    protected $casts = [
        'prize_distribution' => 'array',
        'maps_ids'           => 'array',
        'ignored_streams'    => 'array',
        'notable_teams'      => 'array',
    ];

    protected $guarded = [
    	'id'
    ];

    protected $appends = ['all_streams'];

    public static function listCurrencies() {
        return array(
                self::CURR_EUR => "EUR",
                self::CURR_USD => "USD",
                self::CURR_RMB => "RMB"
            );
    }

    public static function listDistributions() {
        return array(
                self::DISTRO_FIXED => "Fixed Amount",
                self::DISTRO_PERCENTAGE => "Percentage"
            );
    }

    public static function listCountries() {
        $countries = DB::select("SELECT * FROM countries ORDER BY countryName ASC");

        $retData = array();
        foreach ($countries as $country) {
            $retData[$country->id] = $country->countryName;
        }

        return $retData;
    }

    public static function listGames() {
        $games = DB::select("SELECT * FROM games ORDER BY id ASC");

        $retData = array();
        foreach ($games as $game) {
            $retData[$game->id] = $game->name;
        }

        return $retData;
    }

    public static function listRegions() {
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

    public function event()
    {
        return $this->belongsTo('App\Models\Event');
    }

    public function stages()
    {
        return $this->hasMany('App\Models\Stage');
    }

    public function league()
    {
        return $this->hasOne('App\Models\League', 'leagueid', 'league_id');
    }

    public function game()
    {
        return $this->hasOne('App\Models\Game', 'id', 'game_id');
    }

    public function getTotalPrizeAttribute()
    {
        $prize = $this->prize;
        if (!$prize) {
            $stage = Stage::select('stages.*')
                ->where('stages.tournament_id', $this->id)
                ->where('stages.prize', '>', 0)
                ->first();
            try {
                $prize = $stage->prize;
            } catch (\Exception $e) {
                $prize = 0;
            }
        }

        return $prize;
    }

    public function getPrizesAttribute()
    {
        $prizes = $this->prize_distribution;

        if (!is_array($prizes) && $prizes) {
            $prizes = json_decode($prizes, true);
        }

        if (!$prizes) {
            $stage = Stage::select('stages.*')
                ->where('stages.tournament_id', $this->id)
                ->where('stages.prize', '>', 0)
                ->first();
            try {
                $prizes = $stage->prizes;

                if (!is_array($prizes)) {
                    $prizes = json_decode($prizes, true);
                }
            } catch (\Exception $e) {
                $prizes = null;
            }
        }
        if ($this->prize_dist_type == 0) {
            return $prizes;
        }
        if (!$prizes) {
            return [];
        }
        foreach ($prizes as $key => $p) {
            $prizes[$key] = ($p * $this->prize) / 100;
        }
        return $prizes;
    }

    public function getTeamsAttribute()
    {
        return DB::select(DB::raw("SELECT
                        distinct ta.*
                    FROM team_accounts AS ta
                    LEFT JOIN dummy_matches AS dm ON (ta.id = dm.opponent1 OR ta.id = dm.opponent2)
                    LEFT JOIN stage_rounds AS sr ON sr.id = dm.round_id
                    LEFT JOIN stage_formats AS sf ON sf.id = sr.stage_format_id
                    LEFT JOIN stages AS s ON s.id = sf.stage_id
                    LEFT JOIN tournaments AS t ON t.id = s.tournament_id
                    WHERE ta.id NOT IN (34, 35)
                    AND t.id = ".$this->id));
    }

    public function getNotableAttribute()
    {
        return TeamAccount::whereIn('id', $this->notable_teams)->get();
    }

    public function getToutouLogoAttribute($value)
    {
        if ($value === null) {
            return $this->event->toutou_banner;
        } else {
            return $value;
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
        if ($this->event) {
            if ($this->event->streams) {
                $streams->push($this->event->streams->pluck('id')->toArray());
            }
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
}
