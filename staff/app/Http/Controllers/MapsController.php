<?php
namespace App\Http\Controllers;

use App\Maps;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class MapsController extends Controller
{
    public function index(Request $request)
    {
        $data['maps'] = Maps::all();
        return view('maps.list', $data);
    }

    public function form(Request $request)
    {
        $data['map'] = $request->has('id') ? Maps::find($request->get('id')) : new Maps();

        return view('maps.form', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'game_id' => 'numeric|required',
            'name' => 'required|max:255'
        ]);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $map = $request->has('id') ? Maps::find($request->get('id')) : new Maps();
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
            $map->update($request->only($map->getFillable()));
            $map->save();
        } else {
            $map = $map->create($request->only($map->getFillable()));
        }

        return redirect(groute('maps.form', ['id' => $map->id]));
    }

    public function delete(Request $request)
    {
        Maps::find($request->get('id'))->delete();
        return redirect(groute('maps'));
    }
}