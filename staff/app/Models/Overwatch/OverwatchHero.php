<?php

namespace App\Models\Overwatch;

use Illuminate\Database\Eloquent\Model;

class OverwatchHero extends Model
{
    const OW_ROLES = [
        1 => "OFFENSE",
        2 => "DEFENSE",
        3 => "TANK",
        4 => "SUPPORT"
    ];

    protected $guarded = ['id'];
    public $timestamps = false;

    public function portraitUrl()
    {
        return \Storage::disk('s3')->url($this->image);
    }
}
