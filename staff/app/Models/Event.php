<?php

namespace App;

use Fish\Logger\Logger;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use Logger;
    const STATUS_UPCOMING = 0;
    const STATUS_LIVE = 1;
    const STATUS_PAST = 2;

    protected $table = 'events';

    protected $fillable = [
    		'name',
            'short_handle',
            'first_installment',
            'start',
    		'end',
            'description',
            'toutou_info',
            'hidden',
            'active'
    	];

    protected $guarded = [
    	'id'
    ];

    protected $appends = ['is_done'];

    public function getLinkAttribute()
    {
        return groute('event.view', 'current', ['eventId' => $this->id]);
    }

    public function getEventStatusAttribute()
    {
        $tournaments = $this->tournaments;

        if (count($tournaments)) {
            foreach ($tournaments as $tournament) {
                if ($tournament->tournament_status === \App\Tournament::STATUS_LIVE)
                    return self::STATUS_LIVE;
            }

            foreach ($tournaments as $tournament) {
                if ($tournament->tournament_status === \App\Tournament::STATUS_UPCOMING)
                    return self::STATUS_UPCOMING;
            }
        }

        return self::STATUS_PAST;
    }

    public function getEventStartAttribute()
    {
        $tournaments_start = $this->tournaments->min('start');
        return strtotime($tournaments_start) ? $tournaments_start : $this->start;
    }

    public function getEventEndAttribute()
    {
        $tournaments_end = $this->tournaments->max('end');
        return strtotime($tournaments_end) ? $tournaments_end : $this->end;
    }

    public function getIsDoneAttribute()
    {
        if(!$this->tournaments->count()) return false;
        $not_resulted_matches = Event::select(\DB::raw('count(dummy_matches.id) as not_resulted'))
            ->leftJoin('tournaments', 'tournaments.event_id', '=', 'events.id')
            ->leftJoin('stages', 'stages.tournament_id', '=', 'tournaments.id')
            ->leftJoin('stage_formats', 'stage_formats.stage_id', '=', 'stages.id')
            ->leftJoin('stage_rounds', 'stage_rounds.stage_format_id', '=', 'stage_formats.id')
            ->leftJoin('dummy_matches', 'dummy_matches.round_id', '=', 'stage_rounds.id')
            ->whereNull('dummy_matches.winner')
            ->where('tournaments.hidden', 0)
            ->where('stages.hidden', 0)
            ->where('stage_formats.hidden', 0)
            ->where('stage_rounds.hidden', 0)
            ->where('dummy_matches.is_tie', 0)
            ->where('dummy_matches.done', 0)
            ->where('dummy_matches.hidden', 0)
            ->where('events.id', $this->id)
            ->first()
            ->not_resulted;
        if($not_resulted_matches > 0) return false;
        return true;
    }

    public function tournaments()
    {
        return $this->hasMany('App\Tournament')->where('hidden', 0);
    }

    public function streams()
    {
        return $this->belongsToMany('\App\Models\Streams', 'events_streams', 'events_id', 'streams_id');
    }

    public function getFirstGameSlug()
    {
        $model = new Game();
        $table = $model->getTable();
        $throughModel = new EventGame();
        $pivot = $throughModel->getTable();
        $games = $model::join($pivot, $pivot . '.game_id', '=' ,'games.id')
            ->where($pivot . '.event_id', $this->id)
            ->select($table . '.*')->get();
        foreach($games as $game) {
            return $game->slug;
        }
        return 'current';
    }

}
