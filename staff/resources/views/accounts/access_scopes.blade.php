@extends('layouts.default')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>API Scopes</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{groute('/')}}">Home</a>
                </li>
                <li>
                    <a href="{{groute('accounts.api.scopes')}}">API Scopes</a>
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

                                <h2>API Scopes</h2>

                                <div class="full-height-scroll">
                                    <div class="table-responsive">
                                        <table class="footable table table-stripped"  data-filter-minimum="3" data-page-size="10" data-page-navigation-size="4">
                                            <tbody>
                                            @foreach($scopes as $scope)
                                                <tr>

                                                    <td>{{$scope->id}}</td>
                                                    <td>{{$scope->description}}</td>
                                                    <td><button data-target="#api-scope-modal" data-toggle="modal" data-id="{{$scope->id}}" class="btn btn-primary dim">Edit</button></td>
                                                    <td><button data-target="#delete-api-scope-modal" data-toggle="modal" data-id="{{$scope->id}}" class="btn btn-danger dim">Delete scope</button></td>

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
                                        <button data-target="#api-scope-modal" data-toggle="modal" data-id="" class="btn btn-primary dim">Create scope</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- /.modal -->
            <div class="modal fade" tabindex="-1" role="dialog" id="delete-api-scope-modal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Delete scope</h4>
                        </div>
                        <div class="modal-body text-center">

                            <p>You are about to delete a scope.</p>
                            <p>Are you sure?</p>
                            <button data-dismiss="modal" type="button" class="btn btn-danger dim" data-do="deleteApiScope">YES</button>
                            <button data-dismiss="modal" type="button" class="btn btn-primary dim">NO</button>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->

            <div class="modal fade" tabindex="-1" role="dialog" id="api-scope-modal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Add/Edit Scope</h4>
                        </div>
                        <form >
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="scope_id">ID</label>
                                    <input type="text" name="id" id="scope_id" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <input type="text" name="description" id="description" class="form-control">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary dim" data-do="saveApiScope">Save</button>
                            </div>
                        </form>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->

@endsection

@section('scripts')
                @parent
                <script src="/js/api.scopes.js"></script>
@endsection
