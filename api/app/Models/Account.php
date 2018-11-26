<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $table = 'users';

    public $primaryKey  = 'account_id';

    protected $fillable = [
    	'account_id', 'personaname', 'steamid', 'avatar', 'profileurl', 'is_personaname_real'
    ];

    public $timestamps = false;

    public function slots() {
        return $this->hasMany('App\Models\Slot', 'account_id');
    }

    public function history(){
    	return $this->hasMany('App\Models\MatchHistory', 'player_id', 'account_id');
    }

    public function getMatchesAttribute(){
        return \App\Models\Match::whereIn('match_id',$this->history->pluck('match_id')->unique()->toArray())->get();
    }


}
