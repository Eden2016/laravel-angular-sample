<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Role;
use \Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PermissionRole;

class RolesController extends Controller {

    public function getIndex(){
        return response()->json(Role::all());
    }
    public function getRole(Request $request){
        return response()->json(Role::where('id', (int)$request->get('role'))->with('permissions')->first());
    }

    public function postRole(Request $request){
        $role = ($request->has('id') ? Role::find($request->get('id')) : new Role());
        $role->name = $request->get('name');
        $role->display_name = $request->get('display_name');
        $role->description = $request->get('description');
        $role->save();

        if (count($request->input('permissions'))) {
            PermissionRole::where('role_id', $role->id)->delete();
            foreach ($request->input('permissions') as $permission) {
                $perm = new PermissionRole();
                $perm->role_id = $role->id;
                $perm->permission_id = $permission;

                $perm->save();
            }
        }
        else {
            PermissionRole::where('role_id', $role->id)->delete();
        }

        return response()->json($role);
    }

    public function deleteRole(Request $request){
        try {
            /*
             * TODO: fix \App\Models\Role deleting
             * Due some event firing in Role model and relations
             * regular deleting is not possible right now
             */
            //Role::find($request->get('id'))->delete();

            /**
             * So we will do it in old school way
             */
            DB::table('roles')->where('id', $request->get('id'))->delete();
            /**
             * Remove role from connected users
             */
            DB::table('role_user')->where('role_id', $request->get('id'))->delete();
        }catch (\Exception $e){
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ], 500);
        }
        return response()->json(["success" => true, "message" => 'ok'], 200);
    }

}