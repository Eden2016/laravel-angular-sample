@extends('layouts.default')

@section('content')
        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>eSports Event</h2>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{groute('/')}}">Home</a>
                    </li>
                    <li>
                        <a href="{{groute('events')}}">Events</a>
                    </li>
                    <li class="active">
                        <strong>Edit Event</strong>
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
                            <h5>Edit Event</h5>
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

                            @if (!isset($errorMessage))
                            <!-- Create Post Form -->
                                <form action="{{groute('event.save')}}" method="post" enctype="multipart/form-data">
                              <input type="hidden" name="_token" value="{{ csrf_token() }}">
                              <input type="hidden" name="id" value="{{ $event->id }}">
                              <div class="form-group">
                                <label for="name">Event Name</label>
                                <input type="text" class="form-control" name="name" id="name" value="{{ old('name') ? : $event->name }}" />
                              </div>
                              <div class="form-group">
                                <label for="short_handle">Event Short Handle</label>
                                <input type="text" class="form-control" name="short_handle" id="short_handel" value="{{ old('short_handle') ? : $event->short_handle }}" />
                              </div>
                                <div class="form-group">
                                    <label>Event organizer</label>
                                    <input id="eventOrganizer" name="organizer" type="text" class="form-control" value="{{old('organizer', $event->organizer)}}">
                                </div>
                                <div class="form-group">
                                    <label>Event location</label>
                                    <input id="eventLocation" name="location" type="text" class="form-control" value="{{old('location', $event->location)}}">
                                </div>
                              <div class="form-group">
                                  <label for="game">Event Games</label>
                                  <select class="form-control" name="games[]" id="games" multiple>
                                      @foreach ($games as $k=>$game)
                                        <option value="{{ $game->id }}"<?php if (in_array($game->id, $selectedGames)) { ?> selected<?php } ?>>{{ $game->name }}</option>
                                      @endforeach
                                  </select>
                              </div>
                              <div class="form-group">
                                <label for="first_installment">First Installment</label>
                                <input type="text" class="form-control" name="first_installment" id="first_installment" value="{{ old('first_installment') ? : $event->first_installment }}" />
                              </div>
                              <label for="start">Start Date</label>
                              <div class="input-group m-b date form-group" id='startHolder'>

                                  <input type="text" class="form-control" name="start" id="start" value="{{ old('start') ? : date_convert($event->start, 'UTC', Auth::user()->timezone, 'Y-m-d H:i:s', 'Y-m-d H:i') }}" />
                                  <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                               </div>
                               <label for="end">End Date</label>
                              <div class='input-group date form-group' id='endHolder'>
                                  <input type="text" class="form-control" name="end" id="end" value="{{ old('end') ? : date_convert($event->end, 'UTC', Auth::user()->timezone, 'Y-m-d H:i:s', 'Y-m-d H:i') }}" />
                                  <span class="input-group-addon">
                                      <span class="glyphicon glyphicon-calendar"></span>
                                  </span>
                              </div>
                              <div class="form-group">
                                <textarea class="form-control" rows="3" name="description">{{ old('description') ? : $event->description }}</textarea>
                              </div>
                              @role('owner')
                              <div class="form-group">
                                  <label>TouTou Description</label>
                                  <textarea class="form-control" rows="3" name="toutou_info">{{ old('toutou_info') ? : $event->toutou_info }}</textarea>
                              </div>
                              @endrole

                              <div class="checkbox">
                                <label>
                                  <input type="checkbox" name="active" value="1"<?php if ($event->active) { ?> checked<?php } ?>> Is active
                                </label>
                              </div>
                              <div class="checkbox">
                                <label>
                                  <input type="checkbox" name="hidden" value="1"<?php if ($event->hidden) { ?> checked<?php } ?>> Is hidden
                                </label>
                              </div>

                                <div class="form-group">
                                    <label for="streams">Streams</label>
                                    <select class="select2" id="streams" name="streams[]" style="width: 100%" multiple>
                                        @foreach(\App\Models\Streams::all() as $stream)
                                        <option value="{{$stream->id}}" @if(in_array($stream->id, $event->streams->pluck('id')->toArray())) selected @endif>{{$stream->title}} [{{$stream->lang}}]</option>
                                        @endforeach
                                    </select>
                                </div>

                                @if($event->logo)
                                    <div class="form-group">
                                        <label>Current:</label>
                                        <img src="{{url('uploads/'.$event->logo)}}" alt="{{$event->name}}" class="img-responsive">
                                    </div>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="remove_image" value="true"> Remove image file
                                        </label>
                                    </div>
                                @endif
                                <div class="form-group">
                                    <label for="event_logo">Event logo</label>
                                    <input type="file" name="file" id="event_logo">
                                    <p class="help-block text-danger">Recommended dimensions 250x150px</p>
                                </div>
                                @if($event->toutou_banner)
                                    <div class="form-group">
                                        <label>Current TouTou Banner:</label>
                                        <img src="{{url('uploads/'.$event->toutou_banner)}}" alt="{{$event->toutou_banner}}" class="img-responsive">
                                    </div>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="remove_toutou_banner" value="true"> Remove image file
                                        </label>
                                    </div>
                                @endif
                                <div class="form-group">
                                    <label for="event_logo">TouTou Banner</label>
                                    <input type="file" name="toutou_banner" id="event_toutou_banner">
                                    <p class="help-block text-danger">Dimensions 1150x210px</p>
                                </div>
                              <button type="submit" class="btn btn-primary dim">Edit event</button>

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
                      $(function(){
                        $('.select2').select2();
                      });
                    </script>
@endsection