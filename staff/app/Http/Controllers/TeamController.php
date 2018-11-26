<?php
namespace App\Http\Controllers;

use PDO;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use View;
use Validator;
use Datatables;
use Input;
use Cache;
use Log;
use Dota2Api\Api;
use Session;
use Illuminate\Support\Collection;
use App\Services\CdnServices;

class TeamController extends Controller
{
    /**
     * @var S3ClientObject
     **/
    protected $_s3;
    /**
     * @var string
     **/
    protected $_bucket;
    private $_doCache = true;

    public function __construct()
    {
        //Initialize DOTA2 Web API
        Api::init(getenv('STEAM_API_KEY_TEST'), array(getenv('DB_HOST'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'), getenv('DB_DATABASE'), ''));

        if (getenv('APP_ENV') === 'development')
            $this->_doCache = false;

        //Initialize Amazon S3 SDK
        $this->_s3 = \AWS::createClient('s3');
        $this->_bucket = getenv('BUCKET_NAME');
    }

    /**
     * Lists all teams (Steam API teams)
     *
     * @return Illuminate\View\View
     */
    public function listApiTeams()
    {
        if (Input::has("name")) {
            $name = Input::get("name");

            $data['teams'] = \App\TeamAccount::where('name', 'LIKE', '%'.$name.'%')
                ->get();
        }
        else {
            $data['teams'] = \App\Team::orderBy("name", "ASC")
                ->get();
        }

        $data['teamListActiveMenu'] = true;

        return view('team/apilist', $data);
    }

    /**
     * Lists all available teams (TeamAccount)
     *
     * @return Illuminate\View\View
     */
    public function listTeamAccounts()
    {
        $data['teamListActiveMenu'] = true;
        $data['teamsActiveMenu'] = true;
        return view('team.list', $data);
    }

    public function dataTableQuery(Request $request)
    {
            return Datatables::queryBuilder(DB::table('team_accounts')
                ->select(DB::raw('team_accounts.*, MAX(opponents.st) as start'))
                ->leftJoin(DB::raw('((SELECT opponent1 as a, MAX(start) as st FROM dummy_matches 
                                        WHERE start IS NOT NULL 
                                        GROUP BY a) 
                                     UNION 
                                     (SELECT opponent2 as a, MAX(start) as st FROM dummy_matches 
                                        WHERE start IS NOT NULL 
                                        GROUP BY a)) 
                                    as opponents'),'team_accounts.id', '=', 'opponents.a')
                ->groupBy('team_accounts.id')
                ->having('team_accounts.hidden', '=', 0))
                ->setTotalRecords(DB::table('team_accounts')->where('hidden', '=', 0)->count())
                ->filter(function ($query) use ($request) {
                    if ($request->get('name')) {
                        $query->where('name', 'like', "%{$request->get('name')}%");
                    }
                    if ($request->currentGame) {
                        $query->where('game_id', $request->currentGame->id);
                    }
                })
                ->make(true);
    }

    /**
     * Displays information about a given team
     *
     * @param int $teamId
     *
     */
    public function show($teamId)
    {
        $teamId = intval($teamId);
        $data['team'] = \App\TeamAccount::find($teamId);

        if (!$data['team']) {
            $data['isTeamProfile'] = false;
            $data['team'] = \App\Team::find($teamId);
        }
        else {
            $data['isTeamProfile'] = true;

            $data['country'] = $data['team']->country;

            $data['roster'] = \App\PlayerTeam::where('team_id', $data['team']->id)
                ->where(function ($q) {
                    $q->whereNull('end_date');
                    $q->orWhere('end_date', '>', \DB::raw('NOW()'));
                })
                ->with('player')
                ->get();
        }

        if (!$data['team'])
            abort(404);

        $data['matches'] = \App\DummyMatch::where('opponent1', $teamId)
            ->orWhere('opponent2', $teamId)
            ->orderBy('start', 'DESC')
            ->with('opponent1_details')
            ->with('opponent2_details')
            ->with('getWinner')
            ->with('matchGames')
            ->take(5)
            ->get();

        $data['teamsActiveMenu'] = true;
        return view('team/show', $data);
    }

    public function listTeamMatches($teamId)
    {
        $data['team'] = \App\TeamAccount::find($teamId);

        if (null === $data['team'])
            abort(404);

        $data['matches'] = \App\DummyMatch::where('opponent1', $teamId)
            ->orWhere('opponent2', $teamId)
            ->orderBy('start', 'DESC')
            ->with('opponent1_details', 'opponent2_details', 'stageRound.stageFormat.stage.tournament')
            ->get();

        return view('team/team_matches', $data);
    }

    /**
     *
     *
     * @return Illuminate\View\View
     */
    public function listTeamPlayerHistory($teamId)
    {
        $data['team'] = \App\TeamAccount::find($teamId);

        if (null === $data['team'])
            abort(404);

        $data['rosterHistory'] = $data['team']->roster->filter(function($player) {
            if ($player->pivot->end_date != null)
                return true;

            return false;
        });

        return view('team/team_player_history', $data);
    }

    /**
     * Shows a create team form
     *
     * @return Illuminate\View\View
     */
    public function create()
    {
        $data['teamsActiveMenu'] = true;

        return view('team/create');
    }

    /**
     * Shows an edit team form
     *
     * @param int $teamId
     *
     * @return Illuminate\View\View
     */
    public function edit($teamId)
    {
        $data['team'] = \App\TeamAccount::find($teamId);

        if (!$data['team'] instanceof \App\TeamAccount)
            abort(404);

        if ($data['team']->team_id > 0) {
            $apiTeam = \App\Team::find($data['team']->team_id);

            if (null !== $apiTeam) {
                $data['apiTeamName'] = $apiTeam->name;
            }
            else {
                $data['apiTeamName'] = "";
            }
        }
        else {
            $data['apiTeamName'] = "";
        }

        $data['teamsActiveMenu'] = true;
        return view('team/edit', $data);
    }

    /**
     * Adds/Edits a team
     *
     * @param Illuminate\Http\Request $request
     *
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:100',
            'slug' => 'required|max:100',
            'created' => 'required|date'
        ]);

        if ($validator->fails()) {
            return redirect(groute('team.create'))
                ->withErrors($validator)
                ->withInput();
        }

        if (Input::has('id')) {
            $message = "Team edited successfully!";

            $teamId = intval(Input::get('id'));
            $team = \App\TeamAccount::find($teamId);
        } else {
            $message = "Team created successfully!";

            $team = new \App\TeamAccount();
        }

        $team->name 			= $request->input('name');
        $team->slug_name 		= $request->input('slug');
        $team->tag 				= $request->input('tag');
        $team->team_id 			= $request->input('teamid');
        $team->organization_id 	= 0;
        $team->game_id 			= $request->input('game', 1);
        $team->created 			= $request->input('created');
        $team->region 			= $request->input('region');
        $team->location 		= $request->input('country');
        $team->twitter 			= $request->input('twitter');
        $team->facebook 		= $request->input('facebook');
        $team->steam 			= $request->input('steam');
        $team->vk 				= $request->input('vk');
        $team->website 			= $request->input('website');
        $team->active = $request->input('active', 0);
        $team->hidden 			= $request->input('hidden') && $request->input('hidden') == 1 ? 1 : 0;
        $team->description      = $request->input('description');
        $team->total_earnings   = $request->input('total_earnings', 0);

        if(request()->has('remove_image')) {
            $team->logo = null;
        }
        if(request()->hasFile('file')){
            if(request()->file('file')->move(public_path('uploads'), request()->file('file')->getClientOriginalName())){
                $team->logo = request()->file('file')->getClientOriginalName();

                CdnServices::uploadImage($team->logo);
            }
        }

        $team->save();


        return redirect(groute('team.show','current' ,['teamId' => $team->id]));
    }

    /**
     * Soft deletes a team from the databse
     *
     * @param int $teamId
     *
     * @return Illuminate\Http\Response
     */
    public function remove($teamId)
    {
        $teamId = intval($teamId);
        $team = \App\TeamAccount::find($teamId);

        if (null !== $team) {
            $team->hidden = 1;
            $team->save();

            $retData = array(
                "status" => "success",
            );
        }
        else {
            $retData = array(
                "status" => "error",
                "message" => "No team account found!"
            );
        }

        return response()->json($retData);
    }

    /**
     * Get Team (Steam API Team) by a supplied name
     *
     * @param string $name
     *
     * @return string
     */
    public function getTeamByName(Request $request, $name)
    {
        $teams = \App\Team::where('name', 'LIKE', '%'.$name.'%')->where('game_id', $request->currentGame->id)->get();

        if (count($teams) > 0) {
            $parsedTeams = array();
            foreach ($teams as $team) {
                $parsedTeams[] = array(
                    'name' => $team->name,
                    'id' => $team->id
                );
            }

            $return = array(
                "status" => "success",
                "teams" => $parsedTeams
            );
        } else {
            $return = array(
                "status" => "error",
                "message" => "No teams found"
            );
        }

        return response()->json($return);
    }

    /**
     * Get Team by a supplied name
     *
     * @param string $name
     *
     * @return string
     */
    public function getTeamByNameNew($name=false)
    {
        if (!$name)
            $name = Input::get('name');

        $teams = \App\TeamAccount::where('name', 'LIKE', '%'.$name.'%')->get();

        if (count($teams) > 0) {
            $parsedTeams = array();
            foreach ($teams as $team) {
                $parsedTeams[] = array(
                    'name' => $team->name,
                    'id' => $team->id
                );
            }

            $return = array(
                "status" => "success",
                "teams" => $parsedTeams
            );
        } else {
            $return = array(
                "status" => "error",
                "message" => "No teams found"
            );
        }

        return response()->json($return);
    }

    /**
     * Gets prefilled opponents by the supplied name
     *
     * @param string $name
     *
     * @return string
     */
    public function getPrefillsByName(Request $request, $name=false)
    {
        if (!$name)
            $name = $request->input('name');

        $sfId = $request->input('sfId', 0);

        $prefills 	= \App\OpponentPrefill::where('stage_format_id', $sfId)->get()->pluck('opponent_id')->toArray();
        $teams 		= \App\TeamAccount::where('name', 'LIKE', '%'.$name.'%')
            ->whereIn('id', $prefills)
            ->get();

        if (count($teams) > 0) {
            $parsedTeams = array();
            foreach ($teams as $team) {
                $parsedTeams[] = array(
                    'name' => $team->name,
                    'id' => $team->id
                );
            }

            $return = array(
                "status" => "success",
                "teams" => $parsedTeams
            );
        }
        else {
            $return = array(
                "status" => "error",
                "message" => "No teams found"
            );
        }

        return response()->json($return);
    }

    /**
     * Replaces a team in a live tournament
     *
     * @return Illuminate\Http\Response
     */
    public function replace()
    {
        $stageFormat = \App\StageFormat::find(Input::get('sfId'));
        $prefill = \App\OpponentPrefill::where('stage_format_id', $stageFormat->id)->get();

        $replace = new \App\Models\ChangedTeam();

        $replace->original_team_id = Input::get('original_team_id');
        $replace->substitute_team_id = Input::get('substitute_team_id');
        $replace->stage_format_id = $stageFormat->id;
        $replace->match_id = intval(Input::get('matchId'));
        $replace->whole_sf = Input::get('whole_sf');
        $replace->added_at = date("Y-m-d H:i:s");

        $replace->save();

        $matches = \App\StageFormat::where('id', $stageFormat->id)
            ->with('rounds.dummyMatches')
            ->get()
            ->pluck('rounds')->flatten()
            ->pluck('dummyMatches')->flatten()->sortBy('start');

        if (!$replace->whole_sf) {
            $matchIds = array();
            $check = false;
            foreach ($matches as $match) {
                if ($check)
                    $matchIds[] = $match->id;

                if ($match->id === $replace->match_id)
                    $check = true;
            }
        } else {
            $matchIds = $matches->pluck('id')->toArray();
        }


        if ($replace->whole_sf) {
            DB::update('UPDATE dummy_matches SET winner = '.$replace->substitute_team_id.' WHERE winner = '.$replace->original_team_id.' AND id IN ('.implode(",", $matchIds).')');

            DB::update('UPDATE dummy_matches SET opponent1 = '.$replace->substitute_team_id.' WHERE opponent1 = '.$replace->original_team_id.' AND id IN ('.implode(",", $matchIds).')');
            DB::update('UPDATE dummy_matches SET opponent2 = '.$replace->substitute_team_id.' WHERE opponent2 = '.$replace->original_team_id.' AND id IN ('.implode(",", $matchIds).')');

            if (count($prefill)) {
                //If there's opponent prefill - replace
                DB::update('UPDATE opponent_prefill SET opponent_id = '.$replace->substitute_team_id.' WHERE opponent_id = '.$replace->original_team_id.' AND stage_format_id = '.$stageFormat->id.'');
            }
        } else {
            DB::update('UPDATE dummy_matches SET winner = '.$replace->substitute_team_id.' WHERE winner = '.$replace->original_team_id.' AND id IN ('.implode(",", $matchIds).')');

            DB::update('UPDATE dummy_matches SET opponent1 = '.$replace->substitute_team_id.' WHERE opponent1 = '.$replace->original_team_id.' AND id IN ('.implode(",", $matchIds).')');
            DB::update('UPDATE dummy_matches SET opponent2 = '.$replace->substitute_team_id.' WHERE opponent2 = '.$replace->original_team_id.' AND id IN ('.implode(",", $matchIds).')');

            if (count($prefill)) {
                //If there's opponent prefill - add opponent
                DB::insert('INSERT INTO opponent_prefill (stage_format_id, opponent_id) VALUES ('.$stageFormat->id.', '.$replace->substitute_team_id.')');
            }
        }

        return response()->json(array(
            "status" => "success"
        ));
    }

    private function _uniquePlayers(array $players, array $currentPlayers)
    {
        $uniquePlayers = array();
        $playersBuffer = array();

        foreach ($players as $k=>$player) {
            if (!in_array($player['id'], $playersBuffer) && !in_array($player['id'], $currentPlayers)) {
                $uniquePlayers[] = array(
                    'name' => $player['name'],
                    'id' => $player['id']
                );

                $playersBuffer[] = $player['id'];
            }
        }

        return $uniquePlayers;
    }

    private function _uniqueTournaments(array $tournaments)
    {
        $uniqueTournaments = array();
        $tournamentsBuffer = array();

        foreach ($tournaments as $k => $tournament) {
            if (!in_array($tournament['id'], $tournamentsBuffer)) {
                $uniqueTournaments[] = array(
                    'name' => $tournament['name'],
                    'id' => $tournament['id']
                );

                $tournamentsBuffer[] = $tournament['id'];
            }
        }

        return $uniqueTournaments;
    }

    /**
     * Gets and object from Amazon S3 storage
     *
     * @var string $key
     *
     * @return string|bool
     */
    private function _getObject($key)
    {
        try {
            $object = $this->_s3->getObject([
                'Bucket' => $this->_bucket, // REQUIRED
                'Key' => $key,
            ]);
        } catch (\Aws\S3\Exception\S3Exception $e) {
            Log::error($e->getMEssage());
            return false;
        }

        if ($object) {
            return $object['Body'];
        }
        else {
            return false;
        }
    }
}
