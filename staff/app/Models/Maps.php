<?php
namespace App;

use App\Scopes\GameSelectorScope;
use Fish\Logger\Logger;
use Illuminate\Database\Eloquent\Model;

class Maps extends Model
{
    use Logger;

    protected $table = 'maps';
    protected $fillable = ['game_id', 'name', 'image', 'status', 'type', 'description'];

    const OW_MAP_TYPES = [
        'assault' => "Assault",
        'escort' => "Escort",
        'control' => "Control",
        'hybrid' => "Assault/Escort",
        'arena' => "Arena",
        'seasonal' => "Seasonal",
        'unreleased' => "Unreleased"
    ];

    public function getLinkAttribute()
    {
        return groute('maps.form', ['id' => $this->id]);
    }
    public function game()
    {
        return $this->hasOne(Game::class, 'id', 'game_id');
    }

    protected function bootIfNotBooted()
    {
        parent::boot();
        static::addGlobalScope(new GameSelectorScope());
    }

}
