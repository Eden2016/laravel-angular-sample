@extends('layouts.default')

@section('content')
<div style="padding-top:20px;margin-left:10px;width:75px;float:right;">
    <a href="{{groute('gpatch.delete', 'current', ['patchId' => $patch->id])}}" id="delete">
        <button type="button" class="btn btn-danger">Delete</button>
    </a>
</div>
<div style="padding-top:20px;margin-left:10px;width:75px;float:right;">
    <a href="{{groute('gpatch.edit', 'current', ['patchId' => $patch->id])}}">
        <button type="button" class="btn btn-primary">Edit</button>
    </a>
</div>

<h3>Patch for {{ $game->name }}</h3>
<hr />
<div style="margin-bottom: 30px;">
	<pre>{{ $patch->name }} introduced on {{ $patch->date }}</pre>
</div>

@endsection

@section('scripts')
	@parent

	<script type="text/javascript">
      $(document).ready(function () {
        $("#delete").on('click', function () {
          var conf = confirm('Are you sure you want to delete this patch?');

          if (!conf) {
            return false;
          }
        });
      });
	</script>
@endsection