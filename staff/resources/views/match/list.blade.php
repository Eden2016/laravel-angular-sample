@extends('layouts.default')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>Api Matches</h2>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{groute('/')}}">Home</a>
                    </li>
                    <li>
                        <a href="{{groute('matches.list')}}">Matches</a>
                    </li>
                    <li class="active">
                        <strong>Api Matches</strong>
                    </li>
                </ol>
            </div>
            <div class="col-lg-2">

            </div>
        </div>
    <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-md-8">
                    
                    <div class="col-md-12">
                       <h3>Match List</h3>
                            <div class="tabs-container">
                                <ul class="nav nav-tabs">
                                    <li class="active"><a data-toggle="tab" href="#tab-1" aria-expanded="true"> Completed <span class="badge badge-primary"> {{count($matches)}} </span></a></li>
                                    <li class=""><a data-toggle="tab" href="#tab-2" aria-expanded="false">Live <span class="badge badge-danger"> 2 </span></a></li>
                                    
                                </ul>
                                <div class="tab-content">
                                    <div id="tab-1" class="tab-pane active">
                                        <div class="panel-body">
                                            <table class="footable table table-stripped" data-page-size="5">
                                                <thead>
                                                    <tr>
                                                        <th>Radiant Team</th>
                                                        <th>Dire Team</th>
                                                        <th>Game</th>
                                                        <th>League</th>
                                                        <th>Started</th>
                                                        <th>View Match</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if (isset($matches) && count($matches) > 0)
                                                    @foreach ($matches as $match)
                                                        <?php
                                                        if (isset($match->series_type)) {
                                                            switch ($match->series_type) {
                                                                case 0:
                                                                    $gameType = "1";
                                                                    break;
                                                                case 1:
                                                                    $gameType = "3";
                                                                    break;
                                                                case 2:
                                                                    $gameType = "5";
                                                                    break;
                                                                case 3:
                                                                    $gameType = "7";
                                                                    break;
                                                                default:
                                                                    $gameType = "1";
                                                            }
                                                        } else {
                                                            $gameType = "1";
                                                        }

                                                        $league = $match->league;
                                                        $league = $league != null ? $league->name : "";
                                                        ?>
                                                        <tr>
                                                            <td>{{ $match->radiant ? : "Radiant" }}</td>
                                                            <td>{{ $match->dire ? : "Dire"}}</td>
                                                            <td>{{ $match->game_number }} of {{ $gameType }}</td>
                                                            
                                                            <td><a href="{{ groute('tournament.view', 'current', [$match->league_id])}}">{{ $league }}</a></td>
                                                            <td>{{ date("d-m-Y H:i", $match->started_at) }}</td>
                                                            <td><a href="{{ groute('match', 'current',  [$match->match_id])}}"><button class="btn btn-primary dim" type="button">View</button></a></td>
                                                        </tr>
                                                    @endforeach
                                                    @endif
                                                    </tbody>
                                                
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="6">
                                                            <ul class="pagination pull-right"></ul>
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                    <div id="tab-2" class="tab-pane">
                                        <div class="panel-body">
                                            <table class="footable table table-stripped" data-page-size="6">
                                                <thead>
                                                    <tr>
                                                        <th class="text-right">Home Team</th>
                                                        <th class="text-center">Win Percentage</th>
                                                        <th class="text-center">#</th>
                                                        <th class="text-center">Win Percentage</th>
                                                        <th>Away Team</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    
                                                    <tr><td class="text-right">EHOME <img src="/img/flags/16/China.png" alt="flag"></td>
                                                        <td class="text-center"><p data-toggle="tooltip" data-original-title="88%"><span class="pie">88/100</span></p></td>
                                                        <td class="text-center">VS</td>
                                                        <td class="text-center"><p data-toggle="tooltip" data-original-title="91%"><span class="pie">91/100</span></p></td>
                                                        <td><img src="/img/flags/16/Canada.png" alt="flag"> Not Today</td>
                                                        <td class="text-right"><a href="{{groute('match.single')}}"><button class="btn btn-primary dim" type="button">View</button></a></td>
                                                    </tr>
                                                    <tr><td class="text-right">Team Spirit <img src="/img/flags/16/Ukraine.png" alt="flag"></td>
                                                        <td class="text-center"><p data-toggle="tooltip" data-original-title="85%"><span class="pie">85/100</span></p></td>
                                                        <td class="text-center">VS</td>
                                                        <td class="text-center"><p data-toggle="tooltip" data-original-title="95%"><span class="pie">95/100</span></p></td>
                                                        <td><img src="/img/flags/16/Belarus.png" alt="flag"> PowerRangers</td>
                                                        <td class="text-right"><a href="{{groute('match.single')}}"><button class="btn btn-primary dim" type="button">View</button></a></td>
                                                    </tr>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="6">
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

@endsection