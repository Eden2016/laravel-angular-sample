<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class League extends Model
{
    public $primaryKey  = 'leagueid';
    public $timestamps = false;
    protected $fillable = [
    	'leagueid', 'name', 'description', 'tournament_url', 'itemdef', 'is_finished'
    ];

    public function getLinkAttribute()
    {
        return route('league', ['leagueId' => $this->id]);
    }
    public function tournament() {
    	return $this->hasOne('App\Tournament', 'league_id', 'leagueid');
    }

    public function matches() {
    	return $this->hasMany('App\LiveMatch', 'league_id', 'leagueid');
    }
}
