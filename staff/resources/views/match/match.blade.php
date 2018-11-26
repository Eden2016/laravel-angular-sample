@extends('layouts.default')

@section('content')
<div class="match-wrapp">
    <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>Dota 2 Match</h2>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{groute('/')}}">Home</a>
                    </li>
                    <li>
                        <a href="{{groute('matches.list')}}">Matches</a>
                    </li>
                    <li class="active">
                        <strong>Match</strong>
                    </li>
                </ol>
            </div>
            <div class="col-lg-2">

            </div>
        </div>
    <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-md-8">
                    <div class="ibox">
                        <div class="ibox-title text-center">
                            <h2>{{ $match->stageRound->stageFormat->stage->tournament->name }}</h2>
                            <p><span><i class="fa fa-clock-o"></i> {{ $match->stageRound->stageFormat->stage->tournament->game->name }} - May 2016</span></p>
                        </div>
                        <div class="ibox-content">
                           <div class="row">
                                <div class="col-md-3">
                                    <a href="{{groute('team.show', 'current', [$match->opponent1_details->id])}}"><h3>{{ $match->opponent1_details->name }}</h3></a>
                                    <p class="text-muted">Ranked #33</p>
                                </div>
                                <div class="col-md-2 text-right">
                                    <img src="img/players-avatars/player-profile-avatar-01.jpeg" alt="players">
                                </div>
                                <div class="col-md-2 text-center">
                                    <h3>{{ $match->start }}</h3>
                                    <p class="text-danger">
                                        @if ($match->start != null && strtotime($match->start) > time())
                                        {{ $match->start }}
                                        @else
                                        {{ $match->opponent1_score }} 
                                        - 
                                        {{ $match->opponent2_score }}
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-2 text-left">
                                    <img src="img/players-avatars/player-profile-avatar-02.jpeg" alt="players">
                                </div>
                                <div class="col-md-3 text-right">
                                    <a href="{{groute('team.show', 'current', [$match->opponent2_details->id])}}"><h3>{{ $match->opponent2_details->name }}</h3></a>
                                    <p class="text-muted">Ranked #55</p>
                                </div>
                           </div>
                        </div>
                    </div>
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>Recent Performance</h5>
                            <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                <i class="fa fa-wrench"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-user">
                                <li><a href="#">Config option 1</a>
                                </li>
                                <li><a href="#">Config option 2</a>
                                </li>
                            </ul>
                            
                            </div>
                        </div>
                        <div class="ibox-content">
                           <div class="row">
                                <div class="col-md-5 hide-overflow">
                                    <img src="img/players-avatars/player-profile-avatar-02.jpeg" alt="players">
                                    <span> {{ $match->opponent1_details->name }}</span>
                                    <canvas class="m-t-md" id="lineChart" height="140"></canvas>
                                    
                                    <div class="ibox latest-matches">
                                        <div class="ibox-title">
                                            Latest Matches
                                        </div>
                                        <div class="ibox-content">
                                            <table class="table">
                                                <tbody><tr>
                                                    <td>vs <img src="img/players-avatars/player-01.jpg" alt="team" class="img img-circle m-r-md m-l-md"> <a href="/team">Shazam</a></td>

                                                    <td class="text-info text-right">3-0</td>
                                                </tr>
                                                <tr>
                                                    <td>vs <img src="img/players-avatars/player-02.jpg" alt="team" class="img img-circle m-r-md m-l-md"> <a href="/team">DrinkingBoys</a></td>

                                                    <td class="text-info text-right">2-0</td>
                                                </tr>
                                                <tr>
                                                </tr><tr>
                                                    <td>vs <img src="img/players-avatars/player-05.jpg" alt="team" class="img img-circle m-r-md m-l-md"> <a href="/team">Litle Cry</a></td>

                                                    <td class="text-info text-right">4-3</td>
                                                </tr>
                                                    <tr><td>vs <img src="img/players-avatars/player-03.jpg" alt="team" class="img img-circle m-r-md m-l-md"> <a href="/team">Dragon Slayers</a></td>

                                                    <td class="text-danger text-right">1-3</td>
                                                </tr>
                                                <tr>
                                                    <td>vs <img src="img/players-avatars/player-04.jpg" alt="team" class="img img-circle m-r-md m-l-md"> <a href="/team">Fatr Gums</a></td>

                                                    <td class="text-info text-right">4-3</td>
                                                </tr>
                                            </tbody></table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 text-center">
                                    <p>PAST ENCOUNTERS</p>
                                    <p><span class="badge badge-primary">2</span> - <span class="text-danger">0</span></p>
                                    <p><span class="badge badge-primary">3</span> - <span class="text-danger">1</span></p>
                                    <p><span class="text-danger">1</span> - <span class="badge badge-primary">2</span></p>
                                    <p><span class="badge badge-primary">4</span> - <span class="text-danger">3</span></p>
                                </div>
                                <div class="col-md-5 text-right hide-overflow">
                                    <span>{{ $match->opponent2_details->name }}</span>
                                    <img src="img/players-avatars/player-profile-avatar-01.jpeg" alt="players">
                                    <canvas class="m-t-md" id="lineChart2" height="140"></canvas>
                                    <div class="ibox latest-matches">
                                        <div class="ibox-title">
                                            Latest Matches
                                        </div>
                                        <div class="ibox-content">
                                            <table class="table">
                                                <tbody>
                                                <tr>
                                                   <td width="50" class="text-info text-left">3-2</td>
                                                    <td><a href="/team">Shazam</a> <img src="img/players-avatars/player-01.jpg" alt="team" class="img img-circle m-r-md m-l-md"> vs </td>
                                                </tr>
                                                <tr>
                                                   <td width="50" class="text-danger text-left">2-4</td>
                                                    <td><a href="/team">DrinkingBoys</a> <img src="img/players-avatars/player-06.jpg" alt="team" class="img img-circle m-r-md m-l-md"> vs </td>
                                                </tr>
                                                <tr>
                                                   <td width="50" class="text-info text-left">3-0</td>
                                                    <td><a href="/team">Litle Cry</a> <img src="img/players-avatars/player-09.jpg" alt="team" class="img img-circle m-r-md m-l-md"> vs </td>
                                                </tr>
                                                <tr>
                                                   <td width="50" class="text-info text-left">3-0</td>
                                                    <td><a href="/team">Dragon Slayers</a> <img src="img/players-avatars/player-08.jpg" alt="team" class="img img-circle m-r-md m-l-md"> vs </td>
                                                </tr>
                                                
                                                
                                            </tbody></table>
                                        </div>
                                    </div>
                                </div>
                           </div>
                        </div>
                    </div>
                </div>
@endsection

