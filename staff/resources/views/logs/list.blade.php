@extends('layouts.default')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Logs</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{groute('/')}}">Home</a>
            </li>
            <li>
                <a href="{{groute('logs')}}">Logs</a>
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

                            <h2>Logs</h2>

                            <div class="full-height-scroll">
                                <div class="table-responsive">
                                    <label for="">Filters: </label>
                                    <div class="btn-group" role="group">

                                        <div class="btn-group" role="group">
                                            <button class="btn btn-default dropdown-toggle btn-xs" type="button"
                                                    id="dropdownMenu1" data-toggle="dropdown">
                                                User
                                                <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
                                                <li>
                                                    <a href="{{request()->url().'?'.http_build_query(collect(request()->except('user'))->toArray())}}">Clear</a>
                                                </li>
                                                @foreach($users as $user_id)
                                                    <?php $user = \App\User::find($user_id); ?>
                                                    <?php if (!$user) continue ?>
                                                    <li>
                                                        <a href="{{request()->url().'?'.http_build_query(collect(request()->except('user'))->merge(['user' => $user_id])->toArray())}}">{{ $user ? $user->name : '' }}</a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-default dropdown-toggle btn-xs" type="button"
                                                    id="dropdownMenu1" data-toggle="dropdown">
                                                Model
                                                <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
                                                <li>
                                                    <a href="{{request()->url().'?'.http_build_query(collect(request()->except('model'))->toArray())}}">Clear</a>
                                                </li>
                                                @foreach($models as $model)
                                                    <li>
                                                        <a href="{{request()->url().'?'.http_build_query(collect(request()->except('model'))->merge(['model' => $model])->toArray())}}">{{$model}}</a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-default dropdown-toggle btn-xs" type="button"
                                                    id="dropdownMenu1" data-toggle="dropdown">
                                                Action
                                                <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
                                                <li>
                                                    <a href="{{request()->url().'?'.http_build_query(collect(request()->except('action'))->toArray())}}">Clear</a>
                                                </li>
                                                @foreach($actions as $action)
                                                    <li>
                                                        <a href="{{request()->url().'?'.http_build_query(collect(request()->except('action'))->merge(['action' => $action])->toArray())}}">{{$action}}</a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-default dropdown-toggle btn-xs" type="button"
                                                    id="dropdownMenu1" data-toggle="dropdown">
                                                Days to show
                                                <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
                                                <li>
                                                    <a href="{{request()->url().'?'.http_build_query(collect(request()->except('interval'))->toArray())}}">Clear</a>
                                                </li>
                                                @for($i=10, $max=100; $i<=$max; $i+=10)
                                                    <li>
                                                        <a href="{{request()->url().'?'.http_build_query(collect(request()->except('interval'))->merge(['interval' => $i])->toArray())}}">{{$i}}
                                                            days</a>
                                                    </li>
                                                @endfor
                                            </ul>
                                        </div>
                                    </div>
                                    <table class=" table table-stripped" data-filter-minimum="3" data-page-size="10"
                                           data-page-navigation-size="4">
                                        <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>
                                                User
                                            </th>
                                            <th>Game</th>
                                            <th>Model</th>
                                            <th>Item</th>
                                            <th>Action</th>
                                            <th>Date</th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($items as $item)
                                            <tr>
                                                <td>{{$item->id}}</td>
                                                <?php $user = \App\User::find($item->user_id); ?>
                                                <td>[{{$item->user_id}}] {{ $user ? $user->name : '' }}</td>
                                                <td>
                                                    <?php
                                                    try {
                                                        $game = \App\Game::find(Fish\Logger\Log::find($item->id)->loggable->game_id);
                                                        echo $game->name;
                                                    } catch (\Exception $e) {
                                                        echo $item->loggable_id;
                                                    }
                                                    ?>
                                                </td>
                                                <td>{{$item->loggable_type}}</td>
                                                <td>
                                                    <?php
                                                    try {
                                                        $link = Fish\Logger\Log::find($item->id)->loggable->link;
                                                        if ($link) {
                                                            echo '<a href="' . $link . '">' . $item->loggable_id . '</a>';
                                                        } else {
                                                            echo $item->loggable_id;
                                                        }
                                                    } catch (\Exception $e) {
                                                        echo $item->loggable_id;
                                                    }
                                                    ?>
                                                </td>
                                                <td>{{$item->action}}</td>
                                                <td>{{$item->created_at}}</td>
                                                <td>
                                                    <button type="button" data-id="{{$item->id}}" data-toggle="modal"
                                                            data-target="#changesModal" class="btn btn-default btn-xs">
                                                        <i class="fa fa-eye"></i></button>
                                                </td>
                                            </tr>
                                        @endforeach

                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <td colspan="7">
                                                {{$items->links()}}
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

        <!-- /.modal -->
        <!-- /.modal -->

        <div class="modal fade" id="changesModal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Changes</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-xs-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Before</h3>
                                    </div>
                                    <div class="panel-body">
                                        <pre id="before"></pre>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">After</h3>
                                    </div>
                                    <div class="panel-body">
                                        <pre id="after"></pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

        @endsection

        @section('scripts')
            @parent
            <script type="text/javascript">
              $(function () {
                $('body').on('shown.bs.modal', '#changesModal', function (e) {
                  $('#changesModal #before, #changesModal #after').html('');
                  $.getJSON('/logs?id=' + $(e.relatedTarget).data('id'), function (response) {
                    $('#changesModal #before').html(JSON.stringify($.parseJSON(response[0].before), null, 4));
                    $('#changesModal #after').html(JSON.stringify($.parseJSON(response[0].after), null, 4));
                  });
                });
              });
            </script>
@endsection