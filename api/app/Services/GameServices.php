<?php
namespace App\Services;

use App\Models\Game;

class GameServices
{
    /**
     * Fetches game id from game slug
     *
     * @param string $gameSlug
     *
     * @return int
     */
	public static function getGameId($gameSlug)
    {
		try {
			$game = Game::whereSlug($gameSlug)->firstOrFail();
			$gameId = $game->id;
		} catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
			$gameId = false;
		}

		return $gameId;
	}

    /**
     * Lists all available games in a specific format
     *
     * @return array|bool
     */
    public static function getGamesListed()
    {
        $games = Game::all();

        $listed = false;
        if (count($games)) {
            $listed = array();
            foreach ($games as $game) {
                $listed[$game->slug] = $game->id;
            }
        }

        return $listed;
    }
}