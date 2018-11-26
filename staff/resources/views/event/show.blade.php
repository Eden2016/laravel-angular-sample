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
                    <strong>Event</strong>
                </li>
            </ol>
        </div>
    </div>
    <div class="wrapper wrapper-content  animated fadeInRight article">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="pull-right">
                            <!--<button class="btn btn-white btn-xs" type="button">Dota 2</button>
                                <button class="btn btn-white btn-xs" type="button">Chicago Open</button>
                                <button class="btn btn-white btn-xs" type="button">Prizes</button>-->
                        </div>
                        <div class="text-center article-title">
                            <span class="text-muted"><i class="fa fa-clock-o"></i> {{$event->start}} </span>
                            <h1>{{ $event->name }}</h1>
                        </div>
                        @if($event->logo)
                        <p class="text-center">
                            <img height="150" class="img img-responsive" src="{{url('uploads/'.$event->logo)}}" alt="{{$event->name}}">
                        </p>
                        @endif
                        <p>{{ $event->description }}</p>
                        <p>Tournaments:</p>
                        <table class="footable table table-stripped" data-page-size="5">
                            <thead>
                                <tr>
                                    <th>Tournament</th>
                                    <th>Start</th>
                                    <th>End</th>
                                    <th>Tier</th>
                                    <th>Prize Pool</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($tournaments) && count($tournaments) > 0) @foreach ($tournaments as $tournament)
                                <tr>
                                    <td>
                                        <a href="{{groute('tournament.view', 'current', [$tournament->id])}}">{{ $tournament->name }}</a>
                                    </td>

                                    <td>{{ date_convert($tournament->start, 'UTC', Auth::user()->timezone, 'Y-m-d H:i:s', 'd-m-Y') }}</td>
                                    <td>{{ date_convert($tournament->end, 'UTC', Auth::user()->timezone, 'Y-m-d H:i:s', 'd-m-Y') }}</td>
                                    <td>Minor</td>
                                    <td><span class="badge badge-primary">{{number_format($tournament->prize)}}</span> {{ \App\Tournament::listCurrencies()[$tournament->currency] }}</td>
                                    <td>
                                        @if($tournament->status == \App\Tournament::STATUS_CANCELED)Canceled 
                                        @elseif($tournament->start > Carbon\Carbon::now()) Upcoming
                                        @elseif ($tournament->is_done) Completed
                                        @else Live
                                        @endif
                                    </td>

                                </tr>
                                @endforeach @endif
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="6">
                                        <ul class="pagination pull-right"></ul>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>


                        <hr> @if(count($event->streams)) Streams: {{-- TODO: Format streams output $stream attributes: ->link ->title ->description ->lang --}}
                        <ul>
                            @foreach($event->streams as $stream)
                            <li><a href="{{$stream->link}}" target="_blank">{{$stream->title}} [{{$stream->lang}}]</a></li>
                            @endforeach
                        </ul>
                        @endif
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Tags:</h5> @if (isset($games) && count($games) > 0) @foreach ($games as $game)
                                <button class="btn btn-default btn-xs" type="button"> <i class="fa fa-tag"></i> {{ $game->name }}</button> @endforeach @endif @if (isset($tournaments) && count($tournaments) > 0) @foreach ($tournaments as $tournament)
                                <button class="btn btn-default btn-xs" type="button"> <i class="fa fa-tag"></i> {{ explode(" ", $tournament->name)[0] }}</button> @endforeach @endif
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="{{groute('event.delete', 'current', [ $event->id])}}">
                                    <button class="btn btn-danger dim" type="button">Delete</button>
                                </a>
                                <a href="{{groute('event.edit', 'current', [$event->id])}}">
                                    <button class="btn btn-primary dim" type="button">Edit</button>
                                </a>
                                <a href="{{groute('tournament.create', 'current', [ $event->id])}}">
                                    <button class="btn btn-primary dim" type="button">Add Tournament</button>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
@endsection
