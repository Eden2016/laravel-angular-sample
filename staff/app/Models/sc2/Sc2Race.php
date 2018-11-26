<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sc2Race extends Model
{
    protected $table = 'sc2_player_races';

    protected $fillable = [
                'individual_id',
                'race'
    ];

    public $timestamps = false;

    public function player()
    {
        return $this->belongsTo('App\Individual', 'id', 'individual_id');
    }
}
