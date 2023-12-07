<?php

namespace App\Http\Controllers;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;


class ShareVideoController extends Controller
{
    public function shareWidget(int $file_id)
    {
        $jsonFilePath = resource_path('js\keys.json');
        $jsonContent = file_get_contents($jsonFilePath);
        $keys = json_decode($jsonContent, true);
        $client = new Client();
        $first_date = $keys['created_at_token'];
        $second_date = date('Y-m-d');

        $diff_in_seconds = strtotime($second_date) - strtotime($first_date);
        $diff_in_days = floor($diff_in_seconds / (60 * 60 * 24));

        if ($diff_in_days == 0 || $diff_in_days > 50) {
            $long_access_token = 'https://graph.facebook.com/v18.0/oauth/access_token?grant_type=fb_exchange_token&client_id=' . $keys['appId'] . '&client_secret=' . $keys['appSecretKey'] . '&fb_exchange_token=' . $keys['access_token'];
            $connection_long_token = $client->getAsync($long_access_token)->then(function ($response) use ($client, $keys) {
                $data_token = json_decode($response->getBody(), true);
                $keys['access_token'] = $data_token['access_token'];
                $keys['created_at_token'] = date('Y-m-d');
                $new_json_data = json_encode($keys);
                file_put_contents(resource_path('js\keys.json'), $new_json_data);
            }, function (RequestException $exception) {
                echo "Error occured. ";
                echo "Error message: " . $exception->getMessage();
            });
            $connection_long_token->wait();
        }

        $keys = json_decode($jsonContent, true);
        $url_video = 'https://graph.facebook.com/v18.0/' . $keys['pageIdFacebook'] . '/videos?fields=embed_html&access_token=' . $keys['access_token'];
        $connection_data_video = $client->getAsync($url_video)->then(function ($response) use ($client, $keys, $file_id) {
            $data_video = json_decode($response->getBody(), true);
            $dataArray = $data_video['data'];
            foreach ($dataArray as $data) {
                if ($data['id'] == $file_id) {
                    $foundData = $data;
                    break;
                }
            }
            if (isset($foundData)) {
                // In ra dữ liệu khi tìm thấy
                dd($foundData);
            } else {
                // Xử lý khi không tìm thấy
                echo "Không tìm thấy dữ liệu với id ";
            }
        }, function (RequestException $exception) {
            echo "Error occured. ";
            echo "Error message: " . $exception->getMessage();
        });
        $connection_data_video->wait();

    }
}
