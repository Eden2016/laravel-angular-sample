@extends('layouts.default')

@section('content')

<?php
$winner = $match->getWinner;
if ($winner) {
    $matchWinnerName = $winner->name;
} else {
    $matchWinnerName = "";
}
?>

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Matches</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{groute('/')}}">Home</a>
            </li>
            <li>
                <a href="{{groute('event.view', ['eventId' => $match->tournament->event->id])}}">{{$match->tournament->event->name}}</a>
            </li>
            <li>
                <a href="{{groute('tournament.view', ['tournamentId' => $match->tournament->id])}}">{{$match->tournament->name}}</a>
            </li>
            <li>
                <a href="{{groute('stage', ['tournamentId' => $match->stageRound->stageFormat->stage->tournament_id, 'stageId' => $match->stageRound->stageFormat->stage->id])}}">{{$match->stageRound->stageFormat->stage->name}}</a>
            </li>
            <li>
                <a href="{{groute('stages.formats.view', ['sfId' => $match->stageRound->stageFormat->id, 'tournamentsId'=>$match->stageRound->stageFormat->stage->tournament->id, 'stageId' => $match->stageRound->stageFormat->stage->id])}}">{{$match->stageRound->stageFormat->name}}</a>
            </li>
            <li class="active">
                <strong>{{ $match->opponent1_details->name }} vs {{ $match->opponent2_details->name }}</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2">

    </div>
</div>
<div class="wrapper wrapper-content animated fadeInRight" data-ng-app="App" data-ng-controller="matchGameController">
    <div class="row">
        <div class="col-sm-8">
            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif @if (!isset($errorMessage))
                <!-- Create Post Form -->
                <form action="{{groute('match.save')}}" method="post">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id" id="matchId" value="{{ $match->id }}">
                    <input type="hidden" name="tournament" value="{{ $stage->tournament_id }}">
                    <input type="hidden" name="stage" value="{{ $stage->id }}">
                    <input type="hidden" name="round" value="{{ $round->id }}">
                    <input type="hidden" name="sf" value="{{ $sf->id }}">


                    <div class="form-group">
                        <label for="opponent1name">Opponent1 Name</label>
                        <input type="text" class="form-control" name="opponent1name" id="opponent1name"
                               value="{{ old('opponent1name') ? : $match->opponent1_details ? $match->opponent1_details->name : ''}}"
                               autocomplete="off"/>
                        <input type="hidden" name="opponent1" id="opponent1"
                               value="{{ old('opponent1') ? : $match->opponent1 }}"/>
                        <div class="leagueSuggestions" id="suggestion1">
                            <ul id="leagueSuggestions1">

                            </ul>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="opponent2name">Opponent2 Name</label>
                        <input type="text" class="form-control" name="opponent2name" id="opponent2name"
                               value="{{ old('opponent2name') ? : $match->opponent2_details ? $match->opponent2_details->name : '' }}"
                               autocomplete="off"/>
                        <input type="hidden" name="opponent2" id="opponent2"
                               value="{{ old('opponent2') ? : $match->opponent2 }}"/>
                        <div class="leagueSuggestions" id="suggestion2">
                            <ul id="leagueSuggestions2">

                            </ul>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="winnername">Winner</label>
                        <input type="text" class="form-control" name="winnername" id="winnername"
                               value="{{ old('winnername') ? : $matchWinnerName }}" autocomplete="off"/>
                        <input type="hidden" name="winner" id="winner" value="{{ old('winner') ? : $match->winner }}"/>
                        <div class="leagueSuggestions" id="suggestion3">
                            <ul id="leagueSuggestions3">

                            </ul>
                        </div>
                    </div>
                    <label for="start">Start Date</label>
                    <div class='input-group date form-group' id='startHolder'>
                        <input type="text" class="form-control" name="start" id="start"
                               value="{{ old('start') ? : ($match->start ? date_convert($match->start, 'UTC', Auth::user()->timezone, 'Y-m-d H:i:s', 'Y-m-d H:i') : '') }}"/>
                        <span class="input-group-addon">
                              <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>

                    <div class="form-group">
                        <label for="streams">Streams:</label>
                        <select class="select2 all_streams" name="streams[]" id="streams" multiple style="width: 100%">
                            @foreach(\App\Models\Streams::all() as $stream)
                                <option value="{{$stream->id}}" data-link="{{$stream->link}}"
                                        @if(in_array($stream->id, $match->all_streams_ids)) selected @endif>
                                   {{$stream->title}}</option>
                            @endforeach
                        </select>
                    </div>

                    @permission('manage_toutou')
                    <div class="form-group">
                        <label for="toutou_match">Link with TouTou Match</label>
                        <input name="toutou_match" id="toutou_match" />
                    </div>
                    @endpermission

                    <button type="submit" class="btn btn-default dim">Edit match
                    </button> @if (request()->currentGame->slug == 'csgo')
                        <button type="button" class="btn btn-primary dim" data-ng-click="loadDrafts({{ $match->id }})">
                            Add drafts
                        </button> @endif
                </form>

                <div style="padding-top:20px;margin-left:10px;width:140px;float:right;">
                    <button type="button" class="btn btn-primary dim"
                            @if(request()->currentGame->slug=='csgo') data-ng-click="newMatchGame()"
                            @elseif(request()->currentGame->slug=='lol') data-ng-click="newLolMatchGame()"
                            @elseif(request()->currentGame->slug=='dota2') data-ng-click="newDota2MatchGame()"
                            @elseif(request()->currentGame->slug=='overwatch') data-ng-click="newOwMatchGame(<?=$match->id?>)"
                            @else data-toggle="modal" data-target="#addMatchGameModal" @endif>Add match game
                    </button>
                </div>
                <h3>Match Games</h3>
                <hr/>
                <div class="row">
                    <ul class="nav nav-tabs">
                        @if (count($matchGames))
                            <?php $i = 1 ?> @foreach($matchGames as $mg)
                                <li><a data-toggle="tab" href="#tab-{{$i}}"><span
                                                class="badge badge-danger">Game {{$i}}</span></a></li>
                                <?php $i++ ?> @endforeach @endif
                    </ul>
                    <div class="tab-content">
                        @if (count($matchGames))
                            <?php $i = 1 ?> @foreach($matchGames as $mg)
                                <div id="tab-{{$i}}" class="tab-pane">
                                    <div class="panel-body">
                                        <div class="col-md-4">
                                            @if($match->opponent1_details)
                                                <div class="list-group">
                                                    <div class="list-group-item active">
                                                        <h4 class="list-group-item-heading">{{$match->opponent1_details->name}}</h4>
                                                    </div>
                                                    @foreach(\App\Individual::whereIn('id', $mg->opponent1_members)->get() as $player)
                                                        <p class="list-group-item list-group-item-info">{{$player->nickname}}</p>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-4 text-center">
                                            @if($mg->map)
                                                <div class="match-map-img {{$mg->map->name}}"
                                                    @if($mg->map->image)
                                                        style="background-image: url('{{url('uploads/'.$mg->map->image)}}')"
                                                    @endif
                                                >
                                                    <span>{{$mg->map->name}}</span>
                                                </div>
                                            @endif

                                            @if(request()->currentGameSlug == 'overwatch')
                                                    <?php
                                                    $opponent1Score = 0;
                                                    $opponent2Score = 0;
                                                    ?>
                                                        @foreach($mg->rounds as $key => $round)
                                                            @if(isset($round['attack_time_op1']))
                                                                <?php
                                                                $opponent1Score += isset($round['score_op1']) ? $round['score_op1'] : 0;
                                                                $opponent2Score += isset($round['score_op2']) ? $round['score_op2'] : 0;
                                                                ?>
                                                            @if($round['meters_op1'] == '0m' && $round['meters_op2'] == "0m")
                                                                        <strong>Opponent1:</strong>
                                                                        <p>
                                                                            Checkpoints: {{$round['score_op1']}}
                                                                        </p>
                                                                        <strong>Opponent2:</strong>
                                                                        <p>
                                                                            Checkpoints: {{$round['score_op2']}}
                                                                        </p>
                                                                    @else

                                                            <strong>Opponent1:</strong>
                                                            <p>
                                                                Attack time: {{$round['attack_time_op1']}},
                                                                Checkpoints: {{$round['score_op1']}},
                                                                Meters: {{$round['meters_op1']}}
                                                            </p>
                                                            <strong>Opponent2:</strong>
                                                            <p>
                                                                Attack time: {{$round['attack_time_op2']}},
                                                                Checkpoints: {{$round['score_op2']}},
                                                                Meters: {{$round['meters_op2']}}
                                                            </p>
                                                                    @endif
                                                            @else
                                                                <?php
                                                                    $opponent1Score += ($round['team1_score'] > $round['team2_score']) ? 1 : 0;
                                                                    $opponent2Score += ($round['team2_score'] > $round['team1_score']) ? 1 : 0;
                                                                ?>
                                                                <strong>Round: {{$key + 1}}</strong>
                                                                <p>
                                                                    {{ isset($round['team1_score']) ? $round['team1_score'] : 0 }}
                                                                    :
                                                                    {{ isset($round['team2_score']) ? $round['team2_score'] : 0 }}
                                                                </p>
                                                            @endif
                                                        @endforeach
                                                        <p><strong>Final Score</strong></p>
                                                        <p>
                                                            <strong class="badge badge-primary">
                                                                {{ $opponent1Score }} : {{ $opponent2Score }}
                                                            </strong>
                                                        </p>
                                            @else
                                            <?php
                                            $half = 'Fist Half:';
                                            $opponent1Score = 0;
                                            $opponent2Score = 0;
                                            ?>
                                            @foreach($mg->rounds as $round)
                                                <?php
                                                $opponent1Score += isset($round['team1_score']) ? $round['team1_score'] : 0;
                                                $opponent2Score += isset($round['team2_score']) ? $round['team2_score'] : 0;
                                                ?>
                                                <strong>{{ $half }}</strong>
                                                <p>
                                                    {{ isset($round['team1_score']) ? $round['team1_score'] : 0 }}
                                                    :
                                                    {{ isset($round['team2_score']) ? $round['team2_score'] : 0 }}
                                                </p>
                                                <?php $half = 'Second Half:' ?>
                                            @endforeach
                                            <p><strong>Final Score</strong></p>
                                            <p>
                                                <strong class="badge badge-primary">
                                                    {{ $opponent1Score }} : {{ $opponent2Score }}
                                                </strong>
                                            </p>
                                            @endif
                                            <p>
                                                <button @if(request()->currentGame->slug=='csgo') data-ng-click="loadMatchGame(<?=$mg->id?>)"
                                                        @elseif(request()->currentGame->slug=='lol') data-ng-click="loadLolMatchGame(<?=$mg->id?>)"
                                                        @elseif(request()->currentGame->slug=='dota2') data-ng-click="loadDota2MatchGame(<?=$mg->id?>)"
                                                        @elseif(request()->currentGame->slug=='overwatch') data-ng-click="loadOwMatchGame(<?=$mg->id?>)"
                                                        @else class="editMg btn btn-default dim m-r-none"
                                                        @endif data-mgid="{{ $mg->id }}">Edit
                                                </button>
                                            </p>
                                            <p>
                                                <button type="button" data-ng-click="deleteMatchGame(<?=$mg->id?>)"
                                                        class="btn btn-danger">Delete
                                                </button>
                                            </p>
                                        </div>
                                        <div class="col-md-4">
                                            @if($match->opponent2_details)
                                                <div class="list-group">
                                                    <div class="list-group-item active">
                                                        <h4 class="list-group-item-heading">{{$match->opponent2_details->name}}</h4>
                                                    </div>
                                                    @foreach(\App\Individual::whereIn('id', $mg->opponent2_members)->get() as $player)
                                                        <p class="list-group-item list-group-item-info">{{$player->nickname}}</p>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <?php $i++ ?>
                                </div>
                            @endforeach @endif
                    </div>
                </div>
                <!-- Add draft Modal -->
                <div class="modal fade" id="addDraftsModal" tabindex="-1" role="dialog"
                     aria-labelledby="addDraftsModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                            aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel">Add drafts</h4>
                            </div>
                            <div class="modal-body">
                                <form>
                                    <div class="adding-draft-modal">
                                        <h3>Adding draft modal</h3>
                                        <div class="form-group">
                                            <label>Team:</label>
                                            <select class="form-control" id="draft_team">
                                                <option>{{ $match->opponent1_details ? $match->opponent1_details->name : '' }}</option>
                                                <option>{{ $match->opponent2_details ? $match->opponent2_details->name : '' }}</option>
                                            </select>
                                        </div>
                                        <!-- form group -->
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" value="1" id="draft_random"/> Random decider
                                            </label>
                                        </div>
                                        <div class="form-group">
                                            <label>Action</label>
                                            <div>
                                                <label>
                                                    <input type="radio" name="draft_actionRadio" value="picked"/> Pick
                                                </label>
                                                <label>
                                                    <input type="radio" name="draft_actionRadio" value="banned"/> Ban
                                                </label>
                                            </div>
                                        </div>
                                        <!-- form group -->
                                        <div class="form-group">
                                            <label>Map:</label>
                                            <select class="form-control" id="draft_map">
                                                @foreach($match->tournament->maps as $map)
                                                    <option value="{{ $map->name }}">{{ $map->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <!-- form group -->
                                        <button type="submit" class="btn btn-primary dim" data-ng-click="newDraft()">
                                            Save draft
                                        </button>
                                    </div>
                                    <!-- adding draft modal -->
                                </form>
                                <br/><br/>

                                <ul>
                                    <li ng-repeat="draft in matchDrafts">
                                        <span ng-if="!draft.random">@{{ draft.team }}</span>
                                        <span ng-if="draft.random">Random decider</span> @{{ draft.action }}
                                        <span>@{{ draft.map }}</span>
                                        <button type="button" data-ng-click="matchDrafts.splice($index,1)"
                                                class="btn btn-danger btn-xs pull-right"><i class="fa fa-trash"></i>
                                        </button>
                                    </li>
                                </ul>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary" ng-click="saveDrafts()">Save Drafts
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add match game Modal -->
                <div class="modal fade" id="addMatchGameModal" tabindex="-1" role="dialog"
                     aria-labelledby="addMatchGameModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                            aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel">Add match game</h4>
                            </div>
                            <div class="modal-body">
                                <form>
                                    <input type="hidden" name="matchid" id="matchid" value="{{ $match->id }}">
                                    <div class="form-group">
                                        <label for="steamid">Match SteamID</label>
                                        <input type="text" class="form-control" name="steamid" id="steamid"/>
                                    </div>
                                    <div class="form-group">
                                        <label for="opponent1_score">Opponent1 Score</label>
                                        <input type="text" class="form-control" name="opponent1_score"
                                               id="opponent1_score"/>
                                    </div>
                                    <div class="form-group">
                                        <label for="opponent2_score">Opponent2 Score</label>
                                        <input type="text" class="form-control" name="opponent2_score"
                                               id="opponent2_score"/>
                                    </div>
                                    @if($match->opponent1_details)
                                        <div class="form-group">
                                            <label for="opponent1_members">{{$match->opponent1_details->name}}
                                                Players</label>
                                            <select name="opponent1_members[]" id="opponent1_members" class="select2"
                                                    multiple style="width: 100%">
                                                @foreach($match->opponent1_details->roster as $player)
                                                    <option value="{{$player->id}}"
                                                            @if(in_array($player->id, (array)$match->opponent1_members)) selected @endif>{{$player->nickname}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif @if($match->opponent2_details)
                                        <div class="form-group">
                                            <label for="opponent2_members">{{$match->opponent2_details->name}}
                                                Players</label>
                                            <select name="opponent2_members[]" id="opponent2_members" class="select2"
                                                    multiple style="width: 100%">
                                                @foreach($match->opponent2_details->roster as $player)
                                                    <option value="{{$player->id}}"
                                                            @if(in_array($player->id, (array)$match->opponent2_members)) selected @endif>{{$player->nickname}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif @if(count($match->tournament->maps))
                                        <div class="form-group">
                                            <label for="map_id">Map</label>
                                            <select name="map_id" id="map_id" class="select2" style="width: 100%">
                                                @foreach($match->tournament->maps as $map)
                                                    <option value="{{$map->id}}">{{$map->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif
                                    <div class="form-group">
                                        <label for="game_number">Game number</label>
                                        <input type="text" class="form-control" name="game_number" id="game_number"/>
                                    </div>
                                    <label for="start_date">Start Date</label>
                                    <div class='input-group date' id='starDatetHolder'>
                                        <input type="text" class="form-control" name="start_date" id="start_date"/>
                                        <span class="input-group-addon">
                                      <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary" id="saveMatchGame">Save Match Game
                                </button>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Edit match game Modal -->
                <div class="modal fade" id="editMatchGameModal" tabindex="-1" role="dialog"
                     aria-labelledby="editMatchGameModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                            aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel">Edit match game</h4>
                            </div>
                            <div class="modal-body">
                                <form>
                                    <input type="hidden" name="matchid_edit" id="matchid_edit" value="{{ $match->id }}">
                                    <input type="hidden" name="id_edit" id="matchgameid">
                                    <div class="form-group">
                                        <label for="steamid_edit">Match SteamID</label>
                                        <input type="text" class="form-control" name="steamid_edit" id="steamid_edit"/>
                                    </div>
                                    <div class="form-group">
                                        <label for="opponent1_score_edit">Opponent1 Score</label>
                                        <input type="text" class="form-control" name="opponent1_score_edit"
                                               id="opponent1_score_edit"/>
                                    </div>
                                    <div class="form-group">
                                        <label for="opponent2_score_edit">Opponent2 Score</label>
                                        <input type="text" class="form-control" name="opponent2_score_edit"
                                               id="opponent2_score_edit"/>
                                    </div>
                                    @if($match->opponent1_details)
                                        <div class="form-group">
                                            <label for="opponent1_members">{{$match->opponent1_details->name}}
                                                Players</label>
                                            <select name="opponent1_members[]" id="opponent1_members" class="select2"
                                                    multiple style="width: 100%">
                                                @foreach($match->opponent1_details->roster as $player)
                                                    <option value="{{$player->id}}">{{$player->nickname}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif @if($match->opponent2_details)
                                        <div class="form-group">
                                            <label for="opponent2_members">{{$match->opponent2_details->name}}
                                                Players</label>
                                            <select name="opponent2_members[]" id="opponent2_members" class="select2"
                                                    multiple style="width: 100%">
                                                @foreach($match->opponent2_details->roster as $player)
                                                    <option value="{{$player->id}}">{{$player->nickname}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif @if(count($match->tournament->maps))
                                        <div class="form-group">
                                            <label for="map_id">Map</label>
                                            <select name="map_id" id="map_id" class="select2" style="width: 100%">
                                                @foreach($match->tournament->maps as $map)
                                                    <option value="{{$map->id}}">{{$map->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif
                                    <div class="form-group">
                                        <label for="game_number_edit">Game number</label>
                                        <input type="text" class="form-control" name="game_number_edit"
                                               id="game_number_edit"/>
                                    </div>
                                    <label for="start_date_edit">Start Date</label>
                                    <div class='input-group date' id='starDatetHolder'>
                                        <input type="text" class="form-control" name="start_date_edit"
                                               id="start_date_edit"/>
                                        <span class="input-group-addon">
                                      <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" id="deleteMatchGame" style="float:left">
                                    Delete Match Game
                                </button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary" id="saveMatchGame_edit">Save Match Game
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
        </div>


        <div class="modal fade" id="csGoMatchGame">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Add/edit match game</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="steamid_edit">Match SteamID</label>
                            <input type="text" class="form-control" name="steam" title="Match SteamID"/>
                        </div>
                        <div class="row">
                            <div class="col-xs-6">
                                <h4 class="text-right">{{$match->opponent1_details ? $match->opponent1_details->name : 'No team'}}</h4>
                            </div>
                            <div class="col-xs-6">
                                <h4>{{$match->opponent2_details ? $match->opponent2_details->name : 'No team'}}</h4>
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
                                                <input type="text" name="team1_score" class="form-control input-sm"
                                                       value="" data-ng-model="game.team1_score"
                                                       data-ng-value="game.team1_score" title="Team 1 score">
                                            </div>
                                            @if($match->opponent1_details && $match->opponent2_details)
                                                <div class="col-xs-3">
                                                    <div class="row">
                                                        <div class="col-xs-4">
                                                            <input type="radio" data-ng-model="game.ct"
                                                                   data-ng-checked="game.ct==<?=$match->opponent1_details->id?>"
                                                                   value="{{$match->opponent1_details->id}}">
                                                        </div>
                                                        <div class="col-xs-4">CT</div>
                                                        <div class="col-xs-4">
                                                            <input type="radio" data-ng-model="game.ct"
                                                                   data-ng-checked="game.ct==<?=$match->opponent2_details->id?>"
                                                                   value="{{$match->opponent2_details->id}}">
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="col-xs-2">
                                                <input type="text" name="team2_score" data-ng-model="game.team2_score"
                                                       class="form-control input-sm" value=""
                                                       data-ng-value="game.team2_score" title="Team 1 score">
                                            </div>
                                            <div class="col-xs-2">
                                                <button type="button" data-ng-click="matchGameRounds.splice($index,1)"
                                                        class="btn btn-danger btn-xs pull-right"><i
                                                            class="fa fa-trash"></i></button>
                                            </div>

                                        </div>

                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-xs-12">
                                        <button type="button" class="btn btn-default btn-xs"
                                                data-ng-click="matchGameRounds.push({})"><i class="fa fa-plus"></i>
                                            Add round
                                        </button>
                                        <br/><br/> {{-- Add some space under the button --}}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12">
                                @if($match->opponent1_details)
                                    <div class="form-group">
                                        <label for="opponent1_members">{{$match->opponent1_details->name}}
                                            Players</label>
                                        <select name="opponent1_members[]" id="opponent1_members" class="select2"
                                                multiple style="width: 100%">
                                            @foreach($match->opponent1_details->roster as $player)
                                                <option value="{{$player->id}}"
                                                        @if(in_array($player->id, (array)$match->opponent1_members)) selected @endif>{{$player->nickname}}</option>

                                            @endforeach
                                        </select>
                                    </div>


                                    <div class="form-group">
                                        <label for="opponent1_standins">{{$match->opponent1_details->name}}
                                            Stand-ins</label>
                                        <input name="opponent1_standins[]" id="opponent1_standins" style="width: 100%"/>
                                    </div>
                                @endif @if($match->opponent2_details)
                                    <div class="form-group">
                                        <label for="opponent2_members">{{$match->opponent2_details->name}}
                                            Players</label>
                                        <select name="opponent2_members[]" id="opponent2_members" class="select2"
                                                multiple style="width: 100%">
                                            @foreach($match->opponent2_details->roster as $player)
                                                <option value="{{$player->id}}"
                                                        @if(in_array($player->id, (array)$match->opponent2_members)) selected @endif>{{$player->nickname}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="opponent2_standins">{{ $match->opponent2_details->name }}
                                            Stand-ins</label>
                                        <input name="opponent2_standins[]" id="opponent2_standins" style="width: 100%"/>
                                    </div>
                                @endif @if(count($match->tournament->maps))
                                    <div class="form-group">
                                        <label for="map_id">Map</label>
                                        <select name="map_id" title="map" class="select2" style="width: 100%">
                                            <option></option>
                                            @foreach($match->tournament->maps as $map)
                                                <option value="{{$map->id}}">{{$map->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                                <div class="form-group">
                                    <label for="game_number_edit">Map number</label>
                                    <input type="text" class="form-control" name="game_number" value=""/>
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
                                        <select ng-model="matchGameWinner"
                                                ng-options="i.value as i.label for i in teams" name="final_result"
                                                id="final_result" class="form-control">
                                        </select>
                                    </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" data-match-game-id=""
                                data-ng-click="saveMatchGame()">Save changes
                        </button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->

        @include('match._partials.modal_lol_match_game')
        @include('match._partials.modal_dota2_match_game')

        @else
            <div class="alert alert-danger">{{ $errorMessage }}</div>
        @endif

        @include('match._partials.ow_match_game')
@endsection

@section('scripts_head')
    @parent

<script src="/bower_components/angular/angular.min.js" ></script>
<script >
    var app = angular.module('App', []);
    app.controller('matchGameController', function ($scope, $http) {
        $scope.matchGameRounds = [];
          $scope.owMatchNonControlGameRound = [{
            attack_time_op1: '00:00', score_op1: 0, meters_op1: '0m',
            attack_time_op2: '00:00', score_op2: 0, meters_op2: '0m'
          }];
        $scope.matchDrafts = [];
        $scope.teams = [];
        $scope.matchGameWinner = '';
        $scope.match_game = {};

        $scope.deleteMatchGame = function (match_game_id) {
            if (confirm('Are you sure?')) {
                $http.get('/match_game/delete/' + match_game_id)
                        .then(function () {
                            window.location.href = window.location.href;
                        });
            }
        };
        $scope.newMatchGame = function () {
            $scope.matchGameRounds = [];
            $('#csGoMatchGame [name="streams[]"], #csGoMatchGame [name="steam"], #csGoMatchGame [name="map_id"]')
                    .val('')
                    .trigger('change');

            setTimeout(function () {
                $('#opponent1_standins').select2('data', []);
                $('#opponent2_standins').select2('data', []);
            }, 500);

            $('#csGoMatchGame [data-match-game-id]').data('match-game-id', '');


            $('#csGoMatchGame [name="game_number"]').val('');
            $('#csGoMatchGame').modal('show');
        };
        $scope.loadMatchGame = function (game_id) {
            $http.get('/csgo/match_game/' + game_id)
                    .then(function (response) {
                        $scope.match_game = response.data.match_game;

                        if ($scope.match_game.is_tie == 1)
                            $scope.matchGameWinner = "draw";
                        else if ($scope.match_game.winner != 0)
                            $scope.matchGameWinner = $scope.match_game.winner;

                        $scope.teams = [
                            {
                                "value": $scope.match_game.match.opponent1_details.id,
                                "label": $scope.match_game.match.opponent1_details.name
                            },
                            {
                                "value": $scope.match_game.match.opponent2_details.id,
                                "label": $scope.match_game.match.opponent2_details.name
                            },
                            {"value": "draw", "label": "Draw"}
                        ];

                        $('#csGoMatchGame [name="streams[]"]').val(response.data.match_game.streams).trigger('change');
                        $('#csGoMatchGame [name="map_id"]').val(response.data.match_game.map_id).trigger('change');
                        $('#csGoMatchGame [name="opponent1_members[]"]').val(response.data.match_game.opponent1_members).trigger('change');
                        $('#csGoMatchGame [name="opponent2_members[]"]').val(response.data.match_game.opponent2_members).trigger('change');
                        $('#csGoMatchGame [name="steam"]').val(response.data.match_game.steam);
                        $scope.matchGameRounds = response.data.match_game.rounds_data ? response.data.match_game.rounds_data : [];
                        $('#csGoMatchGame [name="game_number"]').val(response.data.match_game.number);
                        $('#csGoMatchGame').modal('show');
                        $('#csGoMatchGame [data-match-game-id]').data('match-game-id', game_id);


                        setTimeout(function () {
                            $('#opponent1_standins').select2('data', response.data.opponent1_standins);
                            $('#opponent2_standins').select2('data', response.data.opponent2_standins);
                        }, 500);
                    });
        };


        $scope.saveMatchGame = function () {
            var data = {
                id: $('#csGoMatchGame [data-match-game-id]').data('match-game-id'),
                streams: $('#csGoMatchGame [name="streams[]"]').val(),
                map_id: $('#csGoMatchGame [name="map_id"]').val(),
                opponent1_members: $('#csGoMatchGame [name="opponent1_members[]"]').val(),
                opponent2_members: $('#csGoMatchGame [name="opponent2_members[]"]').val(),
                opponent1_id: $('#opponent1').val(),
                opponent2_id: $('#opponent2').val(),
                opponent1_standins: $('#csGoMatchGame [name="opponent1_standins[]"]').val(),
                opponent2_standins: $('#csGoMatchGame [name="opponent2_standins[]"]').val(),
                rounds: $scope.matchGameRounds,
                steam: $('#csGoMatchGame [name="steam"]').val(),
                gameNum: $('#csGoMatchGame [name="game_number"]').val(),
                winner: $scope.matchGameWinner,
                match: <?=$match->id?>
            };
            $http.post('/match_game/store', data)
                    .then(function (response) {
                        $('#csGoMatchGame').modal('hide');

                      window.location.href = window.location.href;
                    });
        };

      /*$scope.newOwMatchGame = function(match_id) {
        $scope.matchGameRounds = [];
        $scope.owMatchNonControlGameRound = [{
          attack_time_op1: '00:00', score_op1: 0, meters_op1: '0m',
          attack_time_op2: '00:00', score_op2: 0, meters_op2: '0m'
        }];
        $('#owMatchGame').attr('data-match-game-id', '');

        $('#owMatchGame [name="streams[]"], #owMatchGame [name="steam"], #owMatchGame [name="map_id"]')
          .val('')
          .trigger('change');
        $('#owMatchGame [name="game_number"]').val('');
        $('#owMatchGame [name="final_result"]').val('');

        setTimeout(function () {
          $('#opponent1_standins').select2('data', []);
          $('#opponent2_standins').select2('data', []);
        }, 500);

         $('#owMatchGame').modal('show');

      };*/

      $scope.newOwMatchGame = function(match_id) {
        $scope.matchGameRounds = [];
        $scope.owMatchNonControlGameRound = [{
          attack_time_op1: '00:00', score_op1: 0, meters_op1: '0m',
          attack_time_op2: '00:00', score_op2: 0, meters_op2: '0m'
        }];
        $('#owMatchGame [name="streams[]"], #owMatchGame [name="steam"], #owMatchGame [name="map_id"], #owMatchGame [name="opponent2_members[]"], #owMatchGame [name="opponent1_members[]"]')
          .val('')
          .trigger('change');
        $('#owMatchGame [data-match-id]').data('match-id', match_id);
        $('#owMatchGame [name="game_number"]').val('');
        $('#owMatchGame [name="final_result"]').val('');
        $http.get('/match/' + match_id)
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
            $('#owMatchGame').attr('data-match-game-id', '');
            setTimeout(function() {
              $('#owMatchGame [name="opponent1_members[]"]').val(opponent1_members).trigger('change');
              $('#owMatchGame [name="opponent2_members[]"]').val(opponent2_members).trigger('change');

              $('#opponent1_standins').select2('data', []);
              $('#opponent2_standins').select2('data', []);
            }, 500);
            $('#owMatchGame').modal('show');
          });

      };

      $scope.loadOwMatchGame = function (game_id) {
        $scope.owMatchNonControlGameRound = [{
          attack_time_op1: '00:00', score_op1: 0, meters_op1: '0m',
          attack_time_op2: '00:00', score_op2: 0, meters_op2: '0m'
        }];
        $http.get('/overwatch/match_game/' + game_id)
          .then(function (response) {
            $scope.match_game = response.data.match_game;
            $scope.match = response.data.match_game.match;

            if ($scope.match_game.is_tie == 1)
              $scope.matchGameWinner = "draw";
            else if ($scope.match_game.winner != 0)
              $scope.matchGameWinner = $scope.match_game.winner;

            $scope.teams = [
              {
                "value": $scope.match_game.match.opponent1_details.id,
                "label": $scope.match_game.match.opponent1_details.name
              },
              {
                "value": $scope.match_game.match.opponent2_details.id,
                "label": $scope.match_game.match.opponent2_details.name
              },
              {"value": "draw", "label": "Draw"}
            ];

            var opponent1_members = [],
              opponent2_members = [];
            if(response.data.match_game.opponent1_members) {
              $.each(response.data.match_game.opponent1_members, function(idx, id) {
                opponent1_members.push(parseInt(id));
              });
            }
            if(response.data.match_game.opponent2_members) {
              $.each(response.data.match_game.opponent2_members, function(idx, id) {
                opponent2_members.push(parseInt(id));
              });
            }

            $('#owMatchGame [name="streams[]"]').val(response.data.match_game.streams).trigger('change');
            $('#owMatchGame [name="map_id"]').val(response.data.match_game.map_id).trigger('change');
            $('#owMatchGame [name="opponent1_members[]"]').val(response.data.match_game.opponent1_members).trigger('change');
            $('#owMatchGame [name="opponent2_members[]"]').val(response.data.match_game.opponent2_members).trigger('change');
            $('#owMatchGame [name="steam"]').val(response.data.match_game.steam);
            $('#owMatchGame [name="match_date"]').val(response.data.match_game.start);
            if(response.data.match_game.rounds_data) {
              if(response.data.match_game.rounds_data[0] && response.data.match_game.rounds_data[0]['attack_time_op1']) {
                $scope.owMatchNonControlGameRound = response.data.match_game.rounds_data;
              } else {
                $scope.matchGameRounds = response.data.match_game.rounds_data;
              }
            } else {
              $scope.matchGameRounds = [];
            }

            $('#owMatchGame [name="game_number"]').val(response.data.match_game.number);

            $('#owMatchGame').attr('data-match-game-id', '' + game_id);
            setTimeout(function () {
              $('#owMatchGame [name="opponent1_members[]"]').val(opponent1_members).trigger('change');
              $('#owMatchGame [name="opponent2_members[]"]').val(opponent2_members).trigger('change');

              $('#opponent1_standins').select2('data', response.data.opponent1_standins);
              $('#opponent2_standins').select2('data', response.data.opponent2_standins);
            }, 900);

            $('#owMatchGame').modal('show');
          });
      };

      $scope.saveOwMatchGame = function() {
        var data = {
          id: $('#owMatchGame').attr('data-match-game-id'),
          streams: $('#owMatchGame [name="streams[]"]').val(),
          map_id: $('#owMatchGame [name="map_id"]').val(),
          opponent1_members: $('#owMatchGame [name="opponent1_members[]"]').val(),
          opponent2_members: $('#owMatchGame [name="opponent2_members[]"]').val(),
          opponent1_id: $('#opponent1').val(),
          opponent2_id: $('#opponent2').val(),
          opponent1_standins: $('#owMatchGame [name="opponent1_standins[]"]').val(),
          opponent2_standins: $('#owMatchGame [name="opponent2_standins[]"]').val(),
          rounds: ($('#ow-map-select > option:selected').data('map-type') == 'control') ? $scope.matchGameRounds : $scope.owMatchNonControlGameRound,
          steam: $('#owMatchGame [name="steam"]').val(),
          gameNum: $('#owMatchGame [name="game_number"]').val(),
          match: <?=$match->id?>,
          startDate: $('#owMatchGame [name="match_date"]').val(),
          winner: $scope.matchGameWinner
        };
        $http.post('{{route('match_game.save')}}', data)
          .then(function(response) {
            $('#owMatchGame').modal('hide');
            location.href=location.href;
          });
      };

        $scope.newDraft = function () {
            var data = {
                "team": $('#draft_team').val(),
                "action": $('input[name=draft_actionRadio]:checked').val(),
                "map": $('#draft_map').val(),
                "random": $('#draft_random').is(':checked') ? 1 : 0
            };

            $scope.matchDrafts.push(data);

            $('#draft_random').prop('checked', false);
        };

        $scope.loadDrafts = function (match_id) {
            $http.get('/dummymatch/drafts/' + match_id)
                    .then(function (response) {
                        if (response.data.status == 'success') {
                            $scope.matchDrafts = response.data.data.draft;
                        }

                        $('#addDraftsModal').modal('show');
                    });
        };

        $scope.saveDrafts = function () {
            $http.post('/dummymatch/drafts/save', {
                "match_id": $('#matchId').val(),
                "draft": $scope.matchDrafts
            })
                    .then(function (response) {
                        if (response.data.status == 'success') {
                            $('#addDraftsModal').modal('hide');
                            $scope.matchDrafts = [];
                        } else {
                            console.log(response.data);
                        }
                    });
        };

        /*
         LOL Match Game Functionality below
         */
        $scope.newLolMatchGame = function () {
            $('#lolMatchGame [name="opponent1_bans[]"], #lolMatchGame [name="opponent2_bans[]"], #lolMatchGame [name="opponent1_player1"], #lolMatchGame [name="opponent1_player2"], #lolMatchGame [name="opponent1_player3"], #lolMatchGame [name="opponent1_player4"], #lolMatchGame [name="opponent1_player5"], #lolMatchGame [name="opponent1_picks1"], #lolMatchGame [name="opponent1_picks2"], #lolMatchGame [name="opponent1_picks3"], #lolMatchGame [name="opponent1_picks4"], #lolMatchGame [name="opponent1_picks5"], #lolMatchGame [name="opponent2_player1"], #lolMatchGame [name="opponent2_player2"], #lolMatchGame [name="opponent2_player3"], #lolMatchGame [name="opponent2_player4"], #lolMatchGame [name="opponent2_player5"], #lolMatchGame [name="opponent2_picks1"], #lolMatchGame [name="opponent2_picks2"], #lolMatchGame [name="opponent2_picks3"], #lolMatchGame [name="opponent2_picks4"], #lolMatchGame [name="opponent2_picks5"], #lolMatchGame [name="game_number"]')
                    .val('')
                .select2('data', null)
                    .trigger('change');

            setTimeout(function () {
                $('#lolMatchGame [name="opponent1_standins[]"]').select2('data', []);
                $('#lolMatchGame [name="opponent2_standins[]"]').select2('data', []);
            }, 500);

            $('#lolMatchGame [data-match-game-id]').data('match-game-id', '');

            $('#lolMatchGame').modal('show');
        };

        $scope.loadLolMatchGame = function (game_id) {
            $('#lolMatchGame [name="opponent1_bans[]"], #lolMatchGame [name="opponent2_bans[]"], #lolMatchGame [name="opponent1_player1"], #lolMatchGame [name="opponent1_player2"], #lolMatchGame [name="opponent1_player3"], #lolMatchGame [name="opponent1_player4"], #lolMatchGame [name="opponent1_player5"], #lolMatchGame [name="opponent1_picks1"], #lolMatchGame [name="opponent1_picks2"], #lolMatchGame [name="opponent1_picks3"], #lolMatchGame [name="opponent1_picks4"], #lolMatchGame [name="opponent1_picks5"], #lolMatchGame [name="opponent2_player1"], #lolMatchGame [name="opponent2_player2"], #lolMatchGame [name="opponent2_player3"], #lolMatchGame [name="opponent2_player4"], #lolMatchGame [name="opponent2_player5"], #lolMatchGame [name="opponent2_picks1"], #lolMatchGame [name="opponent2_picks2"], #lolMatchGame [name="opponent2_picks3"], #lolMatchGame [name="opponent2_picks4"], #lolMatchGame [name="opponent2_picks5"], #lolMatchGame [name="game_number"]')
                .val('')
                .select2('data', null)
                .trigger('change');
            $http.get('/lol/match_game/' + game_id)
                    .then(function (response) {
                        $scope.match_game = response.data.match_game;


                        if ($scope.match_game.is_tie == 1)
                            $scope.matchGameWinner = "draw";
                        else if ($scope.match_game.winner != 0)
                            $scope.matchGameWinner = $scope.match_game.winner;

                        $scope.teams = [
                            {
                                "value": $scope.match_game.match.opponent1_details.id,
                                "label": $scope.match_game.match.opponent1_details.name
                            },
                            {
                                "value": $scope.match_game.match.opponent2_details.id,
                                "label": $scope.match_game.match.opponent2_details.name
                            },
                            {"value": "draw", "label": "Draw"}
                        ];

                        $('#lolMatchGame [name="streams[]"]').val(response.data.match_game.streams).trigger('change');
                        $('#lolMatchGame [name="opponent1_bans[]"]').val(response.data.opponent1_bans).trigger('change');
                        $('#lolMatchGame [name="opponent2_bans[]"]').val(response.data.opponent2_bans).trigger('change');

                        angular.forEach(response.data.match_game.opponent1_members, function (value, key) {
                            if (response.data.picks) {
                                var pick = response.data.picks.filter(function (a) {
                                    return a.player_id == value;
                                });
                            }
                            $('#lolMatchGame [name="opponent1_player' + (key + 1) + '"]').val(value).trigger('change');

                            if (pick != undefined && pick[0])
                                $('#lolMatchGame [name="opponent1_picks' + (key + 1) + '"]').val(pick[0].champion_id).trigger('change');
                        });

                        angular.forEach(response.data.match_game.opponent2_members, function (value, key) {
                            if (response.data.picks) {
                                var pick = response.data.picks.filter(function (a) {
                                    return a.player_id == value;
                                });
                            }
                            $('#lolMatchGame [name="opponent2_player' + (key + 1) + '"]').val(value).trigger('change');

                            if (pick != undefined && pick[0])
                                $('#lolMatchGame [name="opponent2_picks' + (key + 1) + '"]').val(pick[0].champion_id).trigger('change');
                        });

                        $('#lolMatchGame [name="game_number"]').val(response.data.match_game.number);
                        $('#lolMatchGame').modal('show');
                        $('#lolMatchGame [data-match-game-id]').data('match-game-id', game_id);


                        setTimeout(function () {
                            $('#lolMatchGame [name="opponent1_standins[]"]').select2('data', response.data.opponent1_standins);
                            $('#lolMatchGame [name="opponent2_standins[]"]').select2('data', response.data.opponent2_standins);
                        }, 500);
                    });
        };

        $scope.saveLolMatchGame = function () {
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
                opponent1_id: $('#opponent1').val(),
                opponent2_id: $('#opponent2').val(),
                opponent1_standins: $('#lolMatchGame [name="opponent1_standins[]"]').val(),
                opponent2_standins: $('#lolMatchGame [name="opponent2_standins[]"]').val(),
                gameNum: $('#lolMatchGame [name="game_number"]').val(),
                winner: $scope.matchGameWinner,
                match: <?=$match->id?>,
                opponent1_members: opponent1_members,
                opponent2_members: opponent2_members,
                opponent1_picks: opponent1_picks,
                opponent2_picks: opponent2_picks,
                radiant_team: $('[name="radiant_team"]:checked').val()
            };
            $http.post('/lol/match_game/store', data)
                    .then(function (response) {
                        window.location.href = window.location.href;
                    });
        };

        $scope.loadDota2MatchGame = function (game_id) {
            $('#dota2MatchGame [name="opponent1_bans[]"], #dota2MatchGame [name="opponent2_bans[]"], #dota2MatchGame [name="opponent1_player1"], #dota2MatchGame [name="opponent1_player2"], #dota2MatchGame [name="opponent1_player3"], #dota2MatchGame [name="opponent1_player4"], #dota2MatchGame [name="opponent1_player5"], #dota2MatchGame [name="opponent1_picks1"], #dota2MatchGame [name="opponent1_picks2"], #dota2MatchGame [name="opponent1_picks3"], #dota2MatchGame [name="opponent1_picks4"], #dota2MatchGame [name="opponent1_picks5"], #dota2MatchGame [name="opponent2_player1"], #dota2MatchGame [name="opponent2_player2"], #dota2MatchGame [name="opponent2_player3"], #dota2MatchGame [name="opponent2_player4"], #dota2MatchGame [name="opponent2_player5"], #dota2MatchGame [name="opponent2_picks1"], #dota2MatchGame [name="opponent2_picks2"], #dota2MatchGame [name="opponent2_picks3"], #dota2MatchGame [name="opponent2_picks4"], #dota2MatchGame [name="opponent2_picks5"], #dota2MatchGame [name="game_number"]')
                .val('')
                .select2('data', null)
                .trigger('change');
            $http.get('/dota2/match_game/' + game_id)
                    .then(function (response) {
                        $scope.match_game = response.data.match_game;

                        if ($scope.match_game.is_tie == 1)
                            $scope.matchGameWinner = "draw";
                        else if ($scope.match_game.winner != 0)
                            $scope.matchGameWinner = $scope.match_game.winner;

                        $scope.teams = [
                            {
                                "value": $scope.match_game.match.opponent1_details.id,
                                "label": $scope.match_game.match.opponent1_details.name
                            },
                            {
                                "value": $scope.match_game.match.opponent2_details.id,
                                "label": $scope.match_game.match.opponent2_details.name
                            },
                            {"value": "draw", "label": "Draw"}
                        ];

                        $('#dota2MatchGame [name="streams[]"]').val(response.data.match_game.streams).trigger('change');
                        $('#dota2MatchGame [name="opponent1_bans[]"]').val(response.data.opponent1_bans).trigger('change');
                        $('#dota2MatchGame [name="opponent2_bans[]"]').val(response.data.opponent2_bans).trigger('change');

                        angular.forEach(response.data.match_game.opponent1_members, function (value, key) {
                            if (response.data.picks) {
                                var pick = response.data.picks.filter(function (a) {
                                    return a.player_id == value;
                                });
                            }
                            $('#dota2MatchGame [name="opponent1_player' + (key + 1) + '"]').val(value).trigger('change');

                            if (pick[0])
                                $('#dota2MatchGame [name="opponent1_picks' + (key + 1) + '"]').val(pick[0].champion_id).trigger('change');
                        });

                        angular.forEach(response.data.match_game.opponent2_members, function (value, key) {
                            if (response.data.picks) {
                                var pick = response.data.picks.filter(function (a) {
                                    return a.player_id == value;
                                });
                            }
                            $('#dota2MatchGame [name="opponent2_player' + (key + 1) + '"]').val(value).trigger('change');

                            if (pick[0])
                                $('#dota2MatchGame [name="opponent2_picks' + (key + 1) + '"]').val(pick[0].champion_id).trigger('change');
                        });

                        $('#dota2MatchGame [name="radiant_team"][value="' + response.data.match_game.radiant_team + '"]').prop('checked', true);

                        $('#dota2MatchGame [name="game_number"]').val(response.data.match_game.number);
                        $('#dota2MatchGame').modal('show');
                        $('#dota2MatchGame [data-match-game-id]').data('match-game-id', game_id);


                        setTimeout(function () {
                            $('#dota2MatchGame [name="opponent1_standins[]"]').select2('data', response.data.opponent1_standins);
                            $('#dota2MatchGame [name="opponent2_standins[]"]').select2('data', response.data.opponent2_standins);
                        }, 500);
                    });
        };
        $scope.newDota2MatchGame = function () {
            $('#dota2MatchGame [name="opponent1_bans[]"], #dota2MatchGame [name="opponent2_bans[]"], #dota2MatchGame [name="opponent1_player1"], #dota2MatchGame [name="opponent1_player2"], #dota2MatchGame [name="opponent1_player3"], #dota2MatchGame [name="opponent1_player4"], #dota2MatchGame [name="opponent1_player5"], #dota2MatchGame [name="opponent1_picks1"], #dota2MatchGame [name="opponent1_picks2"], #dota2MatchGame [name="opponent1_picks3"], #dota2MatchGame [name="opponent1_picks4"], #dota2MatchGame [name="opponent1_picks5"], #dota2MatchGame [name="opponent2_player1"], #dota2MatchGame [name="opponent2_player2"], #dota2MatchGame [name="opponent2_player3"], #dota2MatchGame [name="opponent2_player4"], #dota2MatchGame [name="opponent2_player5"], #dota2MatchGame [name="opponent2_picks1"], #dota2MatchGame [name="opponent2_picks2"], #dota2MatchGame [name="opponent2_picks3"], #dota2MatchGame [name="opponent2_picks4"], #dota2MatchGame [name="opponent2_picks5"], #dota2MatchGame [name="game_number"]')
                .val('')
                .select2('data', null)
                .trigger('change');

            setTimeout(function () {
                $('#dota2MatchGame [name="opponent1_standins[]"]').select2('data', []);
                $('#dota2MatchGame [name="opponent2_standins[]"]').select2('data', []);

            }, 500);
            $scope.teams = [
                {
                    "value": <?=$match->opponent1_details->id?>,
                    "label": "<?=$match->opponent1_details->name?>"
                },
                {
                    "value": <?=$match->opponent2_details->id?>,
                    "label": "<?=$match->opponent2_details->name?>"
                },
                {"value": "draw", "label": "Draw"}
            ];

            $('#dota2MatchGame [data-match-game-id]').data('match-game-id', '');

            $('#dota2MatchGame').modal('show');
        };

        $scope.saveDota2MatchGame = function () {
            var opponent1_members = [
                $('#dota2MatchGame [name="opponent1_player1"]').val(),
                $('#dota2MatchGame [name="opponent1_player2"]').val(),
                $('#dota2MatchGame [name="opponent1_player3"]').val(),
                $('#dota2MatchGame [name="opponent1_player4"]').val(),
                $('#dota2MatchGame [name="opponent1_player5"]').val()
            ];
            var opponent2_members = [
                $('#dota2MatchGame [name="opponent2_player1"]').val(),
                $('#dota2MatchGame [name="opponent2_player2"]').val(),
                $('#dota2MatchGame [name="opponent2_player3"]').val(),
                $('#dota2MatchGame [name="opponent2_player4"]').val(),
                $('#dota2MatchGame [name="opponent2_player5"]').val()
            ];
            var opponent1_picks = [
                $('#dota2MatchGame [name="opponent1_picks1"]').val(),
                $('#dota2MatchGame [name="opponent1_picks2"]').val(),
                $('#dota2MatchGame [name="opponent1_picks3"]').val(),
                $('#dota2MatchGame [name="opponent1_picks4"]').val(),
                $('#dota2MatchGame [name="opponent1_picks5"]').val()
            ];
            var opponent2_picks = [
                $('#dota2MatchGame [name="opponent2_picks1"]').val(),
                $('#dota2MatchGame [name="opponent2_picks2"]').val(),
                $('#dota2MatchGame [name="opponent2_picks3"]').val(),
                $('#dota2MatchGame [name="opponent2_picks4"]').val(),
                $('#dota2MatchGame [name="opponent2_picks5"]').val()
            ];

            var data = {
                id: $('#dota2MatchGame [data-match-game-id]').data('match-game-id'),
                streams: $('#dota2MatchGame [name="streams[]"]').val(),
                opponent1_bans: $('#dota2MatchGame [name="opponent1_bans[]"]').val(),
                opponent2_bans: $('#dota2MatchGame [name="opponent2_bans[]"]').val(),
                opponent1_id: $('#opponent1').val(),
                opponent2_id: $('#opponent2').val(),
                opponent1_standins: $('#dota2MatchGame [name="opponent1_standins[]"]').val(),
                opponent2_standins: $('#dota2MatchGame [name="opponent2_standins[]"]').val(),
                gameNum: $('#dota2MatchGame [name="game_number"]').val(),
                winner: $scope.matchGameWinner,
                match: <?=$match->id?>,
                opponent1_members: opponent1_members,
                opponent2_members: opponent2_members,
                opponent1_picks: opponent1_picks,
                opponent2_picks: opponent2_picks,
                radiant_team: $('[name="radiant_team"]:checked').val()
            };
            $http.post('/match_game/store', data)
                    .then(function (response) {
                        $('#dota2MatchGame').modal('hide');
                      window.location.href = window.location.href;
                    });
        };
    });

</script>
@endsection
@section('scripts')
    @parent
        <script>
            $(function () {
                $('select.select2').select2();

              /// STREAM LINKS
                  var $formfunc = function (state) {
                    if (!state.id) return state.text; // optgroup
                    return state.text + " <a class='info' href='"+ $(state.element).attr('data-link') + "' target='_blank'>link</a>";
                  }
                $('select.all_streams').select2({
                formatSelection: $formfunc,
                escapeMarkup: function(m) { return m; }
              }).data('select2');
              /// END OF STREAM LINKS


                $('#csGoMatchGame, #lolMatchGame, #dota2MatchGame').on('shown.bs.modal', function () {
                    invoke_select2();
                });

                $('#csGoMatchGame').on('hidden.bs.modal', function () {
                    $('#csGoMatchGame [data-match-game-id]').data('match-game-id', null);
                });

                $('[name="streams[]"]').on('change', function (evt) {
                    if (evt.removed) {
                        $.post('/api/matches/ignore_stream', {
                            id: evt.removed.element[0].value,
                            match: <?=$match->id?>
                        });
                    }
                });
            });

            var invoke_select2 = function () {
                $('#opponent1_standins, #opponent2_standins, #lolMatchGame [name="opponent1_standins[]"], #lolMatchGame [name="opponent2_standins[]"], #dota2MatchGame [name="opponent1_standins[]"], #dota2MatchGame [name="opponent2_standins[]"]').select2({
                    ajax: {
                        url: gameUrl + "/player/getPlayerByName/",
                        dataType: 'json',
                        delay: 250,
                        data: function (term) {
                            return {
                                name: term
                            };
                        },
                        processResults: function (data, params) {
                            return {
                                results: $.map(data.players, function (item) {
                                    return {
                                        text: item.nickname,
                                        id: item.id
                                    }
                                })
                            };
                        },
                        cache: true
                    },
                    escapeMarkup: function (markup) {
                        return markup;
                    }, // let our custom formatter work
                    minimumInputLength: 2,
                    placeholder: 'Search for a player',
                    multiple: true
                }).parent().find('.select2-container').css({
                    "width": "100%"
                });

            };
            //,
            $('#lolMatchGame [name="opponent1_standins[]"], #dota2MatchGame [name="opponent1_standins[]"]')
                    .on("change", function (e) {
                        if (e.added != undefined) {
                            $('.modal.in').find('#opponent1_player1,#opponent1_player2,#opponent1_player3,#opponent1_player4,#opponent1_player5')
                                    .append($('<option value="' + e.added.id + '">' + e.added.text + '</option>'))
                                    .trigger('change');
                        } else if (e.removed != undefined) {
                            $('.modal.in').find('#opponent1_player1,#opponent1_player2,#opponent1_player3,#opponent1_player4,#opponent1_player5')
                                    .find('option[value="' + e.removed.id + '"]')
                                    .remove()
                                    .trigger('change');
                        }
                    });
            $('#lolMatchGame [name="opponent2_standins[]"], #dota2MatchGame [name="opponent2_standins[]"]')
                    .on("change", function (e) {
                        if (e.added != undefined) {
                            $('.modal.in').find('#opponent2_player1,#opponent2_player2,#opponent2_player3,#opponent2_player4,#opponent2_player5')
                                    .append($('<option value="' + e.added.id + '">' + e.added.text + '</option>'))
                                    .trigger('change');
                        } else if (e.removed != undefined) {
                            $('.modal.in').find('#opponent2_player1,#opponent2_player2,#opponent2_player3,#opponent2_player4,#opponent2_player5')
                                    .find('option[value="' + e.removed.id + '"]')
                                    .remove()
                                    .trigger('change');
                        }
                    });


        </script>
@endsection