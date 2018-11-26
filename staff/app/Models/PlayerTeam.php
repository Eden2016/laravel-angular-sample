<?php

namespace App;

use Fish\Logger\Logger;
use Illuminate\Database\Eloquent\Model;

class PlayerTeam extends Model
{
    use Logger;
    public $timestamps = false;
    protected $table = 'player_teams';
    protected $guarded = [
    	'id'
    ];
    protected $fillable = [
        'individual_id',
        'team_id',
        'start_date',
        'end_date',
        'is_coach',
        'is_sub',
        'is_standin',
        'is_manager'
    ];
    protected $casts = [
        'is_sub' => 'boolean',
        'is_standin' => 'boolean',
        'is_manager' => 'boolean'
    ];

    public function player() {
    	return $this->hasOne('App\Individual', 'id', 'individual_id');
    }

    public function team() {
    	return $this->hasOne('App\TeamAccount', 'id', 'team_id');
    }
}
