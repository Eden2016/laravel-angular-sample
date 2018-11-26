<?php
namespace App\Http\Controllers\Api;

use PDO;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use View;
use Validator;
use Input;
use Cache;
use Log;
use Dota2Api\Api;
use Session;

class PlayerController extends Controller
{
	private $_doCache = true;

	public function index($game) {
		$gameId = \App\Services\GameServices::getGameId($game);

		$skip 		= Input::has('skip') ? intval(Input::get('skip')) : 0;
		$results 	= Input::has('results') ? intval(Input::get('results')) : 100;
		$birth 		= Input::has('birth') ? Input::get('birth') : false;

		if ($results > 100)
			$results = 100;

		$players = \App\Individual::where('game_id', $gameId);

		if ($results)
			$players->take($results);

		if ($skip > 0)
			$players->skip($skip);

		if ($date)
			$players->where('date_of_birth', '>=', $birth);

		$players = $players->get();

		if (count($players) > 0) {
			$retData = array(
					"status" => "success",
					"result" => $players
				);
		} else {
			$retData = array(
					"status" => "fail",
					"message" => "No players found"
				);
		}

		return response()->json($retData);
	}

	public function create($game) {

	}

	public function store($game) {

	}

	public function show($game, $id) {
		try {
			$player = \App\Individual::where('id', $id)
						->with('playerTeams')
						->with('country')
						->firstOrFail();

			$retData = array(
					"status" => "success",
					"result" => $player
				);
		} catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
			$retData = array(
					"status" => "fail",
					"message" => "No player found"
				);
		}

		return response()->json($retData);
	}

	public function edit($game, $id) {

	}

	public function update($game, $id) {

	}

	public function destroy($game, $id) {

	}
}