@extends('layouts.default')

@section('content')

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>All Matches</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{groute('/')}}">Home</a>
                </li>
                <li class="active">
                    <strong>Matches</strong>
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
                        <div class="clients-list">
                            <div class="btn-group" role="group" id="match-game-select">
                                <button data-game-id="0" type="button" class="btn btn-default{{(request()->currentGame == null) ? ' active': ''}}">All</button>
                                @foreach(\App\Game::allCached() as $game)
                                    <button type="button" data-game-id="{{$game->id}}" class="btn btn-default{{($game->slug == request()->currentGameSlug) ? ' active': ''}}">{{$game->name}}</button>
                                @endforeach
                            </div>
                            <br/><br/>
                            <ul class="nav nav-tabs" id="match-type">
                                <li class="active">
                                    <a data-toggle="tab" data-match-type="live" href="#matches-live">Live
                                        <span class="badge badge-danger">?</span>
                                    </a>
                                </li>
                                <li>
                                    <a data-toggle="tab" data-match-type="upcoming" href="#matches-upcoming">Upcoming
                                        <span class="badge badge-warning">?</span>
                                    </a>
                                </li>
                                <li>
                                    <a data-toggle="tab" data-match-type="completed" href="#matches-completed">Completed
                                        <span class="badge badge-default">?</span>
                                    </a>

                                </li>
                                <li>
                                    <a data-toggle="tab" data-match-type="tba" href="#matches-tba">TBA <span class="badge badge-default">?</span>
                                    </a>
                                </li>
                                <li>
                                    <a data-toggle="tab" data-match-type="all" href="#matches-all">All <span class="badge badge-default">?</span>
                                    </a>
                                </li>
                            </ul>
                            <br/>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered dt-responsive nowrap ec-datatable" id="matches-table">
                                    <thead>
                                    <tr>
                                        <th>Game</th>
                                        <th>Team  1</th>
                                        <th>VS</th>
                                        <th>Team  2</th>
                                        <th>Start</th>
                                        <th>Tournament</th>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

@endsection

            @section('styles')
                @parent
                <link href="/bower_components/datatables/media/css/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
                <link href="/css/datatables.css" rel="stylesheet" type="text/css" />
@endsection

            @section('scripts')
                @parent
                <script src="/bower_components/datatables/media/js/jquery.dataTables.min.js"></script>
                <script src="/bower_components/datatables/media/js/dataTables.bootstrap.min.js"></script>

                <script type="text/javascript">
                  $(document).ready(function() {
                    var currentMatchType = 'live';
                    var currentGame = {{ (request()->currentGame->id) ?  request()->currentGame->id : 0 }};
                    var oTable = $('#matches-table').DataTable({
                      processing: true,
                      serverSide: true,
                      stateSave: true,
                      searching: false,
                      ajax: {
                        url: '{!! groute('matches.general.dataquery') !!}',
                        method: 'GET',
                        data: function (d) {
                          d.name = $('input[name=name]').val();
                          d.match_type = currentMatchType;
                          if(currentGame != 0) {
                            d.game_id = currentGame;
                          }
                        }
                      },
                      columns: [
                        {
                          data: 'game',
                          name: 'game_id'
                        },
                        {
                          data: 'opponent1_detail',
                          name: 'opponent1'
                        },
                        {
                          data: 'versus',
                          name: 'id'
                        },
                        {
                          data: 'opponent2_detail',
                          name: 'opponent2'
                        },
                        {
                          data: 'start',
                          name: 'start'
                        },
                        {
                          data: 'tournament',
                          name: 'tournament_id'
                        }
                      ]
                    });
                    $('#matches-table').on( 'draw.dt', function () {
                      $('#match-type a[data-match-type="' + currentMatchType+ '"] span').html(oTable.page.info().recordsDisplay);
                    });
                    $('#filters-form').on('submit', function(e) {
                      oTable.draw();
                      e.preventDefault();
                    });
                    $('#match-type a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                      currentMatchType = $(e.target).data("match-type");
                      oTable.draw();
                    });
                    $('#match-game-select button').click( function (e) {
                      $('#match-type span').html('?');
                      $('#match-game-select button').removeClass('active');
                      currentGame = $(e.target).data("game-id");
                      $(e.target).addClass('active');
                      oTable.draw();
                    });
                  });
                </script>
@endsection