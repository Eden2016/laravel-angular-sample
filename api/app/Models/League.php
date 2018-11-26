<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class League extends Model
{
    public $primaryKey  = 'leagueid';

    protected $fillable = [
    	'leagueid', 'name', 'description', 'tournament_url', 'itemdef', 'is_finished'
    ];

    public $timestamps = false;

    public function tournament() {
    	return $this->hasOne('App\Models\Tournament', 'league_id', 'leagueid');
    }

    public function matches() {
    	return $this->hasMany('App\Models\LiveMatch', 'league_id', 'leagueid');
    }
}
