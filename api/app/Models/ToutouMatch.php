<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
        'competiton_name',
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

    protected $casts = [
        'odds' => 'array',
        'new_odds' => 'array',
        'odds_hk' => 'array',
        'new_odds_hk' => 'array',
        'odds_malay' => 'array',
        'new_odds_malay' => 'array',
        'odds_indo' => 'array',
        'new_odds_indo' => 'array'
    ];

    public function dummyMatch() {
        return $this->hasOne('App\Models\DummyMatch', 'id', 'dummy_match');
    }

    public function setDummyMatch($value)
    {
        $this->relations['dummyMatch'] = $value;
    }

    public function homeTeam()
    {
        return $this->hasOne('\App\BetTeam', 'id', 'home_team');
    }

    public function awayTeam()
    {
        return $this->hasOne('\App\BetTeam', 'id', 'away_team');
    }

    public function streams()
    {
        return $this->belongsToMany('App\Models\Streams', 'odds_streams', 'event_id', 'stream_id')->where('client_id', 1);
    }

    public function setOddsAttribute($value)
    {
        $this->attributes['odds'] = $value;
    }

    public function setNewOddsAttribute($value)
    {
        $this->attributes['new_odds'] = $value;
    }

    public function setOddsHkAttribute($value)
    {
        $this->attributes['odds_hk'] = $value;
    }

    public function setNewOddsHkAttribute($value)
    {
        $this->attributes['new_odds_hk'] = $value;
    }

    public function setOddsIndoAttribute($value)
    {
        $this->attributes['odds_indo'] = $value;
    }

    public function setNewOddsIndoAttribute($value)
    {
        $this->attributes['new_odds_indo'] = $value;
    }

    public function setOddsMalayAttribute($value)
    {
        $this->attributes['odds_malay'] = $value;
    }

    public function setNewOddsMalayAttribute($value)
    {
        $this->attributes['new_odds_malay'] = $value;
    }

    public function listOddTypes()
    {
        return array(
                self::ODD_TYPE_EURO,
                self::ODD_TYPE_HK,
                self::ODD_TYPE_MALAY,
                self::ODD_TYPE_INDO
            );
    }
}
