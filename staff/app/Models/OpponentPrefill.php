<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OpponentPrefill extends Model
{
    public $timestamps = false;
    protected $table = 'opponent_prefill';
    protected $guarded = [
    	'id'
    ];
    protected $fillable = [
        'stage_format_id',
        'opponent_id',
        'team_members'
    ];
    protected $casts = [
        'team_members' => 'array'
    ];

    public static function getOpponents($stageFormatId)
    {
        return \App\OpponentPrefill::where('stage_format_id', $stageFormatId)
            ->with('opponent')
            ->get();
    }

    public function stageFormat() {
    	return $this->hasOne('App\StageFormat');
    }

    public function opponent() {
    	return $this->hasOne('App\TeamAccount', 'id', 'opponent_id');
    }

    public function getMembersAttribute()
    {
        return Individual::whereIn('id', (array)$this->team_members)->get();
    }
}
