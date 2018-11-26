<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OddStreams extends Model
{
    public $timestamps = false;
    protected $table = 'odds_streams';
    protected $primaryKey = 'id';

    protected $fillable = [
        'event_id',
        'stream_id',
        'client_id'
    ];

    public function stream()
    {
        return $this->belongsTo('\App\Models\Streams', 'stream_id', 'id');
    }

}
