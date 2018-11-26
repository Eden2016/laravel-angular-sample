@extends('layouts.default')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Map</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ groute('/')  }}">Home</a>
            </li>
            <li>
                <a href="{{ groute('maps') }}">Maps</a>
            </li>
            <li class="active">
                <strong>Create/Edit map</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2">

    </div>
</div>
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row ">
        <div class="col-md-8 ibox">
            <div class="col-md-12 ibox-content">
                @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif @if (Session::has('success'))
                    <div class="alert alert-success">{!! session('success') !!}</div>
            @endif

            <!-- Create Post Form -->
                <form action="{{ groute('maps.form', 'current', ['id' => $map->id]) }}" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" name="name" id="name" value="{{old('name', $map->name)}}">
                    </div>
                    <div class="form-group">
                        <label for="game_id">Game</label>
                        <select name="game_id" id="game_id" class="form-control">
                            @if($map->exists)
                                @foreach(\App\Game::allCached() as $game)
                                    <option value="{{$game->id}}" @if($game->id==$map->game_id) selected @endif>{{$game->name}}</option>
                                @endforeach
                            @else
                                @foreach(\App\Game::allCached() as $game)
                                    <option value="{{$game->id}}" @if($game->slug==request()->currentGameSlug) selected @endif>{{$game->name}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="active_duty" @if($map->status=='active_duty') selected @endif>Active duty</option>
                            <option value="reserve_group" @if($map->status=='reserve_group') selected @endif>Reserve group</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="type">Type</label>
                        <select name="type" id="type" class="form-control">
                            <optgroup label="Counter-Strike:GO">
                                <option value="bomb" @if($map->type == 'bomb') selected @endif>Bomb</option>
                                <option value="hostage" @if($map->type == 'hostage') selected @endif>Hostage</option>
                            </optgroup>
                            <optgroup label="Overwatch">
                                @foreach(\App\Maps::OW_MAP_TYPES as $key => $mapType)
                                    <option value="{{$key}}" @if($map->type == $key) selected @endif>{{$mapType}}</option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" rows="5" id="description" name="description">{{ old('description', $map->description) }}</textarea>
                    </div>

                    @if($map->image)
                        <div class="form-group">
                            <label>Current:</label>
                            <img src="{{url('uploads/'.$map->image)}}" alt="{{$map->name}}" class="img-responsive">
                        </div>
                        @if($map->image)
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="remove_image" value="true"> Remove image
                                </label>
                            </div>
                        @endif
                    @endif
                    <div class="form-group">
                        <label for="event_logo">Map image</label>
                        <input type="file" name="file" id="map_image">
                        <p class="help-block text-danger">Recommended dimensions 250x150px</p>
                    </div>
                    <button type="submit" class="btn btn-primary dim">Save</button>
                </form>

            </div>
        </div>
        @endsection

        @section('scripts')
            @parent
            <script type="text/javascript">
              $(function () {
                $('body').on('shown.bs.modal', '#changesModal', function (e) {
                  $('#changesModal #before, #changesModal #after').html('');
                  $.getJSON('/logs?id=' + $(e.relatedTarget).data('id'), function (response) {
                    $('#changesModal #before').html(JSON.stringify($.parseJSON(response[0].before), null, 4));
                    $('#changesModal #after').html(JSON.stringify($.parseJSON(response[0].after), null, 4));
                  });
                });
              });
            </script>
@endsection