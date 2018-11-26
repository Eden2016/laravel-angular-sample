<?php

namespace App;

use Fish\Logger\Logger;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use Logger;
    public $timestamps = false;
    protected $table = 'countries';
    protected $guarded = [
    	'id'
    ];

    protected $fillable = [
    	'countryCode', 'countryName'
    ];
}
