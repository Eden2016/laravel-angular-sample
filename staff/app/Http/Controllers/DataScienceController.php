<?php

namespace App\Http\Controllers;

use App\DummyMatch;
use App\Models\ApiClients;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use View;

class DataScienceController extends Controller
{
    public function prediction() {
        $data = [];
        $matches = DummyMatch::whereHas('prediction', function ($q) {
            $q->where('answered', 1);
        })
            ->with(['prediction', 'game', 'opponent1_details', 'opponent2_details'])
            ->get();
        $data['upcoming_matches'] = $matches->filter(function ($m) {
            return strtotime($m->start) > time();
        });
        $data['finished_matches'] = $matches->filter(function ($m) {
            return strtotime($m->start) < time(); // it has to be $m->is_done, but dummy data is crap
        });

        /**
         * generate months stats
         */
        $data['stats'] = [
            'dummy'  => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            'toutou' => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
        ];
        foreach ($matches as $m) {
            $data['stats']['dummy'][date('m', strtotime($m->start)) - 1]++;
            if ($m->toutou_match) {
                $data['stats']['toutou'][date('m', strtotime($m->start)) - 1]++;
            }
        }

        return view('data_science/prediction', $data);
    }

}