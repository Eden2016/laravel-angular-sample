<?php

namespace App;

use Fish\Logger\Logger;
use Illuminate\Database\Eloquent\Model;

class StageRound extends Model
{
    use Logger;
    const ROUND_TYPE_GROUP = 0;
    const ROUND_TYPE_UPPER_BRACKET = 1;
    const ROUND_TYPE_LOWER_BRACKET = 2;
    const ROUND_TYPE_FINAL = 3;
    const ROUND_TYPE_THIRD_PLACE_PLAYOFF = 4;
    const ROUND_TYPE_DBL_ELIM_GROUP = 5;

    protected $table = 'stage_rounds';

    protected $fillable = [
    	'stage_format_id', 'type', 'number', 'hidden', 'active'
    ];

    protected $appends = ['is_done'];

    protected $guarded = [
    	'id'
    ];

    /**
     * @return array
     */
    public static function getTypesListed($includeEmpty = false)
    {
        $types = array(
            self::ROUND_TYPE_GROUP => 'Group',
            self::ROUND_TYPE_UPPER_BRACKET => 'Upper Bracket',
            self::ROUND_TYPE_LOWER_BRACKET => 'Lower Bracket',
            self::ROUND_TYPE_FINAL => 'Final',
            self::ROUND_TYPE_THIRD_PLACE_PLAYOFF => 'Third Place Playoff',
            self::ROUND_TYPE_DBL_ELIM_GROUP => 'Double Elimination Group'
        );

        if ($includeEmpty) {
            return array(null => null) + $types;
        } else {
            return $types;
        }
    }

    public function stageFormat() {
        return $this->belongsTo('App\StageFormat');
    }

    public function dummyMatches() {
        return $this->hasMany('App\DummyMatch', 'round_id', 'id');
    }

    /**
     * count matches marked as done,
     * return true if no active matches,
     * return false if some matches done==0
     * @return bool
     */
    public function getIsDoneAttribute(){
        if(!$this->dummyMatches->count()) return false;
        $not_resulted_matches = StageRound::select(\DB::raw('count(dummy_matches.id) as not_resulted'))
            ->leftJoin('dummy_matches', 'dummy_matches.round_id', '=', 'stage_rounds.id')
            ->whereNull('dummy_matches.winner')
            ->where('dummy_matches.is_tie', 0)
            ->where('dummy_matches.done', 0)
            ->where('dummy_matches.hidden', 0)
            ->where('stage_rounds.id', $this->id)
            ->first()
            ->not_resulted;
        if($not_resulted_matches > 0) return false;
        return true;
    }

    public function getTeamsAttribute(){
        try{
            $dummy_matches = $this->dummyMatches;
            $teams = $dummy_matches->pluck('opponent1')->unique()->values()->merge(
                $dummy_matches->pluck('opponent2')->unique()->values()
            )->unique();
            return TeamAccount::whereIn('id', $teams)->get();
        }catch(\Exception $e){
            return [];
        }
    }
}
