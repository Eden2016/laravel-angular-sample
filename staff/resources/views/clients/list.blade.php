@extends('layouts.default')

@section('content')
    <div class="players-wrapp">
        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>Client accounts</h2>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{ groute('/') }}">Home</a>
                    </li>
                    <li>
                        <a href="{{ groute('clients.list')  }}">Clients</a>
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
                            <h2>List of client accounts
                                <a href="{{groute('clients.create')}}" class="btn btn-md btn-success pull-right">Create</a>
                            </h2>
                            <div class="clients-list">
                                <div class="table-responsive">
                                    <table class="table footable table-striped table-hover" data-filter="#searchchampions" data-filter-minimum="3" data-page-size="20" data-page-navigation-size="5">
                                        <thead>
                                        <tr>
                                            <th data-sort-initial="descending">#</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th data-sort-ignore="true"><i class="fa fa-cogs"></i></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @forelse ($clients as $client)
                                            <tr>
                                                <td data-value="{{ $client->id }}"><strong>{{ $client->id }}</strong></td>
                                                <td>{{$client->name}}</td>
                                                <td>{{$client->email}}</td>
                                                <td>
                                                    <a href="{{groute('clients.edit', 'current', [$client->id])}}" class="btn btn-xs btn-success">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                    <a href="{{groute('clients.delete','current', [$client->id])}}" class="btn btn-xs btn-danger">
                                                        <i class="fa fa-trash-o"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5">
                                                    <div class="alert alert-info">
                                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                                        No clients yet
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <td colspan="5">
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