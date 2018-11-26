<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use \Illuminate\Http\Request;
use App\Models\RoleUser;
use App\User;

class UserRolesController extends Controller {

    public function getRoles(Request $request){
        return response()->json(User::find((int) $request->get('user'))->roles);
    }
    public function addRole(Request $request){
        if(RoleUser::where('user_id', $request->get('user'))->where('role_id', $request->get('role'))->count()){
            return response()->json(User::find($request->get('user'))->roles, 202);
        }
        try{
            RoleUser::insert([
                'user_id' => (int)$request->get('user'),
                'role_id' => (int)$request->get('role')
            ]);
        }catch (\Exception $e){
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ], 500);
        }

        return response()->json(User::find($request->get('user'))->roles, 201);
    }

    public function removeRole(Request $request){
        try{
            RoleUser::where('user_id', $request->get('user'))->where('role_id', $request->get('role'))->delete();
        }catch (\Exception $e){
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ], 500);
        }
        return response()->json(User::find($request->get('user'))->roles, 200);
    }

}