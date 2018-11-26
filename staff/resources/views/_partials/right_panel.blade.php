<?php
    $upcoming_matches = \Illuminate\Support\Facades\Cache::remember('upcoming_matches', 60, function(){
        return \App\DummyMatch::where('start', '>', \Carbon\Carbon::now()->toDateTimeString())
                ->orWhereNull('start')
                ->with('stageRound.stageFormat.stage.tournament')
                ->with('opponent1_details')
                ->with('opponent2_details')
                ->limit(20)
                ->get();
    });
    $live_matches = \Illuminate\Support\Facades\Cache::remember('live_matches', 60, function(){
       return \App\DummyMatch::where('start', '<=', \Carbon\Carbon::now()->toDateTimeString())
               ->whereNull('winner')
               ->where('is_tie', 0)
               ->where('is_forfeited', 0)
               ->with('stageRound.stageFormat.stage.tournament')
               ->with('opponent1_details')
               ->with('opponent2_details')->get();
    });
    $recent_matches = \Illuminate\Support\Facades\Cache::remember('recent_matches', 60, function(){
        return \App\DummyMatch::whereNotNull('winner')
                ->orWhere('is_tie', 1)
                ->orWhere('is_forfeited', 1)
                ->with('stageRound.stageFormat.stage.tournament')
                ->with('opponent1_details')
                ->with('opponent2_details')->get();
    });

    $upcoming_tournaments = \Illuminate\Support\Facades\Cache::remember('upcoming_tournaments', 60, function(){
        return \App\Tournament::where('start', '>', \DB::raw('NOW()'))->get();
    });
    $live_tournaments = \Illuminate\Support\Facades\Cache::remember('live_tournaments', 60, function(){
        return \App\Tournament::where('start', '<=', \DB::raw('NOW()'))
                ->where('end', '>', \DB::raw('NOW()'))
                ->where('hidden', 0)
                ->where('status', 0)
                ->get();
    });
    $recent_tournaments = \Illuminate\Support\Facades\Cache::remember('recent_tournaments', 60, function(){
        return \App\Tournament::where('start', '<=', \DB::raw('NOW()'))
                ->where('hidden', 0)
                ->where('status', 1)
                ->get();
    });
?>
    <div class="col-md-4">
        <div class="ibox float-e-margins">
            <div class="ibox-content">
                <div>
                    <h3>
                    Matches
                </h3>
                    <div class="tabs-container">
                        <ul class="nav nav-tabs">
                            <li class="active"><a style="padding-right: 10px;" data-toggle="tab" href="#right_tab-1" aria-expanded="true"> Upcoming <span class="badge badge-warning"> {{$upcoming_matches->count()}} </span></a></li>
                            <li class=""><a style="padding-right: 10px;" data-toggle="tab" href="#right_tab-2" aria-expanded="false">Live <span class="badge badge-danger"> {{$live_matches->count()}} </span></a></li>
                            <li class=""><a style="padding-right: 10px;" data-toggle="tab" href="#right_tab-3" aria-expanded="false">Recent <span class="badge badge-primary"> {{$recent_matches->count()}} </span></a></li>

                        </ul>
                        <div class="tab-content">
                            <div id="right_tab-1" class="tab-pane active">
                                <div class="panel-body">
                                    <table class="footable table table-stripped" data-page-size="5" data-limit-navigation="5">
                                        <thead>
                                            <tr>
                                                <th>Start</th>
                                                <th>Home Team</th>
                                                <th>Away Team</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($upcoming_matches as $match)
                                            <tr>
                                                <td>
                                                    <a href="{{ groute('match', 'current', [$match->match_id]) }}">
                                                        {{ $match->stageRound->stageFormat->start }}
                                                    </a>
                                                </td>
                                                <td>{{ $match->opponent1_details->name }}</td>
                                                <td>{{ $match->opponent2_details->name }}</td>

                                            </tr>
                                            @endforeach
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
                            <div id="right_tab-2" class="tab-pane">
                                <div class="panel-body">
                                    <table class="footable table table-stripped" data-page-size="6" data-limit-navigation="5">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Home Team</th>
                                                <th>Away Team</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($live_matches as $match)
                                            <tr>
                                                <td>
                                                    <a href="{{ groute('match', 'current', [$match->match_id]) }}">
                                                    {{ $match->stageRound->stageFormat->start }}
                                                </a>
                                                </td>
                                                <td>{{ $match->opponent1_details->name }}</td>
                                                <td>{{ $match->opponent2_details->name }}</td>

                                            </tr>
                                            @endforeach
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
                            <div id="right_tab-3" class="tab-pane">
                                <div class="panel-body">
                                    <table class="footable table table-stripped" data-page-size="5" data-limit-navigation="5">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Home Team</th>
                                                <th>Away Team</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recent_matches as $match)
                                            <tr>
                                                <td>
                                                    <a href="{{ groute('match', 'current', [$match->match_id]) }}">
                                                    {{ $match->stageRound->stageFormat->start }}
                                                </a>
                                                </td>
                                                <td>{{ $match->opponent1_details->name }}</td>
                                                <td>{{ $match->opponent2_details->name }}</td>

                                            </tr>
                                            @endforeach
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
                </div><br>
                <div>
                    <h3>
                    Tournaments
                </h3>
                    <div class="tabs-container">
                        <ul class="nav nav-tabs">
                            <li class="active"><a style="padding-right: 10px;" data-toggle="tab" href="#right_tab_tournaments-1" aria-expanded="true"> Upcoming <span class="badge badge-warning"> {{$upcoming_tournaments->count()}} </span></a></li>
                            <li class=""><a style="padding-right: 10px;" data-toggle="tab" href="#right_tab_tournaments-2" aria-expanded="false">Live <span class="badge badge-danger"> {{$live_tournaments->count()}} </span></a></li>
                            <li class=""><a style="padding-right: 10px;" data-toggle="tab" href="#right_tab_tournaments-3" aria-expanded="false">Recent <span class="badge badge-primary"> {{$recent_tournaments->count()}} </span></a></li>

                        </ul>
                        <div class="tab-content">
                            <div id="right_tab_tournaments-1" class="tab-pane active">
                                <div class="panel-body">
                                    <table class="footable table table-stripped" data-page-size="5" data-limit-navigation="5">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Start</th>
                                                <th>End</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($upcoming_tournaments as $tournament)
                                            <tr>
                                                <td>
                                                    <a href="{{ groute('tournament.view', 'current', [$tournament->id]) }}">
                                                    {{ $tournament->name }}
                                                </a>
                                                </td>
                                                <td>{{ date('d.m.Y H:i', strtotime($tournament->start)) }}</td>
                                                <td>{{ date('d.m.Y H:i', strtotime($tournament->end)) }}</td>

                                            </tr>
                                            @endforeach
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
                            <div id="right_tab_tournaments-2" class="tab-pane">
                                <div class="panel-body">
                                    <table class="footable table table-stripped" data-page-size="5" data-limit-navigation="5">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Start</th>
                                                <th>End</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($live_tournaments as $tournament)
                                            <tr>
                                                <td>
                                                    <a href="{{ groute('tournament.view', 'current', [$tournament->id]) }}">
                                                    {{ $tournament->name }}
                                                </a>
                                                </td>
                                                <td>{{ date('d.m.Y H:i', strtotime($tournament->start)) }}</td>
                                                <td>{{ date('d.m.Y H:i', strtotime($tournament->end)) }}</td>

                                            </tr>
                                            @endforeach
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
                            <div id="right_tab_tournaments-3" class="tab-pane">
                                <div class="panel-body">
                                    <table class="footable table table-stripped" data-page-size="5" data-limit-navigation="5">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Start</th>
                                                <th>End</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recent_tournaments as $tournament)
                                            <tr>
                                                <td>
                                                    <a href="{{ groute('tournament.view', 'current', [$tournament->id]) }}">
                                                    {{ $tournament->name }}
                                                </a>
                                                </td>
                                                <td>{{ date('d.m.Y H:i', strtotime($tournament->start)) }}</td>
                                                <td>{{ date('d.m.Y H:i', strtotime($tournament->end)) }}</td>

                                            </tr>
                                            @endforeach
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
        </div>
    </div>
