<?php

namespace App;

use Fish\Logger\Logger;
use Illuminate\Database\Eloquent\Model;

class Stage extends Model
{
    use Logger;
    const TYPE_GRID_FORMAT = 0;
    const TYPE_GROUP_FORMAT = 1;

    const STATUS_UPCOMING = 0;
    const STATUS_LIVE = 1;
    const STATUS_CANCELED = 2;
    const STATUS_COMPLETED = 3;

    const CURR_EUR = 0;
    const CURR_USD = 1;
    const CURR_RMB = 2;
    const CURR_SEK = 3;

    const DISTRO_FIXED = 0;
    const DISTRO_PERCENTAGE = 1;

    protected $table = 'stages';

    protected $fillable = [
        'tournament_id',
        'name',
        'format',
        'start',
        'end',
        'status',
        'hidden',
        'active',
        'prize',
        'prize_distribution',
        'currency',
        'prize_dist_type'
    ];

    protected $guarded = [
        'id'
    ];

    /**
     * @return array
     */
    public static function getTypesListed($includeEmpty = false)
    {
        $types = array(
            self::TYPE_GRID_FORMAT => 'Grid Format',
            self::TYPE_GROUP_FORMAT => 'Group Format'
        );

        if ($includeEmpty) {
            return array(null => null) + $types;
        } else {
            return $types;
        }
    }

    public function getLinkAttribute()
    {
        return route('stage', ['tournamentId' => $this->tournament_id, 'stageId' => $this->id]);
    }

    public function tournament()
    {
        return $this->belongsTo('App\Tournament');
    }

    public function stageFormats()
    {
        return $this->hasMany('App\StageFormat');
    }

    public function getPrizesAttribute()
    {
        return json_decode($this->prize_distribution);
    }

    public function getIsDoneAttribute()
    {
        if (!$this->stageFormats->count()) {
            return false;
        }
        $not_resulted_matches = Stage::select(\DB::raw('count(dummy_matches.id) as not_resulted'))
            ->leftJoin('stage_formats', 'stage_formats.stage_id', '=', 'stages.id')
            ->leftJoin('stage_rounds', 'stage_rounds.stage_format_id', '=', 'stage_formats.id')
            ->leftJoin('dummy_matches', 'dummy_matches.round_id', '=', 'stage_rounds.id')
            ->whereNull('dummy_matches.winner')
            ->where('stage_formats.hidden', 0)
            ->where('stage_rounds.hidden', 0)
            ->where('dummy_matches.is_tie', 0)
            ->where('dummy_matches.done', 0)
            ->where('dummy_matches.hidden', 0)
            ->where('stages.id', $this->id)
            ->first()
            ->not_resulted;
        if ($not_resulted_matches > 0) {
            return false;
        }
        return true;
    }

    public function getStageStatusAttribute()
    {
        $start = strtotime($this->start);
        $now = time();
        if ($this->is_done) {
            return 'completed';
        }
        if ($start > $now) {
            return 'upcoming';
        }
        if ($start <= $now && !$this->is_done) {
            return 'live';
        }
        return 'unknown'; // must not happen
    }

    public function getTeamsAttribute()
    {
        try {
            $dummy_matches = $this->stageFormats->pluck('rounds')->flatten()->pluck('dummyMatches')->flatten();
            $teams = $dummy_matches->pluck('opponent1')->unique()->values()->merge(
                $dummy_matches->pluck('opponent2')->unique()->values()
            )->unique();
            return TeamAccount::whereIn('id', $teams)->get();
        } catch (\Exception $e) {
            return [];
        }
    }
}
