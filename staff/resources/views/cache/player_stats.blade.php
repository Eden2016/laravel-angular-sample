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
            {{$all}}
                @if ($refresh == "1")
                <p class="bg-danger">Cache is being generated at the moment!</p>
                <button type="button" class="btn btn-primary btn-lg">Regenerate player statistics</button>
                @else
                <p class="bg-info">Cache isn't being generate at the moment</p>
                <form method="post" action="{{groute('cache.player_stats.start')}}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                    <button type="button" class="btn btn-primary btn-lg active" onclick="this.form.submit();">Regenerate player statistics</button>
                </form>
                @endif
            </div>
@endsection
