@extends('layouts.default')

@section('content')
	<div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>Permissions</h2>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{groute('/')}}">Home</a>
                    </li>
                    <li>
                        <a href="{{groute('user.permissions')}}">Permisions</a>
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

                                    <h2>User permissions</h2>

                                    <div class="full-height-scroll">
                                        <div class="table-responsive">
                                            <table class="footable table table-stripped"  data-filter-minimum="3" data-page-size="10" data-page-navigation-size="4">
                                                <tbody>
                                               @foreach($permissions as $p)
                                                <tr>

                                                    <td><span class="label label-danger">{{$p->display_name}}</span></td>
                                                    <td><button data-target="#edit-permission-modal" data-toggle="modal" data-id="{{$p->id}}" class="btn btn-primary dim">Edit</button></td>
                                                    <td><button data-target="#delete-permission-modal" data-toggle="modal" data-id="{{$p->id}}" class="btn btn-danger dim">Delete</button></td>

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
                                            <button data-target="#add-permission-modal" data-toggle="modal" class="btn btn-primary dim">Add permission</button>
                                        </div>
                                    </div>
								</div>
                           </div>

                        </div>
                    </div>
                </div>

        <div class="modal fade" tabindex="-1" role="dialog" id="edit-permission-modal">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Edit permission</h4>
              </div>
              <div class="modal-body">

                    <form action="#" method="post" enctype='multipart/form-data'>


                      <div class="form-group">
                        <label for="name">Name</label>
                        <input value="Super Admin permission" type="text" class="form-control" name="name" id="name" />
                      </div>
                      <div class="form-group">
                        <label for="display_name">Display Name</label>
                        <input value="Super Admin" type="text" class="form-control" name="display_name" id="display_name" />
                      </div>
                      <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" name="description" id="description">Everything allowed</textarea>
                      </div>

                    </form>

              </div>
              <div class="modal-footer">
                <button data-dismiss="modal" type="button" class="btn btn-primary dim" data-do="savePermission">Save changes</button>
              </div>
            </div><!-- /.modal-content -->
          </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        <div class="modal fade" tabindex="-1" role="dialog" id="delete-permission-modal">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Deleting permission</h4>
              </div>
              <div class="modal-body text-center">

                <p>You are about to delete a permission.</p>
                        <p>Are you sure?</p>
                <button data-dismiss="modal" type="button" class="btn btn-danger dim" data-do="deletePermission">YES</button>
                <button data-dismiss="modal" type="button" class="btn btn-primary dim">NO</button>
              </div>
            </div><!-- /.modal-content -->
          </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        <div class="modal fade" tabindex="-1" role="dialog" id="add-permission-modal">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Adding New permission</h4>
              </div>
               <form >
              <div class="modal-body">
                      <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" name="name" id="name"  required/>
                      </div>
                      <div class="form-group">
                        <label for="display_name">Display Name</label>
                        <input type="text" class="form-control" name="display_name" id="display_name"  required/>
                      </div>
                      <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" required name="description" id="description"></textarea>
                      </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-primary dim" data-do="addPermission">Save</button>
              </div>
              </form>
            </div><!-- /.modal-content -->
          </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
                @endsection

                @section('scripts')
                    @parent
                    <script src="/js/user.permissions.js"></script>
@endsection