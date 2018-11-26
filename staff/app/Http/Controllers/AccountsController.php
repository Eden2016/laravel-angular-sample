<?php

namespace App\Http\Controllers;

use App\Models\ApiClients;
use Illuminate\Http\Request;

use App\Http\Requests;
use View;

class AccountsController extends Controller
{
    public function index() {
        $data = [];
        $data['accounts'] = ApiClients::all();
        return view('accounts/api_access', $data);
    }

    public function getScopes(){
        $data = [];
        $data['scopes'] = \App\Models\OauthScopes::all();
        return view('accounts/access_scopes', $data);
    }

}
