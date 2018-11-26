@extends('layouts.default')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>eSports Events</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{groute('/')}}">Home</a>
                </li>
                <li>
                    <a href="{{groute('events')}}">Events</a>
                </li>
                <li class="active">
                    <strong>Manage</strong>
                </li>
            </ol>
        </div>
        <div class="col-lg-2">

        </div>
    </div>
    <div class="wrapper wrapper-content animated fadeInRight all-events-page">
        <div class="row ">

            <div class="ibox all-event-ibox">
                <div class="ibox-content">
                    <div class="input-group">
                        <input type="text" placeholder="Search event" class="input form-control" id="search-event" />
                        <span class="input-group-btn">
                            <button type="button" class="btn btn btn-primary" disabled> <i class="fa fa-search"></i> Search</button>
                        </span>
                    </div>
                    <div>
                        <ul class="nav nav-tabs">

                            <li class="active"><a data-toggle="tab" href="#tab-1">Current <span class="badge badge-danger">{{ $totals['current'] }}</span></a></li>
                            <li><a data-toggle="tab" href="#tab-2">Upcoming <span class="badge badge-warning">{{ $totals['upcoming'] }}</span></a></li>
                            <li><a data-toggle="tab" href="#tab-3">Past <span class="badge badge-primary">{{ $totals['past'] }}</span></a></li>
                        </ul>
                        <div class="tab-content">
                            <div id="tab-1" class="tab-pane active">

                                <div class="table-responsive">
                                    <table class="footable table table-stripped" data-filter="#search-event" data-filter-minimum="3" data-page-size="10" data-limit-navigation="5">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Date</th>
                                                <th>Image</th>
                                                <th>Description</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            @foreach ($current as $event)
                                            <!-- event box -->
                                            <tr>
                                                <td>
                                                    <a href="{{groute('event.view', $event->getFirstGameSlug(), ['eventId' => $event->id])}}">{{ $event->name }}</a>
                                                </td>
                                                <td>
                                                    Ending at {{ date_convert($event->event_end, 'UTC', Auth::user()->timezone, 'Y-m-d H:i:s', 'Y-m-d H:i') }}
                                                </td>
                                                <td>
                                                    @if(count($event->toutou_banner))
                                                        <a href="{{groute('event.view',$event->getFirstGameSlug(), ['eventId' => $event->id])}}">
                                                            <img width="280" class="img" src="http://static.esportsconstruct.com/{{$event->toutou_banner}}" alt="event"> 
                                                        </a>
                                                    @elseif(count($event->logo))
                                                        <a href="{{groute('event.view', $event->getFirstGameSlug(), ['eventId' => $event->id])}}">
                                                            <img width="280" class="img" src="http://static.esportsconstruct.com/{{$event->logo}}" alt="event"> 
                                                        </a>
                                                    @else
                                                        <a href="{{groute('event.view', $event->getFirstGameSlug(), ['eventId' => $event->id])}}">
                                                            There is no image.
                                                        </a>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{groute('event.view', $event->getFirstGameSlug(), ['eventId' => $event->id])}}">
                                                        {{str_limit($event->description, 100)}}
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach

                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="4">
                                                    <ul class="pagination pull-right"></ul>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>

                                </div>

                            </div>
                            <div id="tab-2" class="tab-pane">

                                <div class="table-responsive">
                                    <table class="footable table table-stripped" data-filter="#search-event" data-filter-minimum="3" data-page-size="10" data-limit-navigation="5">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Date</th>
                                                <th>Image</th>
                                                <th>Description</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($upcoming as $event)
                                            <!-- event box -->
                                            <tr>
                                                <td>
                                                    <a href="{{groute('event.view', $event->getFirstGameSlug(), ['eventId' => $event->id])}}">{{ $event->name }}</a>
                                                </td>
                                                <td>
                                                    Ending at {{$event->event_end}}
                                                </td>
                                                <td>
                                                    @if(count($event->toutou_banner))
                                                        <a href="{{groute('event.view', $event->getFirstGameSlug(), ['eventId' => $event->id])}}">
                                                            <img width="280" class="img" src="http://static.esportsconstruct.com/{{$event->toutou_banner}}" alt="event"> 
                                                        </a>
                                                    @elseif(count($event->logo))
                                                        <a href="{{groute('event.view', $event->getFirstGameSlug(), ['eventId' => $event->id])}}">
                                                            <img width="280" class="img" src="http://static.esportsconstruct.com/{{$event->logo}}" alt="event"> 
                                                        </a>
                                                    @else
                                                        <a href="{{groute('event.view', $event->getFirstGameSlug(), ['eventId' => $event->id])}}">
                                                            There is no image.
                                                        </a>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{groute('event.view', $event->getFirstGameSlug(), ['eventId' => $event->id])}}">
                                                        {{str_limit($event->description, 100)}}
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach

                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="4">
                                                    <ul class="pagination pull-right"></ul>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>

                                </div>

                            </div>
                            <div id="tab-3" class="tab-pane">

                                <div class="table-responsive">
                                    <table class="footable table table-stripped" data-filter="#search-event" data-filter-minimum="3" data-page-size="10" data-limit-navigation="5">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Date</th>
                                                <th>Image</th>
                                                <th>Description</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($past as $event)
                                            <!-- event box -->
                                            <tr>
                                                <td>
                                                    <a href="{{groute('event.view', $event->getFirstGameSlug(), ['eventId' => $event->id])}}">{{ $event->name }}</a>
                                                </td>
                                                <td>
                                                    Ended at {{$event->event_end}}
                                                </td>
                                                <td>
                                                    @if(count($event->toutou_banner))
                                                        <a href="{{groute('event.view', $event->getFirstGameSlug(), ['eventId' => $event->id])}}">
                                                            <img width="280" class="img" src="http://static.esportsconstruct.com/{{$event->toutou_banner}}" alt="event"> 
                                                        </a>
                                                    @elseif(count($event->logo))
                                                        <a href="{{groute('event.view', $event->getFirstGameSlug(), ['eventId' => $event->id])}}">
                                                            <img width="280" class="img" src="http://static.esportsconstruct.com/{{$event->logo}}" alt="event"> 
                                                        </a>
                                                    @else
                                                        <a href="{{groute('event.view', $event->getFirstGameSlug(), ['eventId' => $event->id])}}">
                                                            There is no image.
                                                        </a>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{groute('event.view', $event->getFirstGameSlug(), ['eventId' => $event->id])}}">
                                                        {{str_limit($event->description, 100)}}
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="4">
                                                    <ul class="pagination pull-right"></ul>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
@endsection
