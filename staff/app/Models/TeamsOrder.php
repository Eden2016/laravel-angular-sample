<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TeamsOrder extends Model
{
    public $timestamps = false;
    public $incrementing = false;
    protected $table = 'teams_stage_format_order';
    protected $primaryKey = null;
    protected $fillable = ['stage_formats_id', 'team_accounts_id', 'pos'];

}
