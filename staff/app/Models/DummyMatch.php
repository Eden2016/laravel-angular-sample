<?php

namespace App;

use App\Models\Predictions\Prediction;
use App\Models\Streams;
use App\Scopes\GameSelectorScope;
use Fish\Logger\Logger;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class DummyMatch extends Model
{
    use Logger;
    const STATUS_UPCOMING = 0;
    const STATUS_LIVE = 1;
    const STATUS_FINISHED = 2;

    protected $table = 'dummy_matches';

    protected $fillable = [
        'game_id',
        'position',
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
        'opponent1_members',
        'opponent2_members'
    ];

    protected $guarded = [
        'id'
    ];

    protected $casts = [
        'ignored_streams'   => 'array',
        'winner'            => 'integer',
        'opponent1'         => 'integer',
        'opponent2'         => 'integer',
        'opponent1_members' => 'array',
        'opponent2_members' => 'array',
    ];

    protected $appends = ['is_done'];

    public function getLinkAttribute()
    {
        return route('match', ['matchId' => $this->id]);
    }

    public function stageRound() {
        return $this->belongsTo('App\StageRound', 'round_id', 'id')->withoutGlobalScope(GameSelectorScope::class);
    }

    public function matchGames() {
        return $this->hasMany('App\MatchGame', 'dummy_match_id', 'id')->withoutGlobalScope(GameSelectorScope::class);
    }

    public function opponent1_details() {
        return $this->hasOne('App\TeamAccount', 'id', 'opponent1')->withoutGlobalScope(GameSelectorScope::class);
    }

    public function opponent2_details() {
        return $this->hasOne('App\TeamAccount', 'id', 'opponent2')->withoutGlobalScope(GameSelectorScope::class);
    }

    public function getWinner() {
        return $this->hasOne('App\TeamAccount', 'id', 'winner')->withoutGlobalScope(GameSelectorScope::class);
    }

    public function streams(){
        return $this->belongsToMany(Streams::class, 'matches_streams', 'matches_id', 'streams_id');
    }

    public function prediction()
    {
        return $this->hasOne(Prediction::class, 'dummy_match_id', 'id');
    }

    public function game()
    {
        return $this->hasOne(Game::class, 'id', 'game_id');
    }

    public function getIsDoneAttribute(){
        return $this->winner !== null || $this->is_tie==1 || $this->done==1;
    }

    public function getTournamentAttribute(){
        try{
            $tournament_id = $this->select('tournaments.id')
                ->leftJoin('stage_rounds', 'dummy_matches.round_id', '=', 'stage_rounds.id')
                ->leftJoin('stage_formats', 'stage_rounds.stage_format_id', '=', 'stage_formats.id')
                ->leftJoin('stages', 'stage_formats.stage_id', '=', 'stages.id')
                ->leftJoin('tournaments', 'stages.tournament_id', '=', 'tournaments.id')
                ->whereNotNull('tournaments.id')
                ->where('dummy_matches.id', $this->id)
                ->distinct()->pluck('id');

            return Tournament::find($tournament_id);
        }catch (\Exception $e){
            return new \Illuminate\Support\Collection();
        }
    }

    public function getEventAttribute(){
        return $this->tournament->event;
    }

    public function getAllStreamsIdsAttribute(){
        return $this->all_streams->pluck('id')->toArray();
    }

    public function getAllStreamsAttribute(){
        $streams = new Collection();

        /**
         * Connect streams from match tournament event
         */
        if($this->event->streams){
            $streams->push($this->event->streams->pluck('id')->toArray());
        }

        /**
         * Add streams directly connected to match
         */
        if($this->streams){
            $streams->push($this->streams->pluck('id')->toArray());
        }

        /**
         * Flatten streams collection
         */
        $streams = $streams->flatten();


        /**
         * Finally reject streams that are ignored in match
         */
        if($this->ignored_streams){
            $streams = $streams->reject(function($item){
                return in_array($item, $this->ignored_streams);
            });
        }

        return Streams::whereIn('id',$streams->toArray())->get();
    }

    public function getScoresAttribute()
    {
        $scores = new \stdClass();
        $scores->opponent1 = '-';
        $scores->opponent2 = '-';
        if (count($this->matchGames)) {
            $scores->opponent1 = $this->matchGames->where('winner', $this->opponent1)->count();
            $scores->opponent2 = $this->matchGames->where('winner', $this->opponent2)->count();
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

    public function getScoreAttribute()
    {
        $scores = new \stdClass();
        $scores->opp1score = 0;
        $scores->opp2score = 0;

        if ($this->matchGames && count($this->matchGames) > 0) {
            foreach ($this->matchGames as $matchGame) {
                $scores->opp1score += $matchGame->opponent1_score;
                $scores->opp2score += $matchGame->opponent2_score;
            }
        }

        return $scores;
    }

    public function toutou_match()
    {
        return $this->hasOne(ToutouMatch::class, 'dummy_match', 'id');
    }

}
