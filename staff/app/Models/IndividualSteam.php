<?php
namespace App;


use Illuminate\Database\Eloquent\Model;

class IndividualSteam extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'individual_steam_id';
    public $timestamps = false;

    protected $fillable = [
        'individual_id', 'steam_id'
    ];

    public function user(){
        return $this->belongsTo('\App\Individual', 'id', 'individual_id');
    }

}