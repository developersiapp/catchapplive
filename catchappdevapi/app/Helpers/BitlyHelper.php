<?php


namespace catchapp\Helpers;


use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Log;

class BitlyHelper
{
    static $url = "https://api-ssl.bitly.com/v4/";

    public static function createBitlink($long_url, $title = null)
    {
        $url = self::$url . "shorten";
        $request = [
            'long_url' => $long_url,
            "group_guid" => "Bic59TTL3R8",
            "domain" => "bit.ly",
        ];
        if ($title) {
            $request['title'] = $title;
        }

        $client = new Client();
        try {
            $response = $client->post($url, [
                RequestOptions::HEADERS => [
                    'Authorization' => "Bearer " . config('constants.bitly')
                ],
                RequestOptions::CONNECT_TIMEOUT => 5,
                RequestOptions::TIMEOUT => 6,
                RequestOptions::JSON => $request
            ]);

            if ($response->getStatusCode() == 200) {

                $json = json_decode($response->getBody()->getContents());
                if ($json && isset($json->link) && $json->link) {
                    return $json->link;
                }
            }
        } catch (\Exception $e) {
            Log::info("Bitly Failed: " . $e->getMessage());
        }
        return null;
    }
}
