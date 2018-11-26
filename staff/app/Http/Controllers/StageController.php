<?php
namespace App\Http\Controllers;

use PDO;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use View;
use Validator;
use Input;
use Illuminate\Support\Facades\Auth;

class StageController extends Controller
{
	public function show($tournamentId, $stageId) {
		$stageId = intval($stageId);

		$data['stage'] = \App\Stage::where('id', $stageId)->where('hidden', 0)->first();

		if (null === $data['stage']) {
			$data['error'] = 'No stages found with the specified key!';
		}

		$data['tournament'] = \App\Tournament::find($data['stage']->tournament_id);

		$data['stageFormats'] = \App\StageFormat::where('stage_id', '=', $stageId)->where('hidden', 0)->get();

		$data['breadcrumbs'][] = array(
            'name' => $data['tournament']->event->name,
            'url' => groute('event.view', 'current', ['eventId' => $data['tournament']->event_id]),
                'active' => false
            );
        $data['breadcrumbs'][] = array(
                'name' => $data['tournament']->name,
            'url' => route('tournament.view', ['tournamentId' => $data['tournament']->id]),
                'active' => false
            );
        $data['breadcrumbs'][] = array(
            'name' => $data['stage']->name,
            'url' => route('stage', ['tournamentId' => $tournamentId, 'stageId' => $stageId]),
                'active' => true
            );

        $data['tournamentsActiveMenu'] = true;
		return view('stage/show', $data);
	}

	public function create($tournamentId) {
		$data['tournament'] = intval($tournamentId);
		$data['types']		= \App\Stage::getTypesListed();

		$data['tournamentsActiveMenu'] = true;
		return view('stage/create', $data);
	}

	public function store(Request $request) {
		$validator = Validator::make($request->all(), [
			'format' => 'required',
            'name' => 'required|max:255',
	        'start' => 'required|date',
	        'end' => 'required|date|after:start',
	        'tournament' => 'required|exists:tournaments,id'
        ]);

        if ($validator->fails()) {
            return redirect(groute('stage.create', ['tournamentId' => $request->get('id')]))
                        ->withErrors($validator)
                        ->withInput();
        }

        $tournamentId = intval(Input::get('tournament'));
        $tournament = \App\Tournament::where('id', $tournamentId)->first();

		if (Input::has('id')) {
        	$stageId = intval(Input::get('id'));
        	$stage = \App\Stage::find($stageId);
        } else {
	        $stage = new \App\Stage();
	    }

        $startDate = date_convert(Input::get('start'), Auth::user()->timezone, 'UTC', 'Y-m-d H:i', 'Y-m-d H:i:s');

	    $stage->tournament_id	= $tournamentId;
	    $stage->name 			= Input::get('name');
	    $stage->format 			= intval(Input::get('format'));
	    $stage->start 			= $startDate;
	    $stage->end 			= Input::get('end');
	    $stage->hidden 			= Input::has('hidden') ? Input::get('hidden') : 0;
        $stage->active 			= Input::has('active') ? Input::get('active') : 0;
	    $stage->status 			= \App\Stage::STATUS_UPCOMING;
        $stage->prize = Input::get('prize');
        $stage->currency = Input::get('currency');
        $stage->prize_dist_type = Input::get('distroType');

        if (Input::has('prizeDist')) {
            $prizeDist = new \stdClass();
            foreach (Input::get('prizeDist') as $place => $prize) {
                if ($prize > 0 && $prize != "") {
                    $prizeDist->{$place + 1} = $prize;
                }
            }
            $prizeDist = json_encode($prizeDist);
            $stage->prize_distribution = $prizeDist;
        }

        $stage->save();

        return redirect(groute('stage', \App\Game::allCached($tournament->game_id)->slug, ['tournamentId' => $tournamentId, 'stageId' => $stage->id]));
	}

	public function edit($tournamentId, $stageId) {
		$stageId = intval($stageId);

		$data['types']	= \App\Stage::getTypesListed();
		$data['stage']	= \App\Stage::find($stageId);
		if (null == $data['stage']) {
			$data['errorMessage'] = 'No stage found with the specified key!';
		}

		$data['tournamentsActiveMenu'] = true;
		return view('stage/edit', $data);
	}

	public function remove($tournamentId, $stageId) {
		$stage = \App\Stage::find($stageId);

		if (null !== $stage) {
			//$stage->delete();
			$stage->hidden = 1;
			$stage->save();
            $tournament = \App\Tournament::where('id', $tournamentId)->first();

            return redirect(groute('tournament.view', \App\Game::allCached($tournament->game_id)->slug,['tournamentId' => $tournamentId]));
		} else {
            return redirect()->back();
		}

	}

	private function _numberOfMatches($teams) {
		$number = 0;
		for ($i = $teams-1; $i > 0; $i-- ) {
			$number += $i;
		}

		return $number;
	}
}