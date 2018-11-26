<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cache;

class Match extends Model
{
    public $primaryKey  = 'match_id';

    protected $table = 'matches';

    protected $fillable = [
    	'match_id', 'season', 'radiant_win', 'duration', 'first_blood_time', 'start_time', 'match_seq_num', 'game_mode',
    	'tower_status_radiant', 'tower_status_dire', 'barracks_status_radiant', 'barracks_status_dire', 'replay_salt',
    	'lobby_type', 'human_players', 'leagueid', 'cluster', 'positive_votes', 'negative_votes', 'radiant_team_id', 
    	'radiant_name', 'radiant_logo', 'radiant_team_complete', 'dire_team_id', 'dire_name', 'dire_logo', 'dire_team_complete'
    ];

    public $timestamps = false;

    public function slots() {
    	return $this->hasMany('App\Models\Slot', 'match_id', 'match_id');
    }

    public function liveMatch() {
        return $this->hasOne('App\Models\LiveMatch', 'match_id', 'match_id');
    }

    public function history(){
        return $this->hasMany('\App\Models\MatchHistory', 'match_id', 'match_id');
    }

    public static function getUnassociatedMatches($limit = 10) {
        $cache = \Cache::get('unassociated_matches');

        if ($cache === null) {
        $matches = \DB::select('SELECT 
            `m`.`match_id`, `m`.`start_time`, `lm`.`radiant`, `lm`.`dire`
            FROM `matches` as `m` 
            LEFT JOIN `match_games` as `mg` 
                ON `m`.`match_id` = `mg`.`match_id` 
            LEFT JOIN `live_matches` as `lm`
                ON `m`.`match_id` = `lm`.`match_id`
            WHERE `mg`.`id` IS NULL ORDER BY RAND() LIMIT '.$limit);

            \Cache::put('unassociated_matches', json_encode($matches), 60);

            return $matches;
        } else {
            return json_decode($cache);
        }
    }
}
