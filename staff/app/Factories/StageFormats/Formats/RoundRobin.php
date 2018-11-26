<?php
namespace App\Factories\StageFormats\Formats;

use App\Factories\StageFormats\StageFormatBase;

use App\StageRound;
use App\DummyMatch;
use App\StageFormat;
use App\Stage;

class RoundRobin extends StageFormatBase
{
    public function create(StageFormat $sf)
    {
        //Create Round
        $stageRound = $this->_addRound($sf->id, StageRound::ROUND_TYPE_GROUP, 1);

        //Create 'placeholder' matches
        for ($i = 0; $i < $this->_numberOfMatches($this->opponentCount); $i++) {
            $this->_addMatch($stageRound->id, $i + 1);
        }

        if ($this->doubleRounds && $this->groupsNum == 0) {
            //Create Second Round
            $stageRound = $this->_addRound($sf->id, StageRound::ROUND_TYPE_GROUP, 2);

            //Create 'placeholder' matches
            for ($i = 0; $i < $this->_numberOfMatches($this->opponentCount); $i++) {
                $this->_addMatch($stageRound->id, $i + 1);
            }
        }

        if ($this->groupsNum > 0) {
            $alphabet = 'BCDEFGHIJKLMNOPQRSTUVWXYZ';

            for ($i = 0; $i < $this->groupsNum; $i++) {
                $newSF = $this->_duplicateStageFormat($sf, sprintf('Group %s', $alphabet[$i]));

                //Create Round
                $stageRound = $this->_addRound($newSF->id, StageRound::ROUND_TYPE_GROUP, 1);

                //Create 'placeholder' matches
                for ($l = 0; $l < $this->_numberOfMatches($this->opponentCount); $l++) {
                    $this->_addMatch($stageRound->id, $l + 1);
                }
            }
        }

        return true;
    }
}