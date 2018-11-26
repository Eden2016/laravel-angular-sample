<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Slot extends Model
{
    const RADIANT_ARRAY = array(0, 1, 2, 3, 4);
    const DIRE_ARRAY = array(128, 129, 130, 131, 132);

    const TEAM_RADIANT = 0;
    const TEAM_DIRE = 1;
    public $timestamps = false;
    protected $table = 'slots';
    protected $fillable = [
    	'match_id', 'account_id', 'hero_id', 'player_slot', 'item_0', 'item_1', 'item_2', 'item_3', 'item_4', 'item_5',
    	'kills', 'deaths', 'assists', 'leaver_status', 'gold', 'last_hits', 'denies', 'gold_per_min', 'xp_per_min',
    	'gold_spent', 'hero_damage', 'tower_damage', 'hero_healing', 'level'
    ];
    protected $guarded = [
    	'id'
    ];
    protected $appends = ['hero'];

    public function match() {
        return $this->hasOne('App\Models\Match', 'match_id', 'match_id');
    }

    public function player() {
    	return $this->hasOne('App\Models\Account', 'account_id', 'account_id');
    }

    public function getHeroAttribute()
    {
        $game = get_game();
        if ($game == 'lol') {
            return DB::table('lol_champions')->where('api_id', $this->hero_id)->first();
        } elseif ($game == 'dota2') {
            return DB::table('dota2_heroes')->where('api_id', $this->hero_id)->first();
        }
        return $game;
    }
}
