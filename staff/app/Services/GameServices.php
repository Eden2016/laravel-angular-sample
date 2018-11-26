<?php
namespace App\Services;

class GameServices
{
	public static function getGameId($gameSlug) {
		try {
			$game = \App\Game::where('slug', $gameSlug)->firstOrFail();
			$gameId = $game->id;
		} catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
			$gameId = 1;
		}

		return $gameId;
	}
}