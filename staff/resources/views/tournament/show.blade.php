@extends('layouts.default')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Tournament</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{groute('/')}}">Home</a>
                </li>
                <li>
                    <a href="{{groute('event.view', 'current', [$tournament->event->id])}}">{{$tournament->event->name}}</a>
                </li>
                <li class="active">
                    <strong>{{$tournament->name}}</strong>
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
                    <div class="ibox-title">
                        <h3>
                            <a href="{{groute('tournaments.list')}}">
                              {{$tournament->name}}
                            </a>
                            / Season {{$tournament->season}}
                          </h3>
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-md-7">
                                <table class="table">
                                    <tr>
                                        <td>Prize Pool:</td>
                                        <td><span class="badge badge-primary">{{number_format($tournament->prize)}}</span> {{ \App\Tournament::listCurrencies()[$tournament->currency] }}</td>
                                    </tr>
                                    <tr>
                                        <td>Start / End:</td>
                                        <td>{{ date_convert($tournament->start, 'UTC', Auth::user()->timezone, 'Y-m-d H:i:s', 'd-m-Y') }} to {{ date_convert($tournament->end, 'UTC', Auth::user()->timezone, 'Y-m-d H:i:s', 'd-m-Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td>Group Stage:</td>
                                        <td>GSL</td>
                                    </tr>
                                    <tr>
                                        <td>Number of teams:</td>
                                        <td>{{$tournament->teams->count()}}</td>
                                    </tr>
                                    <tr>
                                        <td>Lead if coming from winner's bracket</td>
                                        <td>{{$tournament->lead_from_winner_bracket ? 'Yes' : 'No'}}</td>
                                    </tr>
                                    @if(count($tournament->maps))
                                        <tr>
                                            <td>Maps</td>
                                            <td>
                                                @foreach($tournament->maps as $index => $map)
                                                    {{$map->name}}@if($index!=count($tournament->maps)-1), @endif
                                                @endforeach
                                            </td>
                                        </tr>
                                    @endif

                                </table>
                                <p>{{$tournament->name}} Season {{$tournament->season}}</p>
                                <p>{{$tournament->description}}</p>
                                <a href="{{groute('tournament.edit', $tournament->game->slug, [ $tournament->id])}}">
                                    <button class="btn btn-primary dim" type="button">Edit</button>
                                </a>
                                <a href="{{groute('tournament.delete', $tournament->game->slug, [ $tournament->id])}}"
                                   id="delete">
                                    <button type="button" class="btn btn-danger dim">Delete</button>
                                </a>
                                <a href="{{groute('stage.create',$tournament->game->slug,  [ $tournament->id])}}">
                                    <button type="button" class="btn btn-primary dim">Create Stage</button>
                                </a>

                            </div>
                            <div class="col-md-5">
                                @if($tournament->logo)
                                <img class="img img-responsive pull-right" src="{{url('uploads/'.$tournament->logo)}}" alt="{{$tournament->name}}"> @endif

                            </div>
                        </div>
                    </div>
                </div>
                <div class="ibox">
                    <div class="ibox-title">
                        <h4>{{$tournament->name}} Season {{$tournament->season}} Stages</h4>
                    </div>
                    <div class="ibox-content">
                        <div class="tabs-container">
                            <ul class="nav nav-tabs">
                                <li class="active"><a data-toggle="tab" href="#tab-1" aria-expanded="true">All Stages</a></li>
                                @if (isset($stages) && count($stages) > 0) @foreach ($stages as $k => $stage)
                                <li class=""><a data-toggle="tab" href="#tab-{{ $k+2 }}" aria-expanded="false">Stage {{ $k+1 }}: {{ $stage->name }}</a></li>
                                @endforeach @endif
                            </ul>
                            <div class="tab-content">
                                <div id="tab-1" class="tab-pane active">
                                    <div class="panel-body">
                                        @if (isset($stages) && count($stages) > 0)

                                        <div class="ibox">

                                            <div class="ibox-content">

                                                <table class="footable table table-stripped">
                                                    <thead>
                                                        <tr>
                                                            <th>Name</th>
                                                            <th>Teams</th>
                                                            <th>Start</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($stages as $stage)
                                                        <tr>
                                                            <td>
                                                                <a href="{{groute('stage', 'current', ['tournamentId' => $tournament->id, 'stage' => $stage->id])}}">{{ $stage->name }}</a>
                                                            </td>
                                                            <td>{{$stage->teams->count()}}</td>
                                                            <td>{{ date_convert($stage->start, 'UTC', Auth::user()->timezone, 'Y-m-d H:i:s', 'Y-m-d H:i') }}</td>
                                                            <td>
                                                                @if ($stage->stage_status == 'live')
                                                                <span class="label label-success">Live</span> @elseif ($stage->stage_status == 'upcoming')
                                                                <span class="label label-warning-light">Upcoming</span> @elseif ($stage->stage_status == 'completed')
                                                                <span class="label label-danger">Completed</span> @endif
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        @endif
                                    </div>
                                </div>
                                @if (isset($stages) && count($stages) > 0) @foreach ($stages as $k => $stage)
                                <div id="tab-{{ $k+2 }}" class="tab-pane">
                                    <div class="panel-body">
                                        @if (isset($stage->stageFormats) && count($stage->stageFormats) > 0)

                                        <div class="ibox">

                                            <div class="ibox-content">

                                                <table class="footable table table-stripped">
                                                    <thead>
                                                        <tr>
                                                            <th>Name</th>
                                                            <th>Teams</th>
                                                            <th>Start</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($stage->stageFormats as $sf) @if ($sf->hidden === 0)
                                                        <tr>
                                                            <td>
                                                                <a href="{{groute('stages.formats.view','current' , ['tournamentId' => $tournament->id, 'stageId' => $stage->id, 'sfId' => $sf->id])}}">{{ $sf->name }}</a>
                                                            </td>
                                                            <td>{{ $sf->teams->count() }}</td>
                                                            <td>{{ date_convert($sf->start, 'UTC', Auth::user()->timezone, 'Y-m-d H:i:s', 'Y-m-d H:i') }}</td>
                                                            <td>
                                                                @if ($sf->format_status == 'live')
                                                                <span class="label label-success">Live</span> @elseif ($sf->format_status == 'upcoming')
                                                                <span class="label label-warning-light">Upcoming</span> @elseif ($sf->format_status == 'completed')
                                                                <span class="label label-danger">Completed</span> @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="4">
                                                                <p>
                                                                    <button class="btn btn-primary dim" type="button">Schedule</button>
                                                                    <button class="btn btn-primary dim" type="button">
                                                                                    @if ($stage->format == \App\Stage::TYPE_GRID_FORMAT)
                                                                            <a href="{{groute('stage_format.bracket', 'current', ['tournamentId' => $tournament->id, 'stageId' => $stage->id, 'sfId' => $sf->id])}}">Bracket</a>
                                                                                    @else
                                                                            <a href="{{groute('stage_format.group', 'current', ['tournamentId' => $tournament->id, 'stageId' => $stage->id, 'sfId' => $sf->id])}}">Group</a>
                                                                                    @endif
                                                                                </button>

                                                                </p>
                                                            </td>
                                                        </tr>

                                                        @endif @endforeach
                                                    </tbody>
                                                </table>

                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                @endforeach @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
@endsection

