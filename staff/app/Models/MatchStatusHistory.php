<?php

namespace App;

use Fish\Logger\Logger;
use Illuminate\Database\Eloquent\Model;

class MatchStatusHistory extends Model
{
    use Logger;
    public $timestamps = false;
    protected $table = 'match_status_history';
    protected $fillable = [
        'match_id',
        'time',
        'duration',
        'tower_status_radiant',
        'tower_status_dire',
    	'barracks_status_radiant', 'barracks_status_dire', 'score_radiant', 'score_dire'
    ];
    protected $guarded = [
    	'id'
    ];
}
