<?php

namespace App\Factories;

use App\BetTeam;
use App\DummyMatch;
use App\TeamAccount;
use App\ToutouMatch;
use App\Models\Toutou\ToutouEvents\ToutouEvents;
use DB;
use App\Game;

class ToutouMatchFactory
{
    public function make($eventData)
    {
        return $this->update(new ToutouMatch(), $eventData);
    }

    public function update($ttMatch, $eventData)
    {
        $gameId = $this->getGame($eventData->_competition_name);
        $homeTeam = $this->mapTeam($eventData->data->Home, $gameId);
        $awayTeam = $this->mapTeam($eventData->data->Away, $gameId);

        $ttMatch->competition_id = $eventData->_competition_id;
        $ttMatch->competition_name = $eventData->_competition_name;
        $ttMatch->competition_no = $eventData->_competition_no;
        $ttMatch->event_id = $eventData->eventId;
        $ttMatch->parent_event = $eventData->parentEventId;
        $ttMatch->event_date = $eventData->eventDate;
        $ttMatch->home_team = $homeTeam->id;
        $ttMatch->away_team = $awayTeam->id;
        $ttMatch->in_play = $eventData->_in_play;
        $ttMatch->active = 1;
        $ttMatch->game_id = $gameId;

        if(!$ttMatch->dummy_match && $ttMatch->automatic_assigment > -1) {
            $ttMatch->dummy_match = $this->mapMatch($ttMatch, $homeTeam, $awayTeam);
            $ttMatch->automatic_assigment = ($ttMatch->dummy_match) ? 1 : 0;
            $ttMatch->game_number = $this->mapGameNumber($eventData->data->Home);
        }

        switch ($eventData->_usedOddType) {
            case ToutouMatch::ODD_TYPE_EURO:
                $ttMatch->odds = json_encode($eventData->odds);
                $ttMatch->new_odds = json_encode($eventData->newOdds);
                break;
            case ToutouMatch::ODD_TYPE_HK:
                $ttMatch->odds_hk = json_encode($eventData->odds);
                $ttMatch->new_odds_hk = json_encode($eventData->newOdds);
                break;
            case ToutouMatch::ODD_TYPE_MALAY:
                $ttMatch->odds_malay = json_encode($eventData->odds);
                $ttMatch->new_odds_malay = json_encode($eventData->newOdds);
                break;
            case ToutouMatch::ODD_TYPE_INDO:
                $ttMatch->odds_indo = json_encode($eventData->odds);
                $ttMatch->new_odds_indo = json_encode($eventData->newOdds);
                break;
        }

        $ttMatch->save();
        return $ttMatch;
    }

    public function mapGameNumber($teamName) {
        preg_match('/\(Game\s+(\d+)\)/', $teamName, $matches);
        return (isset($matches[1])) ? $matches[1] : 0;
    }

    public function mapTeam($teamName, $gameId)
    {
        $teamName = trim(preg_replace('/\(Game\s+(\d+)\)/', '', $teamName));
        $team = BetTeam::where('team_name', $teamName)->where('game_id', $gameId)->first();

        if(!$team) {
            //Get the maximum ID at the moment, because the id field is not auto incremented
            $maxId = BetTeam::max('id');
            $team = new BetTeam();
            $team->id = $maxId + 1;
            $team->team_id = 0;
            $team->team_name = $teamName;
            $team->game_id = $gameId;
        }

        if ($team->team_id === 0) { // still trying to exact match ttTeam with teamAccount
            $teamAccount = TeamAccount::where('name', $teamName)->where('game_id', $gameId)->first();

            if (!$teamAccount)
                $teamAccount = TeamAccount::where('name', 'LIKE', "%".$teamName."%")->where('game_id', $gameId)->first();

            if($teamAccount) {
                $team->team_id = $teamAccount->id;
                $team->save();
                return $team;
            }
            if(!$team->exists) $team->save();
        }

        return $team;
    }

    public static function mapMatch($ttMatch, $homeTeam, $awayTeam)
    {
        if ($ttMatch->automatic_assigment == -1)
            return null;

        if ($homeTeam->team_id == 0 || $awayTeam->team_id == 0)
            return null;

        $dummyMatch = DB::select(DB::raw(sprintf("SELECT
                id
            FROM
                dummy_matches
            WHERE (
                (opponent1 = %d AND opponent2 = %d)
            OR
                (opponent1 = %d AND opponent2 = %d)
            )
            AND start BETWEEN '%s' AND '%s'
            LIMIT 1",
            $homeTeam->team_id,
            $awayTeam->team_id,
            $awayTeam->team_id,
            $homeTeam->team_id,
            date("Y-m-d H:i:s", ($ttMatch->event_date->timestamp) - (60 * 30)),
            date("Y-m-d H:i:s", ($ttMatch->event_date->timestamp) + (60 * 30)))));

        if ($dummyMatch) {
            return $dummyMatch[0]->id;
        }

        return null;
    }

    /**
     * Find out the game of the competiton
     * @param  string $compName
     * @return int
     */
    public function getGame($compName)
    {
        if (stristr($compName, "DOTA2")) {
            $game = Game::where('slug', 'dota2')->first();
        }
        else if (stristr($compName, "CS:GO")) {
            $game = Game::where('slug', 'csgo')->first();
        }
        else if (stristr($compName, "LoL")) {
            $game = Game::where('slug', 'lol')->first();
        }
        else if (stristr($compName, "overwatch")) {
            $game = Game::where('slug', 'overwatch')->first();
        }

        if ($game) {
            return $game->id;
        }
        else {
            return 0;
        }
    }

    public function fetchEvents()
    {
        foreach (ToutouMatch::listOddTypes() as $oddType) {
            if($events = $this->fetchEventsByOddType($oddType)) {
                foreach($events->getAsArray() as $event) {
                    if ($ttMatch = ToutouMatch::where('event_id', $event->eventId)->where('active', 1)->first()) {
                        $this->update($ttMatch, $event);
                    } else {
                        $this->make($event);
                    }
                }
                // deactivate not presented
                if($activeMatches = \App\ToutouMatch::where('active', 1)->whereNotIn('event_id', $events->presented())->get()) { // do not use mass update :(
                    foreach($activeMatches as $ttMatch) {
                        $ttMatch->active = 0;
                        $ttMatch->save();
                    }
                }
            }
        }
        return true;
    }

    private function fetchEventsByOddType($oddType)
    {
        $url = sprintf('%s/en-gb/Service/OddsService?GetEventsByCompetitions&SportId=23&OddsType=%d&Language=en-gb', getenv('SBK_ENDPOINT'), $oddType);
        $curl_handle=curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, $url);
        curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curl_handle, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36');
        $query = curl_exec($curl_handle);
        curl_close($curl_handle);
        $query = substr($query, 0, -2);
        $query = substr($query, 1);
        $result = json_decode($query);
        return (is_object($result)) ? new ToutouEvents($result, $oddType) : false;
    }

}