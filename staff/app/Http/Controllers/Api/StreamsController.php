<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EventsStreams;
use App\Models\MatchesStreams;
use App\Models\Streams;
use \Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StreamsController extends Controller
{

    public function index(Request $request)
    {
        if ($request->has('id')) {
            return Streams::find($request->get('id'));
        }
        return response()->json(Streams::all());
    }

    public function store(Request $request)
    {
        $stream = ($request->has('id') ? Streams::find($request->get('id')) : new Streams());
        $stream->title = $request->get('title');
        $stream->link = $request->get('link');
        $stream->description = $request->get('description');
        $stream->lang = $request->get('lang');
        $stream->platform = $request->get('platform', 'other');
        $stream->game_id = $request->get('game_id');
        $stream->embed_code = $request->get('embed_code');
        $stream->save();
        return response()->json($stream);
    }

    public function delete(Request $request)
    {
        try {
            Streams::find($request->get('id'))->delete();
            EventsStreams::where('streams_id')->delete();
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ], 500);
        }
        return response()->json(["success" => true, "message" => 'ok'], 200);
    }

    public function addStreamToMatches(Request $request)
    {
        try {
            $stream_id = $request->get('stream');
            $matches = $request->get('matches');
            foreach ($matches as $match_id) {
                if (!MatchesStreams::where('matches_id', $match_id)->where('streams_id', $stream_id)->count()) {
                    MatchesStreams::create([
                        'matches_id' => $match_id,
                        'streams_id' => $stream_id
                    ]);
                }
            }
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }

        return response()->json('Stream added to matches', 200);

    }

}