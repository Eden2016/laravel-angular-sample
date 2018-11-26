<?php
namespace App\Http\Controllers;

use App\Services\MainServices;
use App\Services\GameServices;
use App\Services\TeamServices;
use App\Services\TournamentServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use DB;

use App\Models\ToutouMatch;

class TournamentController extends Controller {

    private $_doCache = true;
    private $_cacheTime;

    public function __construct()
    {
        $this->_cacheTime = 60 * 24 * 10; // 10 days
    }

    public function index(Request $request, $game)
    {
        $start      = $request->input('start', false);
        $end        = $request->input('end', false);
        $prize      = $request->input('prize', false);
        $status     = $request->input('status', false);

        $gameId = \App\Services\GameServices::getGameId($game);
        $tournaments = \App\Models\Tournament::with('game');

        if ($gameId)
            $tournaments->where('game_id', $gameId);

        if ($start)
            $tournaments->where('start', '>=', $start);

        if ($end)
            $tournaments->where('end', '>=', $end);

        if ($prize)
            $tournaments->where('prize', '>=', $prize);

        if ($status)
            $tournaments->where('status', $status);

        $tournaments->where('hidden', 0);
        $tournaments->where(function($query) {
            $query->where('start', '>', DB::raw("NOW()"))
                    ->orWhere('end', '>', DB::raw("NOW()"));
        });
        $tournaments->take($request->input('results', 20));
        $tournaments->skip($request->input('skip', 0));
        $sort = explode(',',$request->input('order', 'id,asc'));
        if(!$sort[1]) $sort[1] = 'asc';
        $tournaments->orderBy($sort[0], $sort[1]);

        $tournaments = $tournaments->get();

        foreach ($tournaments as $tournament) {
            $tournament->participating_teams = TeamServices::getTournamentTeams($tournament->id);
            $tournament->notable = $tournament->notable;
            $tournament->prize_distribution = $tournament->prizes;
            $tournament->prize = $tournament->total_prize;
            $tournament->masked_id = MainServices::maskId($tournament->id);
        }

        return response()->json($tournaments);
    }

    public function show($game, $id)
    {
        $id = MainServices::unmaskId($id);

        $tournament = Cache::remember('tournament_' . $id, 60 * 24, function () use ($id) {
            $t = \App\Models\Tournament::with(['event', 'game'])->find($id);
            $t->prizes = $t->prizes;

            $t->team_accounts = collect($t->teams)->map(function ($t) {
                $t->id = MainServices::maskId($t->id);
                return $t;
            });

            $t->prize_distribution = $t->prizes;
            $t->prize = $t->total_prize;
            $t->group = TournamentServices::getStageInfo($t->id);
            return $t;
        });
        if (!$tournament) {
            abort(404);
        }
        $tournament->id = MainServices::maskId($tournament->id);

        return response()->json($tournament);
    }

    public function spotlight(Request $request, $game, $client)
    {
        $clientModel = $client.'Match';

        if (class_exists($clientModel)) {
            $matches = $clientModel::where();
        }
    }

    public function edit($game, $id) {
        abort(501, 'Not Implemented');
    }

    public function update($game, $id) {
        abort(501, 'Not Implemented');
    }

    public function destroy($game, $id) {
        abort(501, 'Not Implemented');
    }

    public function create($game) {
        abort(501, 'Not Implemented');
    }

    public function store($game) {
        abort(501, 'Not Implemented');
    }
}