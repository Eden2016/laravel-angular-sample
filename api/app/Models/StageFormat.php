<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StageFormat extends Model
{
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
        'status',
        'elimination_playoffs',
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

    public function stage() {
        return $this->belongsTo('App\Models\Stage');
    }

    public function rounds() {
        return $this->hasMany('App\Models\StageRound');
    }

    /**
     * @return array
     */
    public static function getTypesListed($format = false, $includeEmpty = false)
    {
    	if (!$format) {
	        $types = array(
	            self::TYPE_SINGLE_ELIM  => 'Single Elimination',
	            self::TYPE_DOUBLE_ELIM  => 'Double Elimination',
	            self::TYPE_ROUND_ROBIN  => 'Round Robin',
	            self::TYPE_SWISS_FORMAT => 'Swiss Format',
	            self::TYPE_GSL_FORMAT   => 'GSL Format'
	        );
    	} else if ($format === \App\Models\Stage::TYPE_GRID_FORMAT) {
    		$types = array(
	            self::TYPE_SINGLE_ELIM  => 'Single Elimination',
	            self::TYPE_DOUBLE_ELIM  => 'Double Elimination',
	            self::TYPE_SWISS_FORMAT => 'Swiss Format'
	        );
    	} else if ($format === \App\Models\Stage::TYPE_GROUP_FORMAT) {
    		$types = array(
	            self::TYPE_ROUND_ROBIN  => 'Round Robin',
	            self::TYPE_GSL_FORMAT   => 'GSL Format'
	        );
    	} else {
            $types = array(
                self::TYPE_SINGLE_ELIM  => 'Single Elimination',
                self::TYPE_DOUBLE_ELIM  => 'Double Elimination',
                self::TYPE_ROUND_ROBIN  => 'Round Robin',
                self::TYPE_SWISS_FORMAT => 'Swiss Format',
                self::TYPE_GSL_FORMAT   => 'GSL Format'
            );
        }

        if ($includeEmpty) {
            return array(null => null) + $types;
        } else {
            return $types;
        }
    }
}
