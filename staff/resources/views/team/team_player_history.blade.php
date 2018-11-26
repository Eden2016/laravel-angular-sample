@extends('layouts.default')

@section('content')
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
                        <strong>Player history</strong>
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
                        <div class="ibox-content teams-latest-matches-tbl">
                            <p>
                                <h3>{{ $team->name }} roster history</h3></p>
                            <table class="table ">
                                <thead>
                                    <tr>
                                        <th>Player</th>
                                        <th>Date from</th>
                                        <th>Date to</th>
                                    </tr>
                                </thead>
                                @if (isset($rosterHistory) && count($rosterHistory))
                                @foreach ($rosterHistory as $player)
                                <tr>
                                    <td>
                                        <a href="{{groute('player.show', 'current', [$player->id])}}">{{ $player->nickname }}</a>
                                    </td>
                                    <td>{{ $player->pivot->start_date }}</td>
                                    <td>{{ $player->pivot->end_date }}</td>
                                </tr>
                                @endforeach
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
                @endsection