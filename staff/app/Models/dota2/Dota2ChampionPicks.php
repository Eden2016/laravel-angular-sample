<?php
namespace App\Models\Dota2;

use App\Individual;
use App\MatchGame;
use Illuminate\Database\Eloquent\Model;

class Dota2ChampionPick extends Model
{
    public $timestamps = false;
    protected $table = 'dota2_champion_picks';
    protected $fillable = [
        'match_game_id',
        'player_id',
        'champion_id'
    ];

    public function match_game()
    {
        return $this->belongsTo(MatchGame::class, 'id', 'match_game_id');
    }

    public function player()
    {
        return $this->hasOne(Individual::class, 'id', 'player_id');
    }

    public function champion()
    {
        return $this->hasOne(Champion::class, 'id', 'champion_id');
    }
}
