<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiClients extends Model
{
    public $timestamps = true;
    public $incrementing = false;
    protected $table = 'oauth_clients';
    protected $guarded = [];
    protected $primaryKey = 'id';
    protected $casts = [
        'id' => 'string'
    ];


    protected $fillable = [
        'id', 'secret', 'name'
    ];

    protected $appends = ['scopes'];

    public function getScopesAttribute(){
        return ClientScopes::where('client_id', $this->id)->pluck('scope_id');
    }



}
