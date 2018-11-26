<?php

namespace App;

use Fish\Logger\Logger;
use Illuminate\Database\Eloquent\Model;

class StageFormat extends Model
{
    use Logger;
    const TYPE_SINGLE_ELIM	= 0;
	const TYPE_DOUBLE_ELIM	= 1;
    const TYPE_ROUND_ROBIN  = 2;
	const TYPE_SWISS_FORMAT	= 3;
	const TYPE_GSL_FORMAT	= 4;

    protected $table = 'stage_formats';

    protected $fillable = [
    	'stage_id',
        'name',
        'type',
        'start',
        'end',
        'number',
        'games_number',
        'hidden',
        'active',
        'points_per_win',
        'points_per_draw',
        'points_distribution',
        'lead_from_winner_bracket'
    ];

    protected $guarded = [
    	'id'
    ];

    protected $casts = [
        'points_per_win' => 'integer',
        'points_per_draw' => 'integer',
        'lead_from_winner_bracket' => 'boolean',
    ];
    protected $appends = ['is_done'];

    /**
     * @return array
     */
    public static function getTypesListed($format = false, $includeEmpty = false)
    {
        if (!$format) {
            $types = array(
                self::TYPE_SINGLE_ELIM => 'Single Elimination',
                self::TYPE_DOUBLE_ELIM => 'Double Elimination',
                self::TYPE_ROUND_ROBIN => 'Round Robin',
                self::TYPE_SWISS_FORMAT => 'Swiss Format',
                self::TYPE_GSL_FORMAT => 'GSL Format'
            );
        } else {
            if ($format === \App\Stage::TYPE_GRID_FORMAT) {
                $types = array(
                    self::TYPE_SINGLE_ELIM => 'Single Elimination',
                    self::TYPE_DOUBLE_ELIM => 'Double Elimination',
                    self::TYPE_SWISS_FORMAT => 'Swiss Format'
                );
            } else {
                if ($format === \App\Stage::TYPE_GROUP_FORMAT) {
                    $types = array(
                        self::TYPE_ROUND_ROBIN => 'Round Robin',
                        self::TYPE_GSL_FORMAT => 'GSL Format'
                    );
                } else {
                    $types = array(
                        self::TYPE_SINGLE_ELIM => 'Single Elimination',
                        self::TYPE_DOUBLE_ELIM => 'Double Elimination',
                        self::TYPE_ROUND_ROBIN => 'Round Robin',
                        self::TYPE_SWISS_FORMAT => 'Swiss Format',
                        self::TYPE_GSL_FORMAT => 'GSL Format'
                    );
                }
            }
        }

        if ($includeEmpty) {
            return array(null => null) + $types;
        } else {
            return $types;
        }
    }

    public function getLinkAttribute()
    {
        return route('stage_format',
            ['tournamentId' => $this->stage->tournament_id, 'stageId' => $this->stage_id, 'sfId' => $this->id]);
    }

    public function stage() {
        return $this->belongsTo('App\Stage');
    }

    public function rounds() {
        return $this->hasMany('App\StageRound');
    }

    /**
     * get all matches for connected rounds
     * if there is undone matches return false, else return true
     * @return bool
     */
    public function getIsDoneAttribute(){
        if(!$this->rounds->count()) return false;
        $not_resulted_matches = StageFormat::select(\DB::raw('count(dummy_matches.id) as not_resulted'))
            ->leftJoin('stage_rounds', 'stage_rounds.stage_format_id', '=', 'stage_formats.id')
            ->leftJoin('dummy_matches', 'dummy_matches.round_id', '=', 'stage_rounds.id')
            ->whereNull('dummy_matches.winner')
            ->where('stage_rounds.hidden', 0)
            ->where('dummy_matches.is_tie', 0)
            ->where('dummy_matches.done', 0)
            ->where('dummy_matches.hidden', 0)
            ->where('stage_formats.id', $this->id)
            ->first()
            ->not_resulted;
        if($not_resulted_matches > 0) return false;
        return true;
    }

    public function getFormatStatusAttribute(){
        $start = strtotime($this->start);
        $now = time();
        if($this->is_done) return 'completed';
        if($start > $now) return 'upcoming';
        if($start <= $now && !$this->is_done ) return 'live';

        return 'unknown'; // must not happen
    }

    public function getTeamsAttribute(){
        try{
            $dummy_matches = $this->rounds->pluck('dummyMatches')->flatten();
            $teams = $dummy_matches->pluck('opponent1')->unique()->values()->merge(
                $dummy_matches->pluck('opponent2')->unique()->values()
            )->unique();
            return TeamAccount::whereIn('id', $teams)->get();
        }catch(\Exception $e){
            return [];
        }
    }
}
