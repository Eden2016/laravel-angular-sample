<?php
namespace App\Http\Controllers;

use App\Scopes\GameSelectorScope;

use App\StageFormat;
use App\StageRound;
use App\TeamsOrder;
use App\Stage;
use App\OpponentPrefill;

use App\Factories\StageFormats\Formats\Gsl;
use App\Factories\StageFormats\Formats\SwissFormat;
use App\Factories\StageFormats\Formats\DoubleElimination;
use App\Factories\StageFormats\Formats\SingleElimination;
use App\Factories\StageFormats\Formats\RoundRobin;
use App\Factories\StageFormats\Formats\PrefilledRoundRobin;

use Illuminate\Support\Collection;
use PDO;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use View;
use Validator;
use Input;
use Sunra\PhpSimple\HtmlDomParser;
use Illuminate\Support\Facades\Auth;

class StageFormatController extends Controller
{
    public function show($tournamentId, $stageId, $sfId)
    {
        $data['stage'] = \App\Stage::withoutGlobalScope(GameSelectorScope::class)->find($stageId);
        if (null === $data['stage']) {
            $data['showError'] = 'No stages found with the specified key!';
        }

        if (!isset($data['showError'])) {
            $data['sf'] = \App\StageFormat::withoutGlobalScope(GameSelectorScope::class)->where('id',
                $sfId)->where('hidden', 0)->first();
            if (null === $data['sf']) {
                $data['showError'] = 'No stage format found with the specified key!';
            }
        }
        if (!isset($data['showError'])) {
            $data['rounds'] = \App\StageRound::withoutGlobalScope(GameSelectorScope::class)->where('stage_format_id',
                '=', $data['sf']->id)->get();
            if (null === $data['rounds']) {
                $data['showError'] = 'No rounds found with the specified key!';
            }
        }

        if (!isset($data['showError'])) {
            $roundIds = array();
            foreach ($data['rounds'] as $k => $round) {
                $data['roundsInfo'][$round->type][$k]['round'] = $round;
            }
        }

        if (!isset($data['showError'])) {
            $data['tournament'] = \App\Tournament::withoutGlobalScope(GameSelectorScope::class)->find($data['stage']->tournament_id);

            $data['breadcrumbs'][] = array(
                'name' => 'Events',
                'url' => groute('events'),
                'active' => false
            );
            $data['breadcrumbs'][] = array(
                'name' => 'Event',
                'url' => groute('event.view', 'current', ['eventId' => $data['tournament']->event_id]),
                'active' => false
            );
            $data['breadcrumbs'][] = array(
                'name' => 'Tournament',
                'url' => groute('tournament.view', 'current',['tournamentId' => $tournamentId]),
                'active' => false
            );
            $data['breadcrumbs'][] = array(
                'name' => 'Stage',
                'url' => groute('stage','current', ['tournamentId' => $tournamentId, 'stageId' => $stageId]),
                'active' => false
            );
            $data['breadcrumbs'][] = array(
                'name' => 'Stage Format',
                'url' => groute('stage','current', ['tournamentId' => $tournamentId, 'stageId' => $stageId]),
                'active' => true
            );
        } else {
            $data['breadcrumbs'][] = array(
                'name' => 'Events',
                'url' => route('events'),
                'active' => false
            );
        }

        if (Input::has('schedule_date')) {
            $this->_setSchedule();
        }

        $matches = \App\StageFormat::withoutGlobalScope(GameSelectorScope::class)->where('id', $sfId)
            ->with('rounds.dummyMatches.opponent1_details', 'rounds.dummyMatches.opponent2_details',
                'rounds.dummyMatches.matchGames')
            ->get()
            ->pluck('rounds')->flatten()
            ->pluck('dummyMatches')->flatten()->sortBy('start');

        $data['tournamentsActiveMenu'] = true;
        $data['resulted'] = new Collection();
        $data['scheduled'] = new Collection();
        $data['not_resulted'] = new Collection();
        $data['matches'] = $matches;
        foreach ($matches as $m) {
            if ($m->winner !== null || $m->is_tie || $m->is_forfeited) {
                $data['resulted']->push($m);
                continue;
            }
            if ($m->start !== null) {
                $data['scheduled']->push($m);
                continue;
            }
            $data['not_resulted']->push($m);
        }
        $data['teams'] = $matches->pluck('opponent1_details')->merge($matches->pluck('opponent2_details'))->unique();

        $data['teams']->map(function ($team, $key) use ($data) {
            $team->resulted_matches = $data['resulted']->where('opponent1',
                $team->id)->merge($data['resulted']->where('opponent2', $team->id));
            $team->total_matches = $team->resulted_matches->count();
            $team->wins = $team->resulted_matches->where('winner', $team->id)->count();
            if ($team->total_matches) {
                $team->win_procentage = round(($team->wins / $team->total_matches) * 100, 2);
            }
            $team->loses = $team->resulted_matches->filter(function ($m) use ($team) {
                return $m->winner != $team->id && !$m->is_tie;
            })->count();
            $team->draws = $team->resulted_matches->where('is_tie', 1)->count();
            $team->country_name = (isset($team->country->countryName) ? ucwords(str_slug($team->country->countryName)) : 'Russia');
            $team->points = (int)($team->wins * $data['sf']->points_per_win) + ($team->draws * $data['sf']->points_per_draw);

            return $team;
        });
        $data['teams'] = $data['teams']->sortByDesc('points');
        $teams_ids = collect($data['teams']->pluck('id'));
        $data['teams']->map(function ($team, $key) use ($data, $teams_ids) {
            try {
                $team->pos = TeamsOrder::where('team_accounts_id', $team->id)->where('stage_formats_id',
                    $data['sf']->id)->first()->pos;
            } catch (\Exception $e) {
                $team->pos = null;
            }
            $team->sorting_value = (int)!is_null($team->pos) ? $team->pos : $teams_ids->search($team->id);
        });
        $data['teams'] = $data['teams']->sortBy('sorting_value');

        $data['json_teams'] = [];
        $teams_ids = $data['teams']->pluck('id')->toArray();
        for ($i = 0, $max = count($teams_ids); $i < $max; $i++) {
            $data['json_teams'][$i]['pos'] = $i;
            $data['json_teams'][$i]['id'] = $teams_ids[$i];
            $data['teams']->where('id', $teams_ids[$i])->pos = $i;
        }

        $data['hasChange'] = \App\Models\ChangedTeam::where('stage_format_id', $sfId)->get();
        if (count($data['hasChange'])) {
            $data['hasChange']->map(function ($changes) use ($data) {
                $changes->original_team_name = \App\TeamAccount::find($changes->original_team_id)->name;
                $changes->substitute_team_name = \App\TeamAccount::find($changes->substitute_team_id)->name;

                return $changes;
            });
        }

        $data['tournamentsActiveMenu'] = true;

        return view('stage_format/show', $data);
    }

    private function _setSchedule()
    {
        $date = date_convert(Input::get('schedule_date'), Auth::user()->timezone, 'UTC', 'Y-m-d H:i', 'Y-m-d H:i:s');

        if (Input::has('stage_round_id')) {
            $srId = Input::get('stage_round_id');

            $round = \App\StageRound::find($srId);
            if (null !== $round) {
                $matches = $round->dummyMatches;
                if (count($matches) > 0) {
                    foreach ($matches as $match) {
                        if ($match->start == null) {
                            $match->start = $date;
                            $match->save();
                        }
                    }
                }
            }
        } else {
            $sfId = \Request::segment(6);

            $sf = \App\StageFormat::find($sfId);
            if (null !== $sf) {
                $rounds = $sf->rounds;

                if (count($rounds) > 0) {
                    foreach ($rounds as $round) {
                        $matches = $round->dummyMatches;
                        if (count($matches) > 0) {
                            foreach ($matches as $match) {
                                if ($match->start == null) {
                                    $match->start = $date;
                                    $match->save();
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function showGroup($tournamentId, $stageId, $sfId)
    {
        $data = [];
        $matches = \App\StageFormat::where('id', $sfId)
            ->with('rounds.dummyMatches.opponent1_details', 'rounds.dummyMatches.opponent2_details',
                'rounds.dummyMatches.matchGames')
            ->get()
            ->pluck('rounds')->flatten()
            ->pluck('dummyMatches')->flatten()->sortByDesc('start');

        $data['tournamentsActiveMenu'] = true;
        $data['resulted'] = $matches->filter(function ($m) {
            return $m->winner != null;
        });
        $data['scheduled'] = $matches->filter(function ($m) {
            return strtotime($m->start) > time() || $m->start == null;
        });
        $data['not_resulted'] = $matches->filter(function ($m) {
            return strtotime($m->start) < time() && $m->winner == null && $m->start != null;
        });
        $data['teams'] = $matches->pluck('opponent1_details')->merge($matches->pluck('opponent2_details'))->unique();

        return view('stage_format/group', $data);
    }

    public function showBracket($tournamentId, $stageId, $sfId)
    {
        $data['tournamentsActiveMenu'] = true;

        return view('stage_format/bracket');
    }

    public function create($tournamentId, $stageId)
    {
        $data['stage'] = \App\Stage::find($stageId);

        if (null === $data['stage']) {
            return redirect(groute('events'));
        }

        $data['types'] = \App\StageFormat::getTypesListed($data['stage']->format);

        $data['tournamentsActiveMenu'] = true;
        return view('stage_format/create', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'format' => 'required',
            'name' => 'required|max:50',
            'start' => 'required|date',
            'stage' => 'required|exists:stages,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $stageId = intval(Input::get('stage'));
        $stage = \App\Stage::find($stageId);

        if (Input::has('id')) {
            $sfId = intval(Input::get('id'));
            $sf = \App\StageFormat::find($sfId);
        } else {
            $sf = new \App\StageFormat();
        }

        $sf->stage_id = $stageId;
        $sf->name = $request->input('name');
        $sf->type = (int)$request->input('format');
        $sf->start = date_convert($request->input('start'), Auth::user()->timezone, 'UTC', 'Y-m-d H:i', 'Y-m-d H:i:s');
        $sf->end = $request->input('end');
        $sf->participants = $request->input('participants');
        $sf->qualifing = $request->input('qualifingParticipants');
        $sf->hidden = $request->input('hidden', 0);
        $sf->active = $request->input('active', 0);
        $sf->status = \App\Stage::STATUS_UPCOMING;
        $sf->lead_from_winner_bracket = $request->input('lead_from_winner_bracket', 0);

        $sf->points_distribution = $request->input('points_distribution', 'per_match');
        $sf->points_per_win = $request->input('points_per_win', 0);
        $sf->points_per_draw = $request->input('points_per_draw', 0);

        $sf->save();

        if (Input::has('id')) {
            return redirect(groute('stages.formats.view', [
                'tournamentId' => $stage->tournament_id,
                'stageId' => $stage->id,
                'sfId' => $sf->id
            ]));
        }

        //Create bracket and matches
        switch ($sf->type) {
            case \App\StageFormat::TYPE_SINGLE_ELIM:
                $stageFormat = new SingleElimination();
                break;

            case \App\StageFormat::TYPE_DOUBLE_ELIM:
                $stageFormat = new DoubleElimination();
                break;

            case \App\StageFormat::TYPE_ROUND_ROBIN:
                $stageFormat = new RoundRobin();
                break;

            case \App\StageFormat::TYPE_SWISS_FORMAT:
                $stageFormat = new SwissFormat();
                break;

            case \App\StageFormat::TYPE_GSL_FORMAT:
                $stageFormat = new Gsl();
                break;
        }

        //Set general properties
        $stageFormat->qualifyingParticipants = $request->input('qualifingParticipants');
        $stageFormat->opponentCount = (int)$request->input('participants');
        $stageFormat->groupsNum = $request->input('groupsNum');

        //GSL Specific
        $stageFormat->disablePlayoffs = $request->input('disable_playoffs', false);
        $stageFormat->eliminationPlayoffs = $request->input('elimination_playoffs', 'single');

        //Single Elimination Specific
        $stageFormat->isInvitational = $request->input('invitational', false);
        $stageFormat->hasThirdPlace = $request->input('third_place', false);

        //Double Elimination Specific
        $stageFormat->upperBracketTeams = $request->input('participantsUpperBracket', 4);
        $stageFormat->lowerBracketTeams = $request->input('participantsLowerBracket', 2);

        //RoundRobin Specific
        $stageFormat->doubleRounds = $request->input('double_rounds', false);

        //Create stage format
        $stageFormat->create($sf);

        return redirect(groute('stage', [
            'tournamentId' => $stage->tournament_id,
            'stageId' => $stage->id
        ]));
    }

    public function addAjax(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'format' => 'required',
            'name' => 'required|max:50',
            'start' => 'required|date',
            'stage' => 'required|exists:stages,id'
        ]);

        if ($validator->fails()) {
            return response()->json(array(
                "status" => "error",
                "message" => $validator->errors()
            ));
        }

        $stageId = (int)$request->input('stage');

        $sf                 = new StageFormat();
        $sf->stage_id       = $stageId;
        $sf->name           = $request->input('name');
        $sf->type           = (int)$request->input('format');
        $sf->start          = date_convert($request->input('start'), Auth::user()->timezone, 'UTC', 'Y-m-d H:i', 'Y-m-d H:i:s');
        $sf->end            = $request->input('end', date('Y-m-d H:i:s', strtotime('+1 week', strtotime($request->input('start')))));
        $sf->participants   = $request->input('participants');
        $sf->qualifing      = $request->input('qualifingParticipants');
        $sf->hidden         = $request->input('hidden', 0);
        $sf->active         = $request->input('active', 0);
        $sf->status         = Stage::STATUS_UPCOMING;

        if (!$request->input('disable_playoffs')) {
            $sf->elimination_playoffs = $request->input('elimination_playoffs', 0);
        }

        $sf->save();

        //Create bracket and matches
        switch ($sf->type) {
            case \App\StageFormat::TYPE_SINGLE_ELIM:
                $stageFormat = new SingleElimination();
                break;

            case \App\StageFormat::TYPE_DOUBLE_ELIM:
                $stageFormat = new DoubleElimination();
                break;

            case \App\StageFormat::TYPE_ROUND_ROBIN:
                $stageFormat = new RoundRobin();
                break;

            case \App\StageFormat::TYPE_SWISS_FORMAT:
                $stageFormat = new SwissFormat();
                break;

            case \App\StageFormat::TYPE_GSL_FORMAT:
                $stageFormat = new Gsl();
                break;
        }

        //Set general properties
        $stageFormat->qualifyingParticipants = $request->input('qualifingParticipants', 1);
        $stageFormat->opponentCount = (int)$request->input('participants', 4);
        $stageFormat->groupsNum = $request->input('groupsNum', 0);

        //GSL Specific
        $stageFormat->disablePlayoffs = $request->input('disable_playoffs', false);
        $stageFormat->eliminationPlayoffs = $request->input('elimination_playoffs', 'single');

        //Single Elimination Specific
        $stageFormat->isInvitational = $request->input('invitational', false);
        $stageFormat->hasThirdPlace = $request->input('third_place', false);

        //Double Elimination Specific
        $stageFormat->upperBracketTeams = $request->input('participantsUpperBracket', 4);
        $stageFormat->lowerBracketTeams = $request->input('participantsLowerBracket', 2);

        //RoundRobin Specific
        $stageFormat->doubleRounds = $request->input('double_rounds', false);

        if ($sf->type != \App\StageFormat::TYPE_GSL_FORMAT) {
            if ($sf->type != \App\StageFormat::TYPE_ROUND_ROBIN) {
                //Create stage format
                $stageFormat->create($sf);
            }

            $retData = array(
                            "status" => "success",
                            "id" => $sf->id,
                            "participants" => ($sf->participants * $request->input('groupsNum', 1)),
                            "format" => $sf->type
                        );
        }
        else {
            //Create stage format
            $gsl = $stageFormat->create($sf);

            $retData = array(
                        "status" => "success",
                        "id" => $sf->id,
                        "format" => $sf->type,
                        "participants" => ($sf->participants * $request->input('groupsNum', 0)),
                        "stage_formats" => $gsl
                    );
        }

        return response()->json($retData);
    }

    public function edit($tournamentId, $stageId, $sfId)
    {
        $data['stage'] = \App\Stage::find($stageId);

        if (null === $data['stage']) {
            return redirect(groute('events'));
        }

        $data['types'] = \App\StageFormat::getTypesListed($data['stage']->format);

        $data['sf'] = \App\StageFormat::find($sfId);

        $data['tournamentsActiveMenu'] = true;
        return view('stage_format/edit', $data);
    }

    public function remove($tournamentId, $stageId, $sfId)
    {
        $stageFormat = \App\StageFormat::find($sfId);

        if (null !== $stageFormat) {
            $stageFormat->hidden = 1;
            $stageFormat->save();

            return redirect(groute('tournament.view', ['tournamentId' => $tournamentId]));
        }
        else {
            return redirect(groute('stage', ['tournamentId' => $tournamentId, 'stageId' => $stageId]));
        }
    }

    public function generatePreFilledRoundRobin(Request $request)
    {
        $sfId = (int)$request->input('id');
        $sf = \App\StageFormat::find($sfId);

        if (null !== $sf) {
            $stageFormat = new PrefilledRoundRobin();

            $stageFormat->opponents = $request->input('data');
            $stageFormat->groupsNum = $request->input('groupsNum', 1);
            $stageFormat->doubleRounds = $request->input('double_rounds', false);
            $stageFormat->create($sf);

            $stage = $sf->stage;
            $retData = array(
                "status" => "success",
                "location" => groute('stages.formats.view',
                    ['tournamentId' => $stage->tournament_id, 'stageId' => $stage->id, 'sfId' => $sf->id])
            );
        } else {
            $retData = array(
                "status" => "error",
                "message" => "No Stage Format found with this ID."
            );
        }

        return response()->json($retData);
    }

    public function addOpponents(Request $request)
    {
        $participants = $request->input('data');
        $errors = false;

        if (!$request->input("gslIds")) {
            $sfId = (int)$request->input('id');
            $stage = $this->_addOpponents($sfId, $participants);
        }
        else {
            $sfIds = explode(",", $request->input("gslIds", ""));

            $stage = null;
            foreach ($sfIds as $k => $sfId) {
                $opponents = array_slice($participants, $k * 4, 4);
                $stage = $this->_addOpponents($sfId, $opponents);

                if (!$stage) {
                    $errors = true;
                }
            }
        }

        if (!$errors) {
            $retData = array(
                "status" => "success",
                "location" => route('stage', ['tournamentId' => $stage->tournament_id, 'stage' => $stage->id])
            );
        } else {
            $retData = array(
                "status" => "error",
                "message" => "An error occured while trying to add participants to a stage format."
            );
        }

        return response()->json($retData);
    }

    public function addMatch()
    {
        $roundId = intval(Input::get('roundId'));

        $round = \App\StageRound::find($roundId);
        $stageFormat = $round->stageFormat;
        $stage = $stageFormat->stage;
        $tournament = $stage->tournament;

        $dummyMatch = new \App\DummyMatch();

        $dummyMatch->opponent1 = 34;
        $dummyMatch->opponent2 = 35;
        $dummyMatch->round_id = $roundId;
        $dummyMatch->game_id = $tournament->game_id;
        $dummyMatch->status = \App\DummyMatch::STATUS_UPCOMING;

        $dummyMatch->save();

        return json_encode(array(
            "status" => "success"
        ));
    }

    public function getBracketData($sfId)
    {
        $sf = \App\StageFormat::where('id', $sfId)
            ->with('rounds.dummyMatches.opponent1_details', 'rounds.dummyMatches.opponent2_details',
                'rounds.dummyMatches.matchGames')
            ->first();

        if (null !== $sf) {
            $bracket = [
                //Upper Bracket
                [],
                //Lower Bracket
                [],
                //Final Bracket
                [],
                //Decider
                []
            ];

            foreach ($sf->rounds as $round) {
                switch ($round->type) {
                    case \App\StageRound::ROUND_TYPE_UPPER_BRACKET:
                        //push match array into upper bracket array
                        array_push($bracket[0], $this->_populateBracketMatches($round));
                        break;
                    case \App\StageRound::ROUND_TYPE_LOWER_BRACKET:
                        //push match array into lower bracket array
                        array_push($bracket[1], $this->_populateBracketMatches($round));
                        break;
                    case \App\StageRound::ROUND_TYPE_FINAL:
                        //push match array into final bracket array
                        array_push($bracket[2], $this->_populateBracketMatches($round));
                        break;
                    case \App\StageRound::ROUND_TYPE_THIRD_PLACE_PLAYOFF:
                        //push match array into decider bracket array
                        array_push($bracket[3], $this->_populateBracketMatches($round));
                        break;
                }
            }

            $retData = array(
                "status" => "success",
                "bracket" => $bracket
            );
        } else {
            $retData = array(
                "status" => "fail",
                "message" => "No Stage Format found!"
            );
        }

        return response()->json($retData);
    }

    public function addRound(Request $request)
    {
        $stage_format = $request->get('stage_format');
        $type = $request->get('type');
        $format = StageFormat::find($stage_format);
        if (!$format) {
            throw new \Exception('Format not found');
        }
        $round = new StageRound();
        $round->stage_format_id = $stage_format;
        $round->type = $type;
        $round->number = StageRound::where('stage_format_id', $stage_format)->where('type', $type)->max('number') + 1;
        $round->save();

        return redirect(groute('stages.formats.view', [
            'tournamentId' => $format->stage->tournament->id,
            'stageId' => $format->stage->id,
            'sfId' => $format->id
        ]));
    }

    public function changePos(Request $request)
    {
        $all_positions = TeamsOrder::where('stage_formats_id', $request->get('stage_format_id'))->get();

        /*
         * if we don't have created positions, create them so can be edited later
         */
        if (!count($all_positions)) {
            foreach ($request->get('teams') as $team) {
                TeamsOrder::create([
                    'stage_formats_id' => $request->get('stage_format_id'),
                    'team_accounts_id' => $team['id'],
                    'pos' => $team['pos']
                ]);
            }
        }
        $current_team = TeamsOrder::where('stage_formats_id',
            $request->get('stage_format_id'))->where('team_accounts_id', $request->get('team_id'))->first();

        if ($request->get('direction') == 'up' && $current_team->pos > 0) {
            TeamsOrder::where('stage_formats_id', $request->get('stage_format_id'))->where('pos',
                $current_team->pos - 1)->update(['pos' => \DB::raw('pos+1')]);

            TeamsOrder::where('stage_formats_id', $request->get('stage_format_id'))->where('team_accounts_id',
                $request->get('team_id'))->update(['pos' => \DB::raw('pos-1')]);

        } elseif ($current_team->pos < TeamsOrder::where('stage_formats_id',
                $request->get('stage_format_id'))->max('pos')
        ) {
            TeamsOrder::where('stage_formats_id', $request->get('stage_format_id'))->where('pos',
                $current_team->pos + 1)->update(['pos' => \DB::raw('pos-1')]);

            TeamsOrder::where('stage_formats_id', $request->get('stage_format_id'))->where('team_accounts_id',
                $request->get('team_id'))->update(['pos' => \DB::raw('pos+1')]);
        }
        return response()->json('ok', 200);
    }

    /**
     * Generate array with matches for a given round
     * @param  \App\StageRound $round
     * @return array
     */
    private function _populateBracketMatches(\App\StageRound $round)
    {
        //order matches by the position attribute
        $sortedMatches = $round->dummyMatches->sortBy('position');

        //add matches to match array
        $matches = [];
        foreach ($sortedMatches as $match) {
            $opp1Score = 0;
            $opp2Score = 0;
            foreach ($match->matchGames as $mg) {
                if ($mg->winner == $match->opponent1 || $mg->opponent1_score > 0)
                    $opp1Score++;

                if ($mg->winner == $match->opponent2 || $mg->opponent2_score > 0)
                    $opp2Score++;
            }

            array_push($matches, [
                    [
                        $match->opponent1_details->id,
                        $match->opponent1_details->name,
                        $opp1Score,
                        $match->id
                    ],
                    [
                        $match->opponent2_details->id,
                        $match->opponent2_details->name,
                        $opp2Score,
                        $match->id
                    ]
                ]);
        }

        return $matches;
    }

    private function _addOpponent($name)
    {
        $opponent = \App\TeamAccount::where('tag', 'LIKE', '%' . $name . '%')->first();

        if (null === $opponent) {
            $newTA = new \App\TeamAccount();

            $newTA->name = $name;
            $newTA->tag = $name;
            $newTA->save();

            $teamID = $newTA->id;
        } else {
            $teamID = $opponent->id;
        }

        return $teamID;
    }

    private function _addOpponents($sfId, $participants)
    {
        $sf = \App\StageFormat::find($sfId);

        if (null !== $sf) {
            foreach ($participants as $participant) {
                $op = new \App\OpponentPrefill();

                $op->stage_format_id = $sf->id;
                $op->opponent_id = $participant['id'];
                $op->team_members = (array)$participant['members'];

                $op->save();
            }

            return $sf->stage;
        } else {
            return false;
        }
    }
}