<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiveMatch extends Model
{
    protected $table = 'live_matches';

    protected $fillable = [
    	'match_id', 'league_id', 'radiant', 'dire', 'stage', 'series_type', 'game_number', 'series_id', 'started_at', 'finished_at', 'is_finished'
    ];

    protected $guarded = ['id'];

    public $timestamps = false;

    public function match() {
    	return $this->hasOne('App\Models\Match', 'match_id', 'match_id');
    }

    public function league() {
    	return $this->hasOne('App\Models\League', 'leagueid', 'league_id');
    }
}
