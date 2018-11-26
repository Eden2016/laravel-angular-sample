<?php

namespace App\Models;

use App\ToutouMatch;
use Illuminate\Database\Eloquent\Model;

class DummyMatch extends Model
{
    const STATUS_UPCOMING = 0;
    const STATUS_LIVE = 1;
    const STATUS_FINISHED = 2;

    protected $table = 'dummy_matches';

    protected $fillable = [
    	'game_id',
        'round_id',
        'opponent1',
        'opponent2',
        'winner',
        'start',
        'status',
        'is_tie',
        'is_forfeited',
        'done',
        'hidden',
        'ignored_streams',
        'disqualified_team',
        'map_id',
        'opponent1_members',
        'opponent2_members'
    ];

    protected $guarded = [
        'id'
    ];

    protected $appends = [
        'scores'
    ];

    public function game()
    {
        return $this->hasOne(Game::class, 'id', 'game_id');
    }

    public function stageRound()
    {
        return $this->belongsTo('App\Models\StageRound', 'round_id', 'id');
    }

    public function matchGames()
    {
        return $this->hasMany('App\Models\MatchGame', 'dummy_match_id', 'id');
    }

    public function opponent1_details()
    {
        return $this->hasOne('App\Models\TeamAccount', 'id', 'opponent1');
    }

    public function opponent2_details()
    {
        return $this->hasOne('App\Models\TeamAccount', 'id', 'opponent2');
    }

    public function getWinner()
    {
        return $this->hasOne('App\Models\TeamAccount', 'id', 'winner');
    }

    public function toutou_match()
    {
        return $this->hasOne(ToutouMatch::class, 'dummy_match', 'id');
    }

    public function getScoresAttribute()
    {
        $scores = new \stdClass();
        if (count($this->matchGames)) {
            $opp1 = $this->opponent1;
            $opp2 = $this->opponent2;
            $scores->opponent1 = $this->matchGames->filter(function($mg, $key) use ($opp1) {
                return $mg->winner == $opp1 || $mg->opponent1_score > 0;
            })->count();
            $scores->opponent2 = $this->matchGames->filter(function($mg, $key) use ($opp2) {
                return $mg->winner == $opp2 || $mg->opponent2_score > 0;
            })->count();
        } else {
            if ($this->winner == $this->opponent1) {
                $scores->opponent1 = 1;
                $scores->opponent2 = 0;
            } elseif ($this->winner == $this->opponent2) {
                $scores->opponent1 = 0;
                $scores->opponent2 = 1;
            } elseif ($this->is_tie == 1) {
                $scores->opponent1 = 1;
                $scores->opponent2 = 1;
            }
        }
        return $scores;
    }

    public function getBestMatchGameAttribute()
    {
        if (!count($this->matchGames)) {
            return null;
        }
        $best_match_game = $this->matchGames->first();
        $team_lookup = null;
        if ($this->winner == $this->opponent1) {
            $team_lookup = 'team1_score';
        } elseif ($this->winner == $this->opponent2) {
            $team_lookup = 'team2_score';
        }
        if (is_null($team_lookup)) {
            return null;
        }

        foreach ($this->matchGames as $matchGame) {

            if (!count($matchGame->rounds)) {
                continue;
            }
            $best_round = collect($matchGame->rounds)->sortByDesc($team_lookup)->first();
            if (collect($best_match_game->rounds)->sortByDesc($team_lookup)->first()[$team_lookup] < $best_round[$team_lookup]) {
                $best_match_game = $matchGame;
            }
        }
        return $best_match_game;
    }

    public function getTournamentAttribute()
    {
        $tournament_id = $this->stageRound->stageFormat->stage->tournament_id;
        unset($this->stageRound);
        try {
            return Tournament::find($tournament_id);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getEventAttribute()
    {
        return $this->tournament->event;
    }

    public function streams()
    {
        return $this->belongsToMany(Streams::class, 'matches_streams', 'matches_id', 'streams_id');
    }

    public function getAllStreamsIdsAttribute()
    {
        return $this->all_streams->pluck('id')->toArray();
    }

    public function getAllStreamsAttribute()
    {
        $streams = new \Illuminate\Support\Collection();

        /**
         * Connect streams from match tournament event
         */
        if ($this->event->streams) {
            $streams->push($this->event->streams->pluck('id')->toArray());
        }

        /**
         * Add streams directly connected to match
         */
        if ($this->streams) {
            $streams->push($this->streams->pluck('id')->toArray());
        }

        /**
         * Flatten streams collection
         */
        $streams = $streams->flatten();


        /**
         * Finally reject streams that are ignored in match
         */
        if ($this->ignored_streams) {
            $streams = $streams->reject(function ($item) {
                return in_array($item, $this->ignored_streams);
            });
        }

        return Streams::whereIn('id', $streams->toArray())->get();
    }
}
