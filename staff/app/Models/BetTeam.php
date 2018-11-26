<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BetTeam extends Model
{
    public $timestamps = false;
    protected $table = '188bet_teams';
    protected $guarded = [
    	'id'
    ];
    protected $fillable = [
    	'team_id',
        'team_name',
        'game_id'
    ];

    public function teamAccount() {
    	return $this->hasOne('App\TeamAccount', 'team_id', 'id');
    }
}
