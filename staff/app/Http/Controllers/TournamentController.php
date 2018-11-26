<?php
namespace App\Http\Controllers;

use App\Models\TournamentStreams;
use App\Tournament;
use Illuminate\Database\Eloquent\Collection;
use PDO;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use View;
use Validator;
use Input;
use App\Services\CdnServices;
use Illuminate\Support\Facades\Auth;

class TournamentController extends Controller
{
	/**
	 *
	 * @var int
	 */
	private $_currentGame;

	public function __construct() {
        if (request()->currentGameSlug) {
            $this->_currentGame = request()->currentGame->id;
        } else {
            $this->_currentGame = null;
        }
	}

	public function show($tournamentId) {
		$tournamentId = intval($tournamentId);

		$data['tournament'] = \App\Tournament::where('id', $tournamentId)->where('hidden', 0)->first();
		if (null === $data['tournament']) {
			$data['league'] = \App\League::find($tournamentId);

			if (null !== $data['league']) {
				$data['tournament'] = $data['league']->tournament;

				$data['matches'] = $data['league']->matches;
			} else {
				$data['error'] = 'No tournament found with the specified key!';
				\App::Abort(404);
			}
		}

		$data['breadcrumbs'][] = array(
                'name' => 'Events',
            'url' => groute('events'),
                'active' => false
            );

		if (null !== $data['tournament']) {
			$data['breadcrumbs'][] = array(
	                'name' => 'Event',
                'url' => groute('event.view','current', ['eventId' => $data['tournament']->event_id]),
	                'active' => false
	            );

	        $data['stages'] = \App\Stage::where('tournament_id', '=', $data['tournament']->id)->where('hidden', 0)
	        		->with('stageFormats')
	        		->get();
    	}

    	$data['breadcrumbs'][] = array(
	                'name' => 'Tournament',
            'url' => groute('tournament.view', 'current',['tournamentId' => $tournamentId]),
	                'active' => true
	            );

    	$data['tournamentsActiveMenu'] = true;

		return view('tournament/show', $data);
	}

	public function showLeague($leagueId) {
		$leagueId = intval($leagueId);

		$data['league'] = \App\League::where('leagueid', $leagueId)->first();
		if (null !== $data['league']) {
			$data['matches'] = $data['league']->matches;
		} else {
			\App::abort(404);
		}

    	$data['tournamentsActiveMenu'] = true;

		return view('tournament/showleague', $data);
	}

    public function tournamentList() {
    	$now = time();
        $tournaments = \App\Tournament::whereHidden(0)->with('event');
        if ($this->_currentGame !== null) {
            $tournaments->where('game_id', $this->_currentGame);
        }
        $tournaments = $tournaments->get();
        $data['liveTournaments'] = new \Illuminate\Support\Collection();
        $data['completedTournaments'] = new \Illuminate\Support\Collection();
        $data['upcomingTournaments'] = new \Illuminate\Support\Collection();
        foreach($tournaments as $t){
            if(strtotime($t->start) > $now){
                $data['upcomingTournaments']->push($t);
                continue;
            }
            if(strtotime($t->start) < $now && strtotime($t->end) >= $now){
                $data['liveTournaments']->push($t);
                continue;
            }

            if(strtotime($t->end) < $now){
                $data['completedTournaments']->push($t);
            }

        }



    	$data['tournamentsActiveMenu'] = true;
        return view('tournament/all', $data);
    }
    public function tournamentAPIList() {
    	$now = date("Y-m-d H:i:s");

    	$data['leagues'] = \App\League::all();

    	$data['tournamentsActiveMenu'] = true;

        return view('tournament/our-engine-list', $data);
    }

	public function create($eventId) {
		$data['event'] = intval($eventId);
		$data['tournamentsActiveMenu'] = true;
		$data['games'] = \App\Game::all();

		return view('tournament/create', $data);
	}

	public function store(Request $request)
    {
		$validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
	        'start' => 'required|date',
	        'end' => 'required|date|after:start',
	        'event' => 'required|exists:events,id',
	        'prize_distribution' => 'max:255'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

		if (Input::has('id')) {
        	$tournamentId = intval(Input::get('id'));
        	$tournament = \App\Tournament::find($tournamentId);
        } else {
	        $tournament = new \App\Tournament();
	    }

        $startDate = Input::get('start') != "" && Input::get('start') != "0000-00-00 00:00" ? date_convert(Input::get('start'), Auth::user()->timezone, 'UTC', 'Y-m-d H:i', 'Y-m-d H:i:s') : '0000-00-00 00:00:00';
        $endDate = Input::get('end') != "" && Input::get('end') != "0000-00-00 00:00" ? date_convert(Input::get('end'), Auth::user()->timezone, 'UTC', 'Y-m-d H:i', 'Y-m-d H:i:s') : '0000-00-00 00:00:00';

        $tournament->event_id	= intval(Input::get('event'));
        $tournament->game_id	= intval(Input::get('game'));
        if($tournament->game_id==1){
            $tournament->league_id	= intval(Input::get('leagueid'));
        }
        $tournament->name 		= Input::get('name');
        $tournament->location   = Input::get('location');
        $tournament->venue   = $request->get('location', '');
        $tournament->start 		= $startDate;
        $tournament->end 		= $endDate;
        $tournament->season 	= Input::get('season');
        $tournament->prize 		= Input::get('prize');
        $tournament->currency 	= Input::get('currency');

        $tournament->prize_dist_type 	= Input::get('distroType');
        $tournament->description 		= Input::get('description') != '' ? Input::get('description') : '';
        $tournament->url 				= Input::get('url');
        $tournament->hidden 			= Input::get('hidden', 0);
        $tournament->active 			= Input::has('active', 0);
        $tournament->status 			= \App\Tournament::STATUS_UPCOMING;
        $tournament->maps_ids = Input::get('maps', []);
        $tournament->notable_teams = Input::get('notable_teams', []);

        //Initialize s3 driver
        $s3 = \Storage::disk('s3');

        if(request()->has('remove_image')){
            $s3->delete($tournament->logo);
            $tournament->logo = null;
        }
        if (request()->has('remove_toutou_logo')) {
            $s3->delete($tournament->toutou_logo);
            $tournament->toutou_logo = null;
        }
        if(request()->hasFile('file')){
            $logo = $request->file('file');

            $filePath = 'tournament-images/' . $logo->getClientOriginalName();
            $s3->put($filePath, file_get_contents($logo), 'public');

            $tournament->logo = $filePath;
        }
        if (request()->hasFile('toutou_logo')) {
            $toutouLogo = $request->file('toutou_logo');

            $filePath = 'tournament-images/' . $toutouLogo->getClientOriginalName();
            $s3->put($filePath, file_get_contents($toutouLogo), 'public');

            $tournament->toutou_logo = $filePath;
        }

        if(request()->has('streams')){
            TournamentStreams::where('tournaments_id', $tournament->id)->delete();
            foreach(request()->get('streams') as $stream){
                TournamentStreams::insert([
                    'tournaments_id' => $tournament->id,
                    'streams_id' => $stream
                ]);
                if(($key = array_search($stream, (array)$tournament->ignored_streams)) !== false) {
                    $ignored_streams = $tournament->ignored_streams;
                    unset($ignored_streams[$key]);
                    $tournament->ignored_streams = array_unique($ignored_streams);
                }
            }
        }

        if (Input::has('prizeDist')) {
	        $prizeDist = "{ ";
	        foreach (Input::get('prizeDist') as $place => $prize) {
	        	if ($prize > 0 && $prize != "")
	        		$prizeDist .= '"'.($place + 1).'" : "'.$prize.'", ';
	        }
	        $prizeDist = substr($prizeDist, 0, -2);
	        $prizeDist .= " }";
    	} else {
    		$prizeDist = "";
    	}

        $tournament->prize_distribution = $prizeDist;

        $tournament->save();

        return redirect(groute('tournament.view', ['tournamentId' => $tournament->id]));
	}

	public function edit($tournamentId) {
		$tournamentId = intval($tournamentId);
		$data['tournamentsActiveMenu'] = true;
		$data['games'] = \App\Game::all();

		$data['tournament'] = \App\Tournament::find($tournamentId);

		$league = $data['tournament']->league;
		if ($league)
			$data['leagueName'] = $league->name;
		else
			$data['leagueName'] = "";

		if (null === $data['tournament']) {
			$data['errorMessage'] = 'No tournament found with the specified key!';
		}

		$data['prizeDistributions'] = array();
		$prizeDist = $data['tournament']->prize_distribution;

		if (is_array($prizeDist) && count($prizeDist)) {
			foreach ($prizeDist as $prize) {
				$data['prizeDistributions'][] = $prize;
			}
		}

		return view('tournament/edit', $data);
	}

	public function remove($tournamentId) {
		$tournament = \App\Tournament::find($tournamentId);

		if (null !== $tournament) {
			//$tournament->delete();
			$tournament->hidden = 1;
			$tournament->save();

            return redirect(groute('events'));
		}
        return redirect(groute('tournament.view', ['tournamentId' => $tournamentId]));

	}

	public function getLeagueByName($league) {
		$leagues = \App\League::where('name', 'LIKE', '%'.$league.'%')->get();

		if (count($league) > 0) {
			$parsedLeagues = array();
			foreach ($leagues as $league) {
				$parsedLeagues[] = array(
						'name' => $league->name,
						'id' => $league->leagueid
					);
			}

			$return = array(
					"status" => "success",
					"leagues" => $parsedLeagues
				);
		} else {
			$return = array(
					"status" => "error",
					"message" => "No leagues found"
				);
		}

		echo json_encode($return);
	}
}