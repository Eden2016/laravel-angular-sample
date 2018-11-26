<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Streams extends Model
{
    public $timestamps = true;
    protected $table = 'streams';
    protected $guarded = ['id'];


    protected $fillable = [
        'title',
        'description',
        'lang',
        'link',
        'platform',
        'embed_code',
        'game_id'
    ];

    public function events()
    {
        return $this->belongsToMany(Event::class, 'events_streams', 'streams_id', 'events_id');
    }

    public function game()
    {
        return $this->belongsTo(Game::class, 'game_id', 'id');
    }

    public function getCodeAttribute()
    {
        if ($this->embed_code) {
            return $this->embed_code;
        }
        switch ($this->platform) {
            case 'Twitch.tv':
                return twitch_code($this->link);
                break;
            case 'Douyutv.com':
                return douyutv_code($this->link);
                break;
            case 'Huomaotv.cn':
                return huomaotv_code($this->link);
                break;
            case 'Hitbox':
                return hitbox_code($this->link);
                break;
            case 'MLG':
                return mlg_code($this->link);
                break;
            case 'Youtube':
                return youtube_code($this->link);
                break;
            case 'Azubu':
                return azubu_code($this->link);
                break;
            case 'Youku':
                return youku_code($this->link);
                break;
            case 'ImbaTV':
                return imbatv($this->link);
                break;
            case 'PandaTV':
                return pandatv($this->link);
                break;
            default:
                return ''; //empty if no platform selected
                break;

        }
    }

}
