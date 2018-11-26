<?php

namespace App;

use Fish\Logger\Logger;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use Logger;
	const TEAM_RADIANT = 0;
	const TEAM_DIRE = 1;
    public $timestamps = false;
    protected $fillable = [
    	'id', 'name'
    ];
}
