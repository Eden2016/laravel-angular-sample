<?php

namespace App\Models\MatchHistory;

use Fish\Logger\Logger;
use Illuminate\Database\Eloquent\Model;

class MatchHistory extends Model
{
    use Logger;
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
