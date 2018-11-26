<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ToutouMatch extends Model
{
    const ODD_TYPE_EURO     = 1;
    const ODD_TYPE_HK       = 2;
    const ODD_TYPE_MALAY    = 3;
    const ODD_TYPE_INDO     = 4;

    protected $table = 'toutou_matches';

    protected $guarded = [
    	'id'
    ];

    protected $fillable = [
        'competition_id',
        'competition_name',
        'competition_no',
        'event_id',
        'parent_event',
        'dummy_match',
        'event_date',
        'odds',
        'new_odds',
        'odds_hk',
        'new_odds_hk',
        'odds_malay',
        'new_odds_malay',
        'odds_indo',
        'new_odds_indo',
        'home_team',
        'away_team',
        'home_score',
        'away_score',
        'in_play',
        'automatic_assigment',
        'active',
        'game_id',
        'game_number'
    ];

    public static function listOddTypes()
    {
        return array(
            self::ODD_TYPE_EURO,
            self::ODD_TYPE_HK,
            self::ODD_TYPE_MALAY,
            self::ODD_TYPE_INDO
        );
    }

    public function getEventDateAttribute($value)
    {
        return Carbon::createFromTimestamp($value/1000, 'Europe/Berlin');
    }

    public function dummyMatch()
    {
    	return $this->hasOne('App\DummyMatch', 'dummy_match', 'id');
    }

    public function homeTeam()
    {
        return $this->hasOne('\App\BetTeam', 'id', 'home_team');
    }

    public function awayTeam()
    {
        return $this->hasOne('\App\BetTeam', 'id', 'away_team');
    }
}
