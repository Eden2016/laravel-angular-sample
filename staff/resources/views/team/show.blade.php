@extends('layouts.default')

@section('content')

    <div class="team-wrapp">
        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>Team Profile</h2>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{groute('/')}}">Home</a>
                    </li>
                    <li>
                        <a href="{{groute('teams.list')}}">Teams</a>
                    </li>
                    <li class="active">
                        <strong>Team</strong>
                    </li>
                </ol>
            </div>
            <div class="col-lg-2">

            </div>
        </div>
        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-sm-8">

                    <div class="ibox">
                        <div class="ibox-content player-detail-card">
                            <div class="row">

                                <div class="col-md-3">
                                    <h3>{{ $team->name }}</h3>
                                    <p>
                                        
                                    @if($team->logo)
                                        <img src="http://static.esportsconstruct.com/{{$team->logo}}" alt="{{$team->name}}" class="img-responsive"> @endif @if (isset($country))
                                        <img src="/img/flags/16/{{ $country->filename }}" alt="den"> @endif
                                    </p>
                                    <p>Earnings: <span class="badge badge-primary">{{ $team->total_earnings }}</span></p>
                                </div>
                                <div class="col-md-6">
                                    <div class="rooster-box text-center">
                                       
                                        @foreach ($roster as $player)
                                        @if ($player && $player->player)
                                        <a class="pull-left m-r-xs" href="{{groute('players.view', 'current', [$player->player->id])}}">
                                           @if($player->player->avatar)
                                            <img data-toggle="tooltip" data-original-title="{{ $player->player->nickname }}" class="img m-t-xs" src="http://static.esportsconstruct.com/{{$player->player->avatar}}" alt="player" />
                                            @else
                                            <img data-toggle="tooltip" class="img m-t-xs" src="/img/profile-photo-blank.jpg" alt="player" />
                                            @endif
                                            <span style="display:block;">{{ $player->player->nickname }}</span>
                                        </a>
                                        @endif
                                        @endforeach

                                        <div class="clearfix"></div>
                                        <a href="{{ groute('team.players.history', 'current', [$team->id]) }}">
                                            <button class="btn btn-primary dim m-t-md">Player history</button>
                                        </a>
                                    </div>
                                    <p>{{ $team->description }}</p>
                                </div>
                                <div class="col-md-3">
                                    <p class="text-right">Rating: <span><strong> 1,382</strong></span></p>
                                    <p class="text-right">World ranking: <strong>3</strong></p>

                                    <p class="text-right">Current Tournament: <br> @if (isset($stats['current_tournaments']))
                                        <ul style="list-style:none;">
                                            @foreach ($stats['current_tournaments'] as $tournament)
                                            <li>
                                                <a href="{{ groute('tournament.view', 'current',  [$tournament['id']]) }}">{{ $tournament['name'] }}</a>
                                            </li>
                                            @endforeach
                                        </ul>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <h3>Statistics</h3>
                                    <?php $stats = $team->simple_stats; ?>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <tr>
                                                <th></th>
                                                <th class="text-center">All time</th>
                                            </tr>
                                            <tr>
                                                <td>Winrate:</td>
                                                <td class="text-center">{{ isset($stats->win_rate) ? $stats->win_rate : "" }}%</td>
                                            </tr>
                                            <tr>
                                                <td>Matches played:</td>
                                                <td class="text-center">
                                                    <span class="label label-primary">{{ isset($stats->wins) ? $stats->wins : "" }}</span> -
                                                    <span class="label label-warning">{{ isset($stats->draws) ? $stats->draws : "" }}</span> -
                                                    <span class="label label-danger">{{ isset($stats->losses) ? $stats->losses : "" }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Current streak:</td>
                                                <td class="text-center">
                                                    @if (isset($stats->streak)) @if ($stats->streak > 0)
                                                    <span class="label label-primary">{{ $stats->streak }}</span> @elseif ($stats->streak
                                                    < 0) <span class="label label-danger">{{ $stats->streak }}</span>
                                                        @else
                                                        <span class="label label-warning">{{ $stats->streak }}</span> @endif @endif
                                                </td>
                                            </tr>
                                        </table>

                                    </div>
                                </div>
                            </div>
                            <a href="{{groute('team.edit', 'current', [$team->id])}}"><button class="btn btn-primary dim">Edit Team</button></a>
                            <button class="btn btn-danger dim" id="deleteTeam" data-id="{{ $team->id }}">Remove Team</button>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="ibox">
                        <div class="ibox-content teams-latest-matches-tbl">
                            <p>
                                <h3>{{ $team->name }} latest matches</h3></p>
                            <table class="table ">
                                @if (isset($matches) && count($matches)) @foreach ($matches as $match)
                                <?php
                                    $vsTeam = $match->opponent1_details;
                                    $vsLogo = $match->opponent1_details->country ? $match->opponent1_details->country->filename : null;
                                    $label = "warning";
                                    if ($match->opponent1 == $team->id) {
                                        $vsTeam = $match->opponent2_details;
                                        $vsLogo = $match->opponent2_details->country->filename;

                                        if ($match->score->opp1score < $match->score->opp2score)
                                            $label = "danger";
                                        else if ($match->score->opp1score > $match->score->opp2score)
                                            $label = "primary";
                                    }
                                    else {
                                        if ($match->score->opp1score < $match->score->opp2score)
                                            $label = "primary";
                                        else if ($match->score->opp1score > $match->score->opp2score)
                                            $label = "danger";
                                    }
                                ?>
                                    <tr>
                                        <td>
                                            <a href="{{ groute('match.view', 'current', [0,0,0,$match->id])}}">
                                        VS
                                        </a>
                                        </td>
                                        <td>
                                            @if ($vsLogo != "")
                                            <img src="/img/flags/16/{{ $vsLogo }}" alt="{{$vsLogo}}"> @endif
                                            <strong>
                                            <a href="{{ groute('team.view', 'current', [$vsTeam->id]) }}">
                                            {{ $vsTeam->name }}
                                            </a>
                                        </strong>
                                        </td>
                                        <td>
                                            <label class="label label-{{ $label }}">{{ $match->score->opp1score }}-{{ $match->score->opp2score }}</label>
                                        </td>
                                    </tr>
                                    @endforeach @endif
                            </table>

                            <a href="{{groute('team.matches', 'current', [$team->id])}}"><button class="btn btn-primary dim">Full match history</button></a>
                        </div>
                    </div>

                </div>
                @endsection

