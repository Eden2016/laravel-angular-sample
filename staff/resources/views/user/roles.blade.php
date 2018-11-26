@extends('layouts.default')

@section('content')

	<div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>User Roles</h2>
                <ol class="breadcrumb">
                    <li>
                        <a href="{{groute('/')}}">Home</a>
                    </li>
                    <li>
                        <a href="{{groute('user.roles')}}">User Roles</a>
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

                                    <h2>Users</h2>
                                    <div class="input-group">
                                        <input type="text" placeholder="Search user" class="input form-control" />
                                        <span class="input-group-btn">
                                                <button type="button" class="btn btn btn-primary" disabled> <i class="fa fa-search"></i> Search</button>
                                        </span>
                                    </div><br>
                                    <div class="full-height-scroll">
                                        <div class="table-responsive">
                                            <table class="footable table table-stripped"  data-filter-minimum="3" data-page-size="10" data-page-navigation-size="4">
                                                <tbody>
                                               @foreach($users as $user)
                                                <tr>

                                                    <td><a href="#">  {{$user->name}} </a></td>
                                                    <td>
                                                        @foreach($user->roles as $role)
                                                        <span class="label label-danger">{{$role->display_name}}</span>
                                                        @endforeach
                                                    </td>
                                                    <td><button data-target="#link-role-modal" data-user="{{$user->id}}" data-toggle="modal" class="btn btn-primary dim">Add role</button></td>
                                                    <td><button data-target="#unlink-role-modal" data-user="{{$user->id}}" data-toggle="modal" class="btn btn-primary dim">Remove role</button></td>
                                                    <td><button data-target="#edit-user-modal" data-id="{{$user->id}}" data-toggle="modal" class="btn btn-primary dim">Edit</button></td>
                                                    <td><button data-id="{{$user->id}}" data-do="deleteUser" class="btn btn-danger dim">Delete</button></td>

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
                                            <button data-target="#add-user-modal" data-toggle="modal" class="btn btn-primary dim">Add User</button>
                                        </div>
                                    </div>
								</div>
                           </div>

                        </div>
                    </div>
                    <div class="ibox">
                        <div class="ibox-content">
                           <div class="row">
                                <div class="col-md-12">

                                    <h2>User Roles</h2>

                                    <div class="full-height-scroll">
                                        <div class="table-responsive">
                                            <table class="footable table table-stripped"  data-filter-minimum="3" data-page-size="10" data-page-navigation-size="4">
                                                <tbody>
                                               @foreach($roles as $role)
                                                <tr>

                                                    <td><span class="label label-danger">{{$role->display_name}}</span></td>
                                                    <td><button data-target="#edit-role-modal" data-role="{{$role->id}}" data-toggle="modal" class="btn btn-primary dim">Edit</button></td>
                                                    <td><button data-target="#delete-role-modal" data-role="{{$role->id}}" data-toggle="modal" class="btn btn-danger dim">Delete</button></td>

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
                                            <button data-target="#add-role-modal" data-toggle="modal" class="btn btn-primary dim">Add Role</button>
                                        </div>
                                    </div>
								</div>
                           </div>

                        </div>
                    </div>
                </div>

        <div class="modal fade" tabindex="-1" role="dialog" id="link-role-modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Link permission to user</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Select Role</label>
                            <div class="col-sm-10">
                                <select class="form-control m-b" name="role" title="Select Role">
                                    @foreach(\App\Role::all() as $role)
                                    <option value="{{$role->id}}">{{$role->display_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="user_id" value="">
                        <button data-dismiss="modal" type="button" class="btn btn-primary dim" data-do="addRoleToUser">Save changes</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

        <div class="modal fade" tabindex="-1" role="dialog" id="unlink-role-modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Unlink permission from user <span id="user_names"></span></h4>
                    </div>
                    <div class="modal-body">
                        <table class="table" id="user_roles_table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>User Role</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody> {{-- Nikola: user roles TRs are filled with jquery from  js/user.roles.js --}}
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="user_id" value="">
                        <button data-dismiss="modal" type="button" class="btn btn-primary dim">Close</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

        <div class="modal fade" tabindex="-1" role="dialog" id="edit-role-modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Edit Role</h4>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input value="" type="text" class="form-control" name="name" id="name" />
                            </div>
                            <div class="form-group">
                                <label for="display_name">Display Name</label>
                                <input value="" type="text" class="form-control" name="display_name" id="display_name" />
                            </div>
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" name="description" id="description"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="edit-permissions">Permissions</label>
                                <select multiple class="form-control" name="edit-permissions" id="edit-permissions">
                                    @if (count($permissions))
                                    @foreach ($permissions as $permission)
                                    <option value="{{ $permission->id }}">{{ $permission->display_name }}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                            <input value="" type="hidden" class="form-control" name="role_id" />
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" type="button" class="btn btn-primary dim" data-do="saveRole">Save changes</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

        <div class="modal fade" tabindex="-1" role="dialog" id="delete-role-modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Deleting Role</h4>
                    </div>
                    <div class="modal-body text-center">

                        <p>You are about to delete a role.</p>
                            <p>Are you sure?</p>
                        <button data-dismiss="modal" type="button" class="btn btn-danger dim" data-do="deleteRole">YES</button>
                        <button data-dismiss="modal" type="button" class="btn btn-primary dim">NO</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

        <div class="modal fade" tabindex="-1" role="dialog" id="add-role-modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Adding New Role</h4>
                    </div>
                    <form>
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
                            <div class="form-group">
                                <label for="permissions">Permissions</label>
                                <select multiple class="form-control" name="permissions" id="permissions">
                                    @if (count($permissions))
                                    @foreach ($permissions as $permission)
                                    <option value="{{ $permission->id }}">{{ $permission->display_name }}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" data-dismiss="modal" class="btn btn-primary dim" data-do="addRole">Save</button>
                        </div>
                    </form>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->


        <div class="modal fade" tabindex="-1" role="dialog" id="add-user-modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Adding New User</h4>
                    </div>
                    <form>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="user-name">Name</label>
                                <input type="text" class="form-control" name="user-name" id="user-name"  required/>
                            </div>
                            <div class="form-group">
                                <label for="user-mail">E-Mail</label>
                                <input type="text" class="form-control" name="user-mail" id="user-mail"  required/>
                            </div>
                            <div class="form-group">
                                <label for="user-password">Password</label>
                                <input type="password" class="form-control" name="user-password" id="user-password"  required/>
                            </div>
                            <div class="form-group">
                                <label for="role">Role</label>
                                <select id="user-role" name="user-role">
                                    @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="timezone">Timezone</label>
                                <select name="timezone" id="timezone">
                                    <option>UTC</option>
                                    <option>Europe/Amsterdam</option>
                                    <option>Europe/Ljubljana</option>
                                    <option>Europe/Moscow</option>
                                    <option>Europe/Sofia</option>
                                    <option>Europe/Berlin</option>
                                    <option>Europe/London</option>
                                    <option>Europe/Dublin</option>
                                    <option>Europe/Minsk</option>
                                    <option>America/New_York</option>
                                    <option>America/Los_Angeles</option>
                                    <option>America/Phoenix</option>
                                    <option>America/Toronto</option>
                                    <option>America/Winnipeg</option>
                                    <option>America/Vancouver</option>
                                    <option>America/Halifax</option>
                                    <option>Asia/Hong_Kong</option>
                                    <option>Asia/Dubai</option>
                                    <option>Asia/Manila</option>
                                    <option>Asia/Tokyo</option>
                                    <option>Asia/Dhaka</option>
                                    <option>Asia/Singapore</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" data-dismiss="modal" class="btn btn-primary dim" data-do="addUser">Add user</button>
                        </div>
                    </form>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        <div class="modal fade" tabindex="-1" role="dialog" id="edit-user-modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Edit User</h4>
                    </div>
                    <form>
                        <input type="hidden" name="user-id" />
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="user-name">Name</label>
                                <input type="text" class="form-control" name="user-name" id="user-name"  required/>
                            </div>
                            <div class="form-group">
                                <label for="user-mail">E-Mail</label>
                                <input type="text" class="form-control" name="user-mail" id="user-mail"  required/>
                            </div>
                            <div class="form-group">
                                <label for="user-password">Password</label>
                                <input type="password" class="form-control" name="user-password" id="user-password"  required/>
                            </div>
                            <div class="form-group">
                                <label for="timezone">Timezone</label>
                                <select name="timezone" id="timezone">
                                    <option>UTC</option>
                                    <option>Europe/Amsterdam</option>
                                    <option>Europe/Ljubljana</option>
                                    <option>Europe/Moscow</option>
                                    <option>Europe/Sofia</option>
                                    <option>Europe/Berlin</option>
                                    <option>Europe/London</option>
                                    <option>Europe/Dublin</option>
                                    <option>Europe/Minsk</option>
                                    <option>America/New_York</option>
                                    <option>America/Los_Angeles</option>
                                    <option>America/Phoenix</option>
                                    <option>America/Toronto</option>
                                    <option>America/Winnipeg</option>
                                    <option>America/Vancouver</option>
                                    <option>America/Halifax</option>
                                    <option>Asia/Hong_Kong</option>
                                    <option>Asia/Dubai</option>
                                    <option>Asia/Manila</option>
                                    <option>Asia/Tokyo</option>
                                    <option>Asia/Dhaka</option>
                                    <option>Asia/Singapore</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" data-dismiss="modal" class="btn btn-primary dim" data-do="editUser">Edit user</button>
                        </div>
                    </form>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        <input type="hidden" id="csrf-token" value="{{ csrf_token() }}" />


                @endsection

                @section('scripts')
                    @parent
                    <script src="/js/user.roles.js"></script>
@endsection