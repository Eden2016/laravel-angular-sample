@extends('layouts.default')

@section('content')
<div class="players-wrapp">
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Maps</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ groute('/') }}">Home</a>
                </li>
                <li>
                    <a href="{{ groute('maps')  }}">Maps</a>
                </li>
                <li class="active">
                    <strong>List</strong>
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

                        <h2>List of Maps @if(request()->currentGame) for {{request()->currentGame->name}} @endif
                            <a href="{{groute('maps.form')}}" class="btn btn-md btn-success pull-right">Create</a>
                        </h2>

                        <div class="clients-list">
                            <div class="table-responsive">
                                <table class="table footable table-striped table-hover" data-page-size="20" data-page-navigation-size="5">
                                    <thead>
                                        <tr>
                                            <th data-sort-initial="descending">#</th>
                                            <th>Name</th>
                                            <th>Status</th>
                                            <th>Type</th>
                                            <th>Game</th>
                                            <th>Description</th>
                                            <th data-sort-ignore="true"><i class="fa fa-cogs"></i></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @if($maps)
                                        @foreach ($maps as $map)

                                            <tr>
                                                <td><strong>{{ $map->id }}</strong></td>
                                                <td>{{$map->name}}</td>
                                                <td>{{ucfirst(str_replace('_', ' ', $map->status))}}</td>
                                                <td>{{ucfirst($map->type)}}</td>
                                                <td>{{$map->game->name}}</td>
                                                <td data-toggle="tooltip" data-container="body" data-original-title="{{strip_tags($map->description, '<br><p>')}}">{{str_limit(strip_tags($map->description), 30)}}</td>
                                                <td>
                                                    <a href="{{groute('maps.form', 'current', ['id' => $map->id])}}" class="btn btn-xs btn-success"><i class="fa fa-pencil"></i></a>
                                                    <a href="{{groute('maps.delete', 'current',  ['id' => $map->id])}}" class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="7">
                                                <div class="alert alert-info">
                                                	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                                	No maps found
                                                </div>
                                            </td>
                                        </tr>
                                    @endif


                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <td colspan="6">
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

@endsection

