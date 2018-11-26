<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatchLineup extends Model
{
    protected $table = 'match_lineups';

    protected $fillable = [
            'match_game_id',
            'individual_id',
            'team_id',
            'is_standin'
        ];

    protected $guarded = [
        'id'
    ];

    public $timestamps = false;

    public function match()
    {
        return $this->hasOne('\App\MatchGame', 'id', 'match_game_id');
    }

    public function player()
    {
        return $this->hasOne('\App\Individual', 'id', 'individual_id');
    }

    public function team()
    {
        return $this->hasOne('\App\TeamAccount', 'id', 'team_id');
    }
}
