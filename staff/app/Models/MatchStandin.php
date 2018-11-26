<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatchStandin extends Model
{
    protected $table = 'match_standins';

    protected $fillable = [
            'individual_id',
            'match_game_id',
            'team_id'
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
