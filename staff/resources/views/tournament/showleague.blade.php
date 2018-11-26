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
                        <a href="{{groute('tournaments.api_list')}}">Teams</a>
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
                                    <h3>{{ $league->name }}</h3>
                                    <p></p>
                                </div>
                                <div class="col-md-6">

                                </div>
                                <div class="col-md-3">

                                </div>
                           </div>
                           <div class="row">
                               <div class="col-md-12">
                                    <table class="footable table table-stripped" data-filter-minimum="3" data-page-size="20" data-limit-navigation="5">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Radiant</th>
                                                <th>Dire</th>
                                                <th>Start Time</th>
                                                <th>End Time</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @if (isset($matches) && count($matches) > 0)
                                        @foreach ($matches as $match)
                                            <tr>
                                                <td>
                                                    <a href="{{groute('match','current', ['matchId' => $match->match_id])}}">{{ $match->match_id }}</a>
                                                </td>

                                                <td>{{ $match->radiant_name }}</td>
                                                <td>{{ $match->dire_name }}</td>
                                                <td>{{ $match->start_time }}</td>
                                                <td>{{ date("Y-m-d H:i:s", strtotime($match->start_time) + $match->duration) }}</td>
                                            </tr>
                                        @endforeach
                                        @endif
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
@endsection