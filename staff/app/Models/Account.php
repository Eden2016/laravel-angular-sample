<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    public $primaryKey  = 'account_id';
    public $timestamps = false;
    protected $table = 'users';
    protected $fillable = [
    	'account_id', 'personaname', 'steamid', 'avatar', 'profileurl', 'is_personaname_real'
    ];

    public function slots() {
        return $this->hasMany('App\Slot', 'account_id');
    }
}
