<?php
namespace App\Factories\StageFormats\Formats;

use App\Factories\StageFormats\StageFormatBase;

use App\StageRound;
use App\DummyMatch;
use App\StageFormat;
use App\Stage;

class DoubleElimination extends StageFormatBase
{
    public function create(StageFormat $sf)
    {
        $upperRoundCount = $this->_roundsNumber($this->upperBracketTeams);
        $lowerRoundCount = $this->_roundsNumber($this->lowerBracketTeams);

        //Create upper bracket rounds
        $stageRounds = array();
        for ($i = 0; $i < $upperRoundCount; $i++) {
            $stageRound = $this->_addRound($sf->id, StageRound::ROUND_TYPE_UPPER_BRACKET, $i + 1);
            $stageRounds[] = $stageRound->id;
        }

        //Create lower bracket rounds
        $lowerStageRounds = array();
        for ($i = 0; $i < $lowerRoundCount * 2; $i++) {
            $stageRound = $this->_addRound($sf->id, StageRound::ROUND_TYPE_LOWER_BRACKET, $i + 1);
            $lowerStageRounds[] = $stageRound->id;
        }

        //Create final round
        $stageRound = $this->_addRound($sf->id, StageRound::ROUND_TYPE_FINAL, 1);

        //Create upper bracket with 'placeholder' matches
        $bufferOpponents = $this->upperBracketTeams;
        foreach ($stageRounds as $k => $round) {
            for ($i = 0; $i < floor($bufferOpponents / 2); $i++) {
                $this->_addMatch($round, $i + 1);
            }

            $bufferOpponents = floor($bufferOpponents / 2);
        }

        //Create lower bracket with 'placeholder' matches
        $bufferOpponents = $this->lowerBracketTeams;
        foreach ($lowerStageRounds as $k => $round) {
            if ($k == 0 || $k % 2 == 0) {
                $bufferOpponents /= 2;
            }

            for ($i = 0; $i < $bufferOpponents; $i++) {
                $this->_addMatch($round, $i + 1);
            }
        }


        //Create final match
        $this->_addMatch($stageRound->id, 1);
    }
}