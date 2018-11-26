@extends('layouts.default')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>All Matches</h2>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{groute('/')}}">Home</a>
                    </li>
                    <li>
                        <a href="{{groute('matches.dummy')}}">Matches</a>
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
                           <div class="row">
                                <div class="col-md-12">
                                <h3>Match List</h3>
                                <div class="tabs-container">
                                    <ul class="nav nav-tabs">
                                        <li class="active"><a data-toggle="tab" href="#tab-1" aria-expanded="false">Live <span class="badge badge-danger">{{ count($live) }}</span></a></li>
                                        <li class=""><a data-toggle="tab" href="#tab-2" aria-expanded="false">Upcoming <span class="badge badge-warning">{{ count($upcoming) }}</span></a></li>
                                        <li class=""><a data-toggle="tab" href="#tab-3" aria-expanded="true"> Completed <span class="badge badge-primary">{{ count($completed) }}</span></a></li>
                                    </ul>
                                    <div class="tab-content">
                                       <div id="tab-1" class="tab-pane active">
                                            <div class="panel-body">
                                                <div class="input-group">
                                                    <input type="text" placeholder="Search" class="input form-control" id="searchLiveMatches" />
                                                    <span class="input-group-btn">
                                                            <button type="button" class="btn btn btn-primary" disabled> <i class="fa fa-search"></i> Search</button>
                                                    </span>
                                                </div>
                                                <table class="footable table table-stripped" data-filter="#searchLiveMatches" data-page-size="20" data-limit-navigation="5">
                                                    <thead>
                                                        <tr>
                                                            <th>ID</th>
                                                            <th>Home Team</th>
                                                            <th>Away Team</th>
                                                            <th>Start Date</th>
                                                            <th>Tournament</th>
                                                            <th>View Match</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>

                                                       @foreach ($live as $match)
                                                           {{--
                                                            Temporary fix for missing data
                                                           --}}
                                                           <?php if(!$match->stageRound || !$match->stageRound->stageFormat || !$match->stageRound->stageFormat->stage || !$match->stageRound->stageFormat->stage->tournament) continue ?>
                                                        <tr>
                                                            <td>
                                                                <a href="{{groute('match.view', 'current', [
                                                                    $match->stageRound->stageFormat->stage->tournament->id,
                                                                    $match->stageRound->stageFormat->stage->id,
                                                                    $match->stageRound->stageFormat->id,
                                                                    $match->id
                                                                ])}}">
                                                                    {{ $match->id }}
                                                                </a>
                                                            </td>
                                                            <td>
                                                                <a href="{{groute('team.view', 'current', [
                                                                    $match->opponent1_details->id
                                                                ])}}">
                                                                    {{ $match->opponent1_details->name }}
                                                                </a>
                                                            </td>
                                                            <td>
                                                                <a href="{{groute('team.view', 'current', [
                                                                    $match->opponent2_details->id
                                                                ])}}">
                                                                    {{ $match->opponent2_details->name }}
                                                                </a>
                                                            </td>
                                                            <td>{{ $match->start }}</td>
                                                            <td>
                                                                <a href="{{groute('tournament.view', 'current', [
                                                                    $match->stageRound->stageFormat->stage->tournament->id
                                                                ])}}">
                                                                    {{ $match->stageRound->stageFormat->stage->tournament->name }}
                                                                </a>
                                                            </td>
                                                            <td>
                                                                <a href="{{groute('match.view', 'current', [
                                                                    $match->stageRound->stageFormat->stage->tournament->id,
                                                                    $match->stageRound->stageFormat->stage->id,
                                                                    $match->stageRound->stageFormat->id,
                                                                    $match->id
                                                                ])}}">
                                                                    <button class="btn btn-primary dim" type="button">View</button>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                      @endforeach
                                            
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
                                        <div id="tab-2" class="tab-pane ">
                                            <div class="panel-body">
                                                <div class="input-group">
                                                    <input type="text" placeholder="Search" class="input form-control" id="searchUpcomingMatches" />
                                                    <span class="input-group-btn">
                                                            <button type="button" class="btn btn btn-primary" disabled> <i class="fa fa-search"></i> Search</button>
                                                    </span>
                                                </div>
                                                <table class="footable table table-stripped" data-page-size="20" data-filter="#searchUpcomingMatches" data-limit-navigation="5">
                                                    <thead>
                                                        <tr>
                                                            <th>ID</th>
                                                            <th>Home Team</th>
                                                            <th>Away Team</th>
                                                            <th>Start Date</th>
                                                            <th>Tournament</th>
                                                            <th>View Match</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>

                                                       @foreach ($upcoming as $match)
                                                           {{--
                                                            Temporary fix for missing data
                                                           --}}
                                                           <?php if(!$match->stageRound || !$match->stageRound->stageFormat || !$match->stageRound->stageFormat->stage || !$match->stageRound->stageFormat->stage->tournament) continue ?>
                                                        <tr>
                                                            <td>
                                                                <a href="{{groute('match.view', 'current', [
                                                                    $match->stageRound->stageFormat->stage->tournament->id,
                                                                    $match->stageRound->stageFormat->stage->id,
                                                                    $match->stageRound->stageFormat->id,
                                                                    $match->id
                                                                ])}}">
                                                                    {{ $match->id }}
                                                                </a>
                                                            </td>
                                                            <td>
                                                                @if($match->opponent1_details)
                                                                <a href="{{groute('team.view', 'current', [
                                                                    $match->opponent1_details->id
                                                                ])}}">
                                                                    {{ $match->opponent1_details->name }}
                                                                </a>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if($match->opponent2_details)
                                                                <a href="{{groute('team.view', 'current', [
                                                                    $match->opponent2_details->id
                                                                ])}}">
                                                                    {{ $match->opponent2_details->name }}
                                                                </a>
                                                                @endif
                                                            </td>
                                                            <td>{{ $match->start }}</td>
                                                            <td>
                                                                <a href="{{groute('tournament.view', 'current', [
                                                                    $match->stageRound->stageFormat->stage->tournament->id
                                                                ])}}">
                                                                    {{ $match->stageRound->stageFormat->stage->tournament->name }}
                                                                </a>
                                                            </td>
                                                            <td>
                                                                <a href="{{groute('match.view', 'current', [
                                                                    $match->stageRound->stageFormat->stage->tournament->id,
                                                                    $match->stageRound->stageFormat->stage->id,
                                                                    $match->stageRound->stageFormat->id,
                                                                    $match->id
                                                                ])}}">
                                                                    <button class="btn btn-primary dim" type="button">View</button>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                      @endforeach
                                            
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
                                        
                                        <div id="tab-3" class="tab-pane">
                                            <div class="panel-body">
                                                <div class="input-group">
                                                    <input type="text" placeholder="Search" class="input form-control" id="searchCompletedMatches" />
                                                    <span class="input-group-btn">
                                                            <button type="button" class="btn btn btn-primary" disabled> <i class="fa fa-search"></i> Search</button>
                                                    </span>
                                                </div>
                                                <table class="footable table table-stripped" data-page-size="20" data-filter="#searchCompletedMatches" data-limit-navigation="5">
                                                    <thead>
                                                        <tr>
                                                            <th>ID</th>
                                                            <th>Home Team</th>
                                                            <th>Away Team</th>
                                                            <th>Start Date</th>
                                                            <th>Tournament</th>
                                                            <th>View Match</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>

                                                       @foreach ($completed as $match)
                                                           {{--
                                                            Temporary fix for missing data
                                                           --}}
                                                           <?php if(!$match->stageRound || !$match->stageRound->stageFormat || !$match->stageRound->stageFormat->stage || !$match->stageRound->stageFormat->stage->tournament) continue ?>
                                                           <tr>
                                                               <td>
                                                                   <a href="{{groute('match.view', 'current', [
                                                                    $match->stageRound->stageFormat->stage->tournament->id,
                                                                    $match->stageRound->stageFormat->stage->id,
                                                                    $match->stageRound->stageFormat->id,
                                                                    $match->id
                                                                ])}}">
                                                                       {{ $match->id }}
                                                                   </a>
                                                               </td>
                                                               <td>
                                                                   @if($match->opponent1_details)
                                                                       <a href="{{groute('team.view', 'current', [
                                                                    $match->opponent1_details->id
                                                                ])}}">
                                                                           {{ $match->opponent1_details->name }}
                                                                       </a>
                                                                   @endif
                                                               </td>
                                                               <td>
                                                                   @if($match->opponent2_details)
                                                                       <a href="{{groute('team.view', 'current', [
                                                                    $match->opponent2_details->id
                                                                ])}}">
                                                                           {{ $match->opponent2_details->name }}
                                                                       </a>
                                                                   @endif
                                                               </td>
                                                               <td>{{ $match->start }}</td>
                                                               <td>
                                                                   <a href="{{groute('tournament.view', 'current', [
                                                                    $match->stageRound->stageFormat->stage->tournament->id
                                                                ])}}">
                                                                       {{ $match->stageRound->stageFormat->stage->tournament->name }}
                                                                   </a>
                                                               </td>
                                                               <td>
                                                                   <a href="{{groute('match.view', 'current', [
                                                                    $match->stageRound->stageFormat->stage->tournament->id,
                                                                    $match->stageRound->stageFormat->stage->id,
                                                                    $match->stageRound->stageFormat->id,
                                                                    $match->id
                                                                ])}}">
                                                                       <button class="btn btn-primary dim" type="button">View</button>
                                                                   </a>
                                                               </td>
                                                           </tr>
                                                      @endforeach
                                            
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
                            
                        </div>
                    </div>
                </div>

@endsection