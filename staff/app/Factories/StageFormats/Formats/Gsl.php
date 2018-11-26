<?php
namespace App\Factories\StageFormats\Formats;

use App\Factories\StageFormats\StageFormatBase;

use App\StageRound;
use App\DummyMatch;
use App\StageFormat;
use App\Stage;

class Gsl extends StageFormatBase
{
    public function create(StageFormat $sf)
    {
        //Create Stage Formats (Group A, Group B, Playoffs)
        $sfIds = array(
            $sf->id
        );

        if ($this->groupsNum - 1 > 0) {
            $alphabet = 'BCDEFGHIJKLMNOPQRSTUVWXYZ';

            for ($i = 0; $i < $this->groupsNum - 1; $i++) {
                $newSF = $this->_duplicateStageFormat($sf, sprintf('Group %s', $alphabet[$i]));
                $sfIds[] = $newSF->id;
            }
        }

        for ($l = 0; $l < count($sfIds); $l++) {
            $qualifyingParticipants = 2;
            $opponents = $opponentCount;
            $upperBracketTeams = 4;
            $lowerBracketTeams = 2;
            $upperRoundCount = 2;
            $lowerRoundCount = 1;

            //Create upper bracket rounds
            $stageRounds = array();
            for ($i = 0; $i < $upperRoundCount; $i++) {
                $stageRound = $this->_addRound($sfIds[$l], StageRound::ROUND_TYPE_UPPER_BRACKET, $i + 1);
                $stageRounds[] = $stageRound->id;
            }

            //Create lower bracket rounds
            $lowerStageRounds = array();
            for ($i = 0; $i < $lowerRoundCount * 2; $i++) {
                $stageRound = $this->_addRound($sfIds[$l], StageRound::ROUND_TYPE_LOWER_BRACKET, $i + 1);
                $lowerStageRounds[] = $stageRound->id;
            }

            //Create upper bracket with 'placeholder' matches
            $bufferOpponents = $upperBracketTeams;
            foreach ($stageRounds as $k => $round) {
                for ($i = 0; $i < floor($bufferOpponents / 2); $i++) {
                    $this->_addMatch($round, $i + 1);
                }

                $bufferOpponents = floor($bufferOpponents / 2);
            }

            //Create lower bracket with 'placeholder' matches
            $bufferOpponents = $lowerBracketTeams;
            foreach ($lowerStageRounds as $k => $round) {
                $this->_addMatch($round, 1);
            }
        }

        if (!$this->disablePlayoffs) {
            $this->_generateGSLPlayoffs($sf->stage->id, $sf->start, $sf->end, $this->groupsNum + 1);
        }

        return $sfIds;
    }

    /**
     * Generates a playoff stage format (either single elimination, or double elimination)
     * @param  int $stageId
     * @param  string $start
     * @param  string $end
     * @param  int $groupsNum
     * @return void
     */
    private function _generateGSLPlayoffs($stageId, $start, $end, $groupsNum)
    {
        $type = StageFormat::TYPE_SINGLE_ELIM;
        if ($this->eliminationPlayoffs == 'double') {
            $type = StageFormat::TYPE_DOUBLE_ELIM;
        }

        $sf = $this->_addStageFormat($stageId, 'Playoffs', $type, $start, $end);

        if ($sf->type == StageFormat::TYPE_SINGLE_ELIM) {
            $qualifyingParticipants = 1;
            $opponents = $groupsNum * 2;
            $opponentCount = $opponents;
            $roundCount = $this->_roundsNumber($opponentCount);

            //Create rounds
            $stageRounds = array();
            for ($i = 0; $i < $roundCount; $i++) {
                $stageRound = $this->_addRound($sf->id, StageRound::ROUND_TYPE_UPPER_BRACKET, $i + 1);
                $stageRounds[] = $stageRound->id;
            }

            //Create upper bracket with 'placeholder' matches
            foreach ($stageRounds as $k => $round) {
                for ($i = 0; $i < floor($opponents / 2); $i++) {
                    $this->_addMatch($round, $i + 1);
                }

                $opponents = floor($opponents / 2);
            }
        }
        else {
            if ($sf->type == StageFormat::TYPE_DOUBLE_ELIM) {
                $qualifyingParticipants = 1;
                $opponents = $groupsNum * 2;
                $opponentCount = $opponents;
                $upperBracketTeams = $opponents;
                $lowerBracketTeams = $opponents / 2;

                $upperRoundCount = $this->_roundsNumber($upperBracketTeams);
                $lowerRoundCount = $this->_roundsNumber($lowerBracketTeams);

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
                $bufferOpponents = $upperBracketTeams;
                foreach ($stageRounds as $k => $round) {
                    for ($i = 0; $i < floor($bufferOpponents / 2); $i++) {
                        $this->_addMatch($round, $i + 1);
                    }

                    $bufferOpponents = floor($bufferOpponents / 2);
                }

                //Create lower bracket with 'placeholder' matches
                $bufferOpponents = $lowerBracketTeams;
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
    }
}