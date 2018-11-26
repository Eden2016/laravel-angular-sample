<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Maps extends Model
{
    protected $table = 'maps';
    protected $fillable = [
            'game_id',
            'name',
            'image',
            'status',
            'type',
            'description'
    ];

    public function game()
    {
        return $this->hasOne(Game::class, 'id', 'game_id');
    }
}