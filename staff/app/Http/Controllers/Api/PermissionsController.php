<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Permission;
use \Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermissionsController extends Controller {

    public function getIndex(){
        return response()->json(Permission::all());
    }
    public function getPermission(Request $request){
        return response()->json(Permission::find((int)$request->get('id')));
    }

    public function postPermission(Request $request){
        $permission = ($request->has('id') ? Permission::find($request->get('id')) : new Permission());
        $permission->name = $request->get('name');
        $permission->display_name = $request->get('display_name');
        $permission->description = $request->get('description');
        $permission->save();
        return response()->json($permission);
    }

    public function deletePermission(Request $request){
        try {
            Permission::find($request->get('id'))->delete();
        }catch(\Exception $e){
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ], 500);
        }
        return response()->json(["success" => true, "message" => 'ok'], 200);
    }

}