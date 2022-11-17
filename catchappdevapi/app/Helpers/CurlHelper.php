<?php


namespace catchapp\Helpers;


use Carbon\Carbon;
use catchapp\Models\ClubStream;
use catchapp\Models\ClubWebStream;

class CurlHelper
{
    public static function live_status($mobile_stream_id, $web_stream_id)
    {
        if (isset($web_stream_id) && !empty($web_stream_id)) {
            $url = "https://api.cloud.wowza.com/api/v1.3/live_streams/$web_stream_id/state";
            $method = "GET";
            $stream_state = CurlHelper::CurlCommand($url, $method, '');
            $decoded_stream_state = json_decode($stream_state['data']);
            if ($decoded_stream_state->live_stream->state != 'stopped') {
                return true;
            }
        }
        if (isset($mobile_stream_id) && !empty($mobile_stream_id)) {
            $url = "https://api.cloud.wowza.com/api/v1.3/live_streams/$mobile_stream_id/state";
            $method = "GET";
            $stream_state = CurlHelper::CurlCommand($url, $method, '');
            $decoded_stream_state = json_decode($stream_state['data']);
            if ($decoded_stream_state->live_stream->state != 'stopped') {
                return true;
            }
        }
        return false;
    }

    public static function liveDj($club)
    {
        $club_mobile_stream = ClubStream::query()->where('club_id','=', $club->id)->first();
        $club_web_stream = ClubWebStream::query()->where('club_id','=', $club->id)->first();
        if ($club_mobile_stream) {
            $mobile_stream_id = $club_mobile_stream->stream_id;
        }
        if ($club_web_stream) {
            $web_stream_id = $club_web_stream->stream_id;
        }
        if (isset($web_stream_id)) {
            $url = "https://api.cloud.wowza.com/api/v1.3/live_streams/$web_stream_id/state";
            $method = "GET";
            $stream_state = CurlHelper::CurlCommand($url, $method, '');
            $decoded_stream_state = json_decode($stream_state['data']);
            if ($decoded_stream_state->live_stream->state != 'stopped') {
                return $club_web_stream->updated_by_dj;
            }
        }
        if (isset($mobile_stream_id)) {
            $url = "https://api.cloud.wowza.com/api/v1.3/live_streams/$mobile_stream_id/state";
            $method = "GET";
            $stream_state = CurlHelper::CurlCommand($url, $method, '');
            $decoded_stream_state = json_decode($stream_state['data']);
            if ($decoded_stream_state->live_stream->state != 'stopped') {
                return $club_mobile_stream->updated_by_dj;
            }
        }
        return false;
    }
    public static function is_live($club_id)
    {
        $club_mobile_stream = ClubStream::query()->where('club_id','=', $club_id)->first();
        $club_web_stream = ClubWebStream::query()->where('club_id','=', $club_id)->first();
        if ($club_mobile_stream) {
            $mobile_stream_id = $club_mobile_stream->stream_id;
        }
        if ($club_web_stream) {
            $web_stream_id = $club_web_stream->stream_id;
        }
        if (isset($web_stream_id)) {
            $url = "https://api.cloud.wowza.com/api/v1.3/live_streams/$web_stream_id/state";
            $method = "GET";
            $stream_state = CurlHelper::CurlCommand($url, $method, '');
            $decoded_stream_state = json_decode($stream_state['data']);
            if ($decoded_stream_state->live_stream->state != 'stopped') {
                return true;
            }
        }
        if (isset($mobile_stream_id)) {
            $url = "https://api.cloud.wowza.com/api/v1.3/live_streams/$mobile_stream_id/state";
            $method = "GET";
            $stream_state = CurlHelper::CurlCommand($url, $method, '');
            $decoded_stream_state = json_decode($stream_state['data']);
            if ($decoded_stream_state->live_stream->state != 'stopped') {
                return true;
            }
        }
        return false;
    }

    public static function CurlCommand($url, $method, $data)
    {
//        if ($development)
//        {
//            $p_url = 'https://api.cloud.wowza.com/api/v1.3/';
//        }
//        else
//        {
//            $p_url = 'https://api-sandbox.cloud.wowza.com/api/v1.3/';
//        }
//        $f_url = $p_url.$url;
//        dd($f_url);
        $curl = curl_init();
        $unix_epoch_timestamp = Carbon::now()->format('U');
        $api_key =env('WOWZA_API_KEY');
        $access_key =env('WOWZA_ACCESS_KEY');
//  $api_key ='K8mRJrB8yWTKnTxXgFXVYboJiOjC3wvpjU2BW0sdWKpZ5H3CeqJREFtQBIpZ3358';
//        $access_key ='8aEAdQzhQjxrb8Hr62g1lcszwDk16zMYJeGcnk4uYsH548uTmg9cjFxDQT1r3445';
//        $api_key ='hzqeXsFhXfc93tlQKaqkQzJ42Fowj9c6JphXQORKQ45ODaTmQLRiQuIpYRZz3522';
//        $access_key ='dI81KX16Q5JpvNptK5xPMhzHMrREpUpr1pK4hGJrEgETC0gAkhGSzVuVm1FF3259';
//        $wsc_signature = $unix_epoch_timestamp.':/api/v1.3/'. $url.':'. $api_key;

        $header_data=array(
            "wsc-access-key: $access_key",
            "wsc-api-key: $api_key",
            "wsc-timestamp: $unix_epoch_timestamp",
//                "wsc-signature: [signature-generated-from-HMAC-256-Hexdigest-algorithm]",
            "content-type: application/json",
        );


        if ($data !='') {
            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => $method,
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_HTTPHEADER => $header_data,
            ));
        }
        if ($data== '') {
            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => $method,
                CURLOPT_HTTPHEADER => $header_data,
            ));
        }


        $res = curl_exec($curl);
        $response=[
            'error' => false,
            'data' => $res
        ];
        $err = curl_error($curl);
        $errNo = curl_errno($curl);

        curl_close($curl);

        if ($err) {
            $response=[
                'error' => true,
                'data' => 'Error: ' . $err . ' - Code: ' . $errNo
            ];
        }
        return $response;
    }
}