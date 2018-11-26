<?php
$champions = \App\Champion::all();
?>
            <div class="modal fade" id="lolMatchGame">
                <div class="modal-dialog modal-md">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Add match game</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="row">
                                    <div class="col-xs-6">
                                        <h4 class="text-left">@{{ match.opponent1_details.name }}</h4>
                                    </div>
                                    <div class="col-xs-6">
                                        <h4 class="text-left">@{{ match.opponent2_details.name }}</h4>
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
                                        <div class="col-xs-6">
                                            <div class="form-group">
                                                <label for="opponent1_player1">Player 1</label>
                                                <select name="opponent1_player1" id="opponent1_player1" class="select2" style="width: 100%">
                                                    <option data-ng-value="player.id"
                                                            data-ng-repeat="player in match.opponent1_details.roster"
                                                            data-ng-selected="player.id==match.opponent1_members[0]">@{{ player.nickname }}</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="opponent1_player2">Player 2</label>
                                                <select name="opponent1_player2" id="opponent1_player2" class="select2" style="width: 100%">
                                                    <option data-ng-value="player.id"
                                                            data-ng-repeat="player in match.opponent1_details.roster"
                                                            data-ng-selected="player.id==match.opponent1_members[1]">@{{ player.nickname }}</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="opponent1_player3">Player 3</label>
                                                <select name="opponent1_player3" id="opponent1_player3" class="select2" style="width: 100%">
                                                    <option data-ng-value="player.id"
                                                            data-ng-repeat="player in match.opponent1_details.roster"
                                                            data-ng-selected="player.id==match.opponent1_members[2]">@{{ player.nickname }}</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="opponent1_player4">Player 4</label>
                                                <select name="opponent1_player4" id="opponent1_player4" class="select2" style="width: 100%">
                                                    <option data-ng-value="player.id"
                                                            data-ng-repeat="player in match.opponent1_details.roster"
                                                            data-ng-selected="player.id==match.opponent1_members[3]">@{{ player.nickname }}</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="opponent1_player5">Player 5</label>
                                                <select name="opponent1_player5" id="opponent1_player5" class="select2" style="width: 100%">
                                                    <option data-ng-value="player.id"
                                                            data-ng-repeat="player in match.opponent1_details.roster"
                                                            data-ng-selected="player.id==match.opponent1_members[4]">@{{ player.nickname }}</option>
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

                                    <div class="row">
                                        <div class="col-xs-6">
                                            <div class="form-group">
                                                <label for="opponent2_player1">Player 1</label>
                                                <select name="opponent2_player1" id="opponent2_player1" class="select2" style="width: 100%">
                                                    <option data-ng-value="player.id"
                                                            data-ng-repeat="player in match.opponent2_details.roster"
                                                            data-ng-selected="player.id==match.opponent2_members[0]">@{{ player.nickname }}</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="opponent2_player2">Player 2</label>
                                                <select name="opponent2_player2" id="opponent2_player2" class="select2" style="width: 100%">
                                                    <option data-ng-value="player.id"
                                                            data-ng-repeat="player in match.opponent2_details.roster"
                                                            data-ng-selected="player.id==match.opponent2_members[1]">@{{ player.nickname }}</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="opponent2_player3">Player 3</label>
                                                <select name="opponent2_player3" id="opponent2_player3" class="select2" style="width: 100%">
                                                    <option data-ng-value="player.id"
                                                            data-ng-repeat="player in match.opponent2_details.roster"
                                                            data-ng-selected="player.id==match.opponent2_members[2]">@{{ player.nickname }}</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="opponent2_player4">Player 4</label>
                                                <select name="opponent2_player4" id="opponent2_player4" class="select2" style="width: 100%">
                                                    <option data-ng-value="player.id"
                                                            data-ng-repeat="player in match.opponent2_details.roster"
                                                            data-ng-selected="player.id==match.opponent2_members[3]">@{{ player.nickname }}</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="opponent2_player5">Player 5</label>
                                                <select name="opponent2_player5" id="opponent2_player5" class="select2" style="width: 100%">
                                                    <option data-ng-value="player.id"
                                                            data-ng-repeat="player in match.opponent2_details.roster"
                                                            data-ng-selected="player.id==match.opponent2_members[4]">@{{ player.nickname }}</option>
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
                                        <select name="final_result" class="form-control">
                                            <option selected="selected"></option>
                                            <option data-ng-value="match.opponent1_details.id">@{{ match.opponent1_details.name }}</option>
                                            <option data-ng-value="match.opponent2_details.id">@{{ match.opponent2_details.name }}</option>
                                            <option value="draw">Draw</option>
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