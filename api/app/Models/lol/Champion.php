<?php
namespace App;

use Fish\Logger\Logger;
use Illuminate\Database\Eloquent\Model;

class Champion extends Model
{
    use Logger;
    public $timestamps = false;
    protected $table = 'lol_champions';
    protected $fillable = [
        'name',
        'slug_name',
        'title',
        'api_id',
        'image',
        'info',
        'active',
    ];

    public function getLinkAttribute()
    {
        return route('champion.form', ['id' => $this->id]);
    }
}
