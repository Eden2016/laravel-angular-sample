<?php
namespace App\Http\Controllers;

use App\Models\DummyMatch;
use App\Models\Streams;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Services\StreamServices;
use App\Services\GameServices;
use Illuminate\Support\Facades\Input;

class StreamsController extends Controller
{

    private $_doCache = true;
    private $_cacheTime;

    public function __construct()
    {
        $this->_cacheTime = 5;
    }

    public function index(Request $request, $game="dota2")
    {
        if ($game != "all" && $game) {
            $gameId = GameServices::getGameId($game);
            $streams = Streams::where('game_id', $gameId)->get();
        }
        else {
            $streams = Streams::all();
        }

        return response()->json(Streams::all());
    }

    public function groupByMatch(Request $request, $game=false, $client=false)
    {
        if ($this->_doCache && Cache::has(sprintf('api.%s.%s.live_match_streams', $game, $client))){
            return response(Cache::get(sprintf('api.%s.%s.live_match_streams', $game, $client)), 200)
                  ->header('Content-Type', 'application/json');
        }

        $streams = StreamServices::getLiveStreams($game, $client);

        if ($streams) {
            $streams = json_encode($streams);
        }

        if ($this->_doCache)
            Cache::put(sprintf('api.%s.%s.live_match_streams', $game, $client), $streams, $this->_cacheTime);

        return response($streams, 200)
                  ->header('Content-Type', 'application/json');
    }

}