@extends('layouts.default')

@section('content')

    <div class="players-wrapp">
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>All Players</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{groute('/')}}">Home</a>
                </li>
                <li>
                    <a href="{{groute('players.list')}}">Players</a>
                </li>
                <li class="active">
                    <strong>Manage</strong>
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
                    <div class="ibox-content">
                        <span class="text-muted small pull-right">Last modification: <i class="fa fa-clock-o"></i> 2:10 pm - 12.06.2014</span>
                        <h2>List of Players</h2>
                        <form action="{{groute('players.api')}}" method="get">
                            <div class="input-group">
                                <input type="text" name="q" placeholder="Search player" class="input form-control" value="{{request()->get('q')}}" />
                                <span class="input-group-btn">
                                    <button type="submit" class="btn btn btn-primary"> <i class="fa fa-search"></i> Search</button>
                                </span>
                            </div>
                        </form>
                        <div class="clients-list">
                            <ul class="nav nav-tabs">
                                <span class="pull-right small text-muted"><span class="badge badge-primary">{{count($players)}}</span> Players</span>
                                <li class="active"><a data-toggle="tab" href="#tab-1"><i class="fa fa-slack" aria-hidden="true"></i> API List</a></li>

                            </ul>
                            <div class="tab-content">
                                <div id="tab-1" class="tab-pane active">
                                    <div class="full-height-scroll">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-hover" data-filter="#searchplayers" data-filter-minimum="3" data-page-size="10" data-page-navigation-size="5">
                                                <tbody>
                                                @if (count($players))
                                                    @foreach ($players as $player)

                                                        <tr>
                                                            <td class="text-center"><strong>{{ $player->account_id }}.</strong></td>
                                                            <td class="text-center">
                                                                @if ($player->avatar_url != "")
                                                                    <img alt="image" src="{{ $player->avatar_url }}" class="img-circle players-avatar">
                                                                @else
                                                                    <img alt="image" src="/img/players-avatars/player-01.jpg" class="img-circle players-avatar">
                                                                @endif
                                                            </td>
                                                            <td class="text-center"><a href="{{groute('player.show', [$player->account_id])}}">{{ $player->personaname }}</a></td>
                                                            <td class="text-center">Steam ID:<a href="{{groute('player.show', [$player->account_id]) }}"> {{ $player->steamid }}</a></td>
                                                            <td>Account ID: <a href="{{ groute('player.show', [$player->account_id])}}">{{ $player->account_id }}</a></td>
                                                            <td class="player-status text-center"><span class="label label-primary">Active</span></td>
                                                        </tr>
                                                    @endforeach
                                                @endif


                                                </tbody>
                                                <tfoot>
                                                <tr>
                                                    <td colspan="6">
                                                        {{$players->appends(request()->only(['q', 'perPage']))->links()}}
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
                </div>
            </div>
            @endsection

