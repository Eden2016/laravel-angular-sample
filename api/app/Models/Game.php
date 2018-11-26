<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    public static $currentGame;

    protected $table = 'games';

    protected $guarded = [
    	'id'
    ];

    protected $fillable = [
    	'name', 
        'slug', 
        'hashtag', 
        'subreddit', 
        'steam_app_id', 
        'hidden'
    ];

    public $timestamps = false;

    public function patches()
    {
    	return $this->hasMany('App\Models\Patch', 'game_id', 'id');
    }
}
