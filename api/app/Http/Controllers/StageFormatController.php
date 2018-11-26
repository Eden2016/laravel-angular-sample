<?php
namespace App\Http\Controllers;

use App\Services\MainServices;
use App\Services\GameServices;
use App\Services\TeamServices;
use App\Models\StageFormat;
use App\Models\StageRound;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Support\Facades\Cache;

class StageFormatController extends Controller {
    private $_cacheTime;
    private $_doCache = true;

    public function __init()
    {
        $this->_cacheTime = 10; //10 minutes
    }

    public function index(Request $request)
    {
        abort(501, 'Not Implemented');
    }

    public function show(Request $request, $game, $sfId)
    {
        if ($this->_doCache)
            $sf = Cache::get(sprintf('sf.%d.show_preview', $sfId));
        else
            $sf = false;

        if (!$sf) {
            $sf = StageFormat::where('id', $sfId)
                ->with('rounds.dummyMatches.opponent1_details', 'rounds.dummyMatches.opponent2_details',
                    'rounds.dummyMatches.matchGames')
                ->first();

            if (null !== $sf) {
                if ($sf->type == StageFormat::TYPE_SWISS_FORMAT || $sf->type == StageFormat::TYPE_ROUND_ROBIN) {
                    $matches = [];
                    foreach ($sf->rounds as $round) {
                        foreach ($round->dummyMatches as $match) {
                            $matches[] = $match;
                        }
                    }
                    $matches = collect($matches);

                    $resulted = $matches->filter(function($m) {
                        return $m->winner != null || $m->is_tie == 1 || $m->is_forfeited == 1;
                    });

                    $teams = $matches->pluck('opponent1_details')->merge($matches->pluck('opponent2_details'))->unique();

                    $teamStats = [];
                    foreach ($teams as $k=>$team) {
                        $won = $resulted->reduce(function ($accumulator, $match) use ($team) {
                            if ($match->winner == $team->id)
                                return $accumulator + 1;
                            else
                                return $accumulator;
                        }, 0);
                        $lost = $resulted->reduce(function($accumulator, $match) use ($team) {
                            if (($match->opponent1==$team->id || $match->opponent2==$team->id) && $match->winner != $team->id)
                                return $accumulator + 1;
                            else
                                return $accumulator;
                        }, 0);
                        $ties = $resulted->reduce(function($accumulator, $match) use ($team) {
                            if (($match->opponent1==$team->id || $match->opponent2==$team->id) && $match->is_tie == 1)
                                return $accumulator + 1;
                            else
                                return $accumulator;
                        }, 0);

                        $teamStats[] = [
                            'id' => $team->id,
                            'name' => $team->name,
                            'tag' => $team->tag,
                            'slug' => $team->slug,
                            'points' => ($won * $sf->points_per_win) + ($ties * $sf->points_per_draw),
                            'won' => $won,
                            'tie' => $ties,
                            'lost' => $lost
                        ];
                    }

                    $retData = [
                        'status' => 'success',
                        'type' => 'group',
                        'team_data' => $teamStats
                    ];
                }
                else {
                    $teamList = [];
                    $roundRes = [];
                    $result = [];

                    foreach ($sf->rounds[0]->dummyMatches as $match) {
                        $teamsList[] = [$match->opponent1_details->name, $match->opponent2_details->name];
                    }

                    //Get upper bracket results
                    foreach ($sf->rounds as $round) {
                        if ($round->type == StageRound::ROUND_TYPE_UPPER_BRACKET) {
                            $roundRes[] = $this->_calculateMatchScore($round);
                        }
                    }

                    if (count($roundRes)) {
                        $result[0] = $roundRes;
                        $roundRes = [];
                    }

                    //Get lower bracket results
                    $i = 0;
                    foreach ($sf->rounds as $round) {
                        if ($round->type == StageRound::ROUND_TYPE_LOWER_BRACKET) {
                            $roundRes[$i] = $this->_calculateMatchScore($round);
                            $i++;
                        }
                    }

                    if (count($roundRes)) {
                        $result[1] = $roundRes;
                        $roundRes = [];
                    }

                    //Get finals
                    $i = 0;
                    foreach ($sf->rounds as $round) {
                        if ($round->type == StageRound::ROUND_TYPE_FINAL) {
                            $roundRes[$i] = $this->_calculateMatchScore($round);
                            $i++;
                        }
                    }

                    if (count($roundRes)) {
                        $result[2] = $roundRes;
                        $roundRes = [];
                    }
                    else {
                        $result[2] = [];
                    }

                    //Get Playoff
                    foreach ($sf->rounds as $round) {
                        if ($round->type == StageRound::ROUND_TYPE_THIRD_PLACE_PLAYOFF) {
                            $result[3] = $this->_calculateMatchScore($round)[0];
                        }
                    }

                    // bracket code
                    $retData = array(
                        "status" => "success",
                        "type" => "bracket",
                        "bracket_data" => [
                            'teams' => $teamsList,
                            'results' => $result
                        ]
                    );
                }

                if ($this->_doCache)
                    Cache::put(sprintf('sf.%d.show_preview', $sfId), $retData, $this->_cacheTime);
            }
            else {
                $retData = array(
                    "status" => "fail",
                    "message" => "No Stage Format found!"
                );
            }
        }
        else {
            $retData = $sf;
        }

        return response()->json($retData);
    }

    public function edit(Request $request, $game, $id)
    {
        abort(501, 'Not Implemented');
    }

    public function update(Request $request, $game, $id)
    {
        abort(501, 'Not Implemented');
    }

    public function destroy(Request $request, $game, $id)
    {
        abort(501, 'Not Implemented');
    }

    public function create(Request $request, $game)
    {
        abort(501, 'Not Implemented');
    }

    public function store(Request $request, $game)
    {
        abort(501, 'Not Implemented');
    }

    private function _calculateMatchScore($round)
    {
        $scores = [];
        foreach ($round->dummyMatches as $k=>$match) {
            $opponent1score = null;
            $opponent2score = null;
            if ($match->is_done) {
                if ($match->is_tie == 1) {
                    $opponent1score = 1;
                    $opponent2score = 1;
                } else if ($match->winner == $match->opponent1) {
                    $opponent1score = 1;
                    $opponent2score = 0;
                } else if ($match->winner == $match->opponent2) {
                    $opponent1score = 0;
                    $opponent2score = 1;
                }
            }

            $scores[$k] = [$opponent1score, $opponent2score];
        }

        return $scores;
    }
}