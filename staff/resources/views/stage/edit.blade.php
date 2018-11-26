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
                    <li>
                        <a href="{{groute('stage', 'current' ,['tournamentId' => $stage->tournament_id, 'stageId' => $stage->id])}}">Stage</a>
                    </li>
                    <li class="active">
                        <strong>Edit Stage {{ $stage->name }}</strong>
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
                           <form action="{{groute('stage.save')}}" method="post">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="tournament" value="{{ $stage->tournament_id }}" />
                              <input type="hidden" name="id" value="{{ $stage->id }}" />

                              <div class="form-group">
                                <label for="name">Stage Name</label>
                                <input type="text" class="form-control" name="name" id="name" value="{{ old('name') ? : $stage->name }}" />
                              </div>
                              <div class="form-group">
                                <label for="format">Format</label>
                                <select class="form-control" name="format" id="format">
                                  @foreach ($types as $k=>$type)
                                  <option value="{{ $k }}"<?php if ((null !== old('format') && old('format') == $k) || (null == old('format') && $stage->format == $k)) { ?>selected<?php } ?>>{{ $type }}</option>
                                  @endforeach
                                </select>
                              </div>
                               <div class="form-group">
                                   <label for="prize">Prize Money</label>
                                   <input type="text" class="form-control" name="prize" id="prize"
                                          value="{{ old('prize', $stage->prize) }}"/>
                               </div>
                               <div class="form-group">
                                   <label for="currency">Prize Currency</label>
                                   <select class="form-control" name="currency" id="currency">
                                       @foreach (\App\Tournament::listCurrencies() as $k=>$currency)
                                           <option value="{{ $k }}"
                                                   @if($k==$stage->currency) selected @endif>{{ $currency }}</option>
                                       @endforeach
                                   </select>
                               </div>
                               <div class="form-group">
                                   <label for="distroType">Distribution Type</label>
                                   <select class="form-control" name="distroType" id="distroType">
                                       @foreach (\App\Tournament::listDistributions() as $k=>$distro)
                                           <option value="{{ $k }}"
                                                   @if($k==$stage->prize_dist_type) selected @endif>{{ $distro }}</option>
                                       @endforeach
                                   </select>
                               </div>
                               <div class="input-group">
                                   <label for="prize_distribution">Prize Distribution</label>
                                   <div id="prizeDistHolder">
                                      @if (count(json_decode($stage->prize_distribution)) > 0)
                                       @foreach (json_decode($stage->prize_distribution) as $distro)
                                           <input type="text" class="form-control" name="prizeDist[]"
                                                  value="{{ $distro }}" style="margin-top: 10px;"/>
                                       @endforeach
                                      @endif
                                   </div>
                                   <span class="input-group-addon" style="background:none;border:none;">
                                    <button type="button" class="btn btn-primary dim" onclick="addPrizeField()">Add Field</button>
                                </span>
                               </div>
                                  <label for="start">Start Date</label>
                              <div class='input-group date' id='startHolder'>
                                  <input type="text" class="form-control" name="start" id="start" value="{{ old('start') ? : date_convert($stage->start, 'UTC', Auth::user()->timezone, 'Y-m-d H:i:s', 'Y-m-d H:i:s') }}" />
                                  <span class="input-group-addon">
                                      <span class="glyphicon glyphicon-calendar"></span>
                                  </span>
                              </div>
                              <input type="hidden" name="end" id="end" value="{{ old('end') ? : $stage->end }}" />

                              <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="active" value="1" @if($stage->active) checked @endif>
                                    Is active
                                </label>
                              </div>
                              <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="hidden" value="1" @if($stage->hidden) checked @endif>
                                    Is hidden
                                </label>
                              </div>
                              <button type="submit" class="btn btn-primary dim">Edit stage</button>
                            </form>
                       </div>
                   </div>
                    

                </div>

                @endsection

                @section('scripts')
                    @parent
                    <script type="text/javascript">
                      function addPrizeField() {
                        var div = $(document.createElement('div'));
                        var input = $(document.createElement('input')).attr({
                          'type': 'text',
                          'class': 'form-control',
                          'style': 'margin-top: 20px',
                          'name': 'prizeDist[]',
                          'placeholder': $('#prizeDistHolder input').length + 1 + ' place'
                        }).appendTo(div);
                        var button = $(document.createElement('button')).attr({
                          'type': 'button',
                          'class': 'btn btn-xs btn-danger'
                        }).text('Delete').on('click', function () {
                          $(this).parent().remove();
                        }).appendTo(div);
                        $('#prizeDistHolder').append(div);
                      }
                    </script>
@endsection