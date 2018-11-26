<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatchHistory extends Model
{
    protected $table = 'match_history';

    protected $fillable = [
    	'match_id', 'player_id', 'time', 'duration',
    	'kills', 'deaths', 'assists', 'level', 'gold',
    	'gold_per_minute', 'xp_per_minute', 'denies',
    	'item0', 'item1', 'item2', 'item3', 'item4', 'item5',
    	'pos_x', 'pos_y', 'net_worth'
    ];

    protected $guarded = [
    	'id'
    ];
}
