<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Patch extends Model
{
    public $timestamps = false;
    protected $table = 'patches';
    protected $guarded = [
    	'id'
    ];
    protected $fillable = [
    	'game_id', 'name', 'date', 'hidden'
    ];

    public function game() {
    	return $this->belongsTo('App\Game');
    }
}
