<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LolChampionBan extends Model
{
    public $timestamps = false;
    protected $table = 'lol_champion_bans';
    protected $fillable = [
        'match_game_id',
        'team_id',
        'champion_id',
    ];

    public function match_game()
    {
        return $this->belongsTo('App\MatchGame', 'id', 'match_game_id');
    }

    public function team()
    {
        return $this->hasOne('App\TeamAccount', 'id', 'team_id');
    }

    public function champion()
    {
        return $this->hasOne('App\Champion', 'id', 'champion_id');
    }
}
