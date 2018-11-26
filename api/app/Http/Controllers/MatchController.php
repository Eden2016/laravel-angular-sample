<?php
namespace App\Http\Controllers;

use App\ToutouMatch;
use App\Models\DummyMatch;
use App\Models\OddStreams;
use App\Models\TeamAccount;

use App\Services\MainServices;
use App\Services\MatchServices;
use App\Services\TournamentServices;
use App\Services\GameServices;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Input;

class MatchController extends Controller
{

    private $_doCache = true;
    private $_cacheTime;

    public function __construct()
    {
        $this->_cacheTime = 60 * 24 * 10; // 10 days
    }

    public function index(Request $request, $game)
    {
        if ($game != "all" && $game)
            $gameId = GameServices::getGameId($game);
        else
            $gameId = false;

        $skip = (int)$request->get('skip', 0);
        $results = (int)$request->get('results', 100);
        $date = $request->get('date', false);

        if ($results > 100) {
            $results = 100;
        }

        $matches = \App\Models\DummyMatch::with('opponent1_details')
            ->with('opponent2_details');

        if ($gameId)
            $matches->where('game_id', $gameId);

        if ($results) {
            $matches->take($results);
        }

        if ($skip > 0) {
            $matches->skip($skip);
        }

        if ($date) {
            $matches->where('start', '>=', $date);
        }

        $sort = explode(',', $request->get('order', 'id,asc'));
        if (!$sort[1]) {
            $sort[1] = 'asc';
        }
        $matches->orderBy($sort[0], $sort[1]);

        $matches = $matches->get();
        $matches = $matches->map(function($match) {
            $match->id = MainServices::maskId($match->id);

            if (isset($match->opponent1_details))
                $match->opponent1_details->id = MainServices::maskId($match->opponent1_details->id);


            if (isset($match->opponent2_details))
                $match->opponent2_details->id = MainServices::maskId($match->opponent2_details->id);

            return $match;
        });

        if (count($matches) > 0) {
            $retData = array(
                "status" => "success",
                "result" => $matches
            );
        } else {
            $retData = array(
                "status" => "fail",
                "message" => "No matches found"
            );
        }

        return response()->json($retData);
    }

    public function show($game, $id, $client='default')
    {
        try {
            $id = MainServices::unmaskId($id);
            $retData = Cache::get(sprintf("match.data.%s.%d", $game, $id));
            if (!$retData) {
                $match = MatchServices::getMatchInfo($id);

                if ($match) {
                    $opponent1 = MainServices::unmaskId($match[0]->opponent1id);
                    $opponent2 = MainServices::unmaskId($match[0]->opponent2id);

                    $retData = array(
                        "status" => "success",
                        "match" => $match[0],
                        "past_matchups" => MatchServices::getLastMatchups($id, $opponent1, $opponent2, $game),
                        "opponent1_past_matches" => MatchServices::getLastMatches($opponent1, $game),
                        "opponent2_past_matches" => MatchServices::getLastMatches($opponent2, $game),
                        "opponent1_performance" => MatchServices::getTeamPerformance($opponent1, 6),
                        "opponent2_performance" => MatchServices::getTeamPerformance($opponent2, 6),
                        "streams" => MatchServices::getMatchStreams($id),
                        "opponent1_members" => MatchServices::getLineups($opponent1, $match[0]->opponent1_members),
                        "opponent2_members" => MatchServices::getLineups($opponent2, $match[0]->opponent2_members),
                        "opponent1" => TeamAccount::with('country')->where('id', $opponent1)->first(),
                        "opponent2" => TeamAccount::with('country')->where('id', $opponent2)->first()
                    );

                    if ($client === '188bet') {
                        $retData['group'] = TournamentServices::getStageInfo($match[0]->tournament_id);

                        if ($match[0]->type !== \App\Models\StageFormat::TYPE_ROUND_ROBIN && $match[0]->type !== \App\Models\StageFormat::TYPE_SWISS_FORMAT)
                        $retData['bracket'] = TournamentServices::getBracketData($match[0]->stage_format_id);
                    }

                    if ($game == "csgo") {
                        $retData['map_matchup_breakdown'] = MatchServices::matchupsMapBreakdown($opponent1, $opponent2);
                        $retData['opponent1_map_breakdown'] = MatchServices::teamMapBreakdown($opponent1);
                        $retData['opponent2_map_breakdown'] = MatchServices::teamMapBreakdown($opponent2);
                    }

                    if ($game == 'dota2') {
                        $retData['match_games'] = MatchServices::getDota2MatchGames($id);
                    }
                    else if ($game == 'lol') {
                        $retData['match_games'] = MatchServices::getMatchGameLineups($id, $game);
                    }
                    else {
                        $retData['match_games'] = MatchServices::getMatchGames($id, $game);
                    }

                    //Mask Tournemnt ID
                    $retData['match']->tournament_id = MainServices::maskId($retData['match']->tournament_id);

                    Cache::put(
                            sprintf("match.data.%s.%d", $game, $id),
                            $retData,
                            60
                        );
                }
                else {
                    $retData = array(
                        "status" => "fail",
                        "message" => "No match found"
                    );
                }
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $retData = array(
                "status" => "fail",
                "message" => "No match found"
            );
        }

        return response()->json($retData);
    }

    public function edit($game, $id)
    {
        abort(501, 'Not Implemented');
    }

    public function update($game, $id)
    {
        abort(501, 'Not Implemented');
    }

    public function destroy($game, $id)
    {
        abort(501, 'Not Implemented');
    }

    public function create($game)
    {
        abort(501, 'Not Implemented');
    }

    public function store($game)
    {
        abort(501, 'Not Implemented');
    }

    /**
     * Provide raw info of event if it hasn't been mapped with a dummy match
     * @param  int $eventId
     * @param  string $client
     * @return \Illuminate\Http\Response
     */
    public function getRawEvent(Request $request, $eventId, $client="toutou")
    {
        //TODO
        //Need to differentiate between clients, e.g. toutou = 1, 188bet = 2, etc.
        $clientId = 1;
        if ($client == 1) {
            $clientId = 1;
        }

        $type = $request->input('type', 1);

        $match = ToutouMatch::where('id', $eventId)
                            ->with('homeTeam')
                            ->with('awayTeam')
                            ->first();
        $streams = OddStreams::where('event_id', $eventId)->where('client_id', $clientId)->with('stream')->get();

        if (count($streams)) {
            foreach ($streams as $stream) {
                $stream->stream->embed = $stream->stream->code;
            }
        }

        if ($match) {
            $match = $this->_removeOdds($match, $type);
        }

        $retData = [
            "event" => $match,
            "streams" => $streams
        ];

        return response()->json($retData);
    }

    /**
     * Returns all active toutou matches
     *
     * @return Illuminate\Http\Response
     */
    public function getTouTouMatches(Request $request, $game)
    {
        $type = 1;
        if (Input::has('type')) {
            $type = (int)Input::get('type');
        }

        if ($game == "all") {
            $excludedGames = $request->input('exclude') ? explode(',', $request->input('exclude')) : [];
            $exclude = $request->input('exclude') ? 'yes' : 'no';
        }
        else {
            $excludedGames = [];
            $exclude = 'no';
        }

        $sort = explode(',',$request->input('order', 'id,asc'));
        if(!$sort[1]) $sort[1] = 'asc';

        $events = Cache::get(sprintf('events_odds_%s_%d_%s', $game, $type, $exclude));

        if (!$events) {
            $inplayEvents = \App\ToutouMatch::where('in_play', 1)->where('active',
                1)
                ->with('homeTeam')
                ->with('awayTeam')
                ->with('streams')
                ->with('dummyMatch.opponent1_details.country')
                ->with('dummyMatch.opponent2_details.country')
                ->with('dummyMatch.game')
                ->with('dummyMatch.streams')
                ->with(array('dummyMatch.stageRound.stageFormat.stage.tournament' => function($query) {
                            $query->select('id', 'name');
                        }))
                ->orderBy('event_date', 'ASC')
                ->take($request->input('results', 50))
                ->skip($request->input('skip', 0))
                ->orderBy($sort[0], $sort[1])
                ->get();

            $furtherEvents = \App\ToutouMatch::where('in_play', 0)->where('active',
                1)
                ->with('homeTeam')
                ->with('awayTeam')
                ->with('streams')
                ->with('dummyMatch.opponent1_details.country')
                ->with('dummyMatch.opponent2_details.country')
                ->with('dummyMatch.game')
                ->with('dummyMatch.streams')
                ->with(array('dummyMatch.stageRound.stageFormat.stage.tournament' => function($query) {
                            $query->select('id', 'name');
                        }))
                ->orderBy('event_date', 'ASC')
                ->take($request->input('results', 50))
                ->skip($request->input('skip', 0))
                ->orderBy($sort[0], $sort[1])
                ->get();

            if ($game != "all" && $game) {
                $gameId = GameServices::getGameId($game);

                if (count($inplayEvents)) {
                    foreach ($inplayEvents as $k=>$event) {
                        if (isset($event->dummyMatch)) {
                            $event->dummyMatch->opponent1_details->streak = $event->dummyMatch->opponent1_details->streak;
                            $event->dummyMatch->opponent2_details->streak = $event->dummyMatch->opponent2_details->streak;

                            $event->dummyMatch->stageRound->stageFormat->stage->tournament->masked_id = MainServices::maskId($event->dummyMatch->stageRound->stageFormat->stage->tournament->id);

                            if (count($event->dummyMatch->streams)) {
                                foreach ($event->dummyMatch->streams as $stream) {
                                    $stream->embed = $stream->code;
                                }
                            }
                        }
                        else if (!isset($event->dummyMatch) && ($event->game_id == null || $event->game_id == 0)) {
                            $inplayEvents->pull($k);
                        }

                        if ((isset($event->dummyMatch) && $event->dummyMatch->game_id != $gameId) || $event->game_id != $gameId)
                            $inplayEvents->pull($k);
                    }
                }

                if (count($furtherEvents)) {
                    foreach ($furtherEvents as $k=>$event) {
                        if (isset($event->dummyMatch)) {
                            $event->dummyMatch->opponent1_details->streak = $event->dummyMatch->opponent1_details->streak;
                            $event->dummyMatch->opponent2_details->streak = $event->dummyMatch->opponent2_details->streak;

                            $event->dummyMatch->stageRound->stageFormat->stage->tournament->masked_id = MainServices::maskId($event->dummyMatch->stageRound->stageFormat->stage->tournament->id);

                            if (count($event->dummyMatch->streams)) {
                                foreach ($event->dummyMatch->streams as $stream) {
                                    $stream->embed = $stream->code;
                                }
                            }
                        }
                        else if (!isset($event->dummyMatch) && ($event->game_id == null || $event->game_id == 0)) {
                            $furtherEvents->pull($k);
                        }

                        if ((isset($event->dummyMatch) && $event->dummyMatch->game_id != $gameId) || $event->game_id != $gameId)
                            $furtherEvents->pull($k);
                    }
                }
            }
            else {
                $excludedGameIds = [];
                if (count($excludedGames)) {
                    foreach ($excludedGames as $game) {
                        $excludedGameIds[] = GameServices::getGameId($game);
                    }
                }

                if (count($inplayEvents)) {
                    foreach ($inplayEvents as $k=>$event) {
                        if (isset($event->dummyMatch)) {
                            $event->dummyMatch->opponent1_details->streak = $event->dummyMatch->opponent1_details->streak;
                            $event->dummyMatch->opponent2_details->streak = $event->dummyMatch->opponent2_details->streak;

                            $event->dummyMatch->stageRound->stageFormat->stage->tournament->masked_id = MainServices::maskId($event->dummyMatch->stageRound->stageFormat->stage->tournament->id);

                            if (count($event->dummyMatch->streams)) {
                                foreach ($event->dummyMatch->streams as $stream) {
                                    $stream->embed = $stream->code;
                                }
                            }
                        }

                        if (count($excludedGameIds) && (in_array($event->game_id, $excludedGameIds) || (isset($event->dummyMatch) && in_array($event->dummyMatch->game_id, $excludedGameIds))))
                            $inplayEvents->pull($k);
                    }
                }

                if (count($furtherEvents)) {
                    foreach ($furtherEvents as $k=>$event) {
                        if (isset($event->dummyMatch)) {
                            $event->dummyMatch->opponent1_details->streak = $event->dummyMatch->opponent1_details->streak;
                            $event->dummyMatch->opponent2_details->streak = $event->dummyMatch->opponent2_details->streak;

                            $event->dummyMatch->stageRound->stageFormat->stage->tournament->masked_id = MainServices::maskId($event->dummyMatch->stageRound->stageFormat->stage->tournament->id);

                            if (count($event->dummyMatch->streams)) {
                                foreach ($event->dummyMatch->streams as $stream) {
                                    $stream->embed = $stream->code;
                                }
                            }
                        }

                        if (count($excludedGameIds) && (in_array($event->game_id, $excludedGameIds) || (isset($event->dummyMatch) && in_array($event->dummyMatch->game_id, $excludedGameIds))))
                            $furtherEvents->pull($k);
                    }
                }
            }

            /*
                Mask match ids
             */
            foreach ($inplayEvents as $event) {
                //Remove unneeded odds
                $event = $this->_removeOdds($event, $type);

                if ($event->dummyMatch)
                    $event->dummyMatch->masked_id = MainServices::maskId($event->dummyMatch->id);
            }

            foreach ($furtherEvents as $event) {
                //Remove unneeded odds
                $event = $this->_removeOdds($event, $type);

                if ($event->dummyMatch)
                    $event->dummyMatch->masked_id = MainServices::maskId($event->dummyMatch->id);
            }

            $retData = array(
                "status" => "success",
                "inPlay" => $this->_groupEvents($inplayEvents),
                "further" => $this->_groupEvents($furtherEvents)
            );

            Cache::put(sprintf('events_odds_%s_%d_%s', $game, $type, $exclude), json_encode($retData), 1);
        } else {
            $retData = json_decode($events);
        }

        return response()->json($retData);
    }

    /**
     * @param  string $game
     * @param  int $id
     * @param  string $platform
     * @return Illuminate\Http\Response
     */
    public function fetchOdds($game, $id, $platform)
    {
        $id = MainServices::unmaskId($id);
        $odds = null;

        $type = Input::get('type', 1);

        /*
            TODO: Make the API differ from clients so it calls the right match (e.g. ToutouMatch, or 188Match)
         */
        if ($platform == "toutou") {
            $odds = \App\ToutouMatch::where('dummy_match', $id)->where('active', 1)->get();

            if (null != $odds) {
                foreach ($odds as $odd) {
                    $odd = $this->_removeOdds($odd, $type);
                }
            }
        }

        return response()->json($odds);
    }

    /**
     * Removes unneeded odds, so json feed is smaller
     * @param  \App\Models\ToutouMatch $event
     * @param  int $type
     * @return \App\Models\ToutouMatch
     */
    private function _removeOdds($event, $type)
    {
            switch ($type) {
                case \App\ToutouMatch::ODD_TYPE_HK:
                    $event->setOddsAttribute(json_encode($event->odds_hk));
                    $event->setNewOddsAttribute(json_encode($event->new_odds_hk));
                    break;
                case \App\ToutouMatch::ODD_TYPE_MALAY:
                    $event->setOddsAttribute(json_encode($event->odds_malay));
                    $event->setNewOddsAttribute(json_encode($event->new_odds_malay));
                    break;
                case \App\ToutouMatch::ODD_TYPE_INDO:
                    $event->setOddsAttribute(json_encode($event->odds_indo));
                    $event->setNewOddsAttribute(json_encode($event->new_odds_indo));
                    break;
            }

            if ($type != "all") {
                $event->setOddsHkAttribute(null);
                $event->setNewOddsHkAttribute(null);
                $event->setOddsIndoAttribute(null);
                $event->setNewOddsIndoAttribute(null);
                $event->setOddsMalayAttribute(null);
                $event->setNewOddsMalayAttribute(null);
            }

            if (count($event->streams)) {
                foreach ($event->streams as $stream) {
                    $stream->embed = $stream->code;
                }
            }

            return $event;
    }

    /**
     * Groups toutou matches via the dummy matches mapped to them
     * @param  array $events
     * @return array
     */
    private function _groupEvents($events)
    {
        if (count($events)) {
            $grouped = [];
            foreach ($events as $event) {
                if (isset($event->dummyMatch)) {
                    $dummyMatch = $event->dummyMatch;
                    $event->setDummyMatch(null);

                    if (!isset($grouped["dm_" . $dummyMatch->id])) {
                        $grouped["dm_" . $dummyMatch->id] = [
                            "dummy_match" => $dummyMatch,
                            "events" => [
                                $event
                            ]
                        ];
                    }
                    else {
                        $grouped["dm_" . $dummyMatch->id]['events'][] = $event;
                    }
                }
                else {
                    $grouped["event_" . $event->id] = [
                        "dummy_match" => null,
                        "events" => [
                            $event
                        ]
                    ];
                }
            }

            //Make array keys incrementing numbers starting from 0, so javascript doesn't shit it's pants
            $grouped = array_values($grouped);

            //Order grouped events so that whole match is on top, followed by game 1, game 2, etc.
            for ($i = 0;$i < count($grouped); $i++) {
                usort($grouped[$i]['events'], function($a, $b) {
                    return $a->game_number > $b->game_number;
                });
            }

            //In case the garbage collector fucks up
            unset($dummyMatch);
            //It's not given by reference, so we should clear it
            unset($events);

            return $grouped;
        }
        else {
            return [];
        }
    }

    /**
     * Get live stats for an ongoing DotA 2 Match
     * @param  int $matchId
     * @return array|bool
     */
    private function _fetchDotaMatch($matchId)
    {
        $match = \App\Models\Match::find($matchId);

        $retData = array();
        if ($match) {
            $retData['match']['id'] = $match->match_id;
            $retData['match']['radiant_win'] = $match->radiant_win;
            $retData['match']['duration'] = $match->duration;
            $retData['match']['start'] = $match->start_time;
            $retData['match']['radiant_team_id'] = $match->radiant_team_id;
            $retData['match']['radiant'] = $match->radiant_name;
            $retData['match']['dire_team_id'] = $match->dire_team_id;
            $retData['match']['dire_name'] = $match->dire_name;


            $league = \App\Models\League::find($match->leagueid);
            if ($league) {
                $retData['match']['league_id'] = $match->leagueid;
                $retdata['match']['league_name'] = $league->name;
            }

            $slots = $match->slots;
            if (count($slots) > 0) {
                foreach ($slots as $k => $slot) {
                    $player = $slot->player;

                    if (in_array($slot->player_slot, \App\Models\Slot::RADIANT_ARRAY)) {
                        $side = 'radiant';
                    } else {
                        $side = 'dire';
                    }

                    $retData['match'][$side][$slot->id]['name'] = $player->name;
                    $retData['match'][$side][$slot->id]['hero_id'] = $slot->hero_id;
                    $retData['match'][$side][$slot->id]['hero_name'] = $slot->hero_name;
                    $retData['match'][$side][$slot->id]['level'] = $slot->level;
                    $retData['match'][$side][$slot->id]['kills'] = $slot->kills;
                    $retData['match'][$side][$slot->id]['deaths'] = $slot->deaths;
                    $retData['match'][$side][$slot->id]['assists'] = $slot->assists;
                    $retData['match'][$side][$slot->id]['gold'] = $slot->gold;
                    $retData['match'][$side][$slot->id]['last_hits'] = $slot->last_hits;
                    $retData['match'][$side][$slot->id]['denies'] = $slot->denies;
                    $retData['match'][$side][$slot->id]['gold_per_min'] = $slot->gold_per_min;
                    $retData['match'][$side][$slot->id]['xp_per_min'] = $slot->xp_per_min;
                    $retData['match'][$side][$slot->id]['gold_spent'] = $slot->gold_spent;
                    $retData['match'][$side][$slot->id]['hero_damage'] = $slot->hero_damage;
                    $retData['match'][$side][$slot->id]['tower_damage'] = $slot->tower_damage;
                    $retData['match'][$side][$slot->id]['hero_healing'] = $slot->hero_healing;
                    $retData['match'][$side][$slot->id]['item_0'] = $slot->item_0;
                    $retData['match'][$side][$slot->id]['item_1'] = $slot->item_1;
                    $retData['match'][$side][$slot->id]['item_2'] = $slot->item_2;
                    $retData['match'][$side][$slot->id]['item_3'] = $slot->item_3;
                    $retData['match'][$side][$slot->id]['item_4'] = $slot->item_4;
                    $retData['match'][$side][$slot->id]['item_5'] = $slot->item_5;
                }
            }
        } else {
            $retData = false;
        }

        return $retData;
    }
}