<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);

$app->get('/', function () use ($app) {
    return $app->version();
});
$app->post('oauth/access_token', function(){
   return response()->json(Authorizer::issueAccessToken());
});
$app->group([
    'middleware' => ['oauth', 'game'],
    'prefix' => 'api/{game}',
    'namespace' => 'App\Http\Controllers'
], function() use ($app){
    $app->get('team', 'TeamController@index');
    $app->get('team/{id}', 'TeamController@show');
    $app->post('team', 'TeamController@store');
    $app->post('team/{id}', 'TeamController@store');
    $app->get('team/{id}/stats', 'TeamController@stats');
    $app->get('team/{id}/delete', 'TeamController@destroy');

    $app->get('player', 'PlayerController@index');
    $app->get('player/{id}', 'PlayerController@show');
    $app->get('player/{id}/delete', 'PlayerController@destroy');
    $app->post('player/{id}', 'PlayerController@store');
    $app->post('player', 'PlayerController@store');

    $app->get('player/{id}/stats', 'PlayerController@stats');


    $app->get('tournament', 'TournamentController@index');
    $app->get('tournament/{id}', 'TournamentController@show');

    $app->get('match', 'MatchController@index');
    $app->get('match/{id}', 'MatchController@show');
    $app->get('match/{id}/client-{client}', 'MatchController@show');
    $app->get('match/{id}/{platform}', 'MatchController@fetchOdds');
    $app->get('streams', 'StreamsController@index');
    $app->get('streams/group', 'StreamsController@groupByMatch');
    $app->get('streams/group/{client}', 'StreamsController@groupByMatch');
    $app->get('calendar', 'CalendarController@index');

    $app->get('blogs/{client_id}', 'BlogController@index');
    $app->get('blogs/{client_id}/{id}', 'BlogController@show');

    $app->get('stage_format/{sfId}', 'StageFormatController@show');

    $app->get('toutou_matches', 'MatchController@getTouTouMatches');

    $app->get('raw/{eventId}/{client}', 'MatchController@getRawEvent');
});