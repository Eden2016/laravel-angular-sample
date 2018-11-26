<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermissionRole extends Model
{
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'permission_role';
    protected $fillable = [
        'permission_id', 'role_id'
    ];

    public function permission(){
        return $this->belongsTo('\App\Permission', 'id', 'permission_id');
    }
    public function role(){
        return $this->belongsTo('\App\Models\Role', 'id', 'role_id');
    }
}