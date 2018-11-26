<?php
namespace App\Http\Controllers;

use App\Event;
use App\Models\EventsStreams;
use Illuminate\Support\Collection;
use PDO;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use View;
use Validator;
use Input;
use App\Services\CdnServices;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
	/**
	 *
	 * @var int
	 */
	private $_currentGame;

	public function __construct() {
		if (request()->currentGameSlug) {
			$this->_currentGame = request()->currentGame->id;
		} else {
			$this->_currentGame = null;
		}
	}

	public function index() {
		if ($this->_currentGame === null) {
			$events = \App\Event::where('hidden', 0)->orderBy('end', 'desc')->get();
		} else {
            $events = \App\Event::select('events.*')
                ->leftJoin('event_games', 'event_games.event_id', '=', 'events.id')
                ->where('event_games.game_id', $this->_currentGame)
                ->where('hidden', 0)
                ->get();
		}
        $events = \App\Event::whereIn('id', $events->pluck('id'))->where('hidden', 0)->get();
        $perPage = request()->get('per_page', 5);
        $page = request()->get('page', 1);

		$data['eventListActiveMenu'] = true;
		$now = time();

		$data['upcoming'] = new Collection();
		$data['current'] = new Collection();
		$data['past'] = new Collection();
		foreach ($events as $event) {
			if ($event->event_status === \App\Event::STATUS_LIVE) {
				$data['current']->push($event);
                continue;
			}

            if ($event->event_status === \App\Event::STATUS_UPCOMING) {
                $data['upcoming']->push($event);
                continue;
            }

			if ($event->event_status === \App\Event::STATUS_PAST) {
				$data['past']->push($event);
			}

		}
		$data['totals'] = [
		    'current' => $data['current']->count(),
            'upcoming' => $data['upcoming']->count(),
            'past' => $data['past']->count(),
        ];
        $data['upcoming_pages'] = ceil($data['totals']['upcoming'] / $perPage);
        $data['current_pages'] = ceil($data['totals']['current'] / $perPage);
        $data['past_pages'] = ceil($data['totals']['past'] / $perPage);

		return view('event.all', $data);
	}

	public function show($eventId) {
		$eventId = intval($eventId);

		$data['event'] = \App\Event::where('id', '=', $eventId)->where('hidden', '=', 0)->first();

		if (null === $data['event']) {
			$data['error'] = 'No event found with the specified key!';
			\App::abort(404);
		} else {
			$data['tournaments'] = \App\Tournament::where('event_id', '=', $eventId)->where('hidden', 0)->orderBy('end', 'desc')->get();
			$data['games'] = \DB::select('SELECT `g`.`name`, `g`.`slug` FROM `event_games` as `eg` LEFT JOIN `games` as `g` ON `g`.`id` = `eg`.`game_id` WHERE `eg`.`event_id` = '.$data['event']->id);
		}

		$data['breadcrumbs'][] = array(
                'name' => 'Events',
                'url' => groute('events'),
                'active' => false
            );
		$data['breadcrumbs'][] = array(
                'name' => 'Event',
                'url' => groute('event.view', 'current', ['eventId' => $eventId]),
                'active' => true
            );

		$data['eventListActiveMenu'] 	= true;
		$data['noAssociatedMatches'] 	= true;
		$data['rightSidePanelOff']		= true;

		return view('event.show', $data);
	}

	public function create() {
		$data['eventListActiveMenu'] = true;
		$data['games'] = \App\Game::all();

		return view('event.create', $data);
	}

	public function store(Request $request) {

		$validator = Validator::make($request->all(), [
            'name' 			=> 'required|max:255',
	        'short_handle' 	=> 'required',
	        'start' 		=> 'date',
	        'end' 			=> 'date|after:start',
	        'games'			=> 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

        if (Input::has('id')) {
        	$eventId = intval(Input::get('id'));
        	$event = \App\Event::find($eventId);
        } else {
	        $event = new \App\Event();
	    }

        $startDate = date_convert(Input::get('start'), Auth::user()->timezone, 'UTC', 'Y-m-d H:i', 'Y-m-d H:i:s');
        $endDate = date_convert(Input::get('end'), Auth::user()->timezone, 'UTC', 'Y-m-d H:i', 'Y-m-d H:i:s');

        $event->name = Input::get('name');
        $event->short_handle = Input::get('short_handle');
        $event->first_installment = Input::get('first_installment');
        $event->start = $startDate;
        $event->end = $endDate;
        $event->description = Input::get('description') != '' ? Input::get('description') : '';
        $event->hidden = Input::has('hidden') ? Input::get('hidden') : 0;
        $event->active = Input::has('active') ? Input::get('active') : 0;
        $event->organizer = request()->get('organizer');
        $event->location = request()->get('location');
        $event->toutou_info = request()->get('toutou_info', '');

        if(request()->has('remove_image')){
            $event->logo = null;
        }
        if(request()->has('remove_toutou_banner')){
            $event->toutou_banner = null;
        }
        if(request()->hasFile('file')){
            if(request()->file('file')->move(public_path('uploads'), request()->file('file')->getClientOriginalName())){
                $event->logo = request()->file('file')->getClientOriginalName();

                CdnServices::uploadImage($event->logo);
            }
        }
        if(request()->hasFile('toutou_banner')){
            if(request()->file('toutou_banner')->move(public_path('uploads'), request()->file('toutou_banner')->getClientOriginalName())){
                $event->toutou_banner = request()->file('toutou_banner')->getClientOriginalName();

                CdnServices::uploadImage($event->toutou_banner );
            }
        }

        $event->save();

        EventsStreams::where('events_id', $event->id)->delete();

        if (Input::has('streams') && count(Input::get('streams')) > 0) {
            foreach(Input::get('streams') as $stream_id){
                EventsStreams::create([
                    'events_id' => $event->id,
                    'streams_id' => $stream_id
                ]);
            }
        }

        if (Input::has('id')) {
        	\DB::delete('DELETE FROM `event_games` WHERE `event_id` = '.$event->id);
        }

        $games = Input::get('games');
    	foreach ($games as $k=>$game) {
    		\DB::insert('INSERT INTO `event_games` (event_id, game_id) VALUES ('.$event->id.', '.$game.')');
    	}

        return redirect(groute('event.view', [$event->id]));
	}

	public function edit($eventId) {
		$eventId = intval($eventId);
		$data['games'] = \App\Game::all();

		$data['event'] = \App\Event::find($eventId);
		if (null === $data['event']) {
			$data['errorMessage'] = 'No event found with the specified key!';
		}

		$data['eventListActiveMenu'] = true;

		$data['selectedGames'] = array();
		$selectedGames = \DB::select('SELECT * FROM `event_games` WHERE `event_id` = '.$eventId);
		if ($selectedGames) {
			foreach ($selectedGames as $game) {
				$data['selectedGames'][] = $game->game_id;
			}
		}

		return view('event.edit', $data);
	}

	public function remove($eventId) {
		$event = \App\Event::find($eventId);

		if (null !== $event) {
			//$event->delete();
			$event->hidden = 1;
			$event->save();
		}

        return redirect()->back();
	}
}
