@extends('layouts.default')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Player</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{groute('/')}}">Home</a>
                </li>
                <li>
                    <a href="{{groute('players.list')}}">Players</a>
                </li>
                <li class="active">
                    <strong>Manage</strong>
                </li>
            </ol>
        </div>
        <div class="col-lg-2">

        </div>
    </div>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row ">
            <div class="col-md-8 ibox">
                <div class="col-md-12 ibox-content">
                    <div class="row">
                        <div class="col-md-6">
                            <h3>{{ $player->first_name }} "{{$player->nickname}}" {{ $player->last_name }}</h3> @if($player->country)
                                <p><img src="/img/flags/16/{{str_replace(' ', '-', $player->country->countryName)}}.png" alt="{{$player->country->countryName}}"></p>
                            @endif
                            <p>Earnings: <span class="badge badge-primary">${{number_format($player->earnings, 2, '.', ',')}}</span>
                            </p>
                            @if($player->player_role)
                                <p>Roles:
                                    {{implode(',', $player->named_roles)}}
                                </p>
                            @endif
                            @if (request()->currentGameSlug == "sc2")
                                <p>Race: {{ implode(",", $player->sc2_race['names']) }}</p>
                            @endif
                            <p>{{$player->bio}}</p>
                        </div>
                        <div class="col-md-6 text-right">
                            <a href="{{ groute('player.edit', 'current', [$player->id ]) }}"><button type="button" class="btn btn-primary dim">Edit player</button></a>
                            <a href="{{ groute('player.delete', 'current', [$player->id ]) }}" id="delete-individual"><button type="button" class="btn btn-danger dim">Delete player</button></a>
                            <div>
                                @if($player->avatar)
                                    <img alt="{{$player->nickname}}" src="http://static.esportsconstruct.com/{{$player->avatar}}" class="img-circle players-avatar img-responsive pull-right">
                                @else
                                    <img alt="image" src="/img/players-avatars/no-player-photo.jpg" class="img-circle players-avatar img-responsive pull-right">
                                @endif
                            </div>
                        </div>
                    </div>
                    <hr />
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Roster History</h4>
                        </div>
                        <div class="col-md-6 text-right">
                            <a href="#no-top"><button type="button" class="btn btn-primary addTeam dim" data-id="{{ $player->id }}">Add team</button></a>
                        </div>
                    </div>
                    <hr />

                    <div>
                        <table class="footable table table-bordered" data-sorting="true">
                            <thead>
                            <tr>
                                <th data-sort-ignore="true">Team Name</th>
                                <th data-sorted="true" data-type="date" data-format-string="YYYY MM DD" data-sort-initial="true">Started</th>
                                <th data-sort-ignore="true">Left</th>
                                <th data-sort-ignore="true">Is Coach</th>
                                <th data-sort-ignore="true">Is a Sub</th>
                                <th data-sort-ignore="true">Is Stand-in</th>
                                <th data-sort-ignore="true">Is a Manager</th>
                                <th data-sort-ignore="true">Edit</th>
                                <th data-sort-ignore="true">Remove</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if (isset($teams) && count($teams) > 0) @foreach($teams as $team)
                                <tr>
                                    <td>{!!  $team->team ? '<a href="'.groute('team.show', 'current', ['teamId' => $team->team->id]).'">'.$team->team->name.'</a>' : '[deleted team]'  !!}</td>
                                    <td>{{ $team->start_date }}</td>
                                    <td>
                                        @if ($team->end_date !== null) {{ $team->end_date }} @else Current @endif
                                    </td>
                                    <td>
                                        @if ($team->is_coach === 0) No @else Yes @endif
                                    </td>
                                    <td>
                                        @if ($team->is_sub == 1) Yes @else No @endif
                                    </td>
                                    <td>
                                        @if ($team->is_standin == 1) Yes @else No @endif
                                    </td>
                                    <td>
                                        @if ($team->is_manager == 1) Yes @else No @endif
                                    </td>
                                    <td><a href="#no-top" class="editTeam" data-id="{{ $team->id }}">Edit</a></td>
                                    <td><a href="#no-top" class="removeTeam" data-id="{{ $team->id }}">Remove</a></td>
                                </tr>
                            @endforeach @endif
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="9">
                                    <ul class="pagination pull-right"></ul>
                                </td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>


                    <div class="row">
                        <div class="col-md-12">
                            <h4>Stats</h4>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                @if (isset($stats) && is_object($stats))
                                    <table class="table table-stripped">
                                        <tbody>
                                        <tr>
                                            <th>Total matches:</th>
                                            <td>{{isset($stats->games_played) ? $stats->games_played : 0}} </td>
                                        </tr>
                                        <tr>
                                            <th>Win rate</th>
                                            <td>{{isset($stats->win_rate) ? $stats->win_rate: 0}}%</td>
                                        </tr>
                                        <tr>
                                            <th>Deaths</th>
                                            <td>{{ isset($stats->deaths) ? $stats->deaths : "" }}</td>
                                        </tr>
                                        <tr>
                                            <th>Kills</th>
                                            <td>{{ isset($stats->kills) ? $stats->kills : "" }}</td>
                                        </tr>
                                        <tr>
                                            <th>Kill/Death ratio</th>
                                            <td>{{ isset($stats->kill_death_ratio) ? $stats->kill_death_ratio : "" }}</td>
                                        </tr>
                                        <tr>
                                            <th>Assists</th>
                                            <td>{{ isset($stats->assists) ? $stats->assists : "" }}</td>
                                        </tr>
                                        <tr>
                                            <th>Average kills</th>
                                            <td>{{ isset($stats->avg_kills) ? $stats->avg_kills : "" }}</td>
                                        </tr>
                                        <tr>
                                            <th>Average deaths</th>
                                            <td>{{ isset($stats->avg_deaths) ? $stats->avg_deaths : "" }}</td>
                                        </tr>
                                        <tr>
                                            <th>Average assists</th>
                                            <td>{{ isset($stats->avg_assists) ? $stats->avg_assists : "" }}</td>
                                        </tr>
                                        <tr>
                                            <th>Average level</th>
                                            <td>{{ isset($stats->avg_level) ? $stats->avg_level : "" }}</td>
                                        </tr>
                                        <tr>
                                            <th>Average denies</th>
                                            <td>{{ isset($stats->avg_denies) ? $stats->avg_denies : "" }}</td>
                                        </tr>
                                        <tr>
                                            <th>Average gold per minute</th>
                                            <td>{{ isset($stats->avg_gold_per_minute) ? $stats->avg_gold_per_minute : "" }}</td>
                                        </tr>
                                        <tr>
                                            <th>Average experience per minute</th>
                                            <td>{{ isset($stats->avg_xp_per_minute) ? $stats->avg_xp_per_minute : "" }}</td>
                                        </tr>
                                        <tr>
                                            <th>Games played</th>
                                            <td>{{ isset($stats->games_played) ? $stats->games_played : "" }}</td>
                                        </tr>
                                        <tr>
                                            <th>Last hits</th>
                                            <td>{{ isset($stats->last_hits) ? $stats->last_hits : "" }}</td>
                                        </tr>
                                        <tr>
                                            <th>Average gold</th>
                                            <td>{{ isset($stats->avg_gold) ? $stats->avg_gold : "" }}</td>
                                        </tr>
                                        <tr>
                                            <th>Total wins</th>
                                            <td>{{ isset($stats->wins) ? $stats->wins : "" }}</td>
                                        </tr>
                                        <tr>
                                            <th>Total loses</th>
                                            <td>{{ isset($stats->loses) ? $stats->loses : "" }}</td>
                                        </tr>
                                        <tr>
                                            <th>Hero with most wins</th>
                                            <td>{{ isset($stats->most_hero_wins) ? $heroes->where('id', $stats->most_hero_wins)->first()->localized_name : "" }}</td>
                                        </tr>
                                        <tr>
                                            <th>Hero with most loses</th>
                                            <td>{{ isset($stats->most_hero_loses) ? $heroes->where('id', $stats->most_hero_loses)->first()->localized_name : "" }}</td>
                                        </tr>
                                        <tr>
                                            <th>Most played hero</th>
                                            <td>{{ isset($stats->most_played_hero) ? $heroes->where('id', $stats->most_played_hero)->first()->localized_name : "" }}</td>
                                        </tr>
                                        <tr>
                                            <th>Latest played hero</th>
                                            <td>{{ isset($stats->lastest_played_hero) ? $heroes->where('id', $stats->lastest_played_hero)->first()->localized_name : "" }}</td>
                                        </tr>
                                        <tr>
                                            <th>Best hero</th>
                                            <td>{{ isset($stats->best_hero) ? $heroes->where('id', $stats->best_hero)->first()->localized_name : "" }}</td>
                                        </tr>
                                        <tr>
                                            <th>Worst hero</th>
                                            <td>{{ isset($stats->worst_hero) ? $heroes->where('id', $stats->worst_hero)->first()->localized_name : "" }}</td>
                                        </tr>
                                        </tbody>
                                        @if(count($stats->recent_tournaments))
                                            <tbody class="player-page-recent-tour">
                                            <tr>
                                                <td><button class="btn btn-primary dim" id="show-all-tour">Show All</button></td>
                                            </tr>
                                            <tr>
                                                <th colspan="2">Recent tournaments:</th>
                                            </tr>
                                            @foreach($stats->recent_tournaments as $t)
                                                <tr>
                                                    <td colspan="2">{{ $t->name }}</td>
                                                </tr>
                                            @endforeach @endif @if(count($stats->next_tournaments))
                                                <tr>
                                                    <th colspan="2">Upcoming tournaments:</th>
                                                </tr>
                                                @foreach($stats->next_tournaments as $t)
                                                    <tr>
                                                        <td colspan="2">{{ $t->name }}</td>
                                                    </tr>
                                                @endforeach

                                            </tbody>
                                        @endif
                                    </table>
                                @endif
                            </div>
                        </div>
                    </div>


                    <!-- Add match game Modal -->
                    <div class="modal fade" id="addTeamModal" tabindex="-1" role="dialog" aria-labelledby="addTeamModalLabel">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="myModalLabel">Add roster history</h4>
                                </div>
                                <div class="modal-body">
                                    <form>
                                        <input type="hidden" name="playerid" id="playerid" />
                                        <div class="form-group">
                                            <label for="teamname">Team Name</label>
                                            <input type="text" class="form-control" name="teamname" id="teamname" autocomplete="off" />
                                            <input type="hidden" name="team" id="team" />
                                            <div class="teamSuggestions" id="suggestion">
                                                <ul id="teamSuggestions">

                                                </ul>
                                            </div>
                                        </div>
                                        <label for="start_date">Start Date</label>
                                        <div class='input-group date' id='starDatetHolder'>
                                            <input type="text" class="form-control" name="start_date" id="start_date" placeholder="YYYY-MM-DD" />
                                            <span class="input-group-addon">
                                          <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                        <label for="end_date">End date</label>
                                        <div class='input-group date' id='endDatetHolder'>
                                            <input type="text" class="form-control" name="end_date" id="end_date" placeholder="YYYY-MM-DD or blank" />
                                            <span class="input-group-addon">
                                          <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="coach" id="coach" value="1"> Is coach
                                            </label>

                                        </div>
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="is_sub" id="is_sub" value="1"> Is a Sub
                                            </label>
                                        </div>
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="is_standin" id="is_standin" value="1"> Is
                                                Stand-in
                                            </label>
                                        </div>
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="is_manager" id="is_manager" value="1"> Is a
                                                Manager
                                            </label>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default dim" data-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary dim" id="saveTeamHistory">Add Roster History</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Edit match Modal -->
                    <div class="modal fade" id="editTeamModal" tabindex="-1" role="dialog" aria-labelledby="editTeamModalLabel">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="myModalLabel">Edit roster history</h4>
                                </div>
                                <div class="modal-body">
                                    <form>
                                        <input type="hidden" name="rosterid" id="rosterid" />
                                        <div class="form-group">
                                            <label for="teamnameEdit">Team Name</label>
                                            <input type="text" class="form-control" name="teamnameEdit" id="teamnameEdit" autocomplete="off" />
                                            <input type="hidden" name="teamEdit" id="teamEdit" />
                                            <div class="teamSuggestions" id="suggestionEdit">
                                                <ul id="teamEditSuggestions">

                                                </ul>
                                            </div>
                                        </div>
                                        <label for="start_date_edit">Start Date</label>
                                        <div class='input-group date' id='starDatetHolder'>
                                            <input type="text" class="form-control" name="start_date_edit" id="start_date_edit" />
                                            <span class="input-group-addon">
                                          <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                        <label for="end_date_edit">End date</label>
                                        <div class='input-group date' id='endDatetHolder'>
                                            <input type="text" class="form-control" name="end_date_edit" id="end_date_edit" />
                                            <span class="input-group-addon">
                                          <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="coachEdit" id="coachEdit" value="1"> Is coach
                                            </label>
                                        </div>
                                        <div class="checkbox">

                                            <label>
                                                <input type="checkbox" name="is_sub" id="is_sub_edit" value="1"> Is a
                                                Sub
                                            </label>
                                        </div>
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="is_standin" id="is_standin_edit" value="1">
                                                Is Stand-in
                                            </label>
                                        </div>
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="is_manager" id="is_manager_edit" value="1">
                                                Is a Manager
                                            </label>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default dim" data-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary dim" id="editTeamHistory">Edit Roster History</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="col-md-4">
                <div class="ibox-content teams-latest-matches-tbl">
                    </p><h3>Young Minds latest matches</h3><p>
                    <table class="table">
                        <tbody><tr>
                            <td>
                                <a href="#">
                                    VS
                                </a>
                            </td>
                            <td>
                                <img src="/img/flags/16/South-Korea.png" alt="South-Korea.png">
                                <strong>
                                    <a href="#">
                                        MVP HOT6ix
                                    </a>
                                </strong>
                            </td>
                            <td>
                                <label class="label label-warning">0-0</label>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a href="#">
                                    VS
                                </a>
                            </td>
                            <td>
                                <img src="/img/flags/16/Philippines.png" alt="Philippines.png">
                                <strong>
                                    <a href="#">
                                        Mineski
                                    </a>
                                </strong>
                            </td>
                            <td>
                                <label class="label label-warning">0-0</label>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a href="#">
                                    VS
                                </a>
                            </td>
                            <td>
                                <img src="/img/flags/16/Singapore.png" alt="Singapore.png">
                                <strong>
                                    <a href="#">
                                        Team Faceless
                                    </a>
                                </strong>
                            </td>
                            <td>
                                <label class="label label-warning">0-0</label>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a href="#">
                                    VS
                                </a>
                            </td>
                            <td>
                                <img src="/img/flags/16/Malaysia.png" alt="Malaysia.png">
                                <strong>
                                    <a href="#">
                                        WarriorsGaming.Unity
                                    </a>
                                </strong>
                            </td>
                            <td>
                                <label class="label label-warning">0-0</label>
                            </td>
                        </tr>
                        </tbody></table>

                    <a href="#"><button class="btn btn-primary dim">Full match history</button></a>
                </div>
            </div>

            @endsection

            @section('scripts')
                @parent
                <script type="text/javascript">
                    $(document).ready(function() {
                        $('#delete-individual').on('click', function(e) {
                            var conf = confirm('Are you sure you want to delete this player?');

                            if (!conf) {
                                return false;
                            }
                        })
                    });
                </script>
@endsection