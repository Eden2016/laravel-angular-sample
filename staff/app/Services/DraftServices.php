<?php
namespace App\Services;

use App\Models\MatchDraft;
use Log;

class DraftServices
{
    /**
     * Creates a draft model, populates the data and saves it into the database
     *
     * @param int $matchId
     * @param string $draft
     *
     * @return App\Models\MatchDraft|bool
     **/
    public static function create($matchId, $drafts) 
    {
        $draft = new MatchDraft();

        $draft->dummy_match_id = $matchId;
        $draft->draft = $drafts;

        try{
            $draft->save();
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error($e->getMessage());
            return false;
        }

        return $draft;
    }

    /**
     * Edits a draft model, populates the data and saves it into the database
     *
     * @param App\Models\MatchDraft $draft
     * @param int $matchId
     * @param string $newDraft
     *
     * @return App\Models\MatchDraft|bool
     **/
    public static function edit(
        $draft, 
        $matchId,
        $newDraft
    ) {
        $draft->dummy_match_id = $matchId;
        $draft->draft = $newDraft;

        try{
            $draft->save();
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error($e->getMessage());
            return false;
        }

        return $draft;
    }
}