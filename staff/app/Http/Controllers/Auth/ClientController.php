<?php
namespace App\Http\Controllers\Auth;

use App\Client;
use App\Http\Requests\ClientCreationRequest;
use App\Http\Requests\ClientEditRequest;
use App\Http\Controllers\Controller;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::all();
        return view('clients.list', compact('clients'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(ClientCreationRequest $request)
    {
        $client = Client::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => bcrypt($request->get('password'))
        ]);

        $client->setHeadlineDimension($request->get('headline_width'), $request->get('headline_height'));
        $client->setThumbDimension($request->get('thumb_width'), $request->get('thumb_height'));
        $client->generateOAuthSecret();
        return redirect()->back();
    }

    public function edit($id)
    {
        $client = Client::findOrFail($id);
        return view('clients.edit', compact('client'));
    }

    public function update(ClientEditRequest $request, $id)
    {
        $client = Client::findOrFail($id);
        $client->name = $request->get('name');
        if($client->email != $request->get('email') && !(Client::where('email', $request->get('email'))->first()))
            $client->email = $request->get('email');
        if($request->get('password', false))
            $client->password = bcrypt($request->input('password'));
        $client->setHeadlineDimension($request->get('headline_width'), $request->get('headline_height'));
        $client->setThumbDimension($request->get('thumb_width'), $request->get('thumb_height'));
        $client->save();

        return redirect()->back();
    }

    public function delete($id)
    {
        $client = Client::findOrFail($id);
        $client->delete();

        return redirect()->back();
    }
}