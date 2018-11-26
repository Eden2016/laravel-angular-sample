@extends('layouts.default')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Dota 2 Matches</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{groute('/', 'dota2')}}">Home</a>
                </li>
                <li class="active">
                    <strong>Dota 2 Matches</strong>
                </li>
            </ol>
        </div>
        <div class="col-lg-2">

        </div>
    </div>
    <div class="wrapper wrapper-content animated fadeInRight ">
        <div class="row ">
            <div class="col-md-8">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="input-group">
                            <input type="text" placeholder="Search match" class="input form-control" id="search-match" />
                            <span class="input-group-btn">
                            <button type="button" class="btn btn btn-primary" disabled> <i class="fa fa-search"></i> Search</button>
                        </span>
                        </div>
                        <div>
                            <ul class="nav nav-tabs">

                                <li class="active">
                                    <a data-toggle="tab" href="#tab-1">Live <span class="badge badge-danger">3</span></a>
                                </li>
                                <li>
                                    <a data-toggle="tab" href="#tab-2">Upcoming <span class="badge badge-warning">15</span></a>
                                </li>
                                <li>
                                    <a data-toggle="tab" href="#tab-3">Completed <span class="badge badge-primary">354</span></a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div id="tab-1" class="tab-pane active">
                                    <div class="table-responsive table-bordered">
                                        <table class="footable table table-stripped" data-filter="#search-match" data-filter-minimum="3" data-page-size="10" data-limit-navigation="5">
                                            <thead>
                                                <tr>
                                                    <th>Team 1</th>
                                                    <th>VS</th>
                                                    <th>Team 2</th>
                                                    <th data-type="numeric">Time</th>
                                                    <th data-type="numeric">Date</th>
                                                    <th>Tournament</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><a href="#">4V+D</a></td>
                                                    <td><a href="#">vs</a></td>
                                                    <td><a href="#">CDEC</a></td>
                                                    <td data-value="1">19:00</td>
                                                    <td data-value="1">11-10-2016</td>
                                                    <td><a href="#">Frankfurt Major</a></td>
                                                </tr>
                                                <tr>
                                                    <td><a href="#">Cloud9</a></td>
                                                    <td><a href="#">vs</a></a>
                                                    </td>
                                                    <td><a href="#">E-LAB</a></td>
                                                    <td data-value="3">23:00</td>
                                                    <td data-value="2">31-12-2015</td>
                                                    <td><a href="#">Munich Major</a></td>
                                                </tr>
                                                <tr>
                                                    <td><a href="#">FlipSid3.</a></td>
                                                    <td><a href="#">vs</a></td>
                                                    <td><a href="#">FXO</a></td>
                                                    <td data-value="2">21:00</td>
                                                    <td data-value="3">02-06-2014</td>
                                                    <td><a href="#">WePlay Season 3</a></td>
                                                </tr>
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
                                <div id="tab-2" class="tab-pane">
                                    <div class="table-responsive table-bordered">
                                        <table class="footable table table-stripped" data-filter="#search-match" data-filter-minimum="3" data-page-size="10" data-limit-navigation="5">
                                            <thead>
                                                <tr>
                                                    <th>Team 1</th>
                                                    <th>VS</th>
                                                    <th>Team 2</th>
                                                    <th data-type="numeric">Time</th>
                                                    <th data-type="numeric">Date</th>
                                                    <th>Tournament</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><a href="#">4V+D</a></td>
                                                    <td><a href="#">vs</a></td>
                                                    <td><a href="#">CDEC</a></td>
                                                    <td data-value="1">19:00</td>
                                                    <td data-value="1">11-10-2016</td>
                                                    <td><a href="#">Frankfurt Major</a></td>
                                                </tr>
                                                <tr>
                                                    <td><a href="#">Cloud9</a></td>
                                                    <td><a href="#">vs</a></a>
                                                    </td>
                                                    <td><a href="#">E-LAB</a></td>
                                                    <td data-value="3">23:00</td>
                                                    <td data-value="2">31-12-2015</td>
                                                    <td><a href="#">Munich Major</a></td>
                                                </tr>
                                                <tr>
                                                    <td><a href="#">FlipSid3.</a></td>
                                                    <td><a href="#">vs</a></td>
                                                    <td><a href="#">FXO</a></td>
                                                    <td data-value="2">21:00</td>
                                                    <td data-value="3">02-06-2014</td>
                                                    <td><a href="#">WePlay Season 3</a></td>
                                                </tr>
                                                <tr>
                                                    <td><a href="#">4V+D</a></td>
                                                    <td><a href="#">vs</a></td>
                                                    <td><a href="#">CDEC</a></td>
                                                    <td data-value="1">19:00</td>
                                                    <td data-value="1">11-10-2016</td>
                                                    <td><a href="#">Frankfurt Major</a></td>
                                                </tr>
                                                <tr>
                                                    <td><a href="#">Cloud9</a></td>
                                                    <td><a href="#">vs</a></a>
                                                    </td>
                                                    <td><a href="#">E-LAB</a></td>
                                                    <td data-value="3">23:00</td>
                                                    <td data-value="2">31-12-2015</td>
                                                    <td><a href="#">Munich Major</a></td>
                                                </tr>
                                                <tr>
                                                    <td><a href="#">FlipSid3.</a></td>
                                                    <td><a href="#">vs</a></td>
                                                    <td><a href="#">FXO</a></td>
                                                    <td data-value="2">21:00</td>
                                                    <td data-value="3">02-06-2014</td>
                                                    <td><a href="#">WePlay Season 3</a></td>
                                                </tr>
                                                <tr>
                                                    <td><a href="#">4V+D</a></td>
                                                    <td><a href="#">vs</a></td>
                                                    <td><a href="#">CDEC</a></td>
                                                    <td data-value="1">19:00</td>
                                                    <td data-value="1">11-10-2016</td>
                                                    <td><a href="#">Frankfurt Major</a></td>
                                                </tr>
                                                <tr>
                                                    <td><a href="#">Cloud9</a></td>
                                                    <td><a href="#">vs</a></a>
                                                    </td>
                                                    <td><a href="#">E-LAB</a></td>
                                                    <td data-value="3">23:00</td>
                                                    <td data-value="2">31-12-2015</td>
                                                    <td><a href="#">Munich Major</a></td>
                                                </tr>
                                                <tr>
                                                    <td><a href="#">FlipSid3.</a></td>
                                                    <td><a href="#">vs</a></td>
                                                    <td><a href="#">FXO</a></td>
                                                    <td data-value="2">21:00</td>
                                                    <td data-value="3">02-06-2014</td>
                                                    <td><a href="#">WePlay Season 3</a></td>
                                                </tr>
                                                <tr>
                                                    <td><a href="#">4V+D</a></td>
                                                    <td><a href="#">vs</a></td>
                                                    <td><a href="#">CDEC</a></td>
                                                    <td data-value="1">19:00</td>
                                                    <td data-value="1">11-10-2016</td>
                                                    <td><a href="#">Frankfurt Major</a></td>
                                                </tr>
                                                <tr>
                                                    <td><a href="#">Cloud9</a></td>
                                                    <td><a href="#">vs</a></a>
                                                    </td>
                                                    <td><a href="#">E-LAB</a></td>
                                                    <td data-value="3">23:00</td>
                                                    <td data-value="2">31-12-2015</td>
                                                    <td><a href="#">Munich Major</a></td>
                                                </tr>
                                                <tr>
                                                    <td><a href="#">FlipSid3.</a></td>
                                                    <td><a href="#">vs</a></td>
                                                    <td><a href="#">FXO</a></td>
                                                    <td data-value="2">21:00</td>
                                                    <td data-value="3">02-06-2014</td>
                                                    <td><a href="#">WePlay Season 3</a></td>
                                                </tr>
                                                <tr>
                                                    <td><a href="#">4V+D</a></td>
                                                    <td><a href="#">vs</a></td>
                                                    <td><a href="#">CDEC</a></td>
                                                    <td data-value="1">19:00</td>
                                                    <td data-value="1">11-10-2016</td>
                                                    <td><a href="#">Frankfurt Major</a></td>
                                                </tr>
                                                <tr>
                                                    <td><a href="#">Cloud9</a></td>
                                                    <td><a href="#">vs</a></a>
                                                    </td>
                                                    <td><a href="#">E-LAB</a></td>
                                                    <td data-value="3">23:00</td>
                                                    <td data-value="2">31-12-2015</td>
                                                    <td><a href="#">Munich Major</a></td>
                                                </tr>
                                                <tr>
                                                    <td><a href="#">FlipSid3.</a></td>
                                                    <td><a href="#">vs</a></td>
                                                    <td><a href="#">FXO</a></td>
                                                    <td data-value="2">21:00</td>
                                                    <td data-value="3">02-06-2014</td>
                                                    <td><a href="#">WePlay Season 3</a></td>
                                                </tr>
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
                                <div id="tab-3" class="tab-pane">
                                    <div class="table-responsive table-bordered">
                                        <table class="footable table table-stripped" data-filter="#search-match" data-filter-minimum="3" data-page-size="10" data-limit-navigation="5">
                                            <thead>
                                                <tr>
                                                    <th>Team 1</th>
                                                    <th>VS</th>
                                                    <th>Team 2</th>
                                                    <th data-type="numeric">Time</th>
                                                    <th data-type="numeric">Date</th>
                                                    <th>Tournament</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><a href="#">4V+D</a></td>
                                                    <td><a href="#">vs</a></td>
                                                    <td><a href="#">CDEC</a></td>
                                                    <td data-value="1">19:00</td>
                                                    <td data-value="1">11-10-2016</td>
                                                    <td><a href="#">Frankfurt Major</a></td>
                                                </tr>
                                                <tr>
                                                    <td><a href="#">Cloud9</a></td>
                                                    <td><a href="#">vs</a></a>
                                                    </td>
                                                    <td><a href="#">E-LAB</a></td>
                                                    <td data-value="3">23:00</td>
                                                    <td data-value="2">31-12-2015</td>
                                                    <td><a href="#">Munich Major</a></td>
                                                </tr>
                                                <tr>
                                                    <td><a href="#">FlipSid3.</a></td>
                                                    <td><a href="#">vs</a></td>
                                                    <td><a href="#">FXO</a></td>
                                                    <td data-value="2">21:00</td>
                                                    <td data-value="3">02-06-2014</td>
                                                    <td><a href="#">WePlay Season 3</a></td>
                                                </tr>
                                                <tr>
                                                    <td><a href="#">4V+D</a></td>
                                                    <td><a href="#">vs</a></td>
                                                    <td><a href="#">CDEC</a></td>
                                                    <td data-value="1">19:00</td>
                                                    <td data-value="1">11-10-2016</td>
                                                    <td><a href="#">Frankfurt Major</a></td>
                                                </tr>
                                                <tr>
                                                    <td><a href="#">Cloud9</a></td>
                                                    <td><a href="#">vs</a></a>
                                                    </td>
                                                    <td><a href="#">E-LAB</a></td>
                                                    <td data-value="3">23:00</td>
                                                    <td data-value="2">31-12-2015</td>
                                                    <td><a href="#">Munich Major</a></td>
                                                </tr>
                                                <tr>
                                                    <td><a href="#">FlipSid3.</a></td>
                                                    <td><a href="#">vs</a></td>
                                                    <td><a href="#">FXO</a></td>
                                                    <td data-value="2">21:00</td>
                                                    <td data-value="3">02-06-2014</td>
                                                    <td><a href="#">WePlay Season 3</a></td>
                                                </tr>
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

@endsection

