<?php

Route::get('/', ['as' => 'home?', 'uses' => 'HomeController@liveMatches']);
// Home routes
Route::get('/home', ['as' => '/', 'uses' => 'HomeController@liveMatches']);
Route::get('/home/dump/{matchId}', ['as' => 'home.dump', 'uses' => 'HomeController@dump'])->where('matchId',
    '[0-9]+');
Route::get('/home/dump/{matchId}/{duration}',
    ['as' => 'home.dump.duration', 'uses' => 'HomeController@dump'])->where([
    'matchId' => '[0-9]+',
    'duration' => '[0-9]+'
]);
Route::get('/home/test', ['as' => 'home.test', 'uses' => 'HomeController@test']);

//Match Games routes
Route::get('/match_game/{matchGameId}',
    ['as' => 'match_game', 'uses' => 'MatchGameController@get'])->where('matchGameId', '[0-9]+');
Route::get('/match_game/delete/{matchGameId}',
    ['as' => 'match_game.delete', 'uses' => 'MatchGameController@remove'])->where('matchGameId', '[0-9]+');

Route::post('/match_game/store', ['as' => 'match_game.save', 'uses' => 'MatchGameController@store']);

//Match routes
Route::get('/match/{matchId}', ['as' => 'match', 'uses' => 'MatchController@showMatch'])->where('matchId',
    '[0-9]+');
Route::get('/match', ['as' => 'match.single', 'uses' => 'MatchController@showSingleMatch']);
Route::get('/match/{matchId}/map', ['as' => 'match.map', 'uses' => 'MatchController@showMap'])->where('matchId',
    '[0-9]+');
Route::get('/match/{matchId}/{matchDuration}',
    ['as' => 'match.history', 'uses' => 'MatchController@showHistoryMatch'])->where([
    'matchId' => '[0-9]+',
    'matchDuration' => '[0-9]+'
]);
Route::get('/tournament/{tournamentId}/stage/{stageId}/stage_format/{sfId}/match/{matchId}',
    ['as' => 'match.view', 'uses' => 'MatchController@edit'])->where([
    'tournamentId' => '[0-9]+',
    'stageId' => '[0-9]+',
    'sfId' => '[0-9]+',
    'matchId' => '[0-9]+'
]);
Route::get('/tournament/{tournamentId}/stage/{stageId}/stage_format/{sfId}/group',
    ['as' => 'stage_format.group', 'uses' => 'StageFormatController@showGroup'])->where([
    'tournamentId' => '[0-9]+',
    'stageId' => '[0-9]+',
    'sfId' => '[0-9]+'
]);
Route::get('/tournament/{tournamentId}/stage/{stageId}/stage_format/{sfId}/bracket', [
    'as' => 'stage_format.bracket',
    'uses' => 'StageFormatController@showBracket'
])->where(['tournamentId' => '[0-9]+', 'stageId' => '[0-9]+', 'sfId' => '[0-9]+']);


Route::get('/dummymatch/{matchId}',
    ['as' => 'dummymatch', 'uses' => 'MatchController@getDummyMatchInfo'])->where('matchId', '[0-9]+');
Route::get('/dummymatch/remove/{matchId}',
    ['as' => 'dummymatch.delete', 'uses' => 'MatchController@removeDummyMatch'])->where('matchId', '[0-9]+');
Route::get('/dummymatch/drafts/{matchId}',
    ['as' => 'dummymatch.drafts', 'uses' => 'MatchController@getDrafts'])->where('matchId', '[0-9]+');

Route::post('/dummymatch/add', ['as' => 'dummymatch.add', 'uses' => 'StageFormatController@addMatch']);
Route::post('/dummymatch/markDone', ['as' => 'dummymatch.mark_done', 'uses' => 'MatchController@markDone']);
Route::post('/dummymatch/changeOpponent',
    ['as' => 'dummymatch.change_opponent', 'uses' => 'MatchController@changeOpponent']);
Route::post('/match/storeMatch', ['as' => 'match.save_match', 'uses' => 'MatchController@storeMatch']);
Route::post('/match/store', ['as' => 'match.save', 'uses' => 'MatchController@store']);
Route::post('/dummymatch/drafts/save', ['as' => 'dummymatch.drafts.save', 'uses' => 'MatchController@saveDrafts']);

Route::get('/dummymatch/moveup/{matchId}',
    ['as' => 'dummymatch.move.up', 'uses' => 'MatchController@moveUp'])->where('matchId', '[0-9]+');
Route::get('/dummymatch/movedown/{matchId}',
    ['as' => 'dummymatch.move.down', 'uses' => 'MatchController@moveDown'])->where('matchId', '[0-9]+');

//Stage Format routes
Route::get('/tournament/{tournamentId}/stage/{stageId}/stage_format/{sfId}/edit',
    ['as' => 'stage_format.edit', 'uses' => 'StageFormatController@edit'])->where([
    'tournamentId' => '[0-9]+',
    'stageId' => '[0-9]+',
    'sfId' => '[0-9]+'
]);
Route::get('/tournament/{tournamentId}/stage/{stageId}/stage_format/{sfId}/remove',
    ['as' => 'stage_format.delete', 'uses' => 'StageFormatController@remove'])->where([
    'tournamentId' => '[0-9]+',
    'stageId' => '[0-9]+',
    'sfId' => '[0-9]+'
]);
Route::get('/tournament/{tournamentId}/stage/{stageId}/stage_format/{sfId}', ['as' => 'stages.formats.view', 'uses' => 'StageFormatController@show'])->where(['tournamentId' => '[0-9]+', 'stageId' => '[0-9]+', 'sfId' => '[0-9]+']);
Route::get('/tournament/{tournamentId}/stage/{stageId}/stage_format/create',
    ['as' => 'stage_format.create', 'uses' => 'StageFormatController@create'])->where([
    'tournamentId' => '[0-9]+',
    'stageId' => '[0-9]+'
]);
Route::get('/stage_format/{sfId}',
    ['as' => 'stage_format.bracket_data', 'uses' => 'StageFormatController@getBracketData'])->where('sfId',
    '[0-9]+');

Route::get('/stage_format/rounds/add', ['as' => 'stages.formats.rounds.add', 'uses' => 'StageFormatController@addRound']);

Route::post('/tournament/{tournamentId}/stage/{stageId}/stage_format/{sfId}',
    ['as' => 'stage_format', 'uses' => 'StageFormatController@show'])->where([
    'tournamentId' => '[0-9]+',
    'stageId' => '[0-9]+',
    'sfId' => '[0-9]+'
]);
Route::post('/stage_format/store', ['as' => 'stage_format.save', 'uses' => 'StageFormatController@store']);
Route::post('/stage_format/add', ['as' => 'stage_format.add', 'uses' => 'StageFormatController@add']);
Route::post('/stage_format/addAjax', ['as' => 'stage_format.add_ajax', 'uses' => 'StageFormatController@addAjax']);
Route::post('/stage_format/addParticipants',
    ['as' => 'stage_format.add_participants', 'uses' => 'StageFormatController@generatePreFilledRoundRobin']);
Route::post('/stage_format/addOpponents',
    ['as' => 'stage_format.add_opponents', 'uses' => 'StageFormatController@addOpponents']);
Route::post('/stage_format/changePos',
    ['as' => 'stage_format.change_pos', 'uses' => 'StageFormatController@changePos']);

//Stages routes
Route::get('/tournament/{tournamentId}/stage/{stageId}',
    ['as' => 'stage', 'uses' => 'StageController@show'])->where([
    'tournamentId' => '[0-9]+',
    'stageId' => '[0-9]+'
]);
Route::get('/tournament/{tournamentId}/stage/create',
    ['as' => 'stage.create', 'uses' => 'StageController@create'])->where('tournamentId', '[0-9]+');
Route::get('/tournament/{tournamentId}/stage/edit/{stageId}',
    ['as' => 'stage.edit', 'uses' => 'StageController@edit'])->where([
    'tournamentId' => '[0-9]+',
    'stageId' => '[0-9]+'
]);
Route::get('/tournament/{tournamentId}/stage/remove/{stageId}',
    ['as' => 'stage.delete', 'uses' => 'StageController@remove'])->where([
    'tournamentId' => '[0-9]+',
    'stageId' => '[0-9]+'
]);

Route::post('/stage/store', ['as' => 'stage.save', 'uses' => 'StageController@store']);

//Tournament routes
Route::get('/tournament/getLeagueByName/{league}',
    ['as' => 'tournament.league.by_name', 'uses' => 'TournamentController@getLeagueByName']);
Route::get('/tournaments/api-list',
    ['as' => 'tournaments.api_list', 'uses' => 'TournamentController@tournamentAPIList']);
Route::get('/tournaments/list', ['as' => 'tournaments.list', 'uses' => 'TournamentController@tournamentList']);
Route::get('/tournament/remove/{tournamentId}',
    ['as' => 'tournament.delete', 'uses' => 'TournamentController@remove'])->where('tournamentId', '[0-9]+');
Route::get('/tournament/edit/{tournamentId}',
    ['as' => 'tournament.edit', 'uses' => 'TournamentController@edit'])->where('tournamentId', '[0-9]+');
Route::get('/tournament/create/{eventId}',
    ['as' => 'tournament.create', 'uses' => 'TournamentController@create'])->where('eventId', '[0-9]+');
Route::get('/tournament/{tournamentId}',
    ['as' => 'tournament.view', 'uses' => 'TournamentController@show'])->where('tournamentId', '[0-9]+');

Route::get('/steamleague/{leagueId}',
    ['as' => 'league', 'uses' => 'TournamentController@showLeague'])->where('leagueId', '[0-9]+');

Route::post('/tournament/store', ['as' => 'tournament.save', 'uses' => 'TournamentController@store']);

//Event routes
Route::get('/events', ['as' => 'events', 'uses' => 'EventController@index']);
Route::get('/event/remove/{eventId}',
    ['as' => 'event.delete', 'uses' => 'EventController@remove'])->where('eventId', '[0-9]+');
Route::get('/event/edit/{eventId}', ['as' => 'event.edit', 'uses' => 'EventController@edit'])->where('eventId',
    '[0-9]+');
Route::get('/event/create', ['as' => 'event.create', 'uses' => 'EventController@create']);
Route::get('/event/{eventId}', ['as' => 'event.view', 'uses' => 'EventController@show'])->where('eventId',
    '[0-9]+');

Route::post('/event/store', ['as' => 'event.save', 'uses' => 'EventController@store']);

// Wrong route? //	Route::get('/player/edit/{teamId}', ['as' => 'player.edit', 'uses' => 'PlayerController@edit'])->where('playerId', '[0-9]+');
Route::get('/player/create', ['as' => 'player.create', 'uses' => 'PlayerController@create']);
Route::get('/player/edit/{playerId}', ['as' => 'player.edit', 'uses' => 'PlayerController@edit'])->where('playerId',
    '[0-9]+');
Route::get('/player/delete/{playerId}',
    ['as' => 'player.delete', 'uses' => 'PlayerController@remove'])->where('playerId', '[0-9]+');
Route::get('/player/refresh_stats/{steamId}',
    ['as' => 'player.refresh_stats', 'uses' => 'PlayerController@refreshStats'])->where('steamId', '[0-9]+');
Route::get('/dotaplayer/{playerId}',
    ['as' => 'player.dota', 'uses' => 'PlayerController@showDota'])->where('playerId', '[0-9]+');

Route::get('/player/{playerId}', ['as' => 'player.show', 'uses' => 'PlayerController@show'])->where('playerId',
    '[0-9]+');
Route::get('/player/getPlayerByName', ['as' => 'player.by_name', 'uses' => 'PlayerController@getPlayerByName']);
Route::get('/player/roster/{rosterId}',
    ['as' => 'player.roster.history', 'uses' => 'PlayerController@getRosterHistory'])->where('rosterId', '[0-9]+');

Route::post('/player/store', ['as' => 'player.save', 'uses' => 'PlayerController@store']);
Route::post('/player/addRoster', ['as' => 'player.roster.add', 'uses' => 'PlayerController@addRoster']);
Route::post('/player/editRoster', ['as' => 'player.roster.edit', 'uses' => 'PlayerController@editRoster']);
Route::post('/player/removeRoster', ['as' => 'player.roster.delete', 'uses' => 'PlayerController@removeRoster']);

Route::get('/players/list', ['as' => 'players.list', 'uses' => 'PlayerController@listPlayers']);
Route::get('/players/data-table-query', ['as' => 'players.dataquery', 'uses' => 'PlayerController@dataTableQuery']);
Route::post('/players/list', ['as' => 'players.list.post', 'uses' => 'PlayerController@listPlayers']);
Route::get('/players/{accountId}', ['as' => 'players.view', 'uses' => 'PlayerController@show'])->where('accountId', '[0-9]+');

Route::get('/players/api', ['as' => 'players.api', 'uses' => 'PlayerController@getApiPlayers']);

//Team routes
Route::get('/team/remove/{teamId}', ['as' => 'team.delete', 'uses' => 'TeamController@remove'])->where('teamId',
    '[0-9]+');
Route::get('/team/edit/{teamId}', ['as' => 'team.edit', 'uses' => 'TeamController@edit'])->where('teamId',
    '[0-9]+');
Route::get('/team/create', ['as' => 'team.create', 'uses' => 'TeamController@create']);
Route::get('/team/{teamId}', ['as' => 'team.show', 'uses' => 'TeamController@show'])->where('teamId', '[0-9]+');
Route::get('/team/getTeamByName/{team}', ['as' => 'team.by_name', 'uses' => 'TeamController@getTeamByName']);
Route::get('/team/getTeamByNameNew/{team}',
    ['as' => 'team.by_name_new', 'uses' => 'TeamController@getTeamByNameNew']);
Route::get('/team/getTeamByNameNew', ['as' => 'teams.by_name', 'uses' => 'TeamController@getTeamByNameNew']);
Route::get('/team/getPrefillsByName',
    ['as' => 'teams.prefilled.by_name', 'uses' => 'TeamController@getPrefillsByName']);

Route::post('/team/store', ['as' => 'team.save', 'uses' => 'TeamController@store']);
Route::post('/team/replace', ['as' => 'team.replace', 'uses' => 'TeamController@replace']);

Route::get('/teams/list', ['as' => 'teams.list', 'uses' => 'TeamController@listTeamAccounts']);
Route::post('/teams/list', ['as' => 'teams.list', 'uses' => 'TeamController@listTeamAccounts']);
Route::get('/teams/data-table-query', ['as' => 'teams.dataquery', 'uses' => 'TeamController@dataTableQuery']);
Route::get('/teams/apilist', ['as' => 'teams.api_list', 'uses' => 'TeamController@listApiTeams']);
Route::get('/teams/{teamId}/matches',
    ['as' => 'team.matches', 'uses' => 'TeamController@listTeamMatches'])->where('teamId', '[0-9]+');
Route::get('/teams/{teamId}/players-history',
    ['as' => 'team.players.history', 'uses' => 'TeamController@listTeamPlayerHistory'])->where('teamId', '[0-9]+');
Route::get('/teams/{teamId}', ['as' => 'team.view', 'uses' => 'TeamController@show'])->where('teamId', '[0-9]+');

Route::get('/teamaccounts/list', ['as' => 'team_accounts.list', 'uses' => 'TeamController@listTeamAccounts']);
Route::post('/teamaccounts/list', ['as' => 'team_accounts.list', 'uses' => 'TeamController@listTeamAccounts']);

Route::get('/matches/dota2', ['as' => 'matches.dota2', 'uses' => 'MatchController@listDotaMatches']);
Route::get('/matches/csgo', ['as' => 'matches.csgo', 'uses' => 'MatchController@listCsgoMatches']);
Route::get('/matches/list/{state}', ['as' => 'matches.by_state', 'uses' => 'MatchController@listMatches']);
Route::post('/matches/list/{state}', ['as' => 'matches.by_state', 'uses' => 'MatchController@listMatches']);
Route::get('/matches/list', ['as' => 'matches.list', 'uses' => 'MatchController@listMatches']);
Route::post('/matches/list', ['as' => 'matches.list', 'uses' => 'MatchController@listMatches']);
Route::get('/matches/general', ['as' => 'matches.general', 'uses' => 'MatchController@listGeneralMatches']);
Route::get('/matches/general/data-table-query', ['as' => 'matches.general.dataquery', 'uses' => 'MatchController@dataTableQuery']);
Route::get('/matches/listDummies', ['as' => 'matches.dummy', 'uses' => 'MatchController@listDummyMatches']);


//Game routes
Route::get('/game/add', ['as' => 'game.add', 'uses' => 'GameController@addGame']);
Route::get('/game/edit/{gameId}', ['as' => 'game.edit', 'uses' => 'GameController@editGame'])->where('gameId',
    '[0-9]+');
Route::get('/game/remove/{gameId}', ['as' => 'game.delete', 'uses' => 'GameController@removeGame'])->where('gameId',
    '[0-9]+');
Route::get('/game/{game}', ['as' => 'game.view', 'uses' => 'GameController@showGame'])->where('game',
    '[0-9a-zA-Z]+');

Route::post('/game/store', ['as' => 'game.save', 'uses' => 'GameController@storeGame']);

//Patch routes
Route::get('/patch/{patchId}', ['as' => 'patch.view', 'uses' => 'GameController@showPatch'])->where('patchId',
    '[0-9]+');
Route::get('/patch/add', ['as' => 'patch.add', 'uses' => 'GameController@addPatch']);
Route::get('/patch/edit/{patchId}', ['as' => 'patch.edit', 'uses' => 'GameController@editPatch'])->where('patchId',
    '[0-9]+');
Route::get('/patch/remove/{patchId}',
    ['as' => 'patch.delete', 'uses' => 'GameController@removePatch'])->where('patchId', '[0-9]+');

Route::post('/patch/store', ['as' => 'patch.save', 'uses' => 'GameController@storePatch']);

Route::get('/user/roles', ['as' => 'user.roles', 'uses' => 'UserController@roles']);
Route::get('/user/permissions', ['as' => 'user.permissions', 'uses' => 'UserController@permissions']);

Route::get('/user/fetch', ['as' => 'user.fetch', 'uses' => 'UserController@getUser']);
Route::post('/user/create', ['as' => 'user.create', 'uses' => 'UserController@create']);
Route::post('/user/edit', ['as' => 'user.edit', 'uses' => 'UserController@edit']);
Route::post('/user/delete', ['as' => 'user.delete', 'uses' => 'UserController@delete']);

Route::get('/clients', ['as' => 'clients.list', 'uses' => 'Auth\ClientController@index']);
Route::get('/clients/create', ['as' => 'clients.create', 'uses' => 'Auth\ClientController@create']);
Route::post('/clients/create', ['as' => 'clients.store', 'uses' => 'Auth\ClientController@store']);
Route::get('/clients/{id}/edit', ['as' => 'clients.edit', 'uses' => 'Auth\ClientController@edit']);
Route::post('/clients/{id}/edit', ['as' => 'clients.update', 'uses' => 'Auth\ClientController@update']);
Route::get('/clients/{id}/delete', ['as' => 'clients.delete', 'uses' => 'Auth\ClientController@delete']);

Route::get('/accounts/api', ['as' => 'accounts.api', 'uses' => 'AccountsController@index']);
Route::get('/accounts/api/scopes', ['as' => 'accounts.api.scopes', 'uses' => 'AccountsController@getScopes']);

Route::group(['prefix' => 'streams'], function() {
    Route::get('/', ['as' => 'streams', 'uses' => 'StreamsController@index']);
    Route::get('/getStreamByTitle', ['as' => 'stream.by_name', 'uses' => 'StreamsController@getStreamByTitle']);
});

Route::group(['prefix' => 'maps'], function() {
    Route::get('/', ['as' => 'maps', 'uses' => 'MapsController@index']);
    Route::get('/form', ['as' => 'maps.form', 'uses' => 'MapsController@form']);
    Route::post('/form', ['as' => 'maps.form.store', 'uses' => 'MapsController@store']);
    Route::get('/delete', ['as' => 'maps.delete', 'uses' => 'MapsController@delete']);
});

//Champions routes
Route::group(['prefix' => 'champion'], function() {
    Route::get('/', ['as' => 'champions', 'uses' => 'ChampionsController@index']);
    Route::get('/form', ['as' => 'champion.form', 'uses' => 'ChampionsController@form']);
    Route::post('/form', ['as' => 'champion.form', 'uses' => 'ChampionsController@store']);
    Route::get('/delete', ['as' => 'champion.delete', 'uses' => 'ChampionsController@delete']);
    Route::get('/auto', ['as' => 'champion.automatic', 'uses' => 'ChampionsController@auto']);
});

//Overwatch heroes
Route::group(['prefix' => 'ow-heroes', 'namespace' => 'Overwatch'], function() {
    Route::get('/', ['as' => 'owheroes', 'uses' => 'HeroesController@index']);
    Route::get('/create', ['as' => 'owheroes.create', 'uses' => 'HeroesController@create']);
    Route::post('/create', ['as' => 'owheroes.store', 'uses' => 'HeroesController@store']);
    Route::get('/{id}', ['as' => 'owheroes.delete', 'uses' => 'HeroesController@show']);
    Route::get('/{id}/edit', ['as' => 'owheroes.edit', 'uses' => 'HeroesController@edit']);
    Route::post('/{id}/edit', ['as' => 'owheroes.update', 'uses' => 'HeroesController@update']);
    Route::get('/{id}/delete', ['as' => 'owheroes.delete', 'uses' => 'HeroesController@destroy']);
});

Route::group(['prefix' => 'cache'], function() {
    Route::get('/player_stats', ['as' => 'cache.player_stats', 'uses' => 'CacheController@playerCache']);
    Route::post('/player_stats/start', ['as' => 'cache.player_stats.start', 'uses' => 'CacheController@playerCacheGenerate']);
});

//Toutou Section
Route::get('/toutou/matches', ['as' => 'toutou.matches', 'uses' => 'MatchController@toutouMatches']);
Route::get('/toutou/matches/{id}',
    ['as' => 'toutou.matches.single', 'uses' => 'MatchController@singleToutouMatch']);
Route::post('/toutou/matches/{id}',
    ['as' => 'toutou.matches.single', 'uses' => 'MatchController@postSingleToutouMatch']);


Route::group(['prefix' => 'logs'], function () {
    Route::get('/', ['as' => 'logs', 'uses' => 'LogsController@getIndex']);
});

Route::group(['prefix' => 'editorial'], function(){
    Route::get('/add', ['as' => 'blog.create', 'uses' => 'BlogController@create']);
    Route::post('/add', ['as' => 'blog.store', 'uses' => 'BlogController@store']);
    Route::get('/{id}/edit', ['as' => 'blog.edit', 'uses' => 'BlogController@edit']);
    Route::post('/{id}/edit', ['as' => 'blog.update', 'uses' => 'BlogController@update']);
    Route::get('/{id}/delete', ['as' => 'blog.delete', 'uses' => 'BlogController@delete']);
    Route::get('/tag-search', ['as' => 'blog.tagsearch', 'uses' => 'BlogController@tagSearch']);
    Route::get('/data-query', ['as' => 'blog.posts.dataquery', 'uses' => 'BlogController@dataquery']);
    Route::get('/image-config/{id}', ['as' => 'blog.imageconfig', 'uses' => 'BlogController@getClientImageConfig']);
    Route::get('/{id}/translations', ['as' => 'blog.post.translations', 'uses' => 'BlogController@getTranslations']);
    Route::get('/{id}/translations/{lang}', ['as' => 'blog.post.translation', 'uses' => 'BlogController@getTranslation']);
    Route::post('/{id}/translations/{lang}', ['as' => 'blog.post.translationset', 'uses' => 'BlogController@setTranslation']);
    Route::get('/manage', ['as' => 'blog.manage', 'uses' => 'BlogController@getPosts']);
});

Route::group(['prefix' => 'data_science'], function(){
    Route::get('/prediction', ['as' => 'prediction', 'uses' => 'DataScienceController@prediction']);
});