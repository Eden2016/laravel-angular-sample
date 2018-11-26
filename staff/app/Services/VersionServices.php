<?php
namespace App\Services;

use Illuminate\Support\Facades\Cache;

class VersionServices
{
    public static function getCurrentVersion() {
        return "EsportsConstruct Staff v1.1.4";
        $release = Cache::get('ec_version');

        if (null === $release) {
            $username   = getenv('JIRA_API_USERNAME') ?: 'jira-api';
            $password   = getenv('JIRA_API_PASSWORD') ?: 'k;WeB@wks9uy)&BG';
            $url        = getenv('JIRA_API_URL') ?: 'https://esportsconstruct.atlassian.net/';

            $curl = curl_init($url . 'rest/api/2/project/EC/versions');
            curl_setopt($curl, CURLOPT_USERPWD, $username . ":" . $password);
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

            $versions = json_decode($json_response);

            $release = new \stdClass();
            foreach ($versions as $version) {
                if ($version->released) {
                    $release = $version;

                    if (strtotime($version->releaseDate) > strtotime($release->releaseDate))
                        $release = $version;
                }
            }

            Cache::put('ec_version', json_encode($release), 60 * 24);
        } else {
            $release = json_decode($release);
        }

        return $release->name;
    }
}