<?php
namespace App\Http\Controllers;

use App\Champion;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Services\RiotApiServices;

class ChampionsController extends Controller
{
    public function index(Request $request)
    {
        $data['champions'] = Champion::all();
        return view('champions.list', $data);
    }

    public function form(Request $request)
    {
        $data['champion'] = $request->has('id') ? Champion::find($request->get('id')) : new Champion();

        return view('champions.form', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:25'
        ]);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $champion = $request->has('id') ? Champion::find($request->get('id')) : new Champion();
        if ($request->has('remove_image')) {
            $request->request->set('image', null);
        }
        if ($request->hasFile('file')) {
            if (request()->file('file')->move(public_path('uploads'),
                request()->file('file')->getClientOriginalName())
            ) {
                $request->request->set('image', request()->file('file')->getClientOriginalName());
            }
        }
        if ($request->has('id')) {
            $champion->update($request->only($champion->getFillable()));
        } else {
            $champion = $champion->create($request->only($champion->getFillable()));
        }

        return redirect(groute('champions'));
    }

    public function delete(Request $request)
    {
        Champion::find($request->get('id'))->delete();
        return redirect(groute('champions'));
    }

    public function auto(Request $request)
    {
        $apiKey = getenv('RIOT_API_KEY');

        RiotApiServices::updateChampionsList($apiKey);

        return redirect(route('champions'));
    }
}