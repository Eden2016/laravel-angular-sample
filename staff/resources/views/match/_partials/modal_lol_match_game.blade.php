<?php
$champions = \App\Champion::all();
?>
            <div class="modal fade" id="lolMatchGame">
                <div class="modal-dialog modal-md">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Add/edit match game</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="row">
                                        <div class="col-xs-6">
                                            <h4 class="text-left">
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" name="radiant_team" id="radiant_team1" value="{{$match->opponent1_details->id}}" @if($match->opponent1_details->id == $match->radiant_team) checked @endif>
                                                        {{$match->opponent1_details ? $match->opponent1_details->name : 'No team'}}
                                                    </label>
                                                </div>
                                            </h4>
                                        </div>
                                        <div class="col-xs-6">
                                            <h4 class="text-left">
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" name="radiant_team" id="radiant_team2" value="{{$match->opponent2_details->id}}" @if($match->opponent2_details->id == $match->radiant_team) checked @endif>
                                                        {{$match->opponent2_details ? $match->opponent2_details->name : 'No team'}}
                                                    </label>
                                                </div>

                                            </h4>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <small class="help-block">Use radio buttons next to team name to select radiant team</small>
                                        </div>
                                    </div>
                                <div class="col-xs-6">
                                    <div class="form-group">
                                        <label for="opponent1_bans">Bans</label>
                                        <select name="opponent1_bans[]" id="opponent1_bans" class="select2" multiple style="width: 100%">
                                        @foreach(\App\Champion::all() as $champ)
                                            <option value="{{$champ->id}}">{{$champ->name}}</option>
                                        @endforeach
                                    </select>
                                    </div>

                                    <div class="row">
                                        @if($match->opponent1_details)
                                        <div class="col-xs-6">
                                            <div class="form-group">
                                                <label for="opponent1_player1">Player 1</label>
                                                <select name="opponent1_player1" id="opponent1_player1" class="select2" style="width: 100%">
                                                @foreach($match->opponent1_details->roster as $player)
                                                    <option value="{{$player->id}}">{{$player->nickname}}</option>
                                                @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="opponent1_player2">Player 2</label>
                                                <select name="opponent1_player2" id="opponent1_player2" class="select2" style="width: 100%">
                                                @foreach($match->opponent1_details->roster as $player)
                                                    <option value="{{$player->id}}">{{$player->nickname}}</option>
                                                @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="opponent1_player3">Player 3</label>
                                                <select name="opponent1_player3" id="opponent1_player3" class="select2" style="width: 100%">
                                                @foreach($match->opponent1_details->roster as $player)
                                                    <option value="{{$player->id}}">{{$player->nickname}}</option>
                                                @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="opponent1_player4">Player 4</label>
                                                <select name="opponent1_player4" id="opponent1_player4" class="select2" style="width: 100%">
                                                @foreach($match->opponent1_details->roster as $player)
                                                    <option value="{{$player->id}}">{{$player->nickname}}</option>
                                                @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="opponent1_player5">Player 5</label>
                                                <select name="opponent1_player5" id="opponent1_player5" class="select2" style="width: 100%">
                                                @foreach($match->opponent1_details->roster as $player)
                                                    <option value="{{$player->id}}">{{$player->nickname}}</option>
                                                @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xs-6">
                                            <div class="form-group">
                                                <label for="opponent1_picks1">Pick</label>
                                                <select name="opponent1_picks1" id="opponent1_picks1" class="select2" style="width: 100%">
                                                    <option></option>
                                                @foreach(\App\Champion::all() as $champ)
                                                    <option value="{{$champ->id}}">{{$champ->name}}</option>
                                                @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="opponent1_picks2">Pick</label>
                                                <select name="opponent1_picks2" id="opponent1_picks2" class="select2" style="width: 100%">
                                                    <option></option>
                                                @foreach(\App\Champion::all() as $champ)
                                                    <option value="{{$champ->id}}">{{$champ->name}}</option>
                                                @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="opponent1_picks3">Pick</label>
                                                <select name="opponent1_picks3" id="opponent1_picks3" class="select2" style="width: 100%">
                                                    <option></option>
                                                @foreach(\App\Champion::all() as $champ)
                                                    <option value="{{$champ->id}}">{{$champ->name}}</option>
                                                @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="opponent1_picks4">Pick</label>
                                                <select name="opponent1_picks4" id="opponent1_picks4" class="select2" style="width: 100%">
                                                    <option></option>
                                                @foreach(\App\Champion::all() as $champ)
                                                    <option value="{{$champ->id}}">{{$champ->name}}</option>
                                                @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="opponent1_picks5">Pick</label>
                                                <select name="opponent1_picks5" id="opponent1_picks5" class="select2" style="width: 100%">
                                                    <option></option>
                                                @foreach(\App\Champion::all() as $champ)
                                                    <option value="{{$champ->id}}">{{$champ->name}}</option>
                                                @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        @endif
                                    </div>


                                    <div class="form-group">
                                        <label for="opponent1_standins">Stand-ins</label>
                                        <input name="opponent1_standins[]" style="width: 100%" />
                                    </div>
                                </div>
                                <div class="col-xs-6">
                                    <div class="form-group">
                                        <label for="opponent2_bans">Bans</label>
                                        <select name="opponent2_bans[]" id="opponent2_bans" class="select2" multiple style="width: 100%">
                                        @foreach(\App\Champion::all() as $champ)
                                            <option value="{{$champ->id}}">{{$champ->name}}</option>
                                        @endforeach
                                    </select>
                                    </div>

                                    @if($match->opponent2_details)
                                    <div class="row">
                                        <div class="col-xs-6">
                                            <div class="form-group">
                                                <label for="opponent2_player1">Player 1</label>
                                                <select name="opponent2_player1" id="opponent2_player1" class="select2" style="width: 100%">
                                                @foreach($match->opponent2_details->roster as $player)
                                                    <option value="{{$player->id}}">{{$player->nickname}}</option>
                                                @endforeach
                                            </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="opponent2_player2">Player 2</label>
                                                <select name="opponent2_player2" id="opponent2_player2" class="select2" style="width: 100%">
                                                @foreach($match->opponent2_details->roster as $player)
                                                    <option value="{{$player->id}}">{{$player->nickname}}</option>
                                                @endforeach
                                             </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="opponent2_player3">Player 3</label>
                                                <select name="opponent2_player3" id="opponent2_player3" class="select2" style="width: 100%">
                                                @foreach($match->opponent2_details->roster as $player)
                                                    <option value="{{$player->id}}">{{$player->nickname}}</option>
                                                @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="opponent2_player4">Player 4</label>
                                                <select name="opponent2_player4" id="opponent2_player4" class="select2" style="width: 100%">
                                                @foreach($match->opponent2_details->roster as $player)
                                                    <option value="{{$player->id}}">{{$player->nickname}}</option>
                                                @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="opponent2_player5">Player 5</label>
                                                <select name="opponent2_player5" id="opponent2_player5" class="select2" style="width: 100%">
                                                @foreach($match->opponent2_details->roster as $player)
                                                    <option value="{{$player->id}}">{{$player->nickname}}</option>
                                                @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-xs-6">
                                            <div class="form-group">
                                                <label for="opponent2_picks1">Pick</label>
                                                <select name="opponent2_picks1" id="opponent2_picks1" class="select2" style="width: 100%">
                                                    <option></option>
                                                @foreach($champions as $champ)
                                                    <option value="{{$champ->id}}">{{$champ->name}}</option>
                                                @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="opponent2_picks2">Pick</label>
                                                <select name="opponent2_picks2" id="opponent2_picks2" class="select2" style="width: 100%">
                                                    <option></option>
                                                @foreach($champions as $champ)
                                                    <option value="{{$champ->id}}">{{$champ->name}}</option>
                                                @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="opponent2_picks3">Pick</label>
                                                <select name="opponent2_picks3" id="opponent2_picks3" class="select2" style="width: 100%">
                                                    <option></option>
                                                @foreach($champions as $champ)
                                                    <option value="{{$champ->id}}">{{$champ->name}}</option>
                                                @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="opponent2_picks4">Pick</label>
                                                <select name="opponent2_picks4" id="opponent2_picks4" class="select2" style="width: 100%">
                                                    <option></option>
                                                @foreach($champions as $champ)
                                                    <option value="{{$champ->id}}">{{$champ->name}}</option>
                                                @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="opponent2_picks5">Pick</label>
                                                <select name="opponent2_picks5" id="opponent2_picks5" class="select2" style="width: 100%">
                                                    <option></option>
                                                @foreach($champions as $champ)
                                                    <option value="{{$champ->id}}">{{$champ->name}}</option>
                                                @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>

                                    <div class="form-group">
                                        <label for="opponent2_standins">Stand-ins</label>
                                        <input name="opponent2_standins[]" style="width: 100%" />
                                    </div>
                                    @endif
                                </div>
                                <div class="clearfix"></div>

                                    <div class="form-group">
                                        <label for="game_number_edit">Map number</label>
                                        <input type="text" class="form-control" name="game_number" value="" />
                                    </div>
                                    <div class="form-group">
                                        <label for="streams">Streams:</label>
                                        <select class="select2" name="streams[]" id="streams" multiple style="width: 100%">
                                        @foreach(\App\Models\Streams::all() as $stream)
                                            <option value="{{$stream->id}}">{{$stream->title}}</option>
                                        @endforeach
                                    </select>
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
                                        <select ng-model="matchGameWinner" ng-options="i.value as i.label for i in teams" name="final_result" id="final_result" class="form-control">
                                        </select>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" data-match-id="" data-match-game-id="" data-ng-click="saveLolMatchGame()">Save changes
                        </button>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!-- /.modal -->