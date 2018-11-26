<?php

namespace App\Providers;

use Route;
use App\Game;
use Illuminate\Routing\Router;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to the controller routes in your routes file.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function boot(Router $router)
    {
        parent::boot($router);
    }

    /**
     * Define the routes for the application.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function map(Router $router)
    {
        $games = Game::allCached();
        $router->group(['namespace' => $this->namespace], function ($router) {
            require base_path('routes/routes.php');
        });

        $router->group([
            'namespace' => 'App\Http\Controllers\Clients',
            'prefix' => 'client',
            'middleware' => ['clients'],
        ], function ($router) {
            require base_path('routes/clients.php');
        });

        // all game routes
        $router->group([
            'namespace' => $this->namespace,
            'middleware' => [ 'web', 'auth', 'users', 'game'],
            'game' => null,
            'gameSlug' => ''
        ], function ($router) {
            require base_path('routes/games.php');
        });

        // per game routes
        foreach($games as $game) {
            $router->group([
                'namespace' => $this->namespace,
                'middleware' => ['web', 'auth', 'users', 'game'],
                'prefix' => $game->slug,
                'game' => $game,
                'gameSlug' => $game->slug,
                'as' => $game->slug.'::'
            ], function ($router) {
                require base_path('routes/games.php');
            });
        }
    }
}
