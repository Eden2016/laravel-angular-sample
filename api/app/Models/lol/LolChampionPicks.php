<?php

namespace App\Models;

use App\Individual;
use Illuminate\Database\Eloquent\Model;

class LolChampionPick extends Model
{
    public $timestamps = false;
    protected $table = 'lol_champion_picks';
    protected $fillable = [
        'match_game_id',
        'player_id',
        'champion_id',
    ];

    public function match_game()
    {
        return $this->belongsTo('App\MatchGame', 'id', 'match_game_id');
    }

    public function player()
    {
        return $this->hasOne(Individual::class, 'id', 'player_id');
    }

    public function champion()
    {
        return $this->hasOne('App\Champion', 'id', 'champion_id');
    }
}
