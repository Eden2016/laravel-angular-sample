<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patch extends Model
{
    protected $table = 'patches';

    protected $guarded = [
    	'id'
    ];

    protected $fillable = [
    	'game_id', 'name', 'date', 'hidden'
    ];

    public $timestamps = false;

    public function game() {
    	return $this->belongsTo('App\Game');
    }
}
