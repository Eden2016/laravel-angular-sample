@extends('layouts.default')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>API access</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{groute('/')}}">Home</a>
            </li>
            <li>
                <a href="{{groute('accounts.api')}}">API Access</a>
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

                            <h2>API Access</h2>

                            <div class="full-height-scroll">
                                <div class="table-responsive">
                                    <table class="footable table table-stripped"  data-filter-minimum="3" data-page-size="10" data-page-navigation-size="4">
                                        <tbody>
                                        @foreach($accounts as $account)
                                            <tr>

                                                <td>{{$account->name}}</td>
                                                <td>{{$account->id}}</td>
                                                <td><button data-target="#api-access-modal" data-toggle="modal" data-id="{{$account->id}}" class="btn btn-primary dim">Edit</button></td>
                                                <td><button data-target="#delete-api-access-modal" data-toggle="modal" data-id="{{$account->id}}" class="btn btn-danger dim">Revoke access</button></td>

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
                                    <button data-target="#api-access-modal" data-toggle="modal" data-id="" class="btn btn-primary dim">Create access</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- /.modal -->
        <div class="modal fade" tabindex="-1" role="dialog" id="delete-api-access-modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Deleting permission</h4>
                    </div>
                    <div class="modal-body text-center">

                        <p>You are about to delete a permission.</p>
                        <p>Are you sure?</p>
                        <button data-dismiss="modal" type="button" class="btn btn-danger dim" data-do="deleteApiAccess">YES</button>
                        <button data-dismiss="modal" type="button" class="btn btn-primary dim">NO</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

        <div class="modal fade" tabindex="-1" role="dialog" id="api-access-modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Add access permission</h4>
                    </div>
                    <form >
                        <div class="modal-body">



                            <div class="form-group">
                                <label>Client ID</label>
                                <p class="form-control-static" id="client_id"></p>
                            </div>
                            <div class="form-group">
                                <label>Client Secret</label>
                                <p class="form-control-static" id="client_secret"></p>
                            </div>
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" name="name" id="name" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="scopes">Scopes</label>
                                    <select class="select2" multiple name="scopes" id="scopes" style="width: 100%">
                                        @foreach(\App\Models\OauthScopes::all() as $scope)
                                            <option value="{{$scope->id}}">{{$scope->id}}</option>
                                        @endforeach
                                    </select>
                            </div>


                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary dim" data-do="saveApiAccess">Save</button>
                        </div>
                    </form>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
@endsection

@section('scripts')
            @parent
            <script src="/js/api.access.js"></script>
            <script>
              $(function(){
                $('.select2').select2();
              });
            </script>
@endsection