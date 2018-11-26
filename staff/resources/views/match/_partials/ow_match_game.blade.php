<div class="modal fade" id="owMatchGame" data-match-game-id="">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Add overwatch match game</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="match_date">Match Date</label>
                    <div class='input-group date match-date-holder' id='matchDateHolder'>

                        @if($match && $match->opponent1_details)
                            <input type="text" class="form-control" name="match_date" id="match_date" />
                        @else
                            <input type="text" class="form-control" name="match_date" id="match_date" />
                        @endif

                        <span class="input-group-addon">
                                              <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 text-center">
                        <h4 class="text-center">
                            <span data-ng-bind="match.opponent1_details.name"></span>
                            vs
                            <span data-ng-bind="match.opponent2_details.name"></span></h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group">
                            <label for="opponent1_members">@{{ match.opponent1_details.name }}
                                Players</label>
                            <select name="opponent1_members[]" id="opponent1_members" class="select2" multiple style="width: 100%">
                                @if($match && $match->opponent1_details && $match->opponent1_details->roster)
                                    @foreach($match->opponent1_details->roster as $player)
                                        <option value="{{$player->id}}"
                                                @if(in_array($player->id, (array)$match->opponent1_members)) selected @endif>{{$player->nickname}}</option>

                                    @endforeach
                                @else
                                    <option data-ng-value="player.id"
                                            data-ng-repeat="player in match.opponent1_details.roster">@{{ player.nickname }}</option>
                                @endif
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
                                @if($match && $match->opponent2_details && $match->opponent2_details->roster)
                                    @foreach($match->opponent2_details->roster as $player)
                                        <option value="{{$player->id}}"
                                                @if(in_array($player->id, (array)$match->opponent2_members)) selected @endif>{{$player->nickname}}</option>

                                    @endforeach
                                @else
                                    <option data-ng-value="player.id"
                                            data-ng-repeat="player in match.opponent2_details.roster">@{{ player.nickname }}</option>
                                @endif
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="opponent2_standins">@{{ match.opponent2_details.name }} Stand-ins</label>
                            <input name="opponent2_standins[]" id="opponent2_standins" style="width: 100%" />
                        </div>
                        @if(count($stage->tournament->maps))
                            <div class="form-group">
                                <label for="map_id">Map</label>
                                <select name="map_id" id="ow-map-select" title="map" class="form-control" style="width: 100%">
                                    <option value="" data-map-type="not_selected" disabled selected>Select the map</option>
                                    @foreach($stage->tournament->maps as $map)
                                        <option data-map-type="{{$map->type}}" value="{{$map->id}}">{{$map->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="form-group">
                            <label for="game_number_edit">Map number</label>
                            <input type="text" class="form-control" name="game_number" title="Game number" value="" />
                        </div>
                        <div class="row map-control-type"  style="display: none">
                            <div class="col-xs-12">
                                <div class="panel panel-default" data-ng-repeat="game in matchGameRounds">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-xs-3 text-right">
                                                <span style="margin-top: 5px; display: block">Team 1 - Score</span>
                                            </div>
                                            <div class="col-xs-2">
                                                <input type="text"
                                                       name="team1_score"
                                                       class="form-control input-sm"
                                                       value=""
                                                       data-ng-model="game.team1_score"
                                                       data-ng-value="game.team1_score"
                                                       title="Team 1 score">
                                            </div>
                                            <div class="col-xs-3 text-right">
                                                <span style="margin-top: 5px; display: block">Team 2 - Score</span>
                                            </div>
                                            <div class="col-xs-2">
                                                <input type="text"
                                                       name="team2_score"
                                                       data-ng-model="game.team2_score"
                                                       class="form-control input-sm"
                                                       value=""
                                                       data-ng-value="game.team2_score"
                                                       title="Team 1 score">
                                            </div>
                                            <div class="col-xs-2">
                                                <button type="button"
                                                        data-ng-click="matchGameRounds.splice($index,1)"
                                                        class="btn btn-danger btn-xs pull-right"><i
                                                            class="fa fa-trash"></i></button>
                                            </div>

                                        </div>

                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-xs-12">
                                        <button type="button" class="btn btn-default btn-xs" data-ng-click="matchGameRounds.push({})"><i class="fa fa-plus"></i>
                                            Add match result
                                        </button>
                                        <br/><br/> {{-- Add some space under the button --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row map-other-type" style="display: none">
                            <div class="col-xs-12">
                                <div class="panel panel-default" data-ng-repeat="game in owMatchNonControlGameRound">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-xs-3 text-right">
                                                <span style="margin-top: 5px; display: block">Team 1 - Attack Time</span>
                                            </div>
                                            <div class="col-xs-2">
                                                <input type="text"
                                                       name="team1_time"
                                                       class="form-control input-sm"
                                                       value=""
                                                       data-ng-model="game.attack_time_op1"
                                                       data-ng-value="game.attack_time_op1"
                                                       title="Team 1 time">
                                            </div>
                                            <div class="col-xs-2 text-right">
                                                <span style="margin-top: 5px; display: block">Checkpoints</span>
                                            </div>
                                            <div class="col-xs-2">
                                                <input type="text"
                                                       name="team1_score"
                                                       class="form-control input-sm"
                                                       value=""
                                                       data-ng-model="game.score_op1"
                                                       data-ng-value="game.score_op1"
                                                       title="Team 1 score">
                                            </div>
                                            <div class="col-xs-1 text-right">
                                                <span style="margin-top: 5px; display: block">Meters</span>
                                            </div>
                                            <div class="col-xs-2">
                                                <input type="text"
                                                       name="team1_meters"
                                                       class="form-control input-sm"
                                                       value=""
                                                       data-ng-model="game.meters_op1"
                                                       data-ng-value="game.meters_op1"
                                                       title="Team 1 meters">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-xs-3 text-right">
                                                <span style="margin-top: 5px; display: block">Team 2 - Attack Time</span>
                                            </div>
                                            <div class="col-xs-2">
                                                <input type="text"
                                                       name="team2_time"
                                                       class="form-control input-sm"
                                                       value=""
                                                       data-ng-model="game.attack_time_op2"
                                                       data-ng-value="game.attack_time_op2"
                                                       title="Team 2 time">
                                            </div>
                                            <div class="col-xs-2 text-right">
                                                <span style="margin-top: 5px; display: block">Checkpoints</span>
                                            </div>
                                            <div class="col-xs-2">
                                                <input type="text"
                                                       name="team2_score"
                                                       class="form-control input-sm"
                                                       value=""
                                                       data-ng-model="game.score_op2"
                                                       data-ng-value="game.score_op2"
                                                       title="Team 2 score">
                                            </div>
                                            <div class="col-xs-1 text-right">
                                                <span style="margin-top: 5px; display: block">Meters</span>
                                            </div>
                                            <div class="col-xs-2">
                                                <input type="text"
                                                       name="team2_meters"
                                                       class="form-control input-sm"
                                                       value=""
                                                       data-ng-model="game.meters_op2"
                                                       data-ng-value="game.meters_op2"
                                                       title="Team 2 meters">
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>
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
                                    <input type="checkbox" value="true" id="walkover" name="walkover"
                                           data-ng-checked="match_game.walkover==true">
                                    Yes
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="final_result">Map Winner:</label>

                            @if($match && $match->opponent1_details)
                                <select ng-model="matchGameWinner"
                                        ng-options="i.value as i.label for i in teams" name="final_result"
                                        id="final_result" class="form-control">
                                </select>
                            @else
                                <select name="final_result" id="final_result" class="form-control">
                                    <option data-ng-selected="!match.winner"></option>
                                    <option data-ng-value="match.opponent1_details.id"
                                            data-ng-selected="match.winner==match.opponent1_details.id">@{{ match.opponent1_details.name }}</option>
                                    <option data-ng-value="match.opponent2_details.id"
                                            data-ng-selected="match.winner==match.opponent2_details.id">@{{ match.opponent2_details.name }}</option>
                                    <option value="draw" data-ng-selected="match.is_tie==true">Draw</option>
                                </select>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" data-match-id="" data-match-game-id="" data-ng-click="saveOwMatchGame()">Save changes
                </button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

@section('scripts')
    @parent
    <script type="text/javascript">
        $('#ow-map-select').on('change', function (e) {
          var optionSelected = $("option:selected", this);
          if($(optionSelected).data('map-type') == 'control') {
            $('.map-control-type').show();
            $('.map-other-type').hide();
          } else {
            $('.map-control-type').hide();
            $('.map-other-type').show();
          }
          if($(optionSelected).data('map-type') == 'not_selected') {
            $('.map-control-type').hide();
            $('.map-other-type').hide();
          }
        });
    </script>
    @endsection