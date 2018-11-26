<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OauthScopes extends Model
{
    public $timestamps = true;
    protected $table = 'oauth_scopes';
    protected $guarded = [];
    protected $primaryKey = 'id';

    protected $casts = [
        'id' => 'string'
    ];


    protected $fillable = [
        'id', 'description'
    ];


}
