<?php
namespace App\Models\Dota2;

use App\MatchGame;
use App\TeamAccount;
use Fish\Logger\Logger;
use Illuminate\Database\Eloquent\Model;

class Dota2ChampionBan extends Model
{
    use Logger;
    public $timestamps = false;
    protected $table = 'dota2_champion_bans';
    protected $fillable = [
        'match_game_id',
        'team_id',
        'champion_id'
    ];

    public function match_game()
    {
        return $this->belongsTo(MatchGame::class, 'id', 'match_game_id');
    }

    public function team()
    {
        return $this->hasOne(TeamAccount::class, 'id', 'team_id');
    }

    public function champion()
    {
        return $this->hasOne(Champion::class, 'id', 'champion_id');
    }
}
