        @if (isset($roundsInfo) && $roundsInfo !== null)
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
            else {
                $opis = '[]';
            }
            ?>
            @foreach ($roundsInfo as $k=>$roundType)
                <div style="padding-left: 30px;">
                    @foreach ($roundType as $round)
                    <?php
                            $round['matches'] = $matches->whereLoose('round_id', $round['round']->id)->sortBy('position');
                        ?>
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Round {{ $round['round']->number }} <button type="button" class="btn btn-success addMatch " data-id="{{ $round['round']->id }}" >Add Match</button></h5>
                            </div>
                            <div class="col-md-6">
                                <form method="post">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                    <input type="hidden" name="stage_round_id" value="{{ $round['round']->id }}" />
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class='input-group date' id='scheduleDatetHolder'>
                                                <input type="text" class="form-control" name="schedule_date" id="schedule_date" />
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <button type="submit" class="btn btn-success" style="margin-left:-20px;">Schedule</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div><br>
                        <div class="ibox">
                            <div class="ibox-content">
                                <table class="footable table table-stripped" data-page-size="10000">
                                    <thead>
                                        <tr>
                                            <th data-sort-ignore="true"></th>
                                            <th data-sort-ignore="true"></th>
                                            <th class="text-right">Home Team</th>
                                            <th class="text-center">Win Percentage</th>
                                            <th class="text-center">Score</th>
                                            <th class="text-center">Win Percentage</th>
                                            <th>Away Team</th>
                                            <th data-sort-ignore="true"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(count($round['matches']))
                                        @foreach($round['matches'] as $match)
                                        <?php $team1 = $teams->where('id', $match->opponent1)->first() ?>
                                        <?php $team2 = $teams->where('id', $match->opponent2)->first() ?>
                                        <tr>
                                            <td class="text-center">
                                                @if($match->position > 1)
                                                <a href="{{groute('dummymatch.move.up', ['matchId' => $match->id])}}" class="btn btn-default btn-xs">
                                                    <i class="fa fa-angle-up"></i>
                                                </a>
                                                @endif
                                                @if($match->position < count($round['matches']))
                                                    <a href="{{groute('dummymatch.move.down', ['matchId' => $match->id])}}" class="btn btn-default btn-xs">
                                                        <i class="fa fa-angle-down"></i>
                                                    </a>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-danger removeMatch" data-id="{{ $match->id }}" data-toggle="tooltip" data-original-title="Delete Match"><i class="fa fa-times"></i></button>
                                                <button type="button" class="btn btn-primary editMatch " data-id="{{ $match->id }}" data-toggle="tooltip" data-original-title="Edit Match"><i class="fa fa-edit"></i></button>
                                            </td>
                                            <td class="text-right">
                                                @if ($opponents && count($opponents) > 0)
                                                <select class="opponent1-change" data-id="{{ $match->id }}">
                                                    <option value="34">Opponent 1</option>
                                                    @foreach ($opponents as $opponent)
                                                        <option value="{{ $opponent->opponent_id }}" @if($opponent->opponent_id == $match->opponent1) selected @endif>{{ $opponent->opponent->name }}</option>
                                                    @endforeach
                                                </select> @else @if(is_coming_from_winner_bracket($round['round'], $team1))
                                                    <i class="fa fa-trophy"></i> @endif {{ $match->opponent1_details->name }}
                                                <img src="/img/flags/16/{{ $team1->country->filename }}"
                                                     alt="{{ $team1->country_name }}"> @endif
                                            </td>
                                            <td class="text-center">
                                                @if($team1->win_procentage)
                                                <p data-toggle="tooltip" data-original-title="{{$team1->win_procentage}}%">
                                                    <span class="pie">{{$team1->wins}}/{{$team1->total_matches}}</span>
                                                </p>
                                                @endif
                                            </td>
                                            <td class="text-center" style="overflow: visible;">

                                                <a href="{{ groute('match.view', 'current', [175, 419, 1140, $match->id]) }}">ID {{ $match->id }}</a>
                                                <div class="dropdown clearfix">
                                                    @if ($match->winner == null && $match->is_tie == 0)
                                                    <button class="btn btn-default dropdown-toggle btn-xs" data-container="body" type="button" id="match_dropdown_{{$match->id}}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Pick result<span class="caret"></span> </button>
                                                    <ul class="dropdown-menu" aria-labelledby="match_dropdown_{{$match->id}}">
                                                        <li><a href="javascript:;" data-do="updateMatch" data-winner="{{$team1->id}}" data-match="{{$match->id}}">Winner is {{$team1->name}}</a></li>
                                                        <li><a href="javascript:;" data-do="updateMatch" data-draw data-match="{{$match->id}}">Draw</a></li>
                                                        <li><a href="javascript:;" data-do="updateMatch" data-forfeit-winner="{{$team1->id}}" data-match="{{$match->id}}">Forfeit winner is {{$team1->name}}</a></li>
                                                        <li><a href="javascript:;" data-do="updateMatch" data-forfeit-winner="{{$team2->id}}" data-match="{{$match->id}}">Forfeit winner is {{$team2->name}}</a></li>
                                                        <li><a href="javascript:;" data-do="updateMatch" data-disqualify-team="{{$team1->id}}" data-disqualification-winner="{{$team2->id}}" data-match="{{$match->id}}">Disqualify {{$team1->name}}</a></li>
                                                        <li><a href="javascript:;" data-do="updateMatch" data-disqualify-team="{{$team2->id}}" data-disqualification-winner="{{$team1->id}}" data-match="{{$match->id}}">Disqualify {{$team2->name}}</a></li>
                                                        <li><a href="javascript:;" data-do="updateMatch" data-winner="{{$team2->id}}" data-match="{{$match->id}}">Winner is {{$team2->name}}</a></li>
                                                    </ul>
                                                    @else
                                                        <a href="{{ groute('match.view', 'current', [175, 419, 1140, $match->id]) }}">
                                                        @if($match->is_forfeited)
                                                            Forfeited
                                                        @elseif($match->disqualified_team)
                                                            Disqualification
                                                        @elseif($match->is_tie)
                                                            Tie
                                                        @else
                                                                {{$match->scores->opponent1}}
                                                            -
                                                                {{$match->scores->opponent2}}
                                                        @endif
                                                    @endif
                                                </div>
                                                @if($match->start)
                                                    {{date_convert($match->start, 'UTC', Auth::user()->timezone, 'Y-m-d H:i:s', 'd M, H:i')}}
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($team2->win_procentage)
                                                <p data-toggle="tooltip" data-original-title="{{$team2->win_procentage}}%">
                                                    <span class="pie">{{$team2->wins}}/{{$team2->total_matches}}</span>
                                                </p>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($opponents && count($opponents) > 0)
                                                <select class="opponent2-change" data-id="{{ $match->id }}">
                                                    <option value="35">Opponent 2</option>
                                                    @foreach ($opponents as $opponent)
                                                        <option value="{{ $opponent->opponent_id }}" @if ($opponent->opponent_id == $match->opponent2) selected @endif>{{ $opponent->opponent->name }}</option>
                                                    @endforeach
                                                </select> @else @if(is_coming_from_winner_bracket($round['round'], $team2))
                                                <i class="fa fa-trophy"></i> @endif
                                                <img src="/img/flags/16/{{ $team2->country->filename  }}"
                                                     alt="{{ $team2->country_name }}"> {{ $match->opponent2_details->name }} @endif
                                            </td>
                                            <td class="text-right">

                                                <button type="button" class="btn btn-green
                                                        @if(request()->currentGameSlug!='csgo'
                                                            && request()->currentGameSlug!='lol'
                                                            && request()->currentGameSlug != 'overwatch')
                                                            addMatchGame
                                                        @endif"
                                                        @if(request()->currentGameSlug=='csgo')
                                                            data-ng-click="newMatchGame({{$match->id}})"
                                                        @elseif(request()->currentGameSlug=='overwatch')
                                                            data-ng-click="newOwMatchGame({{$match->id}})"
                                                        @elseif(request()->currentGameSlug=='lol')
                                                            data-ng-click="newLolMatchGame({{$match->id}})"
                                                        @endif
                                                            data-id="{{ $match->id }}"
                                                            data-toggle="tooltip"
                                                        data-original-title="Add Match Game">
                                                    <i class="fa fa-plus"></i>
                                                </button>@if ($match->done == 0)
                                                <button type="button" class="btn btn-success markDone " data-id="{{ $match->id }}" data-toggle="tooltip" data-original-title="Mark Done"><i class="fa fa-check"></i></button> @else
                                                <button type="button" class="btn btn-default" data-toggle="tooltip" data-original-title="Mark Done"><i class="fa fa-check"></i></button> @endif
                                            </td>
                                        </tr>
                                        @endforeach @endif

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endforeach
                        <a href="{{groute('stages.formats.rounds.add', ['stage_format' => $sf->id, 'type' => $round['round']->type])}}" class="btn btn-success">Add round</a>

                </div>
                @endforeach
            @endif