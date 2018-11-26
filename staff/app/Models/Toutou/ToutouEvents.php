<?php
namespace App\Models\Toutou\ToutouEvents;

class ToutouEvents
{
    private $_data = null;
    private $_all = null;
    private $_presented = null;
    private $_oddType = null;

    public function __construct($events, $oddType)
    {
        $this->_data = $events;
        $this->_oddType = $oddType;
        $eventsIDs = [];
        $all = [];
        foreach ($this->_data->{'further-evs'} as $k => $competition) {
            foreach ($competition->events as $event) {
                $eventsIDs[] = $event->eventId;
                $event->_competition_id = $competition->competitionId;
                $event->_competition_name = $competition->competitionName;
                $event->_competition_no = $competition->competitionNo;
                $event->_usedOddType = $this->_oddType;
                $event->_in_play = 0;
                $all[] = $event;
            }
        }
        foreach ($this->_data->{'inplay-evs'} as $k=>$competition) {
            foreach ($competition->events as $event) {
                $eventsIDs[] = $event->eventId;
                $event->_competition_id = $competition->competitionId;
                $event->_competition_name = $competition->competitionName;
                $event->_competition_no = $competition->competitionNo;
                $event->_usedOddType = $this->_oddType;
                $event->_in_play = 1;
                $all[] = $event;
            }
        }
        $this->_all = $all;
        $this->_presented = array_keys(array_flip($eventsIDs));
    }

    public function getAsArray()
    {
        return $this->_all;
    }

    public function presented()
    {
        return $this->_presented;
    }

    public function getAsIs()
    {
        return $this->_data;
    }
}