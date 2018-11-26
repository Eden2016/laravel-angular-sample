@extends('layouts.default')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>All Matches</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{groute('/')}}">Home</a>
                </li>
                <li>
                    <a href="{{groute('tournaments.list')}}">Tournaments</a>
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
        <div class="row">
            <div class="col-sm-8">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-md-12">
                                <h3>Tournament List</h3>
                                <div class="tabs-container">
                                    <ul class="nav nav-tabs">
                                        <li class="active"><a data-toggle="tab" href="#tab-1" aria-expanded="false">Live <span class="badge badge-danger">{{ count($liveTournaments) }}</span></a></li>
                                        <li class=""><a data-toggle="tab" href="#tab-3" aria-expanded="false">Upcoming <span class="badge badge-warning">{{ count($upcomingTournaments) }}</span></a></li>
                                        <li class=""><a data-toggle="tab" href="#tab-2" aria-expanded="true"> Completed <span class="badge badge-primary">{{ count($completedTournaments) }}</span></a></li>
                                    </ul>
                                    <div class="tab-content">
                                        <div id="tab-1" class="tab-pane active">
                                            <div class="panel-body">
                                                <div class="form-group">
                                                    <input type="text" class="form-control" id="tableFilter" title="Filter" placeholder="Filter results">
                                                </div>
                                                <table class="footable table table-stripped" data-filter="#tableFilter" data-filter-minimum="3" data-filter-timeout="500" data-filter-text-only="true" data-page-size="20" data-limit-navigation="5">
                                                    <thead>
                                                        <tr>
                                                            <th>Tournament</th>
                                                            <th>Start</th>
                                                            <th>End</th>
                                                            <th>Prize Pool</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>

                                                        @foreach ($liveTournaments as $tournament)
                                                        <tr>
                                                            <td><a href="{{ groute('tournament.view', $tournament->game->slug, [$tournament->id]) }}">{{$tournament->name}}</a></td>

                                                            <td data-value="{{ strtotime($tournament->start) }}">{{ date_convert($tournament->start, 'UTC', Auth::user()->timezone, 'Y-m-d H:i:s', 'd-m-Y') }}</td>
                                                            <td data-value="{{ strtotime($tournament->end) }}">{{ date_convert($tournament->end, 'UTC', Auth::user()->timezone, 'Y-m-d H:i:s', 'd-m-Y') }}</td>

                                                            <td data-value="{{ $tournament->prize }}"><span class="badge badge-primary">{{number_format($tournament->prize)}}</span> {{ \App\Tournament::listCurrencies()[$tournament->currency] }}</td>
                                                        </tr>
                                                        @endforeach

                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <td colspan="5">
                                                                <ul class="pagination pull-right"></ul>
                                                            </td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                        <div id="tab-2" class="tab-pane ">
                                            <div class="panel-body">
                                                <div class="form-group">
                                                    <input type="text" class="form-control" id="tableFilter" title="Filter" placeholder="Filter results">
                                                </div>
                                                <table class="footable table table-stripped" data-filter="#tableFilter" data-filter-minimum="3" data-filter-timeout="500" data-filter-text-only="true" data-page-size="20" data-limit-navigation="5">
                                                    <thead>
                                                        <tr>
                                                            <th>Tournament</th>
                                                            <th>Start</th>
                                                            <th>End</th>
                                                            <th>Prize Pool</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($completedTournaments as $tournament)
                                                        <tr>
                                                            <td><a href="{{ groute('tournament.view', $tournament->game->slug, [$tournament->id]) }}">{{$tournament->name}}</a></td>

                                                            <td data-value="{{ strtotime($tournament->start) }}">{{ date("d-m-Y", strtotime($tournament->start)) }}</td>
                                                            <td data-value="{{ strtotime($tournament->end) }}">{{ date("d-m-Y", strtotime($tournament->end)) }}</td>

                                                            <td data-value="{{ $tournament->prize }}"><span class="badge badge-primary">{{number_format($tournament->prize)}}</span> {{ \App\Tournament::listCurrencies()[$tournament->currency] }}</td>

                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <td colspan="5">
                                                                <ul class="pagination pull-right"></ul>
                                                            </td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>

                                        <div id="tab-3" class="tab-pane">
                                            <div class="panel-body">
                                                <div class="form-group">
                                                    <input type="text" class="form-control" id="tableFilter" title="Filter" placeholder="Filter results">
                                                </div>
                                                <table class="footable table table-stripped" data-filter="#tableFilter" data-filter-minimum="3" data-filter-timeout="500" data-filter-text-only="true" data-page-size="20" data-limit-navigation="5">
                                                    <thead>
                                                        <tr>
                                                            <th>Tournament</th>
                                                            <th>Start</th>
                                                            <th>End</th>
                                                            <th>Prize Pool</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>

                                                        @foreach ($upcomingTournaments as $tournament)
                                                        <tr>
                                                            <td><a href="{{ groute('tournament.view', $tournament->game->slug, [$tournament->id]) }}">{{$tournament->name}}</a></td>

                                                            <td data-value="{{ strtotime($tournament->start) }}">{{ date("d-m-Y", strtotime($tournament->start)) }}</td>
                                                            <td data-value="{{ strtotime($tournament->end) }}">{{ date("d-m-Y", strtotime($tournament->end)) }}</td>

                                                            <td data-value="{{ $tournament->prize }}"><span class="badge badge-primary">{{number_format($tournament->prize)}}</span> {{ \App\Tournament::listCurrencies()[$tournament->currency] }}</td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <td colspan="5">
                                                                <ul class="pagination pull-right"></ul>
                                                            </td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
@endsection