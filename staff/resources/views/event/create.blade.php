@extends('layouts.default')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Add Event</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{groute('/')}}">Home</a>
                </li>
                <li>
                    <a href="{{groute('events')}}">Events</a>
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
                        <h5>Create Event Wizard</h5>
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
                        <form method="post" action="{{groute('event.save')}}" enctype="multipart/form-data">
                            <fieldset>

                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <h2>Event Details</h2>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label>Event Name *</label>
                                            <input id="eventName" name="name" type="text" class="form-control required">
                                        </div>
                                        <div class="form-group">
                                            <label>Event Short Handle </label>
                                            <input id="eventShortHandle" name="short_handle" type="text" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>Event Description</label>
                                            <textarea class="form-control" rows="3" name="description"></textarea>
                                        </div>
                                        @role('owner')
                                        <div class="form-group">
                                            <label>TouTou Description</label>
                                            <textarea class="form-control" rows="3" name="toutou_info"></textarea>
                                        </div>
                                        @endrole
                                        <div class="form-group">
                                            <label>Event organizer</label>
                                            <input id="eventOrganizer" name="organizer" type="text" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>Event location</label>
                                            <input id="eventLocation" name="location" type="text" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label for="game">Event Games</label>
                                            <select class="form-control" name="games[]" id="games" multiple>
                                                @foreach ($games as $k=>$game)
                                                    <option value="{{ $game->id }}"<?php if (request()->currentGameSlug == $game->slug) { ?> selected<?php } ?>>{{ $game->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <div>
                                                <label class="m-r-lg"> <input type="checkbox" checked="" value="1" id="isActive" name="active"> Is Active </label>
                                                <label> <input type="checkbox"  value="1" id="isHidden" name="hidden"> Is Hidden </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset>
                                <h2>Date of Event</h2>
                                <div class="row">
                                    <div class="col-lg-12">

                                        <label for="first_installment">First Installment</label>
                                        <div class="input-group m-b date form-group" id="f_installment_holder">
                                            <input type="text" class="form-control" name="first_installment"  />
                                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                        </div>
                                        <label for="start">Start Date</label>
                                        <div class="input-group m-b date form-group" id='startHolder'>

                                            <input type="text" class="form-control" name="start" id="start" />
                                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                        </div>
                                        <label for="end">End Date</label>
                                        <div class='input-group date form-group' id='endHolder'>
                                            <input type="text" class="form-control" name="end" id="end"/>
                                            <span class="input-group-addon">
                                                  <span class="glyphicon glyphicon-calendar"></span>
                                              </span>
                                        </div>
                                    </div>
                                </div>

                            </fieldset>

                            <fieldset>
                                <div class="form-group">
                                    <label for="streams">Streams</label>
                                    <select class="select2" id="streams" name="streams[]" style="width: 100%" multiple>
                                        @foreach(\App\Models\Streams::all() as $stream)
                                            <option value="{{$stream->id}}">{{$stream->title}} [{{$stream->lang}}]</option>
                                        @endforeach
                                    </select>
                                </div>
                            </fieldset>

                            <h2>Upload Event Image</h2>

                            <div class="form-group">
                                <label for="event_logo">Event logo</label>
                                <input type="file" name="file" id="event_logo">
                                <p class="help-block text-danger">Recommended dimensions 250x150px</p>
                            </div>
                            <div class="form-group">
                                <label for="event_logo">Event TouTou banner</label>
                                <input type="file" name="toutou_banner" id="event_toutou_banner">
                                <p class="help-block text-danger">Dimensions 1150x210px</p>
                            </div>
                            <button type="submit" class="btn btn-primary dim">Create</button>
                        </form>
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