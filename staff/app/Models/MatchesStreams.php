<?php

namespace App\Models;

use App\DummyMatch;
use Illuminate\Database\Eloquent\Model;

class MatchesStreams extends Model
{
    protected $table = 'matches_streams';

    protected $fillable = [
        'matches_id', 'streams_id'
    ];

    public $incrementing = false;
    public $timestamps = false;

    public function tournament() {
        return $this->hasOne(DummyMatch::class, 'id', 'matches_id');
    }

    public function stream(){
        return $this->hasOne(Streams::class, 'id', 'streams_id');
    }
}
