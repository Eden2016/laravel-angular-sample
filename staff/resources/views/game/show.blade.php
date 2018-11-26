@extends('layouts.default')

@section('content')
<div style="padding-top:20px;margin-left:10px;width:75px;float:right;">
	<a href="{{ groute('game.delete', 'current', ['gameId' => $game->id]) }}" id="delete">
		<button type="button" class="btn btn-danger">Hide</button>
	</a>
</div>
<div style="padding-top:20px;margin-left:10px;width:75px;float:right;">
	<a href="{{ groute('game.edit', 'current', ['gameId' => $game->id]) }}">
		<button type="button" class="btn btn-primary">Edit</button>
	</a>
</div>

<h3>{{ $game->name }}</h3>
<hr />
<div style="margin-bottom: 30px;">
	{{ $game->slug }} | {{ $game->hashtag }} | {{ $game->subreddit }}
</div>

<div style="margin-left:10px;width:155px;float:right;">
	<a href="{{groute('patch.add')}}">
		<button type="button" class="btn btn-primary">Add Patch</button>
	</a>
</div>

<h4>Patches</h4>
<hr />

@if (isset($patches) && $patches !== null)
	<ul style="list-style: none;">
	@foreach ($patches as $patch)
			<li><a href="{{ groute('patch.view', 'current', ['patchId' => $patch->id]) }}">{{ $patch->name }}</a></li>
	@endforeach
	</ul>
@endif

@endsection

@section('scripts')
	@parent
	<script type="text/javascript">
      $(document).ready(function () {
        $("#delete").on('click', function () {
          var conf = confirm('Are you sure you want to hide this game?');

          if (!conf) {
            return false;
          }
        });
      });
	</script>
@endsection