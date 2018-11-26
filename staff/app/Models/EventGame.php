<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventGame extends Model
{
    protected $table = 'event_games';
    public $timestamps = false;

    protected $guarded = [
        'id'
    ];

}
