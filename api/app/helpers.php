<?php
function get_game()
{
    $segments = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
    $segments = substr($segments, 1);

    $segments = explode("/", $segments);

    $game = $segments[1];

    if (\App\Models\Game::where('slug', $game)->count()) {
        return $game;
    }
    return '';
}

function teams_by_country($t)
{
    try {
        $countries = [];
        foreach ($t->teams as $team) {
            if ($team->id == 34 || $team->id == 35) {
                continue;
            }
            if (!array_key_exists($team->location, $countries)) {
                $countries[$team->location] = new stdClass();
            }
            if (!isset($countries[$team->location]->country)) {
                $countries[$team->location]->country = \App\Models\Country::find($team->location);
            }
            $countries[$team->location]->teams[] = $team;
        }
        return $countries;
    } catch (\Exception $e) {
        return $e->getMessage();
    }

}

function twitch_code($link, $width = '100%', $height = 478)
{
    $segments = explode('/', $link);

    return '<iframe autoplay="false" src="https://player.twitch.tv/?channel=' . end($segments) . '&data-paused=true&autoplay=false&muted=true" frameborder="0" scrolling="no" height="' . $height . '" width="' . $width . '"></iframe>';
}

function douyutv_code($link, $width = '100%', $height = 478)
{
    $segments = explode('/', $link);

    return '<embed width="' . $width . '" height="' . $height . '" allownetworking="all" allowscriptaccess="always" src="https://staticlive.douyucdn.cn/common/share/play.swf?room_id=' . end($segments) . '&autoplay=false" quality="high" bgcolor="#000" wmode="window" allowfullscreen="true" allowFullScreenInteractive="true" type="application/x-shockwave-flash">';
}

function huomaotv_code($link, $width = '100%', $height = 478)
{
    $segments = explode('/', $link);

    return '<iframe autoplay="false" height="' . $height . '" width="' . $width . '" src="http://www.huomao.com/outplayer/index/' . end($segments) . '?autoplay=false" frameborder=0 allowfullscreen></iframe>';
}

function hitbox_code($link, $width = '100%', $height = 478)
{
    $segments = explode('/', $link);

    return '<iframe autoplay="false" width="' . $width . '" height="' . $height . '" src="https://www.hitbox.tv/embed/' . end($segments) . '?autoplay=false" frameborder="0" allowfullscreen></iframe>';
}

function mlg_code($link, $width = '100%', $height = 478)
{
    $segments = explode('/', $link);

    return '<iframe autoplay="false" width="' . $width . '" height="' . $height . '" src="http://www.majorleaguegaming.com/player/embed/' . end($segments) . '?autoplay=false" scrolling="no" name="' . end($segments) . '"></iframe>';
}

function youtube_code($link, $width = '100%', $height = 478)
{
    parse_str(parse_url($link, PHP_URL_QUERY));

    return '<iframe autoplay="false" width="' . $width . '" height="' . $height . '" src="https://www.youtube.com/embed/' . $v . '?autoplay=false" frameborder="0" allowfullscreen></iframe>';
}

function azubu_code($link, $width = '100%', $height = 478)
{
    $segments = explode('/', $link);

    return '<iframe autoplay="false" width="' . $width . '" height="' . $height . '" src="http://embed.azubu.tv/' . end($segments) . '?autoplay=false" scrolling="no"></iframe>';
}

function youku_code($link, $width = '100%', $height = 478)
{
    $clear_link = explode('.html', $link)[0];
    $segments = explode('/', $clear_link);

    return '<iframe autoplay="false" height="' . $height . '" width="' . $width . '" src="http://player.youku.com/embed/' . end($segments) . '?autoplay=false" frameborder=0 "allowfullscreen"></iframe>';
}

function imbatv($link, $width = '100%', $height = 478)
{
    $page_source = file_get_contents($link);
    preg_match_all('/<iframe autoplay="false" src="(.*?)"/', $page_source, $matched);
    if (!count($matched)) {
        return null;
    }

    return '<iframe autoplay="false" src="' . $matched[1][0] . '" width="' . $width . '" height="' . $height . '" frameborder=0 "allowfullscreen" style="border: none!important;"></iframe>';
}

function pandatv($link, $width = '100%', $height = 478)
{
    $segments = explode('/', $link);

    return '<iframe autoplay="false" src="http://www.panda.tv/roomframe/' . end($segments) . '?options={"hideHead":true,"hideChat":true,"hideFoot":true}&autoplay=false" width="' . $width . '" height="' . $height . '" frameborder=0 "allowfullscreen" style="border: none!important;"></iframe>';
}