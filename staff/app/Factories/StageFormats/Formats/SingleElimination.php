<?php
namespace App\Factories\StageFormats\Formats;

use App\Factories\StageFormats\StageFormatBase;

use App\StageRound;
use App\DummyMatch;
use App\StageFormat;
use App\Stage;

class SingleElimination extends StageFormatBase
{
    public function create(StageFormat $sf)
    {
        if ($this->isInvitational) {
            $stageRounds = $this->_generateInvitationalSingleElim($sf);
        }
        else {
            //Get number of rounds
            $roundCount = $this->_roundsNumber($this->opponentCount);

            //Create rounds
            $stageRounds = array();
            for ($i = 0; $i < $roundCount; $i++) {
                $stageRound = $this->_addRound($sf->id, StageRound::ROUND_TYPE_UPPER_BRACKET, $i + 1);
                $stageRounds[] = $stageRound->id;
            }

            //Create upper bracket with 'placeholder' matches
            foreach ($stageRounds as $k => $round) {
                for ($i = 0; $i < floor($this->opponentCount / 2); $i++) {
                    $this->_addMatch($round, $i + 1);
                }

                $this->opponentCount = floor($this->opponentCount / 2);
            }
        }

        if ($this->hasThirdPlace) {
            //Create Thirds place match round
            $round = $this->_addRound($sf->id, StageRound::ROUND_TYPE_THIRD_PLACE_PLAYOFF,
                count($stageRounds) + 1);

            //Create Third place 'placeholder' match
            $this->_addMatch($round->id, 1);
        }

        return true;
    }

    /**
     * Generates an invitational Single Elimination Stage Format
     * @param  StageFormat $sf
     * @return array
     */
    private function _generateInvitationalSingleElim(StageFormat $sf)
    {
        //Get number of rounds
        $roundCount = $this->_roundsNumber($this->opponentCount);

        //Create rounds
        $stageRounds = array();
        for ($i = 0; $i <= $roundCount; $i++) {
            $stageRound = $this->_addRound($sf->id, StageRound::ROUND_TYPE_UPPER_BRACKET, $i + 1);
            $stageRounds[] = $stageRound->id;
        }

        //Create upper bracket with 'placeholder' matches
        foreach ($stageRounds as $k => $round) {
            for ($i = 0; $i < floor($this->opponentCount / 2); $i++) {
                $this->_addMatch($round, $i + 1);
            }

            if ($k != 0) {
                $this->opponentCount = floor($this->opponentCount / 2);
            }
        }

        return $stageRounds;
    }
}