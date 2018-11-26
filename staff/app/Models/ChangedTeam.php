<?php
namespace App\Models;

use Fish\Logger\Logger;
use Illuminate\Database\Eloquent\Model;

class ChangedTeam extends Model
{
    use Logger;
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'changed_teams';
    protected $guarded = [
        'id'
    ];

    protected $fillable = [
        'original_team_id', 'substitute_team_id', 'stage_format_id', 'match_id', 'whole_sf', 'added_at'
    ];
}