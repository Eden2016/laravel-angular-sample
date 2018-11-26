<?php
namespace App\Factories\StageFormats;

use App\StageRound;
use App\DummyMatch;
use App\StageFormat;
use App\Stage;

abstract class StageFormatBase
{
    protected $attributes = array();

    public abstract function create(StageFormat $sf);

    public function __get($key)
    {
        return $this->attributes[ $key ];
    }

    public function __set($key, $value)
    {
        $this->attributes[ $key ] = $value;
    }

    /**
     * Adds a match in a given stage round
     * @param integer $roundId
     * @param integer $position
     * @param integer $opponent1
     * @param integer $opponent2
     *
     * @return  \App\DummyMatch
     */
    protected function _addMatch($roundId, $position = 0, $opponent1 = 34, $opponent2 = 35)
    {
        $round = StageRound::find($roundId);
        $stageFormat = $round->stageFormat;
        $stage = $stageFormat->stage;
        $tournament = $stage->tournament;

        $dummyMatch = new DummyMatch();

        $dummyMatch->opponent1 = is_array($opponent1) ? $opponent1['id'] : $opponent1;
        $dummyMatch->opponent2 = is_array($opponent2) ? $opponent2['id'] : $opponent2;
        $dummyMatch->round_id = $roundId;
        $dummyMatch->game_id = $tournament->game_id;
        $dummyMatch->position = $position;
        $dummyMatch->status = DummyMatch::STATUS_UPCOMING;
        if (is_array($opponent1) && $opponent1['members']) {
            $dummyMatch->opponent1_members = $opponent1['members'];
        }
        if (is_array($opponent2) && $opponent2['members']) {
            $dummyMatch->opponent2_members = $opponent2['members'];
        }
        $dummyMatch->save();

        return $dummyMatch;
    }

    /**
     * Adds a round in a given stage format
     * @param integer $sfId
     * @param integer $type
     * @param integer $number
     *
     * @return  \App\StageRound
     */
    protected function _addRound($sfId, $type = 1, $number = 1)
    {
        $stageRound = new StageRound();

        $stageRound->stage_format_id = $sfId;
        $stageRound->type = $type;
        $stageRound->number = $number;
        $stageRound->hidden = 0;
        $stageRound->active = 1;

        $stageRound->save();

        return $stageRound;
    }

    /**
     * Adds a stage format for a given stage in the database
     * @param integer $stageId
     * @param string  $name
     * @param integer $type
     * @param string  $start
     * @param string  $end
     * @param integer $pointsDistro
     * @param integer $pointsPerWin
     * @param integer $pointsPerDraw
     *
     * @return  \App\StageFormat
     */
    protected function _addStageFormat(
        $stageId,
        $name,
        $type,
        $start,
        $end,
        $pointsDistro = 0,
        $pointsPerWin = 0,
        $pointsPerDraw = 0
    ) {
        $stageFormat = new \App\StageFormat();

        $stageFormat->stage_id = $stageId;
        $stageFormat->name = $name;
        $stageFormat->type = $type;
        $stageFormat->start = $start;
        $stageFormat->end = $end;
        $stageFormat->hidden = 0;
        $stageFormat->active = 1;
        $stageFormat->status = \App\Stage::STATUS_UPCOMING;
        $stageFormat->points_distribution = $pointsDistro;
        $stageFormat->points_per_win = $pointsPerWin;
        $stageFormat->points_per_draw = $pointsPerDraw;

        $stageFormat->save();

        return $stageFormat;
    }

    /**
     * Make a 'clone' of a stage format with a different name
     * @param  \App\StageFormat $sf
     * @param  string           $name
     *
     * @return \App\StageFormat
     */
    protected function _duplicateStageFormat(StageFormat $sf, $name)
    {
        $stageFormat = new StageFormat();

        $stageFormat->stage_id = $sf->stage_id;
        $stageFormat->name = $name;
        $stageFormat->type = $sf->type;
        $stageFormat->start = $sf->start;
        $stageFormat->end = $sf->end;
        $stageFormat->hidden = 0;
        $stageFormat->active = 1;
        $stageFormat->status = Stage::STATUS_UPCOMING;
        $stageFormat->points_distribution = $sf->points_distribution;
        $stageFormat->points_per_win = $sf->points_per_win;
        $stageFormat->points_per_draw = $sf->points_per_draw;

        $stageFormat->save();

        return $stageFormat;
    }

    /**
     * Returns the number of rounds in a stage format
     * @param  integer $opponentsCount
     *
     * @return integer
     */
    protected function _roundsNumber($opponentsCount)
    {
        $roundCount = 0;
        while ($opponentsCount >= ($this->qualifyingParticipants * 2)) {
            $opponentsCount /= 2;
            ++$roundCount;
        }

        return $roundCount;
    }

    /**
     * Returns the number of matches played in a given round
     * @param  integer  $teams
     * @param  boolean  $even
     *
     * @return integer
     */
    protected function _numberOfMatches($teams, $even = false)
    {
        if ($even && $teams % 2 != 0) {
            $teams++;
        }

        $number = 0;
        for ($i = $teams - 1; $i > 0; $i--) {
            $number += $i;
        }

        return $number;
    }
}