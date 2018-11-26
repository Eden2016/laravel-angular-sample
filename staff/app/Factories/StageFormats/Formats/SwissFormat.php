<?php
namespace App\Factories\StageFormats\Formats;

use App\Factories\StageFormats\StageFormatBase;

use App\StageRound;
use App\DummyMatch;
use App\StageFormat;
use App\Stage;

class SwissFormat extends StageFormatBase
{
    public function create(StageFormat $sf)
    {
        //Minimum opponent count should be 8 and the number should be a factor of 8
        if ($this->opponentCount < 8 || $this->opponentCount % 8 != 0) {
            return false;
        }

        //Generate the 5 rounds needed for this format (It's a constant number)
        $rounds = [];
        for ($i = 0; $i < 5; $i++) {
            $rounds[] = $this->_addRound($sf->id, StageRound::ROUND_TYPE_GROUP, $i + 1);
        }

        //Generate the matches in each round
        //1st round matches = opponentCount / 2
        //2nd round matches = opponentCount / 2
        //3rd round matches = opponentCount / 2
        //4th round matches = (opponentCount / 2) - (opponents / 8)
        //5th round matches = 4th / 2
        $matchNum = $this->opponentCount / 2;
        foreach ($rounds as $k => $round) {
            if ($k == 3) {
                $matchNum = $matchNum - ($this->opponentCount / 8);
            }
            else {
                if ($k == 4) {
                    $matchNum = $matchNum / 2;
                }
            }

            //Add matches accoring to the number calculated
            for ($i = 0; $i < $matchNum; $i++) {
                $this->_addMatch($round->id, $i + 1);
            }
        }

        return true;
    }
}