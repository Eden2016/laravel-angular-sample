@extends('layouts.default')

@section('content')

    <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>All Teams</h2>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{groute('/')}}">Home</a>
                    </li>
                    <li>
                        <a href="{{groute('teams.list')}}">Teams</a>
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
                            <h2>Dota2 API Teams</h2>
                            <div class="input-group">
                                <input type="text" placeholder="Search team" class="input form-control" id="searchteams" />
                                <span class="input-group-btn">
                                        <button type="button" class="btn btn btn-primary" disabled> <i class="fa fa-search"></i> Search</button>
                                </span>
                            </div>
                            <div class="clients-list">
                                    <div class="full-height-scroll">
                                        <div class="table-responsive">
                                            <table class="footable table table-stripped" data-filter="#searchteams" data-filter-minimum="3" data-page-size="10" data-limit-navigation="5">
                                                <tbody>
                                                @foreach ($teams as $team)
                                                <tr>
                                                    <td class="text-left"><a href="{{groute('team.view', 'current', [$team->id])}}"> {{ $team->name }}</a></td>
                                                    <td class="text-right">1.437 <span class="fa fa-chevron-up text-info"></span></td>
                                                </tr>
                                                @endforeach
                                                
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
                @endsection
