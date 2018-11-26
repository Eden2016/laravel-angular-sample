@extends('layouts.default')

@section('content')
    <div class="players-wrapp">
        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>Overwatch Heroes</h2>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{ groute('/') }}">Home</a>
                    </li>
                    <li>
                        <a href="{{ groute('owheroes')  }}">Heroes</a>
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
                            <h2>List of Overwatch Heroes
                                <a href="{{groute('owheroes.create')}}" class="btn btn-md btn-success pull-right">Create</a>
                            </h2>
                            <div class="clients-list">
                                <div class="table-responsive">
                                    <table class="table footable table-striped table-hover" data-filter="#searchchampions" data-filter-minimum="3" data-page-size="20" data-page-navigation-size="5">
                                        <thead>
                                        <tr>
                                            <th data-sort-initial="descending">#</th>
                                            <th>Image</th>
                                            <th>Name</th>
                                            <th>Role</th>
                                            <th data-sort-ignore="true"><i class="fa fa-cogs"></i></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($heroes as $hero)
                                                <tr>
                                                    <td data-value="{{ $hero->id }}"><strong>{{ $hero->id }}</strong></td>
                                                    <td><img height="30" src="{{$hero->portraitUrl()}}"/></td>
                                                    <td>{{$hero->name}}</td>
                                                    <td>{{$roles[$hero->role]}}</td>
                                                    <td>
                                                        <a href="{{groute('owheroes.edit', 'current', [$hero->id])}}" class="btn btn-xs btn-success">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                        <a href="{{groute('owheroes.delete','current', [$hero->id])}}" class="btn btn-xs btn-danger">
                                                            <i class="fa fa-trash-o"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5">
                                                        <div class="alert alert-info">
                                                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                                            No heroes yet
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