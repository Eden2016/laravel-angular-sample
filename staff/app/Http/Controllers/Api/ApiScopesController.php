<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OauthScopes;
use \Illuminate\Http\Request;

class ApiScopesController extends Controller {

    public function getScope(Request $request){
        return response()->json(OauthScopes::find($request->get('id')));
    }
    public function postScope(Request $request){
        $record = OauthScopes::firstOrCreate($request->only(['id', 'description']));
        try{
            $record->id = $request->get('id');
            $record->description = $request->get('description');

            $record->save();

        }catch (\Exception $e){
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ], 500);
        }



        return response()->json($record, 200);
    }

    public function deleteScope(Request $request){
        try{
            OauthScopes::find($request->get('id'))->delete();
        }catch (\Exception $e){
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ], 500);
        }
        return response()->json([
            "success" => true,
            "message" => "ok"
        ], 200);
    }
}