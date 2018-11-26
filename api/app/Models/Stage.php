<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stage extends Model
{
    const TYPE_GRID_FORMAT = 0;
    const TYPE_GROUP_FORMAT = 1;

    const STATUS_UPCOMING = 0;
    const STATUS_LIVE = 1;
    const STATUS_CANCELED = 2;
    const STATUS_COMPLETED = 3;

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
        'prize_dist_type',
    ];

    protected $guarded = [
        'id',
    ];

    /**
     * @return array
     */
    public static function getTypesListed($includeEmpty = false)
    {
        $types = [
            self::TYPE_GRID_FORMAT  => 'Grid Format',
            self::TYPE_GROUP_FORMAT => 'Group Format',
        ];

        if ($includeEmpty) {
            return [null => null] + $types;
        } else {
            return $types;
        }
    }

    public function tournament()
    {
        return $this->belongsTo('App\Models\Tournament');
    }

    public function stageFormats()
    {
        return $this->hasMany('App\Models\StageFormat');
    }

    public function getPrizesAttribute()
    {
        return json_decode($this->prize_distribution);
    }
}
