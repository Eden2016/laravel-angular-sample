<?php
namespace App\Http\Controllers;

use PDO;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use View;
use Validator;
use Input;

class GameController extends Controller
{

	public function showPatch($patchId) {
		$patchId = intval($patchId);

		$data['patch'] = \App\Patch::find($patchId);
		$data['game'] = $data['patch']->game;

		return view('patch/show', $data);
	}

	public function addPatch() {
		$data['games'] = \App\Game::all();

		return view('patch/add', $data);
	}

	public function editPatch($patchId) {
		$patchId = intval($patchId);

		$data['patch'] = \App\Patch::find($patchId);
		if (null === $data['patch']) {
			$data['errorMessage'] = 'No patch found with the specified key!';
		}

		$data['games'] = \App\Game::all();

		return view('patch/edit', $data);
	}

	public function removePatch($patchId) {
		$patchId = intval($patchId);

		$patch = \App\Patch::find($patchId);
		if (null === $patch) {
			$data['errorMessage'] = 'No patch found with the specified key!';
		} else {
			$patch->hidden = 1;

			$patch->save();
		}

		return redirect('games');
	}

	public function storePatch(Request $request) {

		$validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
	        'game' => 'required',
	        'date' => 'required|date'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

        if (Input::has('id')) {
        	$patchId = intval(Input::get('id'));
        	$patch = \App\Patch::find($patchId);
        } else {
	        $patch = new \App\Patch();
	    }

        $patch->game_id = Input::get('game');
        $patch->name = Input::get('name');
        $patch->date = Input::get('date');
        $patch->hidden = Input::has('hidden') ? Input::get('hidden') : 0;

        $patch->save();

        return redirect(groute('patch.view', ['patchId' => $patch->id]));
	}

	public function showGame($game) {
		$gameId = intval($game);

		$data['game'] = \App\Game::find($gameId);

		if (null === $data['game']) {
			$data['game'] = \App\Game::where('slug', $game)->firstOrFail();

			if (null !== $data['game']) {
				$data['patches'] = $data['game']->patches;
			}
		} else {
			$data['patches'] = $data['game']->patches;
		}

		return view('game/show', $data);
	}

	public function addGame() {

		return view('game/add');
	}

	public function editGame($gameId) {
		$gameId = intval($gameId);

		$data['game'] = \App\Game::find($gameId);
		if (null === $data['game']) {
			$data['errorMessage'] = 'No game found with the specified key!';
		}

		return view('game/edit', $data);
	}

	public function removeGame($gameId) {
		$gameId = intval($gameId);

		$game = \App\Game::find($gameId);
		if (null === $game) {
			$data['errorMessage'] = 'No game found with the specified key!';
		} else {
			$game->hidden = 1;

			$game->save();
		}

		return redirect('games');
	}

	public function storeGame(Request $request) {

		$validator = Validator::make($request->all(), [
            'name' => 'required|max:20',
	        'slug' => 'required|max:20',
	        'steamid' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

        if (Input::has('id')) {
        	$gameId = intval(Input::get('id'));
        	$game = \App\Game::find($gameId);
        } else {
	        $game = new \App\Game();
	    }

        $game->name = Input::get('name');
        $game->slug = Input::get('slug');
        $game->hashtag = Input::get('hashtag');
        $game->subreddit = Input::get('subreddit');
        $game->steam_app_id = Input::get('steamid');
        $game->hidden = Input::has('hidden') ? Input::get('hidden') : 0;

        $game->save();

        return redirect(groute('game.view', ['gameId' => $game->id]));
	}
}