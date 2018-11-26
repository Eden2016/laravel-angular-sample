@extends('layouts.default')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>All Tournaments</h2>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{groute('/')}}">Home</a>
                    </li>
                    <li>
                        <a href="{{groute('tournaments.list')}}">Tournaments</a>
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
                   <div class="col-md-12 ibox-content">
                            <span class="text-muted small pull-right">Last modification: <i class="fa fa-clock-o"></i> 2:10 pm - 12.06.2014</span>
                            <h2>Dota2 API Leagues</h2>
                            <div class="input-group">
                                <input type="text" placeholder="Search league" class="input form-control" id="searchleagues" />
                                <span class="input-group-btn">
                                        <button type="button" class="btn btn btn-primary" disabled> <i class="fa fa-search"></i> Search</button>
                                </span>
                            </div>
                            <table class="footable table table-stripped" data-filter="#searchleagues" data-filter-minimum="3" data-page-size="20" data-limit-navigation="5">
                                <thead>
                                    <tr>
                                        <th data-type="numeric">ID</th>
                                        <th>League Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @if (isset($leagues) && count($leagues) > 0)
                                @foreach ($leagues as $league)
                                    <tr>
                                        <td>
                                            <a href="{{groute('league', 'current', [$league->leagueid])}}">{{ $league->leagueid }}</a>
                                        </td>

                                        <td>
                                            <a href="{{groute('league', 'current', [$league->leagueid])}}">
                                                {{ $league->name }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                @endif
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
                @endsection

