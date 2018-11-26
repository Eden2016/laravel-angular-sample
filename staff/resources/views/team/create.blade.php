@extends('layouts.default')

@section('content')

<div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>Add Team</h2>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{groute('/')}}">Home</a>
                    </li>
                    <li>
                        <a href="{{groute('teams.list')}}">Teams</a>
                    </li>
                    <li class="active">
                        <strong>Add Event</strong>
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
                            <h5>Create Team </h5>
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
                        @endif

                        @if (Session::has('success'))
                          <div class="alert alert-success">{!! session('success') !!}</div>
                        @endif

                        <!-- Create Post Form -->
                        <form action="{{ groute('team.save')  }}" method="post" enctype='multipart/form-data'>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">

                          <div class="form-group">
                            <label for="name">Team Name</label>
                            <input type="text" class="form-control" name="name" id="name" placeholder="Team Secret" value="{{ old('name') }}" />
                          </div>
                          <div class="form-group">
                            <label for="slug">URL Safe Name</label>
                            <input type="text" class="form-control" name="slug" id="slug" placeholder="team_secret" value="{{ old('slug') }}" />
                          </div>
                          <div class="form-group">
                            <label for="tag">Team Tag</label>
                            <input type="text" class="form-control" name="tag" id="tag" placeholder="TS" value="{{ old('tag') }}" />
                          </div>
                          <div class="form-group">
                          <label for="game">Primary Game</label>
                            <select class="form-control" name="game" id="game">
                            @foreach (\App\Game::allCached() as $k=>$game)
                              <option value="{{ $game->id }}"<?php if (request()->currentGameSlug == $game->slug) { ?> selected<?php } ?>>{{ $game->name }}</option>
                            @endforeach
                            </select>
                          </div>

                          <div class="form-group">
                            <label for="team">Link With Team</label>
                            <input type="text" class="form-control" name="team" id="team" value="{{ old('team') }}" autocomplete="off" />
                            <input type="hidden" name="teamid" id="teamid" value="{{ old('teamid') }}" />
                            <div class="leagueSuggestions">
                                <ul id="leagueSuggestions">

                                </ul>
                            </div>
                          </div>
                            <div class="form-group">
                              <label for="created">Created</label>
                              <div class='input-group date form-group' id='startHolder'>
                                  <input type="text" class="form-control" name="created" id="created" value="{{ old('created') }}" />
                                  <span class="input-group-addon">
                                      <span class="glyphicon glyphicon-calendar"></span>
                                  </span>
                              </div>
                            </div>

                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea name="description" id="description" cols="30" rows="10"
                                          class="form-control">{{old('description')}}</textarea>
                            </div>

                          <div class="form-group">
                            <label for="region">Region</label>
                            <select class="form-control" name="region" id="region">
                            @foreach (\App\Tournament::listRegions() as $k=>$region)
                              <option value="{{ $k }}">{{ $region }}</option>
                            @endforeach
                            </select>
                          </div>
                          <div class="form-group">
                            <label for="country">Country</label>
                            <select class="form-control" name="country" id="country">
                            @foreach (\App\Tournament::listCountries() as $k=>$country)
                              <option value="{{ $k }}">{{ $country }}</option>
                            @endforeach
                            </select>
                          </div>
                            <div class="form-group">
                                <label for="total_earnings">Total earnings</label>
                                <div class="input-group">
                                    <span class="input-group-addon" id="earnings-currency">$</span>
                                    <input type="text" class="form-control" name="total_earnings"  aria-describedby="earnings-currency" id="total_earnings" value="{{ old('total_earnings') }}" />
                                </div>
                            </div>
                          <div class="form-group">
                            <label for="twitter">Twitter</label>
                            <input type="text" class="form-control" name="twitter" id="twitter" value="{{ old('twitter') }}" />
                          </div>
                          <div class="form-group">
                            <label for="facebook">Facebook</label>
                            <input type="text" class="form-control" name="facebook" id="facebook" value="{{ old('facebook') }}" />
                          </div>
                          <div class="form-group">
                            <label for="vk">VK</label>
                            <input type="text" class="form-control" name="vk" id="vk" value="{{ old('vk') }}" />
                          </div>
                          <div class="form-group">
                            <label for="steam">Steam</label>
                            <input type="text" class="form-control" name="steam" id="steam" value="{{ old('steam') }}" />
                          </div>
                          <div class="form-group">
                            <label for="website">Website</label>
                            <input type="text" class="form-control" name="website" id="website" value="{{ old('website') }}" />
                          </div>

                          <h2>Upload Team Logo</h2>

                            <div class="form-group">
                                <label for="event_logo">Team logo</label>
                                <input type="file" name="file" id="team_logo">
                                <p class="help-block text-danger">Recommended dimensions 250x150px</p>
                            </div>
                                <div class="checkbox">
                            <label>
                              <input type="checkbox" name="hidden" value="1"> Is hidden
                            </label>
                          </div>

                          <div class="checkbox">
                            <label>
                              <input type="checkbox" name="active" value="1" checked> Is active
                            </label>
                          </div>
                          
                          <button type="submit" class="btn btn-primary dim">Add team</button>
                        </form>
                            
                                        
                        </div>
                    </div>
                </div>
                @endsection

