@extends('layouts.default')

@section('content')

    <h3>Edit Patch</h3>
<hr />

@if (count($errors) > 0)
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (!isset($errorMessage))
<!-- Create Post Form -->
<form action="{{groute('patch.save')}}" method="post">
  <input type="hidden" name="_token" value="{{ csrf_token() }}">
  <input type="hidden" name="id" value="{{ $patch->id }}">
  <div class="form-group">
    <label for="game">Game</label>
    <select class="form-control" name="game" id="game">
      @foreach ($games as $k=>$game)
      <option value="{{ $game->id }}"<?php if ($patch->game_id == $game->id) { ?> selected<?php } ?>>{{ $game->name }}</option>
      @endforeach
    </select>
  </div>
  <div class="form-group">
    <label for="name">Patch Name</label>
    <input type="text" class="form-control" name="name" id="name" placeholder="v1.1.4" value="{{ old('name') ? : $patch->name }}" />
  </div>
  <div class='input-group date' id='dateHolder'>
      <label for="date">Patch Date</label>
      <input type="text" class="form-control" name="date" id="date" value="{{ old('date') ? : $patch->date }}" />
      <span class="input-group-addon">
          <span class="glyphicon glyphicon-calendar"></span>
      </span>
  </div>
  <div class="checkbox">
    <label>
      <input type="checkbox" name="hidden" value="1"<?php if ($game->hidden) { ?> checked<?php } ?>> Is hidden
    </label>
  </div>
  <button type="submit" class="btn btn-default">Edit patch</button>
</form>


@else
  <div class="alert alert-danger">{{ $errorMessage }}</div>
@endif


@endsection

@section('scripts')
    @parent
    <script type="text/javascript" src="/bower_components/moment/min/moment.min.js"></script>
    <script type="text/javascript" src="/bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript">
      $(document).ready(function () {
        $("#dateHolder").datetimepicker({
          format: "YYYY-MM-DD"
        });
      });
    </script>
@endsection