<?php

namespace App\Models\Predictions;

use Illuminate\Database\Eloquent\Model;

class Prediction extends Model
{
    public $timestamps = false;
    protected $table = 'predictions';
    protected $fillable = [
        'dummy_match_id',
        'home_team_id',
        'away_team_id',
        'model_id',
        'confidence_interval',
        'win_prob_home_team',
        'answered'
    ];

    protected $casts = [
        'win_prob_home_team' => 'double',
    ];
}
