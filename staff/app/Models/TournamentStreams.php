<?php

namespace App\Models;

use App\Tournament;
use Illuminate\Database\Eloquent\Model;

class TournamentStreams extends Model
{
    protected $table = 'tournaments_streams';

    protected $fillable = [
        'tournaments_id', 'streams_id'
    ];

    public $incrementing = false;
    public $timestamps = false;

    public function tournament() {
        return $this->hasOne(Tournament::class, 'id', 'tournaments_id');
    }

    public function stream(){
        return $this->hasOne(Streams::class, 'id', 'streams_id');
    }
}
