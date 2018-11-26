<?php

namespace App\Http\Controllers;

use App\Permission;
use App\Role;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use View;
use App\Models\RoleUser;

class UserController extends Controller
{
    public function roles()
    {
        $data['users'] = User::all();
        $data['roles'] = Role::all();
        $data['permissions'] = Permission::all();
    	return view('user/roles', $data);
    }

    public function permissions()
    {
        $data['permissions'] = Permission::all();
    	return view('user/permissions', $data);
    }

    public function create(Request $request)
    {
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
            'timezone' => $request->input('timezone', 'UTC')
        ]);

        $role = RoleUser::create([
                'user_id' => $user->id,
                'role_id' => $request->input('role')
            ]);

        if ($user) {
            $retData = array(
                    "status" => "success"
                );
        }
        else {
            $retData = array(
                    "status" => "error",
                    "message" => "Something went wrong."
                );
        }

        return response()->json($retData);
    }

    public function edit(Request $request)
    {
        $user = User::find($request->input('id'));

        if (null !== $user) {
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->timezone = $request->input('timezone', 'UTC');

            if ($request->input('password'))
                $user->password = bcrypt($request->input('password'));

            $user->save();

            $retData = array(
                    "status" => "success"
                );
        }
        else {
            $retData = array(
                    "status" => "error",
                    "message" => "Couldn't fetch user with ID ".$request->input('id')."."
                );
        }

        return response()->json($retData);
    }

    public function delete(Request $request)
    {
        $user = User::find($request->input("id"));

        if (null !== $user) {
            $user->delete();
        }

        return response()->json([
                "status" => "success"
            ]);
    }

    public function getUser(Request $request)
    {
        $user = User::find($request->input('id'));

        if (null !== $user) {

            $retData = array(
                    "status" => "success",
                    "id" => $user->id,
                    "name" => $user->name,
                    "email" => $user->email,
                    "timezone" => $user->timezone
                );
        }
        else {
            $retData = array(
                    "status" => "error",
                    "message" => "Couldn't fetch user."
                );
        }

        return response()->json($retData);
    }
}
