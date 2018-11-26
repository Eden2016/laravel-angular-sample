<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/
//Landing pages
Route::get('/landing', 'HomeController@landingPage');
Route::post('/contact', 'HomeController@contact');

Route::group(['middleware' => ['web']], function () {
	// Cron routes
	Route::get('/cron', 'CronController@index');
	Route::get('/cron/heroes', 'CronController@getHeroes');
	Route::get('/cron/leagues', 'CronController@getLeagues');
	Route::get('/cron/matches/{leagueId}', 'CronController@getMatches');
	Route::get('/cron/matches/', 'CronController@getMatches');
	Route::get('/cron/getLiveMatches/{limit}', 'CronController@getLiveMatches');
	Route::get('/cron/leagues/save', 'CronController@saveLeagues');
	Route::get('/cron/save_backlog', 'CronController@saveBacklogMatch');
	Route::get('/cron/backlog', 'CronController@backlog');

	// Get TouTou Events and map them to our matches
	Route::get('/cron/get_toutou', 'CronController@toutouFetchEvents');

	//Authentication routes
	Route::auth();
});

Route::group([
    'prefix' => 'api',
    'namespace' => 'Api',
    'before' => 'auth'
], function(){

    Route::group(['prefix' => 'user'], function(){

        Route::group(['prefix' => 'roles'], function(){
            Route::get('/', ['as' => 'api.user.roles', 'uses' => 'UserRolesController@getRoles']);
            Route::post('add', ['as' => 'api.user.roles.add', 'uses' => 'UserRolesController@addRole']);
            Route::post('remove', ['as' => 'api.user.roles.remove', 'uses' => 'UserRolesController@removeRole']);
        });

    });

    Route::group(['prefix' => 'team'], function () {
        Route::get('members', ['as' => 'api.team.members', 'uses' => 'TeamController@members']);
    });

    Route::group(['prefix' => 'roles'], function(){
       Route::get('/', ['as' => 'api.roles', 'uses' => 'RolesController@getIndex']);
        Route::get('/single', ['as' => 'api.roles.role', 'uses' => 'RolesController@getRole']);
        Route::post('/single', ['as' => 'api.roles.role', 'uses' => 'RolesController@postRole']);

        Route::post('/delete', ['as' => 'api.roles.delete', 'uses' => 'RolesController@deleteRole']);
    });

    Route::group(['prefix' => 'permissions'], function(){
        Route::get('/single', ['as' => 'api.permissions.single', 'uses' => 'PermissionsController@getPermission']);
        Route::post('/single', ['as' => 'api.permissions.single', 'uses' => 'PermissionsController@postPermission']);
        Route::post('/delete', ['as' => 'api.permissions.delete', 'uses' => 'PermissionsController@deletePermission']);
    });

    Route::group(['prefix' => 'api_access'], function(){
        Route::get('/', ['as' => 'api.access', 'uses' => 'ApiAccessController@getAccess']);
        Route::post('/', ['as' => 'api.access', 'uses' => 'ApiAccessController@postAccess']);
        Route::get('/delete', ['as' => 'api.access.delete', 'uses' => 'ApiAccessController@deleteAccess']);
    });

    Route::group(['prefix' => 'api_scopes'], function(){
        Route::get('/', ['as' => 'api.scopes', 'uses' => 'ApiScopesController@getScope']);
        Route::post('/', ['as' => 'api.scopes', 'uses' => 'ApiScopesController@postScope']);
        Route::get('/delete', ['as' => 'api.scopes.delete', 'uses' => 'ApiScopesController@deleteScope']);
    });

    Route::group(['prefix' => 'streams'], function(){
        Route::get('/', ['as' => 'api.streams', 'uses' => 'StreamsController@index']);
        Route::post('/save', ['as' => 'api.streams', 'uses' => 'StreamsController@store']);
        Route::get('/delete', ['as' => 'api.streams.delete', 'uses' => 'StreamsController@delete']);

        Route::post('/add_stream_to_matches',
            ['as' => 'api.streams.add_to_matches', 'uses' => 'StreamsController@addStreamToMatches']);
    });

    Route::group(['prefix' => 'tournament'], function(){
        Route::post('ignore_stream', ['as' => 'api.tournament.remove_stream', 'uses' => 'TournamentController@ignoreStream']);
    });

    Route::group(['prefix' => 'matches'], function(){
        Route::post('ignore_stream', ['as' => 'api.matches.remove_stream', 'uses' => 'MatchController@ignoreStream']);
    });


});

//oAuth token generation route
Route::post('oauth/access_token', function() {
    return Response::json(Authorizer::issueAccessToken());
});


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| This route group applies the "oauth" middleware group to every route
| it contains.
|
*/

Route::group(['middleware' => ['oauth']], function () {
	Route::resource('api/{game}/team', 'Api\TeamController');
	Route::resource('api/{game}/player', 'Api\PlayerController');

	Route::resource('api/{game}/tournament', 'Api\TournamentController');

	Route::resource('api/{game}/match', 'Api\MatchController');
});
