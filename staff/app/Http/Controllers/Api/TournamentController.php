<?php
namespace App\Http\Controllers\Api;

use App\Models\TournamentStreams;
use App\Tournament;
use PDO;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Response;
use Cache;
use Log;
use Session;
use Input;

class TournamentController extends Controller
{
	/**
	 *
	 * @var bool
	 *
	 */
	private $_doCache = true;

	/**
	 *
	 * @var int
	 *
	 */
	private $_cacheTime;

	public function __construct() {

		$this->_cacheTime = 60 * 24 * 10; // 10 days
	}

	/**
	 * Shows list of tournaments in JSON format
	 *
	 * @param string $game
	 * @return string
	 */
	public function index($game) {
		$start 		= Input::has('start') ? Input::get('start') : false;
		$end 		= Input::has('end') ? Input::get('end') : false;
		$prize 		= Input::has('prize') ? Input::get('prize') : false;
		$status 	= Input::has('status') ? Input::get('status') : false;

		$gameId = \App\Services\GameServices::getGameId($game);
		$tournaments = \App\Tournament::where('game_id', $gameId);

		if ($start)
			$tournaments->where('start', '>=', $start);

		if ($end)
			$tournaments->where('end', '>=', $end);

		if ($prize)
			$tournaments->where('prize', '>=', $prize);

		if ($status)
			$tournaments->where('status', $status);


		$tournaments = $tournaments->get();
		return response()->json($tournaments);
	}

	public function create($game) {

	}

	public function store($game) {

	}

	/**
	 * Shows tournament details
	 *
	 * @param string $game
	 * @param int $id
	 * @return string
	 */
	public function show($game, $id) {

		if ($this->_doCache)
			$cache = Cache::get(sprintf('api_tournament_show_%d', $id));
		else
			$cache = null;

		if (null === $cache) {
			$tournament = \App\Tournament::find($id);

			$retData = array();
			if ($tournament) {
				$retData['tournament'] = $tournament->toArray();

				$stages = $tournament->stages;
				if ($stages) {
					foreach ($stages as $stage) {
						$retData['tournament']['stages'][$stage->id] = $stage->toArray();

						$stageFormats = $stage->stageFormats;
						if ($stageFormats) {
							foreach ($stageFormats as $stageFormat) {
								$retData['tournament']['stages'][$stage->id]['stage_formats'][$stageFormat->id] = $stageFormat->toArray();

								$rounds = $stageFormat->rounds;
								if ($rounds) {
									foreach ($rounds as $round) {
										$retData['tournament']['stages'][$stage->id]['stage_formats'][$stageFormat->id]['rounds'][$round->number] = $round->toArray();

										$matches = $round->dummyMatches;
										if ($matches) {
											$retData['tournament']['stages'][$stage->id]['stage_formats'][$stageFormat->id]['rounds'][$round->number]['matches'] = $matches->toArray();
										}
									}
								}
							}
						}
					}
				}
			}

			$retData = json_encode($retData);
			if ($this->_doCache)
					Cache::put(sprintf('api_tournament_show_%d', $id), $retData, $this->_cacheTime);
		} else {
			$retData = $cache;
		}

		return response()->json(json_decode($retData));
	}

    public function ignoreStream(Request $request){
        $stream_id = $request->get('id');
        $tournament_id = $request->get('tournament');
        $tournament = Tournament::find($tournament_id);
        if(!$tournament) {
            return response()->json([
                "success" => false,
                "message" => 'Tournament not found'
            ], 402);
        }

        /**
         * Don't ask why. Some PHP bug disallow $stream_id to be added directly to $tournament->ignored_streams
         */
        $ignored_streams = (array)$tournament->ignored_streams;
        $ignored_streams[] = $stream_id;
        try {

            $tournament->ignored_streams = array_unique($ignored_streams);
            $tournament->save();
            TournamentStreams::where('tournaments_id', $tournament->id)->where('streams_id', $stream_id)->delete();
        }catch(\Exception $e){
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ], 500);
        }



        return response()->json($tournament, 200);
    }

	public function edit($game, $id) {

	}

	public function update($game, $id) {

	}

	public function destroy($game, $id) {

	}
}