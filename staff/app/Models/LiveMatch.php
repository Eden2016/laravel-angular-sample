<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LiveMatch extends Model
{
    public $timestamps = false;
    protected $table = 'live_matches';
    protected $fillable = [
    	'match_id', 'league_id', 'radiant', 'dire', 'stage', 'series_type', 'game_number', 'series_id', 'started_at', 'finished_at', 'is_finished'
    ];
    protected $guarded = ['id'];

    public function match() {
    	return $this->hasOne('App\Match', 'match_id', 'match_id');
    }

    public function league() {
    	return $this->hasOne('App\League', 'leagueid', 'league_id');
    }
}
