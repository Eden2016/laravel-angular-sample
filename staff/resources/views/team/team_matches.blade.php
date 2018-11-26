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
                    <li>
                        <a href="{{groute('team.view', 'current' ,[$team->id])}}">Team</a>
                    </li>
                    <li class="active">
                        <strong>Matches</strong>
                    </li>
                </ol>
            </div>
            <div class="col-lg-2">

            </div>
        </div>
        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-sm-12">
                    <div class="ibox">


                        <ul class="nav nav-tabs">
                            <li class="active"><a data-toggle="tab" href="#home">Live</a></li>
                            <li><a data-toggle="tab" href="#menu1">Upcoming</a></li>
                            <li><a data-toggle="tab" href="#menu2">Complited</a></li>
                        </ul>

                        <div class="tab-content">
                            <div id="home" class="tab-pane fade in active">
                                <div class="ibox-content teams-latest-matches-tbl">
                                    <p>
                                    <h3>{{ $team->name }}'s full match history</h3>
                                    </p>
                                    <table class="table ">
                                        <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th class="text-right"><span class="m-r-xl">Team 1</span></th>
                                            <th></th>
                                            <th>Team 2</th>
                                            <th>Score</th>
                                            <th>Tournament</th>
                                        </tr>
                                        </thead>

                                        @if (isset($matches) && count($matches)) @foreach ($matches as $match)
                                            <?php
                                            $label = "warning";
                                            if ($team->id === $match->opponent1) {
                                                if ($match->score->opp1score > $match->score->opp2score)
                                                    $label = "primary";
                                                else if ($match->score->opp1score < $match->score->opp2score)
                                                    $label = "danger";
                                            }
                                            else {
                                                if ($match->score->opp1score > $match->score->opp2score)
                                                    $label = "danger";
                                                else if ($match->score->opp1score < $match->score->opp2score)
                                                    $label = "primary";
                                            }
                                            ?>
                                            <tr>
                                                <td>{{ $match->start }}</td>
                                                <td class="text-right ">
                                                    <strong>
                                                        <a href="{{groute('team.view', 'current' ,[$match->opponent1])}}">
                                                            {{ $match->opponent1_details->name }}
                                                        </a>
                                                    </strong> @if($match->opponent1_details->logo)
                                                        <img src="http://static.esportsconstruct.com/{{ $match->opponent1_details->logo }}" alt=""> @elseif(isset($match->opponent1_details->country->countryName))
                                                        <img src="/img/flags/16/{{ $match->opponent1_details->country->filename }}"
                                                             alt="den"> @endif
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ groute('match.view', 'current', [
                                                $match->stageRound->stageFormat->stage->tournament->id,
                                                $match->stageRound->stageFormat->stage->id,
                                                $match->stageRound->stageFormat->id,
                                                $match->id])}}">
                                                        vs
                                                    </a>
                                                </td>
                                                <td>
                                                    @if($match->opponent2_details->logo)
                                                        <img src="http://static.esportsconstruct.com/{{ $match->opponent2_details->logo }}" alt=""> @elseif(isset($match->opponent2_details->country->countryName))
                                                        <img src="/img/flags/16/{{ $match->opponent2_details->country->filename }}"
                                                             alt="den"> @endif
                                                    <strong>
                                                        <a href="{{groute('team.view', 'current' ,[$match->opponent2])}}">
                                                            {{ $match->opponent2_details->name }}
                                                        </a>
                                                    </strong>
                                                </td>
                                                <td>
                                                    <label class="label label-{{ $label }}">{{ $match->score->opp1score }}-{{ $match->score->opp2score }}</label>
                                                </td>
                                                <td>
                                                    <a href="{{ groute('tournament.view', 'current', [$match->stageRound->stageFormat->stage->tournament->id])}}">
                                                        {{ $match->stageRound->stageFormat->stage->tournament->name }}
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach @endif
                                    </table>

                                </div>
                            </div>
                            <div id="menu1" class="tab-pane fade">
                                <h3>Upcoming</h3>
                            </div>
                            <div id="menu2" class="tab-pane fade">
                                <h3>Complited</h3>
                            </div>
                        </div>
                    </div>
                </div>
@endsection
