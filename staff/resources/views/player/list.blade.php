@extends('layouts.default')

@section('content')
    <div class="players-wrapp">
        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>All Players</h2>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{groute('/')}}">Home</a>
                    </li>
                    <li>
                        <a href="{{groute('players.list')}}">Players</a>
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
                            <span class="text-muted small pull-right">Updated at timestamp in UTC</span>
                            <h2>List of Players</h2>

                            <form id="filters-form" class="form-horizontal" role="form">
                                <div class="row">
                                    <div class="col-md-4">
                                        <input type="text" placeholder="Search player" name="nickname" value="{{$user_prefilled}}" class="input form-control" id="searchplayers" />
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" placeholder="Steam ID" name="steamid" value="" class="input form-control" id="searchsteam" />
                                    </div>
                                    <div class="col-md-2">
                                        <div class="checkbox">
                                            <label for="emptybios"><input type="checkbox" name="emptybios" id="emptybios" /> <b>Uneditted</b></label>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn btn-primary btn-block"> <i class="fa fa-search"></i> Search</button>
                                    </div>
                                </div>
                            </form>

                            <div class="clients-list">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered dt-responsive nowrap ec-datatable" id="players-table">
                                        <thead>
                                        <tr>
                                            <th width="30">ID</th>
                                            <th width="40">Image</th>
                                            <th>Nickname</th>
                                            <th>Steam ID</th>
                                            <th>Updated At</th>
                                            <th>Action</th>
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
                      function regenerateStats(event, steam_id) {
                        $this = $(event.target);
                        $.get('/player/refresh_stats/' + steam_id, function(data) {
                          if (data.status == "success") {
                            $this.text('Refreshing stats...');
                            setTimeout(function() {
                              $this.text('Regenerate Stats');
                            }, 5000);
                          } else {
                            console.log(data);
                          }
                        });
                      }

                      $(document).ready(function() {

                        var oTable = $('#players-table').DataTable({
                          processing: true,
                          serverSide: true,
                          stateSave: true,
                          searching: false,
                          ajax: {
                            url: '{!! groute('players.dataquery') !!}',
                            method: 'GET',
                            data: function (d) {
                              if($('input[name=nickname]').val() != '') {
                                d.nickname = $('input[name=nickname]').val();
                              }
                              if($('input[name=steamid]').val() != '') {
                                d.steamid = $('input[name=steamid]').val();
                              }
                              if($('#emptybios').prop('checked')) {
                                d.uneditted = '1';
                              }
                            }
                          },
                          columns: [
                            {
                              data: 'id',
                              name: 'id',
                              render: function(data, type, full, meta) {
                                return '<b>' + data + '</b>';
                              }
                            },
                            {
                              data: 'avatar',
                              name: 'avatar',
                              searchable: false,
                              sortable: false,
                              render: function(data, type, full, meta) {
                                if(data) {
                                  return '<div style="height: 32px"><img alt="image" src="http://static.esportsconstruct.com/' + data + '" class="players-avatar"></div>';
                                } else {
                                  return '<div style="height: 32px"><img alt="image" src="/img/players-avatars/no-player-photo.jpg" class="players-avatar"></div>';
                                }
                              }
                            },
                            {
                              data: 'nickname',
                              name: 'nickname',
                              render: function(data, type, full, meta) {
                                return '<a href="{{groute('player.show', 'current', [''])}}/' + full.id + '">'+data+'</a>';
                              }
                            },
                            {
                              data: 'steam_id',
                              name: 'steam_id',
                              render: function(data, type, full, meta) {
                                return 'Steam ID: <a href="{{groute('player.show', 'current', [''])}}/' + full.id + '">'+data+'</a>';
                              }
                            },
                            {
                              data: 'updated_at',
                              name: 'updated_at'
                            },
                            {
                              data: '',
                              name: '',
                              sortable: false,
                              searchable: false,
                              render: function(data, type, full, meta) {
                                return '<span style="cursor:pointer;" class="label regenerate-stats" onclick="regenerateStats(event,'+full.steam_id+')">Regenerate Stats</span>';
                              }
                            }
                          ]
                        });

                        $('#filters-form').on('submit', function(e) {
                          oTable.draw();
                          e.preventDefault();
                        });

                      });
                    </script>
@endsection

