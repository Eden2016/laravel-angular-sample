<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatchGame extends Model
{
    const STATUS_UPCOMING   = 0;
    const STATUS_LIVE       = 1;
    const STATUS_FINISHED   = 2;

    protected $table = 'match_games';

    protected $fillable = [
        'dummy_match_id',
        'match_id',
        'opponent1_score',
        'opponent2_score',
        'number',
        'start',
        'status',
        'is_crawled',
        'opponent1_members',
        'opponent2_members',
        'map_id',
        'rounds_data',
        'streams',
        'is_tie',
        'winner'
    ];

    protected $guarded = [
        'id'
    ];

    protected $casts = [
        'opponent1_members' => 'array',
        'opponent2_members' => 'array',
        'rounds_data' => 'array',
        'streams' => 'array',
        'is_tie' => 'boolean'
    ];

    public static function getAssociatedMatches($limit = 10) {
        return \App\MatchGame::where('match_id', '>', 0)
            ->with('match.opponent1_details')
            ->with('match.opponent2_details')
            ->with('match.stageRound.stageFormat.stage.tournament.game')
            ->with('apiMatch')
            ->take($limit)
            ->orderByRaw("RAND()")
            ->get();
    }

    public function match()
    {
        return $this->belongsTo('App\Models\DummyMatch', 'dummy_match_id', 'id');
    }

    public function apiMatch()
    {
        return $this->hasOne('App\Models\Match', 'match_id', 'match_id');
    }

    public function map()
    {
        return $this->hasOne(Maps::class, 'id', 'map_id');
    }

    public function getRoundsAttribute()
    {
        return $this->rounds_data ? $this->rounds_data : [];
    }
}
