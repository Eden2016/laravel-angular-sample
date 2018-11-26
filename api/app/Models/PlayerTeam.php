<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayerTeam extends Model
{
    protected $table = 'player_teams';

    protected $guarded = [
    	'id'
    ];

    protected $fillable = [
    	'individual_id', 'team_id', 'start_date', 'end_date', 'is_coach',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public $timestamps = false;

    public function player() {
    	return $this->hasOne('App\Models\Individual', 'id', 'individual_id');
    }

    public function team() {
    	return $this->hasOne('App\Models\TeamAccount', 'id', 'team_id');
    }
}
