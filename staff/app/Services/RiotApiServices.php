<?php
namespace App\Services;

use App\Champion;

class RiotApiServices
{
    public static function updateChampionsList($apiKey) {
        $url = 'https://global.api.pvp.net/api/lol/static-data/euw/v1.2/champion?champData=image,info&api_key='.$apiKey;
        $list = self::call($url);

        if ($list) {
            $champions = json_decode($list);

            if (count($champions->data)) {
                foreach ($champions->data as $champ) {
                    $champion = Champion::where('api_id', $champ->id)->first();

                    if (null === $champion) {
                        $champion = new Champion();
                        $champion->name = $champ->name;
                        $champion->title = $champ->title;
                        $champion->api_id = $champ->id;
                        $champion->info = json_encode($champ->info);
                        $champion->image = $champ->image->full;

                        $champion->save();
                    }
                }
            }

            return true;
        }
        else {
            return false;
        }
    }

    public static function call($url) {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER,'Content-Type: application/json');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $json_response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($status != 200) {
            throw new \Exception("Error: call to URL {$url} failed with status {$status}, response {$json_response}, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl) . "\n");
        }
        curl_close($curl);

        return $json_response;
    }
}