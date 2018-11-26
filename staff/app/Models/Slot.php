<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
    protected $with = ['match', 'player'];

    public function match() {
        return $this->hasOne('App\Match', 'match_id', 'match_id');
    }

    public function player() {
    	return $this->hasOne('App\Account', 'account_id', 'account_id');
    }
}
