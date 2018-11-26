<?php

namespace App\Http\Controllers;

use App\Models\Streams;
use Illuminate\Http\Request;

use App\Http\Requests;
use View;

class StreamsController extends Controller
{
    public function index() {
        $data['streams'] = Streams::all();
        return view('streams/list', $data);
    }

    public function getStreamByTitle(Request $request)
    {
        $streams = Streams::where('title', 'LIKE', '%'.$request->input('title').'%')->get();

        if (count($streams)) {
            $return = array(
                    "status" => "success",
                    "streams" => $streams
                );
        } else {
            $return = array(
                    "status" => "error",
                    "message" => "No teams found"
                );
        }

        return response()->json($return);
    }
}
