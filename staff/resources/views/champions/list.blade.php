@extends('layouts.default')

@section('content')
<div class="players-wrapp">
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Champions</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ groute('/') }}">Home</a>
                </li>
                <li>
                    <a href="{{ groute('champions')  }}">Champions</a>
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
                        <h2>List of Champions
                            <a href="{{groute('champion.form')}}" class="btn btn-md btn-success pull-right">Create</a>
                        </h2>
                        <div class="input-group">
                            <input type="text" placeholder="Search champion" class="input form-control" id="searchchampions" />
                            <span class="input-group-btn">
                                    <button type="button" class="btn btn btn-primary"> <i class="fa fa-search"></i> Search</button>
                            </span>
                        </div>
                        <div class="clients-list">
                            <div class="table-responsive">
                                <table class="table footable table-striped table-hover" data-filter="#searchchampions" data-filter-minimum="3" data-page-size="20" data-page-navigation-size="5">
                                    <thead>
                                        <tr>
                                            <th data-sort-initial="descending">#</th>
                                            <th>Name</th>
                                            <th>Title</th>
                                            <th>API Id</th>
                                            <th data-sort-ignore="true"><i class="fa fa-cogs"></i></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @if($champions)
                                        @foreach ($champions as $champ)
                                            <tr>
                                                <td data-value="{{ $champ->id }}"><strong>{{ $champ->id }}</strong></td>
                                                <td>{{$champ->name}}</td>
                                                <td>{{$champ->title}}</td>
                                                <td>{{$champ->api_id}}</td>
                                                <td>
                                                    <a href="{{groute('champion.form', 'current', ['id' => $champ->id])}}" class="btn btn-xs btn-success"><i class="fa fa-pencil"></i></a>
                                                    <a href="{{groute('champion.delete','current', ['id' => $champ->id])}}" class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="7">
                                                <div class="alert alert-info">
                                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                                    No champions found
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
        </div>
    </div>
</div>
@endsection