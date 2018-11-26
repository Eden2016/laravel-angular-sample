<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleUser extends Model
{
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'role_user';

    /**
     * primaryKey
     *
     * @var integer
     * @access protected
     */
    protected $primaryKey = null;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    protected $fillable = [
        'user_id', 'role_id'
    ];

    public function user(){
        return $this->belongsTo('\App\User', 'id', 'user_id');
    }
    public function role(){
        return $this->belongsTo('\App\Models\Role', 'id', 'role_id');
    }
}