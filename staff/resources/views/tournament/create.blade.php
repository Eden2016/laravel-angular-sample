@extends('layouts.default')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Add Tournament</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{groute('/')}}">Home</a>
                </li>
                <li>
                    <a href="{{groute('tournaments.list')}}">Tournaments</a>
                </li>
                <li class="active">
                    <strong>Add Tournament</strong>
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
                        <h5>Create Tournament</h5>
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

                        <form action="{{groute('tournament.save')}}" method="post">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="event" value="{{ $event }}" />
                            <fieldset>
                                <h2>Tournament Details</h2>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label>Tournament Name *</label>
                                            <input id="tournName" name="name" type="text" class="form-control required">
                                        </div>
                                        <div class="form-group">
                                            <label for="location">Tournament Location</label>
                                            <input type="text" id="location" name="location" class="form-control" />
                                        </div>
                                        <div class="form-group">
                                            <label for="location">Tournament Venue</label>
                                            <input type="text" id="venue" name="venue" class="form-control" value="" />
                                        </div>
                                        <div class="form-group">
                                            <label for="game">Tournament Game</label>
                                            <select class="form-control" name="game" id="game">
                                                    @foreach ($games as $k=>$game)
                                                    <option value="{{ $game->id }}"
                                                            @if(request()->currentGameSlug == $game->slug)  selected @endif>{{ $game->name }}</option>
                                                    @endforeach
                                                </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="maps">Maps:</label>
                                            <select class="select2" name="maps[]" id="maps" multiple style="width: 100%">
                                                @foreach(\App\Game::allCached() as $game)
                                                    @if(request()->currentGameSlug == $game->slug)
                                                    <optgroup label="{{$game->name}}">
                                                        @foreach($game->maps as $map)
                                                            <option value="{{$map->id}}">{{$map->name}}</option>
                                                        @endforeach
                                                    </optgroup>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group" id="link_with_league_container">
                                            <label for="league">Link With League</label>
                                            <input type="text" class="form-control" name="league" id="league" value="" autocomplete="off">
                                            <input type="hidden" name="leagueid" id="leagueid" value="">
                                            <div class="leagueSuggestions" style="display: none;">
                                                <ul id="leagueSuggestions">

                                                </ul>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="season">Season</label>
                                            <input type="text" class="form-control" name="season" id="season" value="">
                                        </div>
                                        <div class="form-group">
                                            <label for="url">URL</label>
                                            <input type="text" class="form-control" name="url" id="url" value="">
                                        </div>
                                        <div class="form-group">
                                            <label for="prize">Prize Money</label>
                                            <input type="text" class="form-control" name="prize" id="prize" value="">
                                        </div>
                                        <label for="currency">Prize Currency</label>
                                        <div class="form-group">
                                            <select class="form-control" name="currency" id="currency">
                                                    @foreach (\App\Tournament::listCurrencies() as $k=>$currency)
                                                      <option value="{{ $k }}">{{ $currency }}</option>
                                                    @endforeach
                                                </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="distroType">Distribution Type</label>
                                            <select class="form-control" name="distroType" id="distroType">
                                                    @foreach (\App\Tournament::listDistributions() as $k=>$distro)
                                                      <option value="{{ $k }}">{{ $distro }}</option>
                                                    @endforeach
                                                </select>
                                        </div>
                                        <div class="input-group form-group">
                                            <label for="prize_distribution">Prize Distribution</label>
                                            <input type="text" class="form-control" name="prizeDist[]" id="prize_distribution" placeholder="1 place" value="">
                                            <div id="prizeDistHolder"></div>
                                            <span class="input-group-addon" style="background:none;border:none;padding:0;vertical-align:bottom;">
                                                    <button type="button" class="btn btn-primary" id="addField">Add Field</button>
                                                </span>
                                        </div>
                                        <div class="form-group">
                                            <label>Tournament Description</label>
                                            <textarea class="form-control" rows="3" name="description"></textarea>
                                        </div>
                                    </div>

                                </div>

                            </fieldset>
                            <hr />
                            <fieldset>
                                <h2>Date of Tournament</h2>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label for="start">Start Date</label>
                                        <div class="input-group date form-group" id="startHolder">
                                            <input type="text" class="form-control" name="start" id="start" value="{{\App\Event::find($event)->start}}">
                                            <span class="input-group-addon">
                                                      <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                        <label for="start">End Date</label>
                                        <div class="input-group date form-group" id="endHolder">
                                            <input type="text" class="form-control" name="end" id="end" />
                                            <span class="input-group-addon">
                                                      <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>

                                        <div class="form-group">
                                            <div class="checkbox">
                                                <label>
                                                      <input type="checkbox" name="active" value="1" checked> Is active
                                                    </label>
                                            </div>
                                            <div class="checkbox">
                                                <label>
                                                      <input type="checkbox" name="hidden" value="1"> Is hidden
                                                    </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>

                            <button type="submit" class="btn btn-primary dim ">Create</button>

                            <!--
                                <h1>Upload Tournament Image</h1>
                                <fieldset>
                                <div class="row">
                                        <div class="col-lg-8">
                                            <div id="my-awesome-dropzone" class="dropzone" action="#">
                                                <div class="dropzone-previews"></div>
                                                
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
-->

                            @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>

            @endsection

            @section('scripts')
                @parent
                <script>
                  $(function() {
                    $('[name="maps[]"]').select2();
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