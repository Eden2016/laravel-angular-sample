<?php

namespace App\Models;

use Fish\Logger\Logger;
use Illuminate\Database\Eloquent\Model;

class MatchDraft extends Model
{
    use Logger;
    public $timestamps = false;
    protected $table = 'match_drafts';
    protected $fillable = [
            'dummy_match_id',
            'draft'
        ];
    protected $guarded = [
        'id'
    ];
    protected $casts = [
        'draft' => 'array'
    ];

    public function match()
    {
        return $this->belongsTo('\App\DummyMatch');
    }
}
