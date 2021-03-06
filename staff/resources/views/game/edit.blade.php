@extends('layouts.default')

@section('content')
<h3>Edit Game</h3>
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
<form action="{{groute('game.save')}}" method="post">
  <input type="hidden" name="_token" value="{{ csrf_token() }}">
  <input type="hidden" name="id" value="{{ $game->id }}">
  <div class="form-group">
    <label for="name">Game Name</label>
    <input type="text" class="form-control" name="name" id="name" placeholder="DotA 2"
           value="{{ old('name', $game->name)}}"/>
  </div>
  <div class="form-group">
    <label for="slug">Game Slug</label>
    <input type="text" class="form-control" name="slug" id="slug" value="{{ old('slug',$game->slug) }}"/>
  </div>
  <div class="form-group">
    <label for="steamid">Game Steam App Id</label>
    <input type="text" class="form-control" name="steamid" id="steamid" placeholder="773"
           value="{{ old('steamid', $game->steam_app_id)}}"/>
  </div>
  <div class="form-group">
    <label for="hashtag">Game Hashtag</label>
    <input type="text" class="form-control" name="hashtag" id="hashtag" placeholder="#dota2"
           value="{{ old('hashtag', $game->hashtag) }}"/>
  </div>
  <div class="form-group">
    <label for="subreddit">Game Subreddit</label>
    <input type="text" class="form-control" name="subreddit" id="subreddit" placeholder="dota"
           value="{{ old('subreddit', $game->subreddit)}}"/>
  </div>
  <div class="checkbox">
    <label>
      <input type="checkbox" name="hidden" value="1" @if($game->hidden) checked @endif> Is hidden
    </label>
  </div>
  <button type="submit" class="btn btn-default">Edit game</button>
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
      $("#startHolder").datetimepicker({
        format: "YYYY-MM-DD hh:mm:ss"
      });
      $("#endHolder").datetimepicker({
        format: "YYYY-MM-DD hh:mm:ss"
      });
    });
  </script>
@endsection