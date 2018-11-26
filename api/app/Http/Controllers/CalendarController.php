<?php
namespace App\Http\Controllers;

use App\Models\DummyMatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Services\MainServices;
use App\Services\GameServices;
use Illuminate\Support\Facades\Input;

class CalendarController extends Controller
{

    private $_doCache = true;
    private $_cacheTime;

    public function __construct()
    {
        $this->_cacheTime = 60 * 24 * 10; // 10 days
    }

    public function index(Request $request, $game="dota2")
    {
        $matches = DummyMatch::with(['opponent1_details.country', 'opponent2_details.country', 'toutou_match', 'game'])
            ->whereNotNull('start')
            ->where('start', '>', DB::raw("DATE_FORMAT(NOW(), '%Y-%m-%d 00:00:00')"))
            ->where('start', '<',
                DB::raw("(DATE_ADD(CURDATE(), INTERVAL " . (int)$request->get('interval', 9) . " DAY))"))
            ->orderBy('start', 'asc');

        if ($game != "all" && $game) {
            $gameId = GameServices::getGameId($game);
            $matches->where('dummy_matches.game_id', $gameId);
        }
        $matches = $matches->get();
        $data = [];
        /**
         * Generate calendar
         */
        foreach ($matches as $m) {
            $day = date('d', strtotime($m->start));
            if (!array_key_exists($day, $data)) {
                $timestamp = strtotime($m->start);
                $data[$day] = new \stdClass();
                $data[$day]->date = [
                    'full_date' => date('d.m.Y', $timestamp),
                    'day' => date('d', $timestamp),
                    'day_name' => date('l', $timestamp),
                    'month' => date('m', $timestamp),
                    'month_name' => date('F', $timestamp),
                ];
                $data[$day]->matches = [];
            }
            $m->id = MainServices::maskId($m->id);
            $m->slug = str_slug($m->opponent1_details->name . '-' . $m->opponent2_details->name);
            $data[$day]->matches[] = $m;
        }

        if (count($data)) {
            usort($data, function ($a, $b) {
                $adate = strtotime($a->date['full_date']);
                $bdate = strtotime($b->date['full_date']);

                if ($adate ==  $bdate)
                    return 0;

                return ($adate < $bdate) ? -1 : 1;
            });
        }

        return response()->json($data);
    }

}