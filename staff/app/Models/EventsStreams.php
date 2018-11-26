<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventsStreams extends Model
{
    protected $table = 'events_streams';
    public $timestamps = false;

    protected $fillable = [
        'events_id', 'streams_id'
    ];

    protected $primaryKey = false;
    public $incrementing = false;

    public function event(){
        return $this->belongsTo('\App\Models\Events', 'id', 'events_id');
    }

    public function stream(){
        return $this->belongsTo('\App\Models\Streams', 'id', 'streams_id');
    }

}
