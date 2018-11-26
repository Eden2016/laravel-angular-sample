@extends('layouts.default')

@section('content')
    <div class="match-wrapp">
        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>All Tournaments</h2>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{groute('/')}}">Home</a>
                    </li>
                    <li>
                        <a href="{{groute('tournaments.list')}}">Tournaments</a>
                    </li>
                    <li class="active">
                        <strong>Group</strong>
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
                            <h3>Group A</h3>

                        </div>
                        <div class="ibox-content">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Participant</th>
                                        <th class="center" style="width:40px">Matches</th>
                                        <th class="center" style="width:40px">Wins</th>
                                        <th class="center" style="width:40px">Draws</th>
                                        <th class="center" style="width:40px">Losses</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($teams)) @foreach($teams as $team)
                                    <tr>
                                        <td>
                                            <span title="Korea, Republic of" class="flag KR"></span>
                                            <a href="{{groute('team.show', 'current', [$team->id] ) }}" class="opponent">
                                            {{ $team->name }}
                                            </a>
                                        </td>
                                        <td class="center">{{ $resulted->filter(function($m) use ($team){
                                            return $m->opponent1==$team->id || $m->opponent2==$team->id;
                                        })->count() }}</td>
                                        <td class="center">{{ $resulted->where('winner', $team->id)->count() }}</td>
                                        <td class="center">{{ $resulted->filter(function($m) use ($team){
                                            return ($m->opponent1==$team->id || $m->opponent2==$team->id) && $m->winner==0;
                                        })->count() }}</td>
                                        <td class="center">{{ $resulted->filter(function($m) use ($team){
                                            return $m->winner != null && $m->winner != $team->id;
                                        })->count() }}</td>
                                    </tr>
                                    @endforeach @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="ibox">
                        <div class="ibox-title">
                            <h3>Matches</h3>
                        </div>
                        <div class="ibox-content">
                            <h4>Resulted</h4>
                            <table class="footable table table-stripped" data-page-size="5">
                                <thead>
                                    <tr>
                                        <th class="text-right">Home Team</th>
                                        <th class="text-center">Win Percentage</th>
                                        <th class="text-center">Score</th>
                                        <th class="text-center">Win Percentage</th>
                                        <th>Away Team</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (count($resulted)) @foreach ($resulted as $match)

                                    <tr>
                                        <td class="text-right">
                                           {{ $match->opponent1_details->name }} <img src="/img/flags/16/{{str_slug($match->opponent1_details->country->countryName)}}.png" alt="{{$match->opponent1_details->country->countryName}}">
                                        </td>
                                        <td class="text-center">
                                            @if($team1_matches = $resulted->filter(function($m) use ($match){ return $m->opponent1==$match->opponent1 || $m->opponent2==$match->opponent1; })->count())
                                            <p data-toggle="tooltip" data-original-title="{{($resulted->where('winner', $match->opponent1)->count() / $team1_matches) * 100}}%">
                                                <span class="pie">{{$resulted->where('winner', $match->opponent1)->count()}}/{{$team1_matches}}</span>
                                            </p>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            {{ $match->matchGames->sum('opponent1_score') }} - {{ $match->matchGames->sum('opponent2_score') }}
                                            @if($match->start)
                                            <p>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $match->start)->format('d M, H:i') }}</p>
                                            @else
                                                <p>---</p>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($team2_matches = $resulted->filter(function($m) use ($match){ return $m->opponent1==$match->opponent2 || $m->opponent2==$match->opponent2; })->count())
                                            <p data-toggle="tooltip" data-original-title="{{($resulted->where('winner', $match->opponent2)->count() / $team2_matches) * 100}}%">
                                                <span class="pie">{{$resulted->where('winner', $match->opponent2)->count()}}/{{$team2_matches}}</span>
                                            </p>
                                            @endif
                                        </td>
                                        <td>
                                            <img src="/img/flags/16/{{str_slug($match->opponent2_details->country->countryName)}}.png" alt="{{$match->opponent2_details->country->countryName}}"> {{ $match->opponent2_details->name }}
                                        </td>
                                        <td class="text-right">
                                            <a href="{{ groute('match', 'current', [$match->id])}}">
                                                <button class="btn btn-primary dim" type="button">View</button>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach @endif
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="5">
                                            <ul class="pagination pull-right"></ul>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                            <h4>Scheduled</h4>
                            <table class="footable table table-stripped" data-page-size="5">
                                <thead>
                                    <tr>
                                        <th class="text-right">Home Team</th>
                                        <th class="text-center">Win Percentage</th>
                                        <th class="text-center">Date</th>
                                        <th class="text-center">Win Percentage</th>
                                        <th>Away Team</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($scheduled)) @foreach($scheduled as $match)
                                    <tr>
                                        <td class="text-right">
                                            {{ $match->opponent1_details->name }} <img src="/img/flags/16/{{str_slug($match->opponent1_details->country->countryName)}}.png" alt="{{$match->opponent1_details->country->countryName}}">
                                        </td>
                                        <td class="text-center">
                                            @if($team1_matches = $resulted->filter(function($m) use ($match){ return $m->opponent1==$match->opponent1 || $m->opponent2==$match->opponent1; })->count())
                                                <p data-toggle="tooltip" data-original-title="{{($resulted->where('winner', $match->opponent1)->count() / $team1_matches) * 100}}%">
                                                    <span class="pie">{{$resulted->where('winner', $match->opponent1)->count()}}/{{$team1_matches}}</span>
                                                </p>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($match->start)
                                                <p>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $match->start)->format('d M, H:i') }}</p>
                                            @else
                                                <p>---</p>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($team2_matches = $resulted->filter(function($m) use ($match){ return $m->opponent1==$match->opponent2 || $m->opponent2==$match->opponent2; })->count())
                                                <p data-toggle="tooltip" data-original-title="{{($resulted->where('winner', $match->opponent2)->count() / $team2_matches) * 100}}%">
                                                    <span class="pie">{{$resulted->where('winner', $match->opponent2)->count()}}/{{$team2_matches}}</span>
                                                </p>
                                            @endif
                                        </td>
                                        <td>
                                            <img src="/img/flags/16/{{str_slug($match->opponent2_details->country->countryName)}}.png" alt="{{$match->opponent2_details->country->countryName}}"> {{ $match->opponent2_details->name }}
                                        </td>
                                        <td class="text-right">
                                            <a href="{{ groute('match', 'current', [$match->id])}}">
                                                <button class="btn btn-primary dim" type="button">View</button>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach @endif
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="5">
                                            <ul class="pagination pull-right"></ul>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                            <h4>Not Resulted</h4>
                            <table class="footable table table-stripped" data-page-size="5">
                                <thead>
                                    <tr>
                                        <th class="text-right">Home Team</th>
                                        <th class="text-center">Win Percentage</th>
                                        <th class="text-center">Score</th>
                                        <th class="text-center">Win Percentage</th>
                                        <th>Away Team</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($not_resulted)) @foreach($not_resulted as $match)
                                    <tr>
                                        <td class="text-right">
                                            {{ $match->opponent1_details->name }} <img src="/img/flags/16/{{str_slug($match->opponent1_details->country->countryName)}}.png" alt="{{$match->opponent1_details->country->countryName}}">
                                        </td>
                                        <td class="text-center">
                                            @if($team1_matches = $resulted->filter(function($m) use ($match){ return $m->opponent1==$match->opponent1 || $m->opponent2==$match->opponent1; })->count())
                                                <p data-toggle="tooltip" data-original-title="{{($resulted->where('winner', $match->opponent1)->count() / $team1_matches) * 100}}%">
                                                    <span class="pie">{{$resulted->where('winner', $match->opponent1)->count()}}/{{$team1_matches}}</span>
                                                </p>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <p>-</p>
                                        </td>
                                        <td class="text-center">
                                            @if($team2_matches = $resulted->filter(function($m) use ($match){ return $m->opponent1==$match->opponent2 || $m->opponent2==$match->opponent2; })->count())
                                                <p data-toggle="tooltip" data-original-title="{{($resulted->where('winner', $match->opponent2)->count() / $team2_matches) * 100}}%">
                                                    <span class="pie">{{$resulted->where('winner', $match->opponent2)->count()}}/{{$team2_matches}}</span>
                                                </p>
                                            @endif
                                        </td>
                                        <td>
                                            <img src="/img/flags/16/{{str_slug($match->opponent2_details->country->countryName)}}.png" alt="{{$match->opponent2_details->country->countryName}}"> {{ $match->opponent2_details->name }}
                                        </td>
                                        <td class="text-right">
                                            <a href="{{ groute('match', 'current', [$match->id])}}">
                                                <button class="btn btn-primary dim" type="button">View</button>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach @endif

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
@endsection