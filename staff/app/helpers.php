<?php
function is_coming_from_winner_bracket($round, $team)
{
    $stage_format = \App\StageFormat::find($round->stage_format_id);
    if (!$stage_format->lead_from_winner_bracket) {
        return false;
    }
    if ($round->number < $stage_format->rounds->max('number')) {
        return false;
    }
    try {
        $pre_final_round = $stage_format->rounds->where('type', 1)->where('number',
            $stage_format->rounds->where('type', 1)->max('number') - 1
        );

        if (\App\StageRound::find($pre_final_round->first()['id'])->dummyMatches->where('winner', $team->id)->count()) {
            return true;
        }
    } catch (\Exception $e) {
        return false;
    }
    return false;
}

function groute($routeName, $gameSlug = 'current', $attributes = [])
{
    if(is_array($gameSlug)) {
        $attributes = $gameSlug;
        $gameSlug = 'current';
    }
    if($gameSlug == 'all') {
        $arRoute = explode('::', $routeName);
        if(count($arRoute) > 1) {
            $routeName = $arRoute[1];
        }
        return route($routeName, $attributes);
    }
    if($gameSlug == 'current') {
        $gameSlug = request()->currentGameSlug;
    }
    if($gameSlug) {
        $arRoute = explode('::', $routeName);
        if(count($arRoute) > 1) {
            $routeName = $gameSlug.'::'.$arRoute[1];
        } else {
            $routeName = $gameSlug.'::'.$routeName;
        }
    }
    return route($routeName, $attributes);
}

function date_convert($dateTime, $timeZoneFrom, $timeZoneTo, $dateFormatInput, $dateFormatOutput) {
    if (empty($dateTime) || $dateTime == "0000-00-00 00:00" || $dateTime == "0000-00-00 00:00:00")
        return "0000-00-00 00:00:00";

    $d = DateTime::createFromFormat($dateFormatInput, $dateTime, new DateTimeZone($timeZoneFrom));
    $d->setTimeZone(new DateTimeZone($timeZoneTo));

    return $d->format($dateFormatOutput);
}

/**
 * @param $link
 * @param string $width
 * @param int $height
 * @return string
 */
function twitch_code($link, $width = '100%', $height = 478)
{
    $segments = explode('/', $link);

    return '<iframe src="https://player.twitch.tv/?video=' . end($segments) . '&autoplay=false" frameborder="0" scrolling="no" height="' . $height . '" width="' . $width . '"></iframe>';
}

function douyutv_code($link, $width = '100%', $height = 478)
{
    $segments = explode('/', $link);

    return '<embed width="' . $width . '" height="' . $height . '" allownetworking="all" allowscriptaccess="always" src="https://staticlive.douyucdn.cn/common/share/play.swf?room_id=' . end($segments) . '&autoplay=false" quality="high" bgcolor="#000" wmode="window" allowfullscreen="true" allowFullScreenInteractive="true" type="application/x-shockwave-flash">';
}

function huomaotv_code($link, $width = '100%', $height = 478)
{
    $segments = explode('/', $link);

    return '<iframe height="' . $height . '" width="' . $width . '" src="http://www.huomao.com/outplayer/index/' . end($segments) . '?autoplay=false" frameborder=0 allowfullscreen></iframe>';
}

function hitbox_code($link, $width = '100%', $height = 478)
{
    $segments = explode('/', $link);

    return '<iframe width="' . $width . '" height="' . $height . '" src="https://www.hitbox.tv/#!/embedvideo/' . end($segments) . '?autoplay=false" frameborder="0" allowfullscreen></iframe>';
}

function mlg_code($link, $width = '100%', $height = 478)
{
    $segments = explode('/', $link);

    return '<iframe width="' . $width . '" height="' . $height . '" src="http://www.majorleaguegaming.com/player/embed/' . end($segments) . '?autoplay=false" scrolling="no" name="' . end($segments) . '"></iframe>';
}

function youtube_code($link, $width = '100%', $height = 478)
{
    parse_str(parse_url($link, PHP_URL_QUERY));

    return '<iframe width="' . $width . '" height="' . $height . '" src="https://www.youtube.com/embed/' . $v . '?autoplay=false" frameborder="0" allowfullscreen></iframe>';
}

function azubu_code($link, $width = '100%', $height = 478)
{
    $segments = explode('/', $link);

    return '<iframe width="' . $width . '" height="' . $height . '" src="http://embed.azubu.tv/' . end($segments) . '?autoplay=false" scrolling="no"></iframe>';
}

function youku_code($link, $width = '100%', $height = 478)
{
    $clear_link = explode('.html', $link)[0];
    $segments = explode('/', $clear_link);

    return '<iframe height="' . $height . '" width="' . $width . '" src="http://player.youku.com/embed/' . end($segments) . '?autoplay=false" frameborder=0 "allowfullscreen"></iframe>';
}

function imbatv($link, $width = '100%', $height = 478)
{
    $page_source = file_get_contents($link);
    preg_match_all('/<iframe src="(.*?)"/', $page_source, $matched);
    if (!count($matched)) {
        return null;
    }

    return '<iframe src="' . $matched[1][0] . '?autoplay=false" width="' . $width . '" height="' . $height . '" frameborder=0 "allowfullscreen" style="border: none!important;"></iframe>';
}

function pandatv($link, $width = '100%', $height = 478)
{
    $segments = explode('/', $link);

    return '<iframe src="http://www.panda.tv/roomframe/' . end($segments) . '?options={"hideHead":true,"hideChat":true,"hideFoot":true}&autoplay=false" width="' . $width . '" height="' . $height . '" frameborder=0 "allowfullscreen" style="border: none!important;"></iframe>';
}