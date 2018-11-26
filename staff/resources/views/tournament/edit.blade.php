@extends('layouts.default')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Tournament</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{groute('/')}}">Home</a>
                </li>
                <li>
                    <a href="{{groute('tournaments.list')}}">Tournaments</a>
                </li>
                <li class="active">
                    <strong>Edit Tournament</strong>
                </li>
            </ol>
        </div>
        <div class="col-lg-2">

        </div>
    </div>
    <div class="wrapper wrapper-content  animated fadeInRight article">
        <div class="row">
            <div class="col-lg-8">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Edit Tournament</h5>
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
                        @endif @if (!isset($errorMessage))
                        <!-- Create Post Form -->
                            <form action="{{groute('tournament.save')}}" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="event" value="{{ $tournament->event_id }}" />
                            <input type="hidden" name="id" value="{{ $tournament->id }}" />

                            <div class="form-group">
                                <label for="name">Tournament Name</label>
                                <input type="text" class="form-control" name="name" id="name" value="{{ old('name') ? : $tournament->name }}" />
                            </div>
                            <div class="form-group">
                                <label for="location">Tournament Location</label>
                                <input type="text" id="location" name="location" class="form-control" value="{{ old('location') ? : $tournament->location }}" />
                            </div>
                            <div class="form-group">
                                <label for="location">Tournament Venue</label>
                                <input type="text" id="venue" name="venue" class="form-control" value="{{ old('venue') ? : $tournament->venue }}" />
                            </div>
                            <div class="form-group">
                                <label for="game">Tournament Game</label>
                                <select class="form-control" name="game" id="game">
                            @foreach ($games as $k=>$game)
                              <option value="{{ $game->id }}" @if($tournament->game_id == $game->id) selected @endif>{{ $game->name }}</option>
                            @endforeach
                            </select>
                            </div>
                            <div class="form-group">
                                <label for="maps">Maps:</label>
                                <select class="select2" name="maps[]" id="maps" multiple style="width: 100%">
                                    @foreach(\App\Game::allCached() as $game)
                                        <optgroup label="{{$game->name}}">
                                            @foreach($game->maps as $map)
                                                <option value="{{$map->id}}"
                                                        @if(in_array($map->id, $tournament->maps_ids)) selected @endif>{{$map->name}}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group" id="link_with_league_container">
                                <label for="league">Link With League</label>
                                <input type="text" class="form-control" name="league" id="league" value="{{ old('league') ? : $leagueName }}" autocomplete="off" />
                                <input type="hidden" name="leagueid" id="leagueid" value="{{ old('leagueid') ? : $tournament->league_id }}" />
                                <div class="leagueSuggestions">
                                    <ul id="leagueSuggestions">

                                    </ul>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="season">Season</label>
                                <input type="text" class="form-control" name="season" id="season" value="{{ old('season') ? : $tournament->season }}" />
                            </div>
                            <div class="form-group">
                                <label for="url">URL</label>
                                <input type="text" class="form-control" name="url" id="url" value="{{ old('url') ? : $tournament->url }}" />
                            </div>
                            <label for="start">Start Date</label>
                            <div class='input-group date' id='startHolder'>
                                <input type="text" class="form-control" name="start" id="start" value="{{ old('start') ? : date_convert($tournament->start, 'UTC', Auth::user()->timezone, 'Y-m-d H:i:s', 'Y-m-d H:i:s') }}" />
                                <span class="input-group-addon">
                                  <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                            <label for="end">End Date</label>
                            <div class='input-group date' id='endHolder'>
                                <input type="text" class="form-control" name="end" id="end" value="{{ old('end') ? : date_convert($tournament->end, 'UTC', Auth::user()->timezone, 'Y-m-d H:i:s', 'Y-m-d H:i:s') }}" />
                                <span class="input-group-addon">
                                  <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                            <div class="form-group">
                                <label for="prize">Prize Money</label>
                                <input type="text" class="form-control" name="prize" id="prize" value="{{ old('prize') ? : $tournament->prize }}" />
                            </div>
                            <div class="form-group">
                                <label for="currency">Prize Currency</label>
                                <select class="form-control" name="currency" id="currency">
                            @foreach (\App\Tournament::listCurrencies() as $k=>$currency)
                              <option value="{{ $k }}" @if($tournament->currency == $k) selected @endif>{{ $currency }}</option>
                            @endforeach
                            </select>
                            </div>
                            <div class="form-group">
                                <label for="distroType">Distribution Type</label>
                                <select class="form-control" name="distroType" id="distroType">
                            @foreach (\App\Tournament::listDistributions() as $k=>$distro)
                              <option value="{{ $k }}" @if($tournament->prize_dist_type == $k) selected @endif>{{ $distro }}</option>
                            @endforeach
                            </select>
                            </div>
                            <div class="input-group">
                                <label for="prize_distribution">Prize Distribution</label>
                                <div id="prizeDistHolder">
                                    @foreach ($prizeDistributions as $distro)
                                    <input type="text" class="form-control" name="prizeDist[]" value="{{ $distro }}" style="margin-top: 10px;" /> @endforeach
                                </div>
                                <span class="input-group-addon" style="background:none;border:none;">
                                <button type="button" class="btn btn-primary dim" id="addField" >Add Field</button>
                            </span>
                            </div>
                            <div class="form-group">
                                <textarea class="form-control" rows="3" name="description" style="margin-top:20px;">{{ old('description') ? : $tournament->description }}</textarea>
                            </div>


                            <div class="checkbox">
                                <label>
                              <input type="checkbox" name="active" value="1" @if($tournament->active) checked @endif> Is active
                            </label>
                            </div>
                            <div class="checkbox">
                                <label>
                              <input type="checkbox" name="hidden" value="1" @if($tournament->hidden) checked @endif> Is hidden
                            </label>
                            </div>
                            <div class="form-group">
                                <label for="streams">Streams:</label>
                                <select class="select2" name="streams[]" id="streams" multiple style="width: 100%">
                                    @foreach(\App\Models\Streams::all() as $stream)
                                        <option value="{{$stream->id}}" @if(is_array($tournament->all_streams_ids) && in_array($stream->id, $tournament->all_streams_ids)) selected @endif>{{$stream->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                                <div class="form-group">
                                    <label for="notable_teams">Notable teams:</label>
                                    <select class="select2" name="notable_teams[]" id="notable_teams" multiple
                                            style="width: 100%">
                                        @foreach($tournament->teams as $team)
                                            <option value="{{$team->id}}"
                                                    @if(is_array($tournament->notable_teams) && in_array($team->id, $tournament->notable_teams)) selected @endif>{{$team->name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                            @if($tournament->logo)
                            <div class="form-group">
                                <label>Current:</label>
                                <img src="http://static.esportsconstruct.com/{{$tournament->logo}}" alt="{{$tournament->name}}" class="img-responsive">
                            </div>
                            <div class="checkbox">
                                <label>
                                        <input type="checkbox" name="remove_image" value="true"> Remove image file
                                    </label>
                            </div>
                            @endif
                            <div class="form-group">
                                <label for="tournament_logo">Tournament logo</label>
                                <input type="file" name="file" id="tournament_logo">
                                <p class="help-block text-danger">Recommended dimensions 326x218px</p>
                            </div>

                            @if($tournament->toutou_logo)
                            <div class="form-group">
                                <label>Current:</label>
                                <img src="http://static.esportsconstruct.com/{{ $tournament->toutou_logo }}" alt="{{$tournament->name}}" class="img-responsive">
                            </div>
                            <div class="checkbox">
                                <label>
                                        <input type="checkbox" name="remove_toutou_logo" value="true"> Remove image file
                                    </label>
                            </div>
                            @endif
                            <div class="form-group">
                                <label for="tournament_logo">Tournament TouTou Logo</label>
                                <input type="file" name="toutou_logo" id="tournament_toutou_logo">
                                <p class="help-block text-danger">Dimensions 1150x210px</p>
                            </div>
                            <button type="submit" class="btn btn-primary dim">Edit Tournament</button>
                        </form>

                        @else
                        <div class="alert alert-danger">{{ $errorMessage }}</div>
                        @endif

                    </div>
                </div>
            </div>

            @endsection

            @section('scripts')
                @parent
                <script>
                  $(function() {
                      $('[name="streams[]"], [name="maps[]"], [name="notable_teams[]"]').select2();
                    $('[name="streams[]"]').on('change', function(evt) {
                      if (evt.removed) {
                        $.post('/api/tournament/ignore_stream', {
                          id: evt.removed.element[0].value,
                          tournament: <?php echo $tournament->id ?>
                        });
                      }
                    });

                    $('#game').on('change', function() {
                      var league_container = $('#link_with_league_container');
                      if ($(this).val() != 1) {
                        league_container.hide().find('input[name="league"]').prop('disabled', true);
                      } else {
                        league_container.show().find('input[name="league"]').prop('disabled', false);
                      }
                    });
                  });

                </script>
@endsection