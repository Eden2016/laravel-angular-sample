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
                        <h2>Teams</h2>
                        <form id="filters-form" class="form-horizontal" role="form">
                        <div class="input-group">
                            <input type="text" placeholder="Search team" name="name" class="input form-control" id="searchteams" />
                            <span class="input-group-btn">
                                        <button type="button" class="btn btn btn-primary" disabled> <i class="fa fa-search"></i> Search</button>
                                </span>
                        </div>
                        </form>
                        <div class="clients-list">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered dt-responsive nowrap ec-datatable" id="all-teams-table">
                                    <thead>
                                    <tr>
                                        <th width="36" style="width: 36px;">Logo</th>
                                        <th>Team Name</th>
                                        @if ( request()->currentGameSlug == 'dota2')
                                            <th>Linked ?</th>
                                        @endif
                                        <th>Recent match</th>
                                    </tr>
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
                    var oTable = $('#all-teams-table').DataTable({
                      processing: true,
                      serverSide: true,
                      stateSave: true,
                      searching:   false,
                      ajax: {
                        url: '{!! groute('teams.dataquery') !!}',
                        method: 'GET',
                        data: function (d) {
                          d.name = $('input[name=name]').val();
                        }
                      },
                      columns: [
                        {
                          data: 'logo',
                          name: 'logo',
                          render: function(data, type, full, meta) {
                            if(data) {
                              return '<img style="width:32px" src="http://static.esportsconstruct.com/' + data + '" alt="' + full.name + '">';
                            } else {
                              return '<img style="width:32px" src="/img/profile-photo-blank.jpg" alt="' + full.name + '">';
                            }
                          }
                        },
                        {
                          data: 'name',
                          name: 'name',
                          render: function(data, type, full, meta) {
                            return '<a href="{{groute('team.show',[''])}}/' + full.id + '">' + data + '</a>';
                          }
                        },
                        @if ( request()->currentGameSlug == 'dota2')
                        {
                          data: 'team_id',
                          name: 'team_id',
                          render: function(data, type, full, meta) {
                            console.log(full);
                            if(data > 0) {
                              return 'Linked  <span class="fa fa-chevron-up text-info"></span>';
                            } else {
                              return 'Not Linked  <span class="fa fa-chevron-down text-danger"></span>';
                            }
                          }
                        },
                        @endif
                        {
                          data: 'start',
                          name: 'start'
                        }
                      ]
                    });
                    $('#filters-form').on('submit', function(e) {
                      oTable.draw();
                      e.preventDefault();
                    });
                  } );
                </script>
@endsection