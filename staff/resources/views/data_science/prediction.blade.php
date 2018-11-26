@extends('layouts.default')

@section('content')

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Manage</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{groute('/')}}">Home</a>
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

            <!---------- Upcoming Matches ------------>

            <div class="col-sm-6">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="col-sm-12">
                            <div class="row">
                                <ul class="nav nav-tabs">
                                    <li class="active"><a data-toggle="tab" href="#all-games">All</a></li>
                                    <li><a data-toggle="tab" href="#dota">Dota2</a></li>
                                    <li><a data-toggle="tab" href="#csgo">CS:GO</a></li>
                                    <li><a data-toggle="tab" href="#lol">LoL</a></li>
                                    <li><a data-toggle="tab" href="#ow">OW</a></li>
                                </ul>
                            </div>
                        </div>

                        <div class="tab-content">

                            <!--------- All Matches ---------->

                            <div id="all-games" class="tab-pane fade in active">
                                <table class="footable table table-bordered" data-page-size="10" data-limit-navigation="5" data-filter="#search-match" data-filter-minimum="3">
                                    <thead>
                                        <tr>
                                            <th>Upcoming matches<br>(Team A vs Team B)</th>
                                            <th>Date&time</th>
                                            <th>Predicted result</th>
                                            <th>Odds</th>
                                            <th>Client 0</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($upcoming_matches as $match)
                                        <tr>
                                            <td>{{$match->opponent1_details->name}}
                                                vs {{$match->opponent2_details->name}}</td>
                                            <td>{{date_convert($match->start, 'UTC', Auth::user()->timezone, 'Y-m-d H:i:s', 'd M Y H:i')}}</td>
                                            <td>
                                                @if($match->prediction->win_prob_home_team > 0.5)
                                                    {{$match->opponent1_details->name}}
                                                @else
                                                    {{$match->opponent2_details->name}}
                                                @endif
                                            </td>
                                            <td>
                                                @if($match->prediction->win_prob_home_team > 0)
                                                {{number_format(100 / ($match->prediction->win_prob_home_team*100), 2)}}
                                                @else
                                                    -
                                                @endif
                                                /
                                                @if($match->prediction->win_prob_home_team > 0)
                                                    @if($match->prediction->win_prob_home_team==1)
                                                        100
                                                    @else
                                                {{number_format(100 / ((1-$match->prediction->win_prob_home_team)*100), 2)}}
                                                    @endif
                                                @else
                                                    -
                                                @endif

                                            </td>
                                            <td>
                                                @if($match->toutou_match)
                                                    {{json_decode($match->toutou_match->odds)->ml[1]}}
                                                    /
                                                    {{json_decode($match->toutou_match->odds)->ml[3]}}
                                                @else
                                                    -
                                                @endif
                                            </td>
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

                            <!--------- Dota tab ---------->

                            <div id="dota" class="tab-pane fade">
                                <table class="footable table table-bordered" data-page-size="10" data-limit-navigation="5" data-filter="#search-match" data-filter-minimum="3">
                                    <thead>
                                        <tr>
                                            <th>Upcoming matches<br>(Team A vs Team B)</th>
                                            <th>Date&time</th>
                                            <th>Predicted result</th>
                                            <th>Odds</th>
                                            <th>Client 0</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($upcoming_matches->where('game.slug', 'dota2') as $match)
                                        <tr>
                                            <td>{{$match->opponent1_details->name}}
                                                vs {{$match->opponent2_details->name}}</td>
                                            <td>{{date_convert($match->start, 'UTC', Auth::user()->timezone, 'Y-m-d H:i:s', 'd M Y H:i')}}</td>
                                            <td>
                                                @if($match->prediction->win_prob_home_team > 0.5)
                                                    {{$match->opponent1_details->name}}
                                                @else
                                                    {{$match->opponent2_details->name}}
                                                @endif
                                            </td>
                                            <td>
                                                @if($match->prediction->win_prob_home_team > 0)
                                                    {{number_format(100 / ($match->prediction->win_prob_home_team*100), 2)}}
                                                @else
                                                    -
                                                @endif
                                                /
                                                @if($match->prediction->win_prob_home_team > 0)
                                                    @if($match->prediction->win_prob_home_team==1)
                                                        100
                                                    @else
                                                    {{number_format(100 / ((1-$match->prediction->win_prob_home_team)*100), 2)}}
                                                    @endif
                                                @else
                                                    -
                                                @endif

                                            </td>
                                            <td>
                                                @if($match->toutou_match)
                                                    {{json_decode($match->toutou_match->odds)->ml[1]}}
                                                    /
                                                    {{json_decode($match->toutou_match->odds)->ml[3]}}
                                                @else
                                                    -
                                                @endif
                                            </td>
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

                            <!--------- CS:GO tab ---------->

                            <div id="csgo" class="tab-pane fade">
                                <table class="footable table table-bordered" data-page-size="10" data-limit-navigation="5" data-filter="#search-match" data-filter-minimum="3">
                                    <thead>
                                    <tr>
                                        <th>Upcoming matches<br>(Team A vs Team B)</th>
                                        <th>Date&time</th>
                                        <th>Predicted result</th>
                                        <th>Odds</th>
                                        <th>Client 0</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($upcoming_matches->where('game.slug', 'csgo') as $match)
                                        <tr>
                                            <td>{{$match->opponent1_details->name}}
                                                vs {{$match->opponent2_details->name}}</td>
                                            <td>{{date_convert($match->start, 'UTC', Auth::user()->timezone, 'Y-m-d H:i:s', 'd M Y H:i')}}</td>
                                            <td>
                                                @if($match->prediction->win_prob_home_team > 0.5)
                                                    {{$match->opponent1_details->name}}
                                                @else
                                                    {{$match->opponent2_details->name}}
                                                @endif
                                            </td>
                                            <td>
                                                @if($match->prediction->win_prob_home_team > 0)
                                                    {{number_format(100 / ($match->prediction->win_prob_home_team*100), 2)}}
                                                @else
                                                    -
                                                @endif
                                                /
                                                @if($match->prediction->win_prob_home_team > 0)
                                                    @if($match->prediction->win_prob_home_team==1)
                                                        100
                                                    @else
                                                    {{number_format(100 / ((1-$match->prediction->win_prob_home_team)*100), 2)}}
                                                    @endif
                                                @else
                                                    -
                                                @endif

                                            </td>
                                            <td>
                                                @if($match->toutou_match)
                                                    {{json_decode($match->toutou_match->odds)->ml[1]}}
                                                    /
                                                    {{json_decode($match->toutou_match->odds)->ml[3]}}
                                                @else
                                                    -
                                                @endif
                                            </td>
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

                            <!--------- LoL tab ---------->

                            <div id="lol" class="tab-pane fade">
                                <table class="footable table table-bordered" data-page-size="10" data-limit-navigation="5" data-filter="#search-match" data-filter-minimum="3">
                                    <thead>
                                    <tr>
                                        <th>Upcoming matches<br>(Team A vs Team B)</th>
                                        <th>Date&time</th>
                                        <th>Predicted result</th>
                                        <th>Odds</th>
                                        <th>Client 0</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($upcoming_matches->where('game.slug', 'lol') as $match)
                                        <tr>
                                            <td>{{$match->opponent1_details->name}}
                                                vs {{$match->opponent2_details->name}}</td>
                                            <td>{{date_convert($match->start, 'UTC', Auth::user()->timezone, 'Y-m-d H:i:s', 'd M Y H:i')}}</td>
                                            <td>
                                                @if($match->prediction->win_prob_home_team > 0.5)
                                                    {{$match->opponent1_details->name}}
                                                @else
                                                    {{$match->opponent2_details->name}}
                                                @endif
                                            </td>
                                            <td>
                                                @if($match->prediction->win_prob_home_team > 0)
                                                    {{number_format(100 / ($match->prediction->win_prob_home_team*100), 2)}}
                                                @else
                                                    -
                                                @endif
                                                /
                                                @if($match->prediction->win_prob_home_team > 0)
                                                    @if($match->prediction->win_prob_home_team==1)
                                                        100
                                                    @else
                                                    {{number_format(100 / ((1-$match->prediction->win_prob_home_team)*100), 2)}}
                                                    @endif
                                                @else
                                                    -
                                                @endif

                                            </td>
                                            <td>
                                                @if($match->toutou_match)
                                                    {{json_decode($match->toutou_match->odds)->ml[1]}}
                                                    /
                                                    {{json_decode($match->toutou_match->odds)->ml[3]}}
                                                @else
                                                    -
                                                @endif
                                            </td>
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

                            <!--------- Overwatch tab ---------->

                            <div id="ow" class="tab-pane fade">
                                <table class="footable table table-bordered" data-page-size="10" data-limit-navigation="5" data-filter="#search-match" data-filter-minimum="3">
                                    <thead>
                                    <tr>
                                        <th>Upcoming matches<br>(Team A vs Team B)</th>
                                        <th>Date&time</th>
                                        <th>Predicted result</th>
                                        <th>Odds</th>
                                        <th>Client 0</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($upcoming_matches->where('game.slug', 'overwatch') as $match)
                                        <tr>
                                            <td>{{$match->opponent1_details->name}}
                                                vs {{$match->opponent2_details->name}}</td>
                                            <td>{{date_convert($match->start, 'UTC', Auth::user()->timezone, 'Y-m-d H:i:s', 'd M Y H:i')}}</td>
                                            <td>
                                                @if($match->prediction->win_prob_home_team > 0.5)
                                                    {{$match->opponent1_details->name}}
                                                @else
                                                    {{$match->opponent2_details->name}}
                                                @endif
                                            </td>
                                            <td>
                                                @if($match->prediction->win_prob_home_team > 0)
                                                    {{number_format(100 / ($match->prediction->win_prob_home_team*100), 2)}}
                                                @else
                                                    -
                                                @endif
                                                /
                                                @if($match->prediction->win_prob_home_team > 0)
                                                    @if($match->prediction->win_prob_home_team==1)
                                                        100
                                                    @else
                                                    {{number_format(100 / ((1-$match->prediction->win_prob_home_team)*100), 2)}}
                                                    @endif
                                                @else
                                                    -
                                                @endif

                                            </td>
                                            <td>
                                                @if($match->toutou_match)
                                                    {{json_decode($match->toutou_match->odds)->ml[1]}}
                                                    /
                                                    {{json_decode($match->toutou_match->odds)->ml[3]}}
                                                @else
                                                    -
                                                @endif
                                            </td>
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

                    <!---------- Finished Matches ------------>

                    <div class="col-sm-12">
                        <div class="col-sm-12">
                            <div class="row">
                                <ul class="nav nav-tabs">
                                    <li class="active"><a data-toggle="tab" href="#fin-all-games">All</a></li>
                                    <li><a data-toggle="tab" href="#fin-dota">Dota2</a></li>
                                    <li><a data-toggle="tab" href="#fin-csgo">CS:GO</a></li>
                                    <li><a data-toggle="tab" href="#fin-lol">LoL</a></li>
                                    <li><a data-toggle="tab" href="#fin-ow">OW</a></li>

                                </ul>
                            </div>
                        </div>

                        <div class="tab-content">
                            <!--------- All Matches ---------->

                            <div id="fin-all-games" class="tab-pane fade in active">
                                <table class="footable table table-bordered" data-page-size="10" data-limit-navigation="5" data-filter="#search-match" data-filter-minimum="3">
                                    <thead>
                                    <tr>
                                        <th>Finished matches<br>(Team A vs Team B)</th>
                                        <th>Date&time</th>
                                        <th>Predicted result</th>
                                        <th>Odds</th>
                                        <th>Client 0</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($finished_matches as $match)
                                        <tr>
                                            <td>{{$match->opponent1_details->name}}
                                                vs {{$match->opponent2_details->name}}</td>
                                            <td>{{date_convert($match->start, 'UTC', Auth::user()->timezone, 'Y-m-d H:i:s', 'd M Y H:i')}}</td>
                                            <td>
                                                @if($match->prediction->win_prob_home_team > 0.5)
                                                    {{$match->opponent1_details->name}}
                                                @else
                                                    {{$match->opponent2_details->name}}
                                                @endif
                                            </td>
                                            <td>
                                                @if($match->prediction->win_prob_home_team > 0)
                                                    {{number_format(100 / ($match->prediction->win_prob_home_team*100), 2)}}
                                                @else
                                                    -
                                                @endif
                                                /
                                                @if($match->prediction->win_prob_home_team > 0)
                                                    @if($match->prediction->win_prob_home_team==1)
                                                        100
                                                    @else
                                                    {{number_format(100 / ((1-$match->prediction->win_prob_home_team)*100), 2)}}
                                                    @endif
                                                @else
                                                    -
                                                @endif

                                            </td>
                                            <td>
                                                @if($match->toutou_match)
                                                    {{json_decode($match->toutou_match->odds)->ml[1]}}
                                                    /
                                                    {{json_decode($match->toutou_match->odds)->ml[3]}}
                                                @else
                                                    -
                                                @endif
                                            </td>
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

                            <!--------- Dota tab ---------->

                            <div id="fin-dota" class="tab-pane fade">
                                <table class="footable table table-bordered" data-page-size="10" data-limit-navigation="5" data-filter="#search-match" data-filter-minimum="3">
                                    <thead>
                                    <tr>
                                        <th>Finished matches<br>(Team A vs Team B)</th>
                                        <th>Date&time</th>
                                        <th>Predicted result</th>
                                        <th>Odds</th>
                                        <th>Client 0</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($finished_matches->where('game.slug', 'dota2') as $match)
                                        <tr>
                                            <td>{{$match->opponent1_details->name}}
                                                vs {{$match->opponent2_details->name}}</td>
                                            <td>{{date_convert($match->start, 'UTC', Auth::user()->timezone, 'Y-m-d H:i:s', 'd M Y H:i')}}</td>
                                            <td>
                                                @if($match->prediction->win_prob_home_team > 0.5)
                                                    {{$match->opponent1_details->name}}
                                                @else
                                                    {{$match->opponent2_details->name}}
                                                @endif
                                            </td>
                                            <td>
                                                @if($match->prediction->win_prob_home_team > 0)
                                                    {{number_format(100 / ($match->prediction->win_prob_home_team*100), 2)}}
                                                @else
                                                    -
                                                @endif
                                                /
                                                @if($match->prediction->win_prob_home_team > 0)
                                                    @if($match->prediction->win_prob_home_team==1)
                                                        100
                                                    @else
                                                    {{number_format(100 / ((1-$match->prediction->win_prob_home_team)*100), 2)}}
                                                    @endif
                                                @else
                                                    -
                                                @endif

                                            </td>
                                            <td>
                                                @if($match->toutou_match)
                                                    {{json_decode($match->toutou_match->odds)->ml[1]}}
                                                    /
                                                    {{json_decode($match->toutou_match->odds)->ml[3]}}
                                                @else
                                                    -
                                                @endif
                                            </td>
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

                            <!--------- CS:GO tab ---------->

                            <div id="fin-csgo" class="tab-pane fade">
                                <table class="footable table table-bordered" data-page-size="10" data-limit-navigation="5" data-filter="#search-match" data-filter-minimum="3">
                                    <thead>
                                    <tr>
                                        <th>Finished matches<br>(Team A vs Team B)</th>
                                        <th>Date&time</th>
                                        <th>Predicted result</th>
                                        <th>Odds</th>
                                        <th>Client 0</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($finished_matches->where('game.slug', 'csgo') as $match)
                                        <tr>
                                            <td>{{$match->opponent1_details->name}}
                                                vs {{$match->opponent2_details->name}}</td>
                                            <td>{{date_convert($match->start, 'UTC', Auth::user()->timezone, 'Y-m-d H:i:s', 'd M Y H:i')}}</td>
                                            <td>
                                                @if($match->prediction->win_prob_home_team > 0.5)
                                                    {{$match->opponent1_details->name}}
                                                @else
                                                    {{$match->opponent2_details->name}}
                                                @endif
                                            </td>
                                            <td>
                                                @if($match->prediction->win_prob_home_team > 0)
                                                    {{number_format(100 / ($match->prediction->win_prob_home_team*100), 2)}}
                                                @else
                                                    -
                                                @endif
                                                /
                                                @if($match->prediction->win_prob_home_team > 0)
                                                    @if($match->prediction->win_prob_home_team==1)
                                                        100
                                                    @else
                                                    {{number_format(100 / ((1-$match->prediction->win_prob_home_team)*100), 2)}}
                                                    @endif
                                                @else
                                                    -
                                                @endif

                                            </td>
                                            <td>
                                                @if($match->toutou_match)
                                                    {{json_decode($match->toutou_match->odds)->ml[1]}}
                                                    /
                                                    {{json_decode($match->toutou_match->odds)->ml[3]}}
                                                @else
                                                    -
                                                @endif
                                            </td>
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

                            <!--------- LoL tab ---------->

                            <div id="fin-lol" class="tab-pane fade">
                                <table class="footable table table-bordered" data-page-size="10" data-limit-navigation="5" data-filter="#search-match" data-filter-minimum="3">
                                    <thead>
                                    <tr>
                                        <th>Finished matches<br>(Team A vs Team B)</th>
                                        <th>Date&time</th>
                                        <th>Predicted result</th>
                                        <th>Odds</th>
                                        <th>Client 0</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($finished_matches->where('game.slug', 'lol') as $match)
                                        <tr>
                                            <td>{{$match->opponent1_details->name}}
                                                vs {{$match->opponent2_details->name}}</td>
                                            <td>{{date_convert($match->start, 'UTC', Auth::user()->timezone, 'Y-m-d H:i:s', 'd M Y H:i')}}</td>
                                            <td>
                                                @if($match->prediction->win_prob_home_team > 0.5)
                                                    {{$match->opponent1_details->name}}
                                                @else
                                                    {{$match->opponent2_details->name}}
                                                @endif
                                            </td>
                                            <td>
                                                @if($match->prediction->win_prob_home_team > 0)
                                                    {{number_format(100 / ($match->prediction->win_prob_home_team*100), 2)}}
                                                @else
                                                    -
                                                @endif
                                                /
                                                @if($match->prediction->win_prob_home_team > 0)
                                                    @if($match->prediction->win_prob_home_team==1)
                                                        100
                                                    @else
                                                    {{number_format(100 / ((1-$match->prediction->win_prob_home_team)*100), 2)}}
                                                    @endif
                                                @else
                                                    -
                                                @endif

                                            </td>
                                            <td>
                                                @if($match->toutou_match)
                                                    {{json_decode($match->toutou_match->odds)->ml[1]}}
                                                    /
                                                    {{json_decode($match->toutou_match->odds)->ml[3]}}
                                                @else
                                                    -
                                                @endif
                                            </td>
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

                            <!--------- Overwatch tab ---------->

                            <div id="fin-ow" class="tab-pane fade">
                                <table class="footable table table-bordered" data-page-size="10" data-limit-navigation="5" data-filter="#search-match" data-filter-minimum="3">
                                    <thead>
                                    <tr>
                                        <th>Finished matches<br>(Team A vs Team B)</th>
                                        <th>Date&time</th>
                                        <th>Predicted result</th>
                                        <th>Odds</th>
                                        <th>Client 0</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($finished_matches->where('game.slug', 'overwatch') as $match)
                                        <tr>
                                            <td>{{$match->opponent1_details->name}}
                                                vs {{$match->opponent2_details->name}}</td>
                                            <td>{{date_convert($match->start, 'UTC', Auth::user()->timezone, 'Y-m-d H:i:s', 'd M Y H:i')}}</td>
                                            <td>
                                                @if($match->prediction->win_prob_home_team > 0.5)
                                                    {{$match->opponent1_details->name}}
                                                @else
                                                    {{$match->opponent2_details->name}}
                                                @endif
                                            </td>
                                            <td>
                                                @if($match->prediction->win_prob_home_team > 0)
                                                    {{number_format(100 / ($match->prediction->win_prob_home_team*100), 2)}}
                                                @else
                                                    -
                                                @endif
                                                /
                                                @if($match->prediction->win_prob_home_team > 0)
                                                    @if($match->prediction->win_prob_home_team==1)
                                                        100
                                                    @else
                                                    {{number_format(100 / ((1-$match->prediction->win_prob_home_team)*100), 2)}}
                                                    @endif
                                                @else
                                                    -
                                                @endif

                                            </td>
                                            <td>
                                                @if($match->toutou_match)
                                                    {{json_decode($match->toutou_match->odds)->ml[1]}}
                                                    /
                                                    {{json_decode($match->toutou_match->odds)->ml[3]}}
                                                @else
                                                    -
                                                @endif
                                            </td>
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

            <div class="col-sm-6">
                <div class="row">

                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-sm-12">
                                <h2>Matches / Month</h2>
                                <div class="resizing">
                                    <canvas id="matches"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{--<div class="col-sm-6">--}}
                    {{--<h2>Who was right ?</h2>--}}
                    {{--<div class="resizing">--}}
                    {{--<canvas id="who"></canvas>--}}
                    {{--</div>--}}
                    {{--</div>--}}

                    {{--<div class="col-sm-6">--}}
                    {{--<h2>Prediction Rate</h2>--}}
                    {{--<div class="resizing">--}}
                    {{--<canvas id="timeData"></canvas>--}}
                    {{--</div>--}}
                    {{--</div>--}}

                </div>
            </div>

            @endsection

            @section('scripts')
                @parent
                <script src="/js/chartJs/Chart.min.js"></script>
                <script>

                    // Matches / Month

                    var matches = {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                        datasets: [
                            {
                                label: "Dummy Matches",
                                fillColor: "rgba(220,220,220,0.5)",
                                strokeColor: "rgba(220,220,220,0.8)",
                                highlightFill: "rgba(220,220,220,0.75)",
                                highlightStroke: "rgba(220,220,220,1)",
                                data: {{json_encode(array_values($stats['dummy']))}}
                            },
                            {
                                label: "TouTou Matches",
                                fillColor: "rgba(151,187,205,0.5)",
                                strokeColor: "rgba(151,187,205,0.8)",
                                highlightFill: "rgba(151,187,205,0.75)",
                                highlightStroke: "rgba(151,187,205,1)",
                                data: {{json_encode(array_values($stats['toutou']))}}
                            }
                        ]
                    };

                    var ctx = document.getElementById("matches").getContext("2d");
                    window.myBarChart = new Chart(ctx).Bar(matches, {responsive: true});

                    // Who was right ?

                    var who = {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr'],
                        datasets: [
                            {
                                label: "Data",
                                fillColor: "rgba(220,220,220,0.5)",
                                strokeColor: "rgba(220,220,220,0.8)",
                                highlightFill: "rgba(220,220,220,0.75)",
                                highlightStroke: "rgba(220,220,220,1)",
                                data: [65, 59, 80, 81]
                            },
                            {
                                label: "Data 2",
                                fillColor: "rgba(151,187,205,0.5)",
                                strokeColor: "rgba(151,187,205,0.8)",
                                highlightFill: "rgba(151,187,205,0.75)",
                                highlightStroke: "rgba(151,187,205,1)",
                                data: [28, 48, 40, 19]
                            }
                        ]
                    };

                    var ctx = document.getElementById("who").getContext("2d");
                    window.myBarChart = new Chart(ctx).Bar(who, {responsive: true});

                    //  Prediction Rate

                    var timeData = {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
                        datasets: [
                            {
                                label: "This Year",
                                fillColor: "rgba(220,220,220,0.2)",
                                strokeColor: "rgba(220,220,220,1)",
                                pointColor: "rgba(220,220,220,1)",
                                pointStrokeColor: "#fff",
                                pointHighlightFill: "#fff",
                                pointHighlightStroke: "rgba(220,220,220,1)",
                                data: [65, 59, 80, 81, 56]
                            },
                            {
                                label: "Last Year",
                                fillColor: "rgba(151,187,205,0.2)",
                                strokeColor: "rgba(151,187,205,1)",
                                pointColor: "rgba(151,187,205,1)",
                                pointStrokeColor: "#fff",
                                pointHighlightFill: "#fff",
                                pointHighlightStroke: "rgba(151,187,205,1)",
                                data: [28, 48, 40, 19, 86]
                            }
                        ]
                    };

                    var ctx = document.getElementById("timeData").getContext("2d");
                    window.myLineChart = new Chart(ctx).Line(timeData, {responsive: true});
                </script>
@endsection