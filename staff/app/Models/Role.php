<?php 
namespace App;

use Zizaco\Entrust\EntrustRole;
use App\Models\PermissionRole;
use App\Permission;

class Role extends EntrustRole
{
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'display_name', 'description',
    ];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_role', 'role_id',
            'permission_id');
    }
}