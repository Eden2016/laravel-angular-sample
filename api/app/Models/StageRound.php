<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StageRound extends Model
{
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

    protected $guarded = [
    	'id'
    ];

    public function stageFormat() {
        return $this->belongsTo('App\Models\StageFormat');
    }

    public function dummyMatches() {
        return $this->hasMany('App\Models\DummyMatch', 'round_id', 'id');
    }

    /**
     * @return array
     */
    public static function getTypesListed($includeEmpty = false)
    {
        $types = array(
            self::ROUND_TYPE_GROUP                  => 'Group',
            self::ROUND_TYPE_UPPER_BRACKET          => 'Upper Bracket',
            self::ROUND_TYPE_LOWER_BRACKET          => 'Lower Bracket',
            self::ROUND_TYPE_FINAL                  => 'Final',
            self::ROUND_TYPE_THIRD_PLACE_PLAYOFF    => 'Third Place Playoff',
            self::ROUND_TYPE_DBL_ELIM_GROUP         => 'Double Elimination Group'
        );

        if ($includeEmpty) {
            return array(null => null) + $types;
        } else {
            return $types;
        }
    }
}
