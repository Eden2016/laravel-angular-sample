<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;

class LogsController extends Controller
{
    public function getIndex(Request $r)
    {
        $records = DB::table('mf_logs');
        $records->where('created_at', '>', DB::raw('DATE_SUB(NOW(), INTERVAL ' . $r->get('interval', 10) . ' day)'));
        $data['users'] = array_unique($records->pluck('user_id'));
        $data['models'] = array_unique($records->pluck('loggable_type'));
        $data['actions'] = array_unique($records->pluck('action'));
        if ($r->has('model')) {
            $records->where('loggable_type', 'like', addslashes($r->get('model')));
        }
        if ($r->has('action')) {
            $records->where('action', 'like', $r->get('action'));
        }
        if ($r->has('user')) {
            $records->where('user_id', $r->get('user'));
        }
        if ($r->has('for_id')) {
            $records->where('loggable_id', $r->get('for_id'));
        }
        if ($r->has('id')) {
            $records->where('id', $r->get('id'));
        }

        $records->orderBy('created_at', 'desc');

        if ($r->wantsJson()) {
            return response()->json($records->get());
        }

        $data['items'] = $records->paginate(20);
        return view('logs.list', $data);
    }
}
