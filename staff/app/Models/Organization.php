<?php

namespace App;

use Fish\Logger\Logger;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use Logger;
    protected $table = 'organizations';

    protected $guarded = [
    	'id'
    ];

    protected $fillable = [
    	'name', 'slug_name', 'short_handle', 'created', 'region', 'country', 'ceo', 'manager', 'bio', 'shareholders', 'twitter', 'facebook', 'website', 'instagram', 
    	'youtube', 'vk', 'twitch', 'steam', 'active'
    ];
}
