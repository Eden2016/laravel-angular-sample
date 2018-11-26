<?php

namespace App;

use Fish\Logger\Logger;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use Logger;

    private static $_allGames;
    public $timestamps = false;
    protected $table = 'games';
    protected $guarded = [
    	'id'
    ];
    protected $fillable = [
    	'name', 'slug', 'hashtag', 'subreddit', 'steam_app_id', 'hidden'
    ];

    public function patches() {
    	return $this->hasMany('App\Patch', 'game_id', 'id');
    }

    public function maps()
    {
        return $this->hasMany(Maps::class, 'game_id', 'id');
    }

    public static function allCached($id = false)
    {
        if(!static::$_allGames)
            static::$_allGames = static::all();

        if($id) {
            foreach(static::$_allGames as $game) {
                if($game->id == $id) {
                    return $game;
                }
            }
        } else {
            return static::$_allGames;
        }
    }

    public static function allSlugsCached()
    {
        return static::allCached()->pluck('slug')->all();
    }
}
