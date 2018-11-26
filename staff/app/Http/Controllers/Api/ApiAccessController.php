<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApiClients;
use App\Models\ClientScopes;
use \Illuminate\Http\Request;

class ApiAccessController extends Controller {

    public function getAccess(Request $request){
        return response()->json(ApiClients::find($request->get('id')));
    }
    public function postAccess(Request $request){
        $record = ($request->has('id') ? ApiClients::find($request->get('id')) : new ApiClients());
        try{
            $record->name = $request->get('name');
            if(!$request->has('id')){
                /*
                 * Generate client id
                 */
                $new_client_id = $this->_random_string('alnum', 10);
                while (ApiClients::where('id', $new_client_id)->count()){
                    $new_client_id = $this->_random_string('alnum', 10);
                }
                $record->id = $new_client_id;

                /**
                 * Generate client secret
                 */
                $new_client_secret = $this->_random_string('alnum', 10);
                while (ApiClients::where('secret', $new_client_secret)->count()){
                    $new_client_secret = $this->_random_string('alnum', 10);
                }
                $record->secret = $new_client_secret;
            }

            $record->save();
            /**
             * Save user scopes access
             */
            ClientScopes::where('client_id', $record->id)->delete();
            foreach($request->get('scopes') as $scope){
                if($scope){
                    ClientScopes::create([
                        'client_id' => $record->id,
                        'scope_id' => $scope
                    ]);
                }
            }
        }catch (\Exception $e){
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ], 500);
        }



        return response()->json($record, 200);
    }

    public function deleteAccess(Request $request){
        try{
            ApiClients::find($request->get('id'))->delete();
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

    private function _random_string($type = 'alnum', $len = 10)
    {
        switch($type)
        {
            case 'basic'	: return mt_rand();
                break;
            case 'alnum'	:
            case 'numeric'	:
            case 'nozero'	:
            case 'alpha'	:

                switch ($type)
                {
                    case 'alpha'	:	$pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                        break;
                    case 'alnum'	:	$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                        break;
                    case 'numeric'	:	$pool = '0123456789';
                        break;
                    case 'nozero'	:	$pool = '123456789';
                        break;
                    default: $pool = '123456789';
                        break;
                }

    $str = '';
    for ($i=0; $i < $len; $i++)
    {
        $str .= substr($pool, mt_rand(0, strlen($pool) -1), 1);
    }
    return $str;
    break;
    case 'unique'	:
            case 'md5'		:

                return md5(uniqid(mt_rand()));
                break;
        }
    }
}