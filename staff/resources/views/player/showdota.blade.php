@extends('layouts.default')

@section('content')

<h3>{{ $player->personaname }}</h3>
<hr />

<div class="row" style="padding-bottom: 10px;margin-bottom: 10px;border-bottom: 1px solid #ccc;">
	<div class="col-md-3">Current Team</div>
    <div class="col-md-3"><a
                href="{{groute('team.view', 'current', ['teamId' => $stats['current_team_id']])}}">{{ $stats['current_team'] }}</a>
    </div>
	<div class="col-md-3">Recent Teams</div>
	<div class="col-md-3">
		@if (isset($stats['recent_teams']))
		<ul style="list-style:none;">
		@foreach ($stats['recent_teams'] as $team)
			<li>
                <a href="{{groute('team.view', 'current', ['teamId' => $team['team_id']])}}">{{ $team['team'] }}</a>
			</li>
		@endforeach
		</ul>
		@endif
	</div>
</div>

<div class="row" style="padding-bottom: 10px;margin-bottom: 20px;border-bottom: 1px solid #ccc;">
	<div class="col-md-3">Current Tournaments</div>
	<div class="col-md-3">
		@if (isset($stats['current_tournaments']))
		<ul style="list-style:none;">
		@foreach ($stats['current_tournaments'] as $tournament)
			<li>
                <a href="{{groute('tournament.view', 'current', ['tournamentId' => $tournament['id']])}}">{{ $tournament['name'] }}</a>
			</li>
		@endforeach
		</ul>
		@endif
	</div>

	<div class="col-md-3">Recent Tournaments</div>
	<div class="col-md-3">
		@if (isset($stats['recent_tournaments']))
		<ul style="list-style:none;">
		@foreach ($stats['recent_tournaments'] as $tournament)
			<li>
                <a href="{{groute('tournament.view', 'current', ['tournamentId' => $tournament['id']])}}">{{ $tournament['name'] }}</a>
			</li>
		@endforeach
		</ul>
		@endif
	</div>
</div>

<div class="row" style="padding-bottom: 10px;margin-bottom: 20px;border-bottom: 1px solid #ccc;">
	<div class="col-md-3">Current Streak</div>
	<div class="col-md-3">
		@if ($stats['winStreak'] > 0)
			@if ($stats['winStreak'] > 1)
				<span style="color:green;">Won {{ $stats['winStreak'] }} matches in a row</span>
			@else
				<span style="color:green;">Won last match</span>
			@endif
		@else
			@if ($stats['loseStreak'] > 1)
				<span style="color:red;">Lost {{ $stats['loseStreak'] }} matches in a row</span>
			@else
				<span style="color:red;">Lost last match</span>
			@endif
		@endif
	</div>

	<div class="col-md-3"></div>
	<div class="col-md-3"></div>
</div>

<table class="table table-bordered">
	<thead>
		<tr>
			<td></td>
			<td>All Time</td>
			<td>12 Months</td>
			<td>3 Months</td>
			<td>1 Month</td>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td># of Matches</td>
			<td>{{ $stats['alltime']['num'] }}</td>
			<td>{{ $stats['year']['num'] }}</td>
			<td>{{ $stats['quarter']['num'] }}</td>
			<td>{{ $stats['month']['num'] }}</td>
		</tr>
		<tr>
			<td>Win %</td>
			<td>{{ number_format($stats['alltime']['win_pc'], 2) }}</td>
			<td>{{ number_format($stats['year']['win_pc'], 2) }}</td>
			<td>{{ number_format($stats['quarter']['win_pc'], 2) }}</td>
			<td>{{ number_format($stats['month']['win_pc'], 2) }}</td>
		</tr>
		<tr>
			<td>K/D/A</td>
			<td>{{ $stats['alltime']['kda'] }}</td>
			<td>{{ $stats['year']['kda'] }}</td>
			<td>{{ $stats['quarter']['kda'] }}</td>
			<td>{{ $stats['month']['kda'] }}</td>
		</tr>
		<tr>
			<td>Avarage GPM</td>
			<td>{{ number_format($stats['alltime']['gold_pm'], 2) }}</td>
			<td>{{ number_format($stats['year']['gold_pm'], 2) }}</td>
			<td>{{ number_format($stats['quarter']['gold_pm'], 2) }}</td>
			<td>{{ number_format($stats['month']['gold_pm'], 2) }}</td>
		</tr>
		<tr>
			<td>Avarage Total Gold</td>
			<td>{{ round($stats['alltime']['total_gold']) }}</td>
			<td>{{ round($stats['year']['total_gold']) }}</td>
			<td>{{ round($stats['quarter']['total_gold']) }}</td>
			<td>{{ round($stats['month']['total_gold']) }}</td>
		</tr>
		<tr>
			<td>Avarage Last Hits</td>
			<td>{{ round($stats['alltime']['last_hits']) }}</td>
			<td>{{ round($stats['year']['last_hits']) }}</td>
			<td>{{ round($stats['quarter']['last_hits']) }}</td>
			<td>{{ round($stats['month']['last_hits']) }}</td>
		</tr>
		<tr>
			<td>Avarage Denies</td>
			<td>{{ round($stats['alltime']['denies']) }}</td>
			<td>{{ round($stats['year']['denies']) }}</td>
			<td>{{ round($stats['quarter']['denies']) }}</td>
			<td>{{ round($stats['month']['denies']) }}</td>
		</tr>
		<tr>
			<td>Avarage XPM</td>
			<td>{{ round($stats['alltime']['xp_pm']) }}</td>
			<td>{{ round($stats['year']['xp_pm']) }}</td>
			<td>{{ round($stats['quarter']['xp_pm']) }}</td>
			<td>{{ round($stats['month']['xp_pm']) }}</td>
		</tr>
		<tr>
			<td>Avarage Level</td>
			<td>{{ round($stats['alltime']['level']) }}</td>
			<td>{{ round($stats['year']['level']) }}</td>
			<td>{{ round($stats['quarter']['level']) }}</td>
			<td>{{ round($stats['month']['level']) }}</td>
		</tr>
		<tr>
			<td>Most Played Hero</td>
			<td>{{ $heroMap[$stats['alltime']['hero']] }}</td>
			<td>{{ $heroMap[$stats['year']['hero']] }}</td>
			<td>{{ $heroMap[$stats['quarter']['hero']] }}</td>
			<td>{{ $heroMap[$stats['month']['hero']] }}</td>
		</tr>
	</tbody>
</table>

@endsection
