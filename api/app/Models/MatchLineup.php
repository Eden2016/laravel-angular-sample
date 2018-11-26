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

    public function matchGame()
    {
        return $this->hasOne('\App\Models\MatchGame', 'id', 'match_game_id');
    }

    public function player()
    {
        return $this->hasOne('\App\Models\Individual', 'id', 'individual_id');
    }

    public function team()
    {
        return $this->hasOne('\App\Models\TeamAccount', 'id', 'team_id');
    }
}
