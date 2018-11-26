<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
	const TEAM_RADIANT = 0;
	const TEAM_DIRE = 1;

    protected $fillable = [
    	'id', 'name'
    ];

    public $timestamps = false;
}
