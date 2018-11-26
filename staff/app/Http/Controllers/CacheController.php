<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use View;
use Cache;
use App\Jobs\RefreshPlayerStats;

class CacheController extends Controller
{
    public function __construct()
    {

    }

    public function playerCache()
    {
        //Cache::forget('refresh.in_progress');
        $data['refresh'] = Cache::get('refresh.in_progress');
        $data['all'] = Cache::get('players');

        return view('cache/player_stats', $data);
    }

    public function playerCacheGenerate()
    {
        Cache::forever('refresh.in_progress', '1');

        $this->dispatch(new RefreshPlayerStats());

        return redirect()->back();
    }
}