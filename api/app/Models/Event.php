<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table = 'events';

    protected $fillable = [
    		'name', 'short_handle', 'first_installment', 'start',
    		'end', 'description', 'hidden', 'active', 'logo', 'location', 'organizer', 'toutou_banner', 'toutou_info'
    	];

    protected $guarded = [
    	'id'
    ];

    public function tournaments() {
    	return $this->hasMany('App\Models\Tournament');
    }
}
