<?php
namespace App\Factories\StageFormats\Formats;

use App\Factories\StageFormats\StageFormatBase;

use App\OpponentPrefill;
use App\StageRound;
use App\DummyMatch;
use App\StageFormat;
use App\Stage;

class PrefilledRoundRobin extends StageFormatBase
{
    public function create(StageFormat $sf)
    {
        $teams = collect($this->opponents);
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $group_chunk = $teams->chunk(count($this->opponents) / $this->groupsNum)->toArray();

        /**
         * Someone decided to create the first group outside this function
         * So we need to double the function here and inside group creation
         */
        $stageRound = StageRound::create([
            'stage_format_id' => $sf->id,
            'type' => StageRound::ROUND_TYPE_GROUP,
            'number' => 1,
            'hidden' => 0,
            'active' => 2
        ]);
        if ($this->doubleRounds) {
            $stageRound2 = StageRound::create([
                'stage_format_id' => $sf->id,
                'type' => StageRound::ROUND_TYPE_GROUP,
                'number' => 2,
                'hidden' => 0,
                'active' => 2
            ]);
        }
        $generated_matches1 = [];
        $generated_matches2 = [];
        $group_teams = $group_chunk[0];
        foreach ($group_teams as $home) {
            foreach ($group_teams as $away) {
                /**
                 * skip $home==$away
                 * skip duplicated matches
                 */
                if ($home == $away
                    || in_array([$home, $away], $generated_matches1)
                    || in_array([$away, $home], $generated_matches1)
                    || in_array([$home, $away], $generated_matches2)
                    || in_array([$away, $home], $generated_matches2)
                ) {
                    continue;
                }
                $this->_addMatch($stageRound->id, 0, $home, $away);
                $generated_matches1[] = [$home, $away];
                if (isset($stageRound2)) {
                    $this->_addMatch($stageRound2->id, 0, $home, $away);
                    $generated_matches2[] = [$home, $away];
                }
            }
        }

        if (count($group_chunk) > 1) {
            for ($g = 1; $g < count($group_chunk); $g++) {

                $newSF = $this->_duplicateStageFormat($sf, sprintf('Group %s', $alphabet[$g]));
                //Create Round
                $stageRound = StageRound::create([
                    'stage_format_id' => $newSF->id,
                    'type' => StageRound::ROUND_TYPE_GROUP,
                    'number' => 1,
                    'hidden' => 0,
                    'active' => 2
                ]);

                $stageRound->save();
                if (request()->get('double_rounds') == 1) {
                    $stageRound2 = StageRound::create([
                        'stage_format_id' => $newSF->id,
                        'type' => StageRound::ROUND_TYPE_GROUP,
                        'number' => 2,
                        'hidden' => 0,
                        'active' => 2
                    ]);
                }
                $group_teams = $group_chunk[$g];

                $generated_matches1 = [];
                $generated_matches2 = [];
                foreach ($group_teams as $home) {
                    foreach ($group_teams as $away) {
                        /**
                         * skip $home==$away
                         * skip duplicated matches
                         */
                        if ($home == $away
                            || in_array([$home, $away], $generated_matches1)
                            || in_array([$away, $home], $generated_matches1)
                            || in_array([$home, $away], $generated_matches2)
                            || in_array([$away, $home], $generated_matches2)
                        ) {
                            continue;
                        }
                        $this->_addMatch($stageRound->id, 0, $home, $away);
                        $generated_matches1[] = [$home, $away];
                        if ($this->doubleRounds) {
                            $this->_addMatch($stageRound2->id, 0, $home, $away);
                            $generated_matches2[] = [$home, $away];
                        }
                    }
                }

            }
        }

        return true;
    }

    /**
     * Schedules round robin matches
     * @param  array $participants
     * @return array
     */
    private function _schedule($participants)
    {
        $teams = $participants;
        $total_teams = count($teams);
        if ($total_teams % 2 != 0) {
            $additional_team = array_pop($teams);
        }

        $away = array_splice($teams, count($teams) / 2);
        $home = $teams;

        $rounds = [];
        for ($i = 0; $i < $total_teams; $i++) {
            for ($j = 0; $j < count($home); $j++) {
                $rounds[$i][$j]['home'] = $home[$j];
                $rounds[$i][$j]['away'] = $away[$j];
            }
            if (count($home) + count($away) - 1 > 2) {
                array_splice($home, 1, 1);
                array_unshift($away, array_shift($home));
                array_push($home, array_pop($away));
            }
        }

        if (getenv('APP_DEBUG'))
            debugbar()->info($rounds);

        $matches = [];
        foreach ($rounds as $round) {
            foreach ($round as $match) {
                $matches[] = $match;
            }
        }

        if (getenv('APP_DEBUG'))
            debugbar()->info($matches);

        return $matches;
    }
}