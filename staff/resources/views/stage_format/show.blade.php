@extends('layouts.default')

@section('content')

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>All Tournaments</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{groute('/')}}">Home</a>
                </li>
                <li>
                    <a href="{{groute('event.view', 'current', ['eventId' => $stage->tournament->event->id])}}">{{$stage->tournament->event->name}}</a>
                </li>
                <li>
                    <a href="{{groute('tournament.view', 'current',  ['tournamentId' => $stage->tournament_id])}}">{{$stage->tournament->name}}</a>
                </li>
                <li>
                    <a href="{{groute('stage',  'current', ['tournamentId' => $stage->tournament_id, 'stageId' => $stage->id])}}">{{$stage->name}}</a>
                </li>
                <li class="active">
                    <strong>{{ $sf->name }}</strong>
                </li>
            </ol>
        </div>
        <div class="col-lg-2">
        </div>
    </div>
    @if (!isset($showError))
    <div class="wrapper wrapper-content animated fadeInRight" data-ng-app="App" data-ng-controller="stageFormatController">
        <div class="row">
            <div class="col-sm-8">
                <div class="ibox">
                    <div class="ibox-title">
                        <h3>{{ $sf->name }}</h3></div>
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-xs-6">
                                <h4>Matches</h4>
                            </div>
                            <div class="col-xs-6 text-right">
                                <a href="{{groute('stage_format.delete', 'current',  ['tournamentId' => $stage->tournament_id, 'stageId' => $stage->id, 'sfId' => $sf->id])}}"
                                   id="delete">
                                    <button type="button" class="btn btn-danger dim">Delete</button>
                                </a>

                                <a href="{{groute('stage_format.edit',  'current', ['tournamentId' => $stage->tournament_id, 'stageId' => $stage->id, 'sfId' => $sf->id])}}">
                                    <button type="button" class="btn btn-primary dim">Edit</button>
                                </a>

                            </div>
                        </div>
                    </div>
                </div>
                @if( $sf->type==1 || $sf->type==2 || $sf->type==4)
                <div id="minimal">
                    <div class="bracket"></div>
                </div>
                <div class="clearfix"></div>
                @endif
                <hr />
                <div>
                    <h3>Schedule all matches</h3>
                    <form method="post">
                        <input type="hidden" name="_token" id="csrfToken" value="{{ csrf_token() }}" />
                        <label for="start_date">Date</label>
                        <div class='input-group date' id='scheduleDatetHolder'>
                            <input type="text" class="form-control" name="schedule_date" id="schedule_date" />
                            <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                        <div style="margin-top:5px;">
                            <button type="submit" class="btn btn-primary dim">Add Schedule</button>
                        </div>
                    </form>
                </div>
                <hr>
                <div>
                    <form action="">
                        <h3>Add stream to all matches</h3>
                        <div class="form-group">
                            <select name="add_stream_to_all_matches" id="add_stream_to_all_matches" title="add stream" class="select2" style="width: 100%">
                                <option value="" disabled selected>Select a stream</option>
                                @foreach(\App\Models\Streams::all() as $stream)
                                    <option value="{{$stream->id}}">{{$stream->title}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <button type="button" class="btn btn-default" id="addSelectedStream" data-ng-click="addSelectedStream()">Add selected stream
                            </button>
                        </div>
                    </form>
                </div>
                <hr>

                @if($sf->type == \App\StageFormat::TYPE_ROUND_ROBIN)
                    @include('stage_format._partials.round_robin_table_view')
                @else
                    @include('stage_format._partials.bracket_table_view')
                @endif
                <!-- Add match game Modal -->
                <div class="modal fade" id="addMatchGameModal" tabindex="-1" role="dialog" aria-labelledby="addMatchGameModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel">Add match game</h4>
                            </div>
                            <div class="modal-body">
                                <form>
                                    <input type="hidden" name="matchid" id="matchid" />
                                    <div class="form-group">
                                        <label for="steamid">Match SteamID</label>
                                        <input type="text" class="form-control" name="steamid" id="steamid" />
                                    </div>
                                    <div class="form-group">
                                        <label for="opponent1_score">Opponent1 Score</label>
                                        <input type="text" class="form-control" name="opponent1_score" id="opponent1_score" />
                                    </div>
                                    <div class="form-group">
                                        <label for="opponent2_score">Opponent2 Score</label>
                                        <input type="text" class="form-control" name="opponent2_score" id="opponent2_score" />
                                    </div>
                                    <div class="form-group">
                                        <label for="game_number">Game number</label>
                                        <input type="text" class="form-control" name="game_number" id="game_number" />
                                    </div>
                                    <label for="start_date">Start Date</label>
                                    <div class='input-group date' id='starDatetHolder'>
                                        <input type="text" class="form-control" name="start_date" id="start_date" />
                                        <span class="input-group-addon">
                                          <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default dim" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary dim" id="saveMatchGame">Save Match Game</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit match Modal -->
                <div class="modal fade" id="editMatchModal" tabindex="-1" role="dialog" aria-labelledby="editMatchModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel">Edit match</h4>
                            </div>
                            <div class="modal-body">
                                <form>
                                    <input type="hidden" name="matchid" id="matchid" />
                                    <div class="form-group">
                                        <label for="opponent1name">Opponent1 Name</label><br />
                                        <input name="opponent1" id="opponent1" class="select2-participants" />
                                    </div>
                                    <div class="form-group">
                                        <label for="opponent2name">Opponent2 Name</label><br />
                                        <input name="opponent2" id="opponent2" class="select2-participants" />
                                    </div>
                                    <div class="checkbox">
                                        <label for="tie">
                                          <input type="checkbox" name="tie" id="tie" value="1" /> Is Tie
                                        </label>
                                                <label for="forfeited">
                                          <input type="checkbox" name="forfeited" id="forfeited" value="1" /> Is Forfeited
                                        </label>
                                    </div>
                                    <div class="form-group">
                                        <label for="winnername">Winner</label>
                                        <input type="text" class="form-control" name="winnername" id="winnername" autocomplete="off" />
                                        <input type="hidden" name="winner" id="winner" />
                                        <div class="leagueSuggestions" id="suggestion3">
                                            <ul id="leagueSuggestions3">

                                            </ul>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="opponent2name">Position in round</label><br />
                                        <input name="match_position" id="match_position" />
                                    </div>
                                    <div class="form-group">
                                        <label for="start">Start Date</label>
                                        <div class='input-group date' id='startHolder'>
                                            <input type="text" class="form-control" name="start" id="start" />
                                            <span class="input-group-addon">
                                              <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                    @if(count($stage->tournament->maps))
                                    <div class="form-group">
                                        <label for="map">Map</label>
                                        <select name="map_id" id="map_id" class="select2 form-control">
                                                @foreach($stage->tournament->maps as $map)
                                                    <option value="{{$map->id}}">{{$map->name}}</option>
                                                @endforeach
                                            </select>
                                    </div>
                                    @endif @permission('manage_toutou')
                                    <div class="form-group">
                                        <label for="toutou_match">Link with TouTou Match</label>
                                        <input name="toutou_match" id="toutou_match" />
                                    </div>
                                    @endpermission
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default dim" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary dim" id="saveMatch">Edit Match</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <!-- Change Team Modal -->
            <div class="modal fade" id="changeTeamModal" tabindex="-1" role="dialog" aria-labelledby="changeTeamModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">Edit match</h4>
                        </div>
                        <div class="modal-body">
                            <form>
                                <input type="hidden" name="sfid" id="sfid" value="{{ $sf->id }}" />
                                <div class="form-group">
                                    <label for="original_team">Change Team</label><br />
                                    <input type="text" name="original_team" id="original_team" class="form-control" disabled />
                                    <input type="hidden" name="original_team_id" id="original_team_id" class="form-control" />
                                </div>
                                <div class="form-group">
                                    <label for="replacement_team">With Team</label><br />
                                    <input name="replacement_team" id="replacement_team" class="select2-participants" />
                                </div>
                                <div class="form-group">
                                    <label for="round">After match ID:</label>
                                    <input type="text" class="form-control" name="after_match_id" id="after_match_id" />
                                </div>
                                <div class="checkbox">
                                    <label for="whole_sf">
                                            <input type="checkbox" name="whole_sf" id="whole_sf" value="1" /> For all matches
                                        </label>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default dim" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary dim" id="changeTeamSave">Change Team</button>
                        </div>
                    </div>
                </div>
            </div>


            <div class="modal fade" id="csGoMatchGame">
                <div class="modal-dialog modal-md">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Add match game</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                        <label for="match_date">Match Date</label>
                                        <div class='input-group date' id='matchDateHolder'>
                                            <input type="text" class="form-control" name="match_date" id="match_date" />
                                            <span class="input-group-addon">
                                              <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                            <div class="row">
                                <div class="col-xs-6">
                                    <h4 class="text-right" data-ng-bind="match.opponent1_details.name"></h4>
                                </div>
                                <div class="col-xs-6">
                                    <h4 data-ng-bind="match.opponent2_details.name"></h4>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="panel panel-default" data-ng-repeat="game in matchGameRounds">
                                        <div class="panel-body">
                                            <div class="row">

                                                <div class="col-xs-3">
                                                    <label>
                                                        <input type="checkbox" value="true" name="round_overtime"
                                                               class="pull-left" data-ng-model="game.round_overtime"
                                                               data-ng-checked="game.round_overtime==true">
                                                        <small> Overtime</small>
                                                    </label>
                                                </div>
                                                <div class="col-xs-2">
                                                    <input type="text" name="team1_score" class="form-control input-sm" value="" data-ng-model="game.team1_score" data-ng-value="game.team1_score" title="Team 1 score">
                                                </div>
                                                <div class="col-xs-3">
                                                    <div class="row">
                                                        <div class="col-xs-4">
                                                            <input type="radio" data-ng-model="game.ct" data-ng-checked="game.ct==match.opponent1_details.id" data-ng-value="match.opponent1_details.id">
                                                        </div>
                                                        <div class="col-xs-4">CT</div>
                                                        <div class="col-xs-4">
                                                            <input type="radio" data-ng-model="game.ct" data-ng-checked="game.ct==match.opponent2_details.id" data-ng-value="match.opponent2_details.id">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-2">
                                                    <input type="text" name="team2_score" data-ng-model="game.team2_score" class="form-control input-sm" value="" data-ng-value="game.team2_score" title="Team 1 score">
                                                </div>
                                                <div class="col-xs-2">
                                                    <button type="button" data-ng-click="matchGameRounds.splice($index,1)" class="btn btn-danger btn-xs pull-right"><i
                                                                class="fa fa-trash"></i></button>
                                                </div>

                                            </div>

                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <button type="button" class="btn btn-default btn-xs" data-ng-click="matchGameRounds.push({})"><i class="fa fa-plus"></i>
                                                Add round
                                            </button>
                                            <br/><br/> {{-- Add some space under the button --}}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label for="opponent1_members">@{{ match.opponent1_details.name }}
                                            Players</label>
                                        <select name="opponent1_members[]" id="opponent1_members" class="select2" multiple style="width: 100%">

                                            <option data-ng-value="player.id"
                                                    data-ng-repeat="player in match.opponent1_details.roster">@{{ player.nickname }}</option>

                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="opponent1_standins">@{{ match.opponent1_details.name }} Stand-ins</label>
                                        <input name="opponent1_standins[]" id="opponent1_standins" style="width: 100%" />
                                    </div>
                                    <div class="form-group">
                                        <label for="opponent2_members">@{{ match.opponent2_details.name }}
                                            Players</label>
                                        <select name="opponent2_members[]" id="opponent2_members" class="select2" multiple style="width: 100%">
                                            <option data-ng-value="player.id"
                                                    data-ng-repeat="player in match.opponent2_details.roster">@{{ player.nickname }}</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="opponent2_standins">@{{ match.opponent2_details.name }} Stand-ins</label>
                                        <input name="opponent2_standins[]" id="opponent2_standins" style="width: 100%" />
                                    </div>
                                    @if(count($stage->tournament->maps))
                                    <div class="form-group">
                                        <label for="map_id">Map</label>
                                        <select name="map_id" title="map" class="select2" style="width: 100%">
                                                <option></option>
                                                @foreach($stage->tournament->maps as $map)
                                                    <option value="{{$map->id}}">{{$map->name}}</option>
                                                @endforeach
                                            </select>
                                    </div>
                                    @endif
                                    <div class="form-group">
                                        <label for="game_number_edit">Map number</label>
                                        <input type="text" class="form-control" name="game_number" title="Game number" value="" />
                                    </div>
                                    <div class="form-group">
                                        <label for="streams">Streams:</label>
                                        <select class="select2" name="streams[]" id="streams" multiple style="width: 100%">
                                            @foreach(\App\Models\Streams::all() as $stream)
                                                <option value="{{$stream->id}}">{{$stream->title}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="final_result">Is Walkover:</label>
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" value="true" id="walkover" name="walkover">
                                                Yes
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="final_result">Map Winner:</label>
                                        <select name="final_result" id="final_result" class="form-control">
                                            <option data-ng-selected="!match.winner"></option>
                                            <option data-ng-value="match.opponent1_details.id"
                                                    data-ng-selected="match.winner==match.opponent1_details.id">@{{ match.opponent1_details.name }}</option>
                                            <option data-ng-value="match.opponent2_details.id"
                                                    data-ng-selected="match.winner==match.opponent2_details.id">@{{ match.opponent2_details.name }}</option>
                                            <option value="draw" data-ng-selected="match.is_tie==true">Draw</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" data-match-id="" data-ng-click="saveMatchGame()">Save changes
                            </button>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>

            @include('match._partials.new_lol_match_game')

            @include('match._partials.ow_match_game')

            @else
            <div class="wrapper wrapper-content animated fadeInRight">
                <div class="row">
                    <div class="col-sm-8">
                        <div class="alert alert-danger">{{ $showError }}</div>
                    </div>

                    @endif




                    @endsection


                    @section('scripts_head')
                        @parent

                    <?php
                        $opponents = \App\OpponentPrefill::getOpponents($sf->id);

                        $opp = [];
                        if (count($opponents)) {
                            foreach ($opponents as $opponent) {
                                $opp[] = [
                                    $opponent->opponent_id,
                                    $opponent->opponent->name
                                ];
                            }

                            $opis = json_encode($opp);
                        }
                        ?>
                        <script src="/bower_components/angular/angular.min.js"></script>
                        <script type="text/javascript">
                          var opp = <?php echo ($opis) ? $opis : '{}'?>;
                        </script>
                        <script>
                          var app = angular.module('App', []);
                          app.controller('stageFormatController', function($scope, $http) {
                            $scope.matchGameRounds = [];
                            $scope.owMatchNonControlGameRound = [{
                              attack_time_op1: '00:00', score_op1: 0, meters_op1: '0m',
                              attack_time_op2: '00:00', score_op2: 0, meters_op2: '0m'
                            }];
                            $scope.matchDrafts = [];
                            $scope.match = {};

                            $scope.newMatchGame = function(match_id) {
                              $scope.matchGameRounds = [];
                              $('#csGoMatchGame [name="streams[]"], #csGoMatchGame [name="steam"], #csGoMatchGame [name="map_id"], #csGoMatchGame [name="opponent2_members[]"], #csGoMatchGame [name="opponent1_members[]"]')
                                .val('')
                                .trigger('change');
                                $('[name="walkover"]').prop('checked', false);
                              $('#csGoMatchGame [data-match-id]').data('match-id', match_id);
                              $('#csGoMatchGame [name="game_number"]').val('');
                              $('#csGoMatchGame [name="final_result"]').val('');
                              $http.get('/csgo/match/' + match_id)
                                .then(function(response) {
                                  $scope.match = response.data.match;
                                  var opponent1_members = [],
                                    opponent2_members = [];
                                    if (response.data.match.opponent1_members != null && response.data.match.opponent1_members.length) {
                                        $.each(response.data.match.opponent1_members, function (idx, id) {
                                            opponent1_members.push(parseInt(id));
                                        });
                                    }
                                    if (response.data.match.opponent2_members != null && response.data.match.opponent2_members.length) {
                                        $.each(response.data.match.opponent2_members, function (idx, id) {
                                            opponent2_members.push(parseInt(id));
                                        });
                                    }

                                  setTimeout(function() {
                                    $('#csGoMatchGame [name="opponent1_members[]"]').val(opponent1_members).trigger('change');
                                    $('#csGoMatchGame [name="opponent2_members[]"]').val(opponent2_members).trigger('change');

                                    $('#opponent1_standins').select2('data', []);
                                    $('#opponent2_standins').select2('data', []);
                                  }, 500);
                                  $('#csGoMatchGame').modal('show');
                                });

                            };
                            $scope.saveMatchGame = function() {
                              var data = {
                                streams: $('#csGoMatchGame [name="streams[]"]').val(),
                                map_id: $('#csGoMatchGame [name="map_id"]').val(),
                                opponent1_members: $('#csGoMatchGame [name="opponent1_members[]"]').val(),
                                opponent2_members: $('#csGoMatchGame [name="opponent2_members[]"]').val(),
                                opponent1_id: $scope.match.opponent1,
                                opponent2_id: $scope.match.opponent2,
                                opponent1_standins: $('#csGoMatchGame [name="opponent1_standins[]"]').val(),
                                opponent2_standins: $('#csGoMatchGame [name="opponent2_standins[]"]').val(),
                                rounds: $scope.matchGameRounds,
                                steam: $('#csGoMatchGame [name="steam"]').val(),
                                gameNum: $('#csGoMatchGame [name="game_number"]').val(),
                                match: $('#csGoMatchGame [data-match-id]').data('match-id'),
                                winner: $('#csGoMatchGame [name="final_result"]').val() != 'draw' ? $('#csGoMatchGame [name="final_result"]').val() : null,
                                is_tie: $('#csGoMatchGame [name="final_result"]').val() == 'draw',
                                  match_date: $('#csGoMatchGame [name="match_date"]').val(),
                                  walkover: $('#csGoMatchGame [name="walkover"]').is(':checked')
                              };
                              $http.post('/csgo/match_game/store', data)
                                .then(function(response) {
                                  $('#csGoMatchGame').modal('hide');
                                });
                            };

                            $scope.addSelectedStream = function() {
                              $http.post('{{route('api.streams.add_to_matches')}}', {
                                matches: <?=$resulted->pluck('id')->merge($scheduled->pluck('id'))->merge($not_resulted->pluck('id'))->unique()?>,
                                stream: $('#add_stream_to_all_matches').val()
                              }).then(function(response) {
                                if (response.status == 200) {
                                  $('#add_stream_to_all_matches').val('').trigger('change');
                                } else {
                                  alert(response.statusText);
                                }
                              });
                            };

                            $scope.reArrange = function (direction, team_id, stage_format_id) {
                              $http.post('{{route('stage_format.change_pos')}}', {
                                direction: direction,
                                team_id: team_id,
                                stage_format_id: stage_format_id,
                                teams: <?=json_encode($json_teams)?>
                              }).then(function (response) {
                                window.location.href = window.location.href;
                              });
                            };

                            /*
                            * Overwatch match game functionality below
                            */
                            $scope.newOwMatchGame = function(match_id) {
                              $scope.matchGameRounds = [];
                              $scope.owMatchNonControlGameRound = [{
                                attack_time_op1: '00:00', score_op1: 0, meters_op1: '0m',
                                attack_time_op2: '00:00', score_op2: 0, meters_op2: '0m'
                              }];
                              $('#owMatchGame [name="streams[]"], #owMatchGame [name="steam"], #owMatchGame [name="map_id"], #owMatchGame [name="opponent2_members[]"], #owMatchGame [name="opponent1_members[]"]')
                                .val('')
                                .trigger('change');
                                $('[name="walkover"]').prop('checked', false);
                              $('#owMatchGame [data-match-id]').data('match-id', match_id);
                              $('#owMatchGame [name="game_number"]').val('');
                              $('#owMatchGame [name="final_result"]').val('');
                              $http.get('/overwatch/match/' + match_id)
                                .then(function(response) {
                                  $scope.match = response.data.match;
                                  var opponent1_members = [],
                                    opponent2_members = [];
                                  if(response.data.match.opponent1_members) {
                                    $.each(response.data.match.opponent1_members, function(idx, id) {
                                      opponent1_members.push(parseInt(id));
                                    });
                                  }
                                  if(response.data.match.opponent2_members) {
                                    $.each(response.data.match.opponent2_members, function(idx, id) {
                                      opponent2_members.push(parseInt(id));
                                    });
                                  }

                                  setTimeout(function() {
                                    $('#owMatchGame [name="opponent1_members[]"]').val(opponent1_members).trigger('change');
                                    $('#owMatchGame [name="opponent2_members[]"]').val(opponent2_members).trigger('change');

                                    $('#opponent1_standins').select2('data', []);
                                    $('#opponent2_standins').select2('data', []);
                                  }, 500);
                                  $('#owMatchGame').modal('show');
                                });

                            };

                            $scope.saveOwMatchGame = function() {
                              var data = {
                                streams: $('#owMatchGame [name="streams[]"]').val(),
                                map_id: $('#owMatchGame [name="map_id"]').val(),
                                opponent1_members: $('#owMatchGame [name="opponent1_members[]"]').val(),
                                opponent2_members: $('#owMatchGame [name="opponent2_members[]"]').val(),
                                opponent1_id: $scope.match.opponent1,
                                opponent2_id: $scope.match.opponent2,
                                opponent1_standins: $('#owMatchGame [name="opponent1_standins[]"]').val(),
                                opponent2_standins: $('#owMatchGame [name="opponent2_standins[]"]').val(),
                                rounds: ($('#ow-map-select > option:selected').data('map-type') == 'control') ? $scope.matchGameRounds : $scope.owMatchNonControlGameRound,
                                steam: $('#owMatchGame [name="steam"]').val(),
                                gameNum: $('#owMatchGame [name="game_number"]').val(),
                                match: $('#owMatchGame [data-match-id]').data('match-id'),
                                winner: $('#owMatchGame [name="final_result"]').val() != 'draw' ? $('#owMatchGame [name="final_result"]').val() : null,
                                is_tie: $('#owMatchGame [name="final_result"]').val() == 'draw',
                                match_date: $('#owMatchGame [name="match_date"]').val(),
                                  startDate: $('#owMatchGame [name="match_date"]').val(),
                                  walkover: $('#owMatchGame [name="walkover"]').is(':checked')
                              };
                              $http.post('/overwatch/match_game/store', data)
                                .then(function(response) {
                                  $('#owMatchGame').modal('hide');
                                });
                            };
                              /*
                               LOL Match Game Functionality below
                               */
                            $scope.newLolMatchGame = function(match_id) {
                              $('#lolMatchGame [name="opponent1_bans[]"], #lolMatchGame [name="opponent2_bans[]"], #lolMatchGame [name="game_number"], #lolMatchGame [name="streams[]"]')
                                .val('')
                                .trigger('change');

                              $('#lolMatchGame [data-match-id]').data('match-id', match_id);
                                $('[name="walkover"]').prop('checked', false);
                              $http.get('/lol/match/' + match_id)
                                .then(function(response) {
                                  $scope.match = response.data.match;

                                  setTimeout(function() {
                                      $('#lolMatchGame [name^="opponent1_player"]').trigger('change');
                                      $('#lolMatchGame [name^="opponent2_player"]').trigger('change');
                                    $('#opponent1_standins').select2('data', []);
                                    $('#opponent2_standins').select2('data', []);
                                  }, 100);
                                  $('#lolMatchGame').modal('show');
                                });

                              $('#lolMatchGame [data-match-game-id]').data('match-game-id', '');

                              $('#lolMatchGame').modal('show');
                            };

                            $scope.saveLolMatchGame = function() {
                              var opponent1_members = [
                                $('#lolMatchGame [name="opponent1_player1"]').val(),
                                $('#lolMatchGame [name="opponent1_player2"]').val(),
                                $('#lolMatchGame [name="opponent1_player3"]').val(),
                                $('#lolMatchGame [name="opponent1_player4"]').val(),
                                $('#lolMatchGame [name="opponent1_player5"]').val(),
                              ];
                              var opponent2_members = [
                                $('#lolMatchGame [name="opponent2_player1"]').val(),
                                $('#lolMatchGame [name="opponent2_player2"]').val(),
                                $('#lolMatchGame [name="opponent2_player3"]').val(),
                                $('#lolMatchGame [name="opponent2_player4"]').val(),
                                $('#lolMatchGame [name="opponent2_player5"]').val(),
                              ];
                              var opponent1_picks = [
                                $('#lolMatchGame [name="opponent1_picks1"]').val(),
                                $('#lolMatchGame [name="opponent1_picks2"]').val(),
                                $('#lolMatchGame [name="opponent1_picks3"]').val(),
                                $('#lolMatchGame [name="opponent1_picks4"]').val(),
                                $('#lolMatchGame [name="opponent1_picks5"]').val(),
                              ];
                              var opponent2_picks = [
                                $('#lolMatchGame [name="opponent2_picks1"]').val(),
                                $('#lolMatchGame [name="opponent2_picks2"]').val(),
                                $('#lolMatchGame [name="opponent2_picks3"]').val(),
                                $('#lolMatchGame [name="opponent2_picks4"]').val(),
                                $('#lolMatchGame [name="opponent2_picks5"]').val(),
                              ];

                              var data = {
                                id: $('#lolMatchGame [data-match-game-id]').data('match-game-id'),
                                streams: $('#lolMatchGame [name="streams[]"]').val(),
                                opponent1_bans: $('#lolMatchGame [name="opponent1_bans[]"]').val(),
                                opponent2_bans: $('#lolMatchGame [name="opponent2_bans[]"]').val(),
                                opponent1_id: $scope.match.opponent1,
                                opponent2_id: $scope.match.opponent2,
                                opponent1_standins: $('#lolMatchGame [name="opponent1_standins[]"]').val(),
                                opponent2_standins: $('#lolMatchGame [name="opponent2_standins[]"]').val(),
                                gameNum: $('#lolMatchGame [name="game_number"]').val(),
                                winner: $('#lolMatchGame [name="final_result"]').val() != 'draw' ? $('#lolMatchGame [name="final_result"]').val() : null,
                                is_tie: $('#lolMatchGame [name="final_result"]').val() == 'draw',
                                match: $('#lolMatchGame [data-match-id]').data('match-id'),
                                opponent1_members: opponent1_members,
                                opponent2_members: opponent2_members,
                                opponent1_picks: opponent1_picks,
                                  opponent2_picks: opponent2_picks,
                                  walkover: $('#lolMatchGame [name="walkover"]').is(':checked')
                              };
                              $http.post('/lol/match_game/store', data)
                                .then(function(response) {
                                  $('#lolMatchGame').modal('hide');
                                });
                            };
                          });

                        </script>
                    @endsection

                    @section('scripts')
                        @parent

                        <script type="text/javascript">
                          $('.select2').select2();
                          $('[data-do="updateMatch"]').on('click', function(e) {
                            var button = $(this);
                            var match_id = button.attr('data-match');
                            var data = {
                              "id": match_id
                            };
                            if (button.attr('data-draw') != undefined) {
                              data.is_tie = 1;
                            } else if (button.attr('data-forfeit-winner') != undefined) {
                              data.is_forfeited = 1;
                              data.winner = button.attr('data-forfeit-winner');
                            } else if (button.attr('data-winner') != undefined) {
                              data.winner = button.attr('data-winner');
                            } else if (button.attr('data-disqualify-team') != undefined) {
                              data.winner = button.attr('data-disqualification-winner');
                              data.disqualified_team = button.attr('data-disqualify-team');
                            } else {
                              console.log('Unknown error');
                            }
                            $.post('{{route('match.save_match')}}', data, function(response) {
                              button.parent().parent().parent().find('button.dropdown-toggle').text(button.text());
                            });
                          });

                          $('#changeTeamSave').click(function() {
                            var whole_sf = 0;
                            if ($('#whole_sf').is(":checked"))
                              whole_sf = 1;

                            $.post('{{groute('team.replace')}}', {
                              original_team_id: $('#original_team_id').val(),
                              substitute_team_id: $('#replacement_team').val(),
                              sfId: $('#sfid').val(),
                              matchId: $('#after_match_id').val(),
                              whole_sf: whole_sf,
                              _token: $('#csrfToken').val()
                            }, function(data) {
                              if (data.status == "success") {
                                $('#editMatchModal').modal('hide');
                                window.location.reload();
                              } else {
                                console.log(data);
                              }
                            });
                          });

                          $(".change_team").on('click', function(e) {
                            $("#changeTeamModal").modal('show');

                            $("#original_team").val($(this).data("name"));
                            $("#original_team_id").val($(this).data("id"));
                          });

                          $('#editMatchModal, #changeTeamModal, #csGoMatchGame, #lolMatchGame, #owMatchGame').on('shown.bs.modal', function() {
                            invoke_select2();
                          });


                          var invoke_select2 = function() {
                            //Selecting participants for round robin stage format
                            $('.select2-participants').select2({
                              ajax: {
                                url: "{{groute('teams.by_name')}}",
                                dataType: 'json',
                                delay: 250,
                                data: function(term) {
                                  return {
                                    name: term
                                  };
                                },
                                processResults: function(data, params) {
                                  return {
                                    results: $.map(data.teams, function(item) {
                                      return {
                                        text: item.name,
                                        id: item.id
                                      }
                                    })
                                  };
                                },
                                cache: true
                              },
                              escapeMarkup: function(markup) {
                                return markup;
                              }, // let our custom formatter work
                              minimumInputLength: 2,
                              placeholder: 'Search for a team',
                              width: 'resolve'
                            });

                            $('.select2-teamchange').select2({
                              ajax: {
                                url: "{{groute('teams.prefilled.by_name')}}",
                                dataType: 'json',
                                delay: 250,
                                data: function(term) {
                                  return {
                                    name: term,
                                    sfId: $('.changeTeam').data('id')
                                  };
                                },
                                processResults: function(data, params) {
                                  return {
                                    results: $.map(data.teams, function(item) {
                                      return {
                                        text: item.name,
                                        id: item.id
                                      }
                                    })
                                  };
                                },
                                cache: true
                              },
                              escapeMarkup: function(markup) {
                                return markup;
                              }, // let our custom formatter work
                              minimumInputLength: 2,
                              placeholder: 'Search for a team',
                              width: 'resolve'
                            });

                            $('#opponent1_standins, #opponent2_standins').select2({
                              ajax: {
                                url: '{{groute('player.by_name')}}',
                                dataType: 'json',
                                delay: 250,
                                data: function(term) {
                                  return {
                                    name: term
                                  };
                                },
                                processResults: function(data, params) {
                                  return {
                                    results: $.map(data.players, function(item) {
                                      return {
                                        text: item.nickname,
                                        id: item.id
                                      }
                                    })
                                  };
                                },
                                cache: true
                              },
                              escapeMarkup: function(markup) {
                                return markup;
                              }, // let our custom formatter work
                              minimumInputLength: 2,
                              placeholder: 'Search for a player',
                              multiple: true
                            }).parent().find('.select2-container').css({
                              "width": "100%"
                            });

                            $('#lolMatchGame [name="opponent1_standins[]"], #lolMatchGame [name="opponent2_standins[]"]').select2({
                              ajax: {
                                url: '{{groute('player.by_name')}}',
                                dataType: 'json',
                                delay: 250,
                                data: function(term) {
                                  return {
                                    name: term
                                  };
                                },
                                processResults: function(data, params) {
                                  return {
                                    results: $.map(data.players, function(item) {
                                      return {
                                        text: item.nickname,
                                        id: item.id
                                      }
                                    })
                                  };
                                },
                                cache: true
                              },
                              escapeMarkup: function(markup) {
                                return markup;
                              }, // let our custom formatter work
                              minimumInputLength: 2,
                              placeholder: 'Search for a player',
                              multiple: true
                            }).parent().find('.select2-container').css({
                              "width": "100%"
                            });
                          }

                        </script>
                        @if( $sf->type==0 || $sf->type==1 || $sf->type==2 || $sf->type==4)


                            <script type="text/javascript">
                              const ROUND_TYPE_GROUP = 0;
                              const ROUND_TYPE_UPPER_BRACKET = 1;
                              const ROUND_TYPE_LOWER_BRACKET = 2;
                              const ROUND_TYPE_FINAL = 3;
                              const ROUND_TYPE_THIRD_PLACE_PLAYOFF = 4;
                              const ROUND_TYPE_DBL_ELIM_GROUP = 5;

                              var calculateMatchScore = function(single_round, callback) {
                                var scores = [];
                                $.each(single_round.dummy_matches, function(dummy_index, dummy) {
                                  var opponent1score = null,
                                    opponent2score = null;
                                  if (dummy.is_done) {
                                    if (dummy.is_tie == 1) {
                                      opponent1score = 1;
                                      opponent2score = 1;
                                    } else if (dummy.winner == dummy.opponent1) {
                                      opponent1score = 1;
                                      opponent2score = 0;
                                    } else if (dummy.winner == dummy.opponent2) {
                                      opponent1score = 0;
                                      opponent2score = 1;
                                    }
                                  }


                                  scores[dummy_index] = [opponent1score, opponent2score];
                                });

                                callback(scores);
                              }

                              var getBracket = function(callback) {
                                var newURL = window.location.protocol + "://" + window.location.host;
                                var pathArray = window.location.pathname.split('/');
                                var sfID = pathArray[6];

                                if (sfID == 'stage_format')
                                  sfID = pathArray[7];

                                $.get('/stage_format/' + sfID, function(data) {
                                  if (data.status == "success") {
                                    callback(data.bracket);
                                  } else {
                                    console.log(data);
                                  }
                                });
                              }

                              $(document).ready(function() {
                                getBracket(function(minimalData) {
                                  console.log(minimalData);
                                  $('#minimal .bracket').bracket(minimalData, {
                                    teams: opp
                                  });
                                });
                              });

                            </script>


    @endif

@endsection