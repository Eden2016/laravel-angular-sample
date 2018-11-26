@extends('layouts.default')

@section('content')

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Streams</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{groute('/')}}">Home</a>
                </li>
                <li>
                    <a href="{{ groute('streams') }}">Streams</a>
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

                                <h2>Streams</h2>
                                <div class="input-group">
                                    <input type="text" placeholder="Search stream" class="input form-control" id="searchstreams" />
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn btn-primary" disabled> <i class="fa fa-search"></i> Search</button>
                                    </span>
                                </div>

                                <div class="full-height-scroll">
                                    <div class="table-responsive">
                                        <table class="footable table table-stripped" data-filter-minimum="3" data-page-size="10" data-page-navigation-size="4" data-filter="#searchstreams">
                                            <thead>
                                                <tr>
                                                    <th>Name of stream</th>
                                                    <th>Platform</th>
                                                    <th>Game associated</th>
                                                    <th><i class="fa fa-cogs"></i></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($streams as $stream)
                                                <tr>

                                                    <td>{{$stream->title}}</td>
                                                    <td>{{$stream->platform}}</td>
                                                    <td>{{$stream->game ? $stream->game->name : 'No game specified'}}</td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <button data-target="#stream-form-modal" data-toggle="modal" data-id="{{$stream->id}}" class="btn btn-default btn-xs"><i
                                                                    class="fa fa-pencil"></i></button>
                                                            <button data-target="#delete-stream-modal" data-toggle="modal" data-id="{{$stream->id}}" class="btn btn-danger btn-xs">
                                                            <i class="fa fa-trash"></i></button>
                                                        </div>
                                                    </td>


                                                </tr>
                                                @endforeach

                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="4">
                                                        <ul class="pagination pull-right"></ul>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                        <button data-target="#stream-form-modal" data-toggle="modal" class="btn btn-primary dim">Add stream</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="modal fade" tabindex="-1" role="dialog" id="stream-form-modal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Edit stream</h4>
                        </div>
                        <div class="modal-body">

                            <div class="form-group">
                                <label for="title">Title</label>
                                <input type="text" class="form-control" name="title" id="title" required />
                            </div>
                            <div class="form-group">
                                <label for="link">Link</label>
                                <input type="text" class="form-control" name="link" id="link" required/>
                            </div>
                            <div class="form-group">
                                <label for="platform">Platform</label>
                                <select class="form-control" name="platform" id="platform">
                                <option value="Twitch.tv">Twitch.tv</option>
                                <option value="Douyutv.com">Douyutv.com</option>
                                <option value="Huomaotv.cn">Huomaotv.cn</option>
                                <option value="Hitbox">Hitbox</option>
                                <option value="MLG">MLG</option>
                                <option value="Youtube">Youtube</option>
                                <option value="Azubu">Azubu</option>
                                    <option value="Youku">YouKu</option>
                                    <option value="ImbaTV">ImbaTV</option>
                                    <option value="PandaTV">Panda.tv</option>
                                <option value="other">Other</option>
                            </select>
                            </div>
                            <div class="form-group">
                                <label for="embed_code">Embed code</label>
                                <textarea class="form-control" name="embed_code" id="embed_code"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" required name="description" id="description"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="lang">Language</label>
                                <input type="text" name="lang" id="lang" style="width: 100%" required/>
                            </div>

                            <div class="form-group">
                                <label for="game_id">Game</label>
                                <select class="form-control" name="game_id" id="game_id">
                                @foreach(\App\Game::allCached() as $game)
                                    <option value="{{$game->id}}">{{$game->name}}</option>
                                @endforeach
                            </select>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" type="button" class="btn btn-primary dim" data-do="saveStream">Save changes</button>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!-- /.modal -->
            <div class="modal fade" tabindex="-1" role="dialog" id="delete-stream-modal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Deleting stream</h4>
                        </div>
                        <div class="modal-body text-center">

                            <p>You are about to delete a stream.</p>
                            <p>Are you sure?</p>
                            <button data-dismiss="modal" type="button" class="btn btn-danger dim" data-do="deleteStream">YES</button>
                            <button data-dismiss="modal" type="button" class="btn btn-primary dim">NO</button>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!-- /.modal -->
            <!-- /.modal -->

            @endsection

            @section('scripts')
                @parent
                <script src="/js/streams.js"></script>
@endsection