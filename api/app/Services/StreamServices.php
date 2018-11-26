<?php
namespace App\Services;

use DB;
use Cache;
use Log;
use Session;

class StreamServices
{
    /**
     * @param  int $id
     * @return array|null
     */
    public static function getLiveStreams($game, $client=false)
    {
        $gameId = GameServices::getGameId($game);

        $andGame = "";
        if ($gameId)
            $andGame = "AND t.game_id = ".$gameId;

        if ($client)
            $logo = ', e.'.$client.'_banner AS '.$client.'_logo';
        else
            $logo = '';

        $matches = DB::select(DB::raw(sprintf("SELECT
                    dm.id AS match_id,
                    taa.name AS opponent1_name,
                    tab.name AS opponent2_name,
                    st.name AS stage_name,
                    t.name AS tournament_name,
                    t.logo AS tournament_logo,
                    e.logo AS event_logo,
                    g.name AS game_name,
                    g.slug AS game_slug
                    %s
                FROM dummy_matches as dm
                LEFT JOIN matches_streams AS ms ON ms.matches_id = dm.id
                LEFT JOIN streams AS s ON s.id = ms.streams_id
                LEFT JOIN stage_rounds AS sr ON sr.id = dm.round_id
                LEFT JOIN stage_formats AS sf ON sf.id = sr.stage_format_id
                LEFT JOIN stages AS st ON st.id = sf.stage_id
                LEFT JOIN tournaments AS t on t.id = st.tournament_id
                LEFT JOIN events AS e on e.id = t.event_id
                LEFT JOIN team_accounts AS taa ON taa.id = dm.opponent1
                LEFT JOIN team_accounts AS tab ON tab.id = dm.opponent2
                LEFT JOIN games AS g ON g.id = t.game_id
                WHERE dm.start < NOW()
                AND dm.start IS NOT NULL
                AND dm.winner IS NULL
                AND dm.is_tie = 0
                AND ms.matches_id IS NOT NULL
                AND sf.hidden = 0
                AND st.hidden = 0
                AND t.hidden = 0
                AND e.hidden = 0
                %s
                GROUP BY dm.id
                LIMIT 4", $logo, $andGame)));

        if (count($matches)) {
            foreach ($matches AS $match) {
                $streams = DB::select(DB::raw("SELECT
                                s.lang AS stream_lang,
                                s.platform,
                                s.embed_code,
                                s.link AS stream_link,
                                s.title AS stream_title
                            FROM matches_streams AS ms
                            LEFT JOIN streams AS s ON s.id = ms.streams_id
                            WHERE ms.matches_id = ".$match->match_id));

                foreach ($streams as $k=>$stream) {
                    $match->streams[$k] = $stream;
                    if ($stream->embed_code) {
                        $match->streams[$k]->embed = $stream->embed_code;
                    }
                    else {
                        switch ($stream->platform) {
                            case 'Twitch.tv':
                                $match->streams[$k]->embed = twitch_code($stream->stream_link);
                                break;
                            case 'Douyutv.com':
                                $match->streams[$k]->embed = douyutv_code($stream->stream_link);
                                break;
                            case 'Huomaotv.cn':
                                $match->streams[$k]->embed = huomaotv_code($stream->stream_link);
                                break;
                            case 'Hitbox':
                                $match->streams[$k]->embed = hitbox_code($stream->stream_link);
                                break;
                            case 'MLG':
                                $match->streams[$k]->embed = mlg_code($stream->stream_link);
                                break;
                            case 'Youtube':
                                $match->streams[$k]->embed = youtube_code($stream->stream_link);
                                break;
                            case 'Azubu':
                                $match->streams[$k]->embed = azubu_code($stream->stream_link);
                                break;
                            default:
                                $match->streams[$k]->embed = ''; //empty if no platform selected
                                break;

                        }
                    }
                }

                $match->match_id = MainServices::maskId($match->match_id);
            }
        }

        return $matches;
    }
}