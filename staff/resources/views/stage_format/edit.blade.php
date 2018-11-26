@extends('layouts.default')

@section('content')

    <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>All Tournaments</h2>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{groute('/')}}">Home</a>
                    </li>
                    <li>
                        <a href="{{groute('tournaments.list')}}">Tournaments</a>
                    </li>
                    <li>
                        <a href="{{groute('stage', 'current', ['tournamentId' => $stage->tournament_id, 'stageId' => $stage->id])}}">Stage</a>
                    </li>
                    <li class="active">
                        <strong>Edit Stage Format {{ $sf->name }}</strong>
                    </li>
                </ol>
            </div>
            <div class="col-lg-2">

            </div>
        </div>
        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-sm-8">
                   <div class="ibox">
                       <div class="ibox-title">
                           <h3>Edit Stage</h3>
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

                <!-- Create Post Form -->
                <form action="{{groute('stage_format.save')}}" method="post">
                  <input type="hidden" name="_token" value="{{ csrf_token() }}">
                  <input type="hidden" name="stage" value="{{ $sf->stage_id }}" />
                  <input type="hidden" name="id" value="{{ $sf->id }}" />
                  <input type="hidden" name="format" value="{{ $sf->type }}" />
                  <input type="hidden" name="participants" value="{{ $sf->participants }}" />
                  <input type="hidden" name="qualifingParticipants" value="{{ $sf->qualifing }}" />

                  <div class="form-group">
                    <label for="name">Stage Format Name</label>
                    <input type="text" class="form-control" name="name" id="name" value="{{ old('name') ? : $sf->name }}" />
                  </div>
                      <label for="start">Start Date</label>
                  <div class='input-group date' id='startHolder'>
                      <input type="text" class="form-control" name="start" id="start" value="{{ old('start') ? : date_convert($sf->start, 'UTC', Auth::user()->timezone, 'Y-m-d H:i:s', 'Y-m-d H:i') }}" />
                      <span class="input-group-addon">
                          <span class="glyphicon glyphicon-calendar"></span>
                      </span>
                  </div>
                  <div class="form-group" id="pointsDistribution">
                      <label for="points_distribution">Points distribution type</label>
                      <select name="points_distribution" id="points_distribution"
                              class="form-control">
                          <option value="per_match"<?php if ($sf->points_distribution == 'per_match') { ?> selected<?php } ?>>Per match</option>
                          <option value="per_game"<?php if ($sf->points_distribution == 'per_game') { ?> selected<?php } ?>>Per game</option>
                      </select>
                  </div>
                  <div class="form-group" id="points_per_win_holder">
                      <label for="points_per_win">Points per win</label>
                      <input type="text" class="form-control" name="points_per_win" id="points_per_win" value="{{ old('points_per_win') ? : $sf->points_per_win }}" />
                  </div>
                  <div class="form-group" id="points_per_draw_holder">
                      <label for="points_per_draw">Points per draw</label>
                      <input type="text" class="form-control" name="points_per_draw" id="points_per_draw" value="{{ old('points_per_draw') ? : $sf->points_per_draw }}" />
                  </div>
                  <input type="hidden" name="end" id="end" value="{{ old('end') ? : $sf->end }}" />
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" value="1" id="lead_from_winner_bracket" name="lead_from_winner_bracket" @if($sf->lead_from_winner_bracket) checked @endif>
                            Lead if coming from winner's bracket
                        </label>
                    </div>
                  <div class="checkbox">
                    <label>
                      <input type="checkbox" name="active" value="1"<?php if ($sf->active) { ?> checked<?php } ?>> Is active
                    </label>
                  </div>
                  <div class="checkbox">
                    <label>
                      <input type="checkbox" name="hidden" value="1"<?php if ($sf->hidden) { ?> checked<?php } ?>> Is hidden
                    </label>
                  </div>
                  <button type="submit" class="btn btn-primary dim">Edit stage format</button>
                </form>
                       </div>
                   </div>
                    
                </div>
@endsection