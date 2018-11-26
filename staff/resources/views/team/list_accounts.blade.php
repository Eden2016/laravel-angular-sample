@extends('layouts.default')

@section('content')

<ul class="nav nav-pills">
    <li role="presentation" class="active"><a href="{{groute('team.create')}}">Create Team</a></li>
</ul>

<div style="float: right;margin-top: 20px;width: 300px;">
	<form method="post" name="searchform">
	<input type="hidden" name="_token" value="{{ csrf_token() }}">
	<div class="input-group">
      <input type="text" name="name" class="form-control" placeholder="Name">
      <span class="input-group-btn">
        <button class="btn btn-default" type="button" onclick="javascript: submitform()">Search</button>
      </span>
    </div><!-- /input-group -->
    </form>
</div>
<h3>List of Team Accounts</h3>
<hr />

<table class="table table-bordered">
	<thead>
		<tr>
			<td>ID</td>
			<td>Team Name</td>
			<td>Team Tag</td>
			<td>Created</td>
		</tr>
	</thead>
	<tbody>
	@if (isset($teams) && count($teams) > 0)
	@foreach ($teams as $team)
		<tr>
			<td>{{ $team->id }}</td>
			<td><a href="{{ groute('team.show', 'current', [$team->id]) }}">{{ $team->name }}</a></td>
			<td><a href="{{ groute('team.show', 'current', [$team->id]) }}">{{ $team->tag }}</a></td>
			<td><a href="{{ groute('team.show', 'current', [$team->id]) }}">{{ $team->created }}</a></td>
		</tr>
	@endforeach
	@endif
	</tbody>
</table>

@if (isset($pagesNum) && $pagesNum > 0)
<div style="margin-top: 10px;">
	<nav>
		<ul class="pagination pagination-lg">
		<li>
            <a href="{{groute('teams.list')}}" aria-label="First">
	        <span aria-hidden="true">First</span>
	      </a>
	    </li>
		<li>
            <a href="{{groute('teams.list', 'current', ['page' => $currentPage - 1])}}" aria-label="Previous">
	        <span aria-hidden="true">&laquo;</span>
	      </a>
	    </li>
	    @if ($startPage > 1)
		<li><a href="javascript:void()"><span>...</span></a></li>
		@endif

		@for ($i = $startPage; $i <= $endPage; $i++)
		@if ($i == $currentPage)
		<li class="active"><a href="javascript:void()"><span>{{ $i }}</span></a></li>
		@else
                    <li><a href="{{groute('teams.list', 'current', ['page' => $i])}}"><span>{{ $i }}</span></a></li>
		@endif
		@endfor

		@if ($endPage < $pagesNum)
		<li><a href="javascript:void()"><span>...</span></a></li>
		@endif
		<li>
            <a href="{{groute('teams.list', 'current', ['page' => ($currentPage + 1) > $pagesNum ? $pagesNum : ($currentPage + 1)])}}"
               aria-label="Next">
	        <span aria-hidden="true">&raquo;</span>
	      </a>
	    </li>
	    <li>
            <a href="{{groute('teams.list', 'current', ['page' => $pagesNum])}}" aria-label="Last">
	        <span aria-hidden="true">Last</span>
	      </a>
	    </li>
	  </ul>
	</nav>
</div>
@endif

@endsection

@section('scripts')
	@parent
	<script type="text/javascript">
      function submitform()
      {
        document.searchform.submit();
      }
	</script>
@endsection