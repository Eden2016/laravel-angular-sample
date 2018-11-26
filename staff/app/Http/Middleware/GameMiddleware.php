<?php
namespace App\Http\Middleware;

use Route;
use Closure;

class GameMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $request->allGames = \App\Game::allCached();
        $request->allGamesSlugs = \App\Game::allSlugsCached();
        if (Route::getCurrentRoute()->getAction()['game']) {
            $request->currentGame = Route::getCurrentRoute()->getAction()['game'];
            $request->currentGameSlug = Route::getCurrentRoute()->getAction()['gameSlug'];
        } else {
            $request->currentGameSlug = '';
            $request->currentGame = null;
        }
        return $next($request);
    }
}
