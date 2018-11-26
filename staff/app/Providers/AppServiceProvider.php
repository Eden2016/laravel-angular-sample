<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\DummyMatch;
use App\Models\Predictions\Prediction;
use DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);

        DummyMatch::saved(function ($match) {
            if ($match->start && (strtotime($match->start)) > time()) {
                $discardedOpponents = [
                    34,
                    35
                ];

                if (!in_array($user->opponent1, $discardedOpponents) && !in_array($user->opponent2, $discardedOpponents)) {
                    $isMatch = Prediction::where('dummy_match_id', $match->id)->first();

                    if (!$isMatch) {
                        Prediction::create([
                                'dummy_match_id' => $match->id,
                                'home_team_id' => $match->opponent1,
                                'away_team_id' => $match->opponent2,
                                'answered' => 0
                            ]);
                    }
                }
            }

            return true;
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
