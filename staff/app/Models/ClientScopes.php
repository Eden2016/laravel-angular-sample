<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientScopes extends Model
{
    public $timestamps = true;
    protected $table = 'oauth_client_scopes';
    protected $guarded = [];
    protected $primaryKey = 'id';

    protected $casts = [
        'client_id' => 'string',
        'scope_id' => 'string'
    ];

    protected $fillable = [
        'client_id', 'scope_id'
    ];


    public function client(){
        return $this->belongsTo('\App\Models\ApiClients', 'id', 'client_id');
    }

    public function scope(){
        return $this->belongsTo('\App\Models\OauthScopes', 'id', 'client_id');
    }

}
