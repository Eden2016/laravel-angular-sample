@extends('layouts.default')

@section('content')

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Add Player</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{groute('/')}}">Home</a>
                </li>
                <li>
                    <a href="{{groute('players.list')}}">Players</a>
                </li>
                <li class="active">
                    <strong>Add Player</strong>
                </li>
            </ol>
        </div>
        <div class="col-lg-2">

        </div>
    </div>
    <div class="wrapper wrapper-content animated fadeInRight">

        <div class="row">
            <div class="col-lg-8">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Create Player </h5>
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
                        <form action="{{ groute('player.save')  }}" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">

                            <div class="form-group">
                                <label for="biography">Biography paragraph</label>
                                <textarea class="form-control" rows="5" id="biography" name="biography"></textarea>
                            </div>
                            @if (request()->currentGameSlug != "overwatch")
                            <div class="form-group">
                                <label for="steamid">Steam IDs</label>
                                <input type="text" class="form-control" name="steamid" id="steamid" placeholder="7656119xxxxxxxxxx, 7656119xxxxxxxxxx, 7656119xxxxxxxxxx" value="{{ old('steamid') }}" />
                            </div>
                            @endif
                            <div class="form-group">
                                <label for="earnings">Earnings</label>
                                <input type="text" class="form-control" name="earnings" id="earnings" placeholder="0" value="{{ old('earnings') }}" />
                            </div>
                            <div class="form-group">
                                <label for="nickname">Current Nick / Handle</label>
                                <input type="text" class="form-control" name="nickname" id="nickname" placeholder="supportKiller" value="{{ old('nickname') }}" />
                            </div>
                            <div class="form-group">
                                <label for="historical_handles">Historical handles, known aliases</label>
                                <input type="text" class="form-control" name="historical_handles" id="historical_handles" placeholder="theDestroyer" value="{{ old('historical_handles') }}" />
                            </div>
                            <div class="form-group">
                                <label for="first_name">First name</label>
                                <input type="text" class="form-control" name="first_name" id="first_name" placeholder="David" value="{{ old('first_name') }}" />
                            </div>

                            <div class="form-group">
                                <label for="last_name">Last name</label>
                                <input type="text" class="form-control" name="last_name" id="last_name" placeholder="Jones" value="{{ old('last_name') }}" />
                            </div>
                            <label for="date_of_birth">Date of Birth</label>
                            <div class='input-group date form-group' id='startHolder'>
                                <input type="text" class="form-control" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth') }}" />
                                <span class="input-group-addon">
                                  <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                            <div class="form-group">
                                <label for="nationality">Nationality</label>
                                <select class="form-control" name="nationality" id="nationality">
                            @foreach (\App\Tournament::listCountries() as $k=>$country)
                              <option value="{{ $k }}">{{ $country }}</option>
                            @endforeach
                            </select>
                            </div>
                            <div class="form-group">
                                <label for="location">Current location</label>
                                <select class="form-control" name="location" id="location">
                            @foreach (\App\Tournament::listCountries() as $k=>$country)
                              <option value="{{ $k }}">{{ $country }}</option>
                            @endforeach
                            </select>
                            </div>
                            <div class="form-group">
                                <label for="game">Primary Game</label>
                                <select class="form-control" name="game" id="game">
                            @foreach (\App\Game::allCached() as $k=>$game)
                              <option value="{{ $game->id }}"<?php if (request()->currentGameSlug == $game->slug) { ?> selected<?php } ?>>{{ $game->name }}</option>
                            @endforeach
                            </select>
                            </div>
                            @if (request()->currentGameSlug == "lol")
                            <div class="form-group">
                                <label for="player_role">Player Role</label>
                                <select name="player_role[]" id="player_role" class="select2" multiple
                                        style="width: 100%">
                                    <option value="1">AD</option>
                                    <option value="2">Top</option>
                                    <option value="3">Mid</option>
                                    <option value="4">Jungle</option>
                                    <option value="5">Support</option>
                                </select>
                            </div>
                            @endif
                            @if (request()->currentGameSlug == "overwatch")
                                <div class="form-group">
                                    <label for="ow_roles">The most common team role</label>
                                    <select name="ow_role[]" id="ow_roles" class="select2"
                                            style="width: 100%" multiple>
                                        <option value="1">DPS</option>
                                        <option value="2">Tank</option>
                                        <option value="3">Support</option>
                                        <option value="4">Flex</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="ow_sign_heroes">Signature Hero</label>
                                    <select name="ow_sign_heroes[]" id="ow_sign_heroes" class="select2"
                                            style="width: 100%"  multiple>
                                        @foreach($ow_heroes as $hero)
                                            <option value="{{$hero->id}}">{{$hero->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            @if (request()->currentGameSlug == "sc2")
                            <div class="form-group">
                                <label for="player_role">Player Race</label>
                                <select name="player_race[]" id="player_race" class="select2" multiple
                                        style="width: 100%">
                                    @foreach (\App\Individual::SC2_RACES as $k=>$race)
                                        <option value="{{ $k }}">{{ $race }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                            <div class="form-group">
                                <label for="twitter">Source</label>
                                <input type="text" class="form-control" name="source" id="source" value="{{ old('source') }}" />
                            </div>
                            <div class="form-group">
                                <label for="twitter">Twitter profile</label>
                                <input type="text" class="form-control" name="twitter" id="twitter" value="{{ old('twitter') }}" />
                            </div>
                            <div class="form-group">
                                <label for="facebook">Facebook profile</label>
                                <input type="text" class="form-control" name="facebook" id="facebook" value="{{ old('facebook') }}" />
                            </div>
                            <div class="form-group">
                                <label for="twitch">Twitch profile</label>
                                <input type="text" class="form-control" name="twitch" id="twitch" value="{{ old('twitch') }}" />
                            </div>
                            <div class="checkbox">
                                <label>
                              <input type="checkbox" name="active" value="1" checked> Is active
                            </label>
                            </div>
                            <div class="form-group">
                                <label for="event_logo">Player avatar</label>
                                <input type="file" name="file" id="event_logo">
                                <p class="help-block text-danger">Recommended dimensions 250x150px</p>
                            </div>
                            <button type="submit" class="btn btn-primary dim">Create player</button>
                        </form>


                    </div>
                </div>
            </div>
@endsection

@section('scripts')
    @parent
    <script type="text/javascript">
      $(function() {
        $('#ow_sign_heroes, #ow_roles').select2({ maximumSelectionSize: 3 });
      });
    </script>
@endsection