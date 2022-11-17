<?php


namespace catchapp\Http\Controllers\API;

use Carbon\Carbon;
use catchapp\Helpers\CurlHelper;
use catchapp\Http\Controllers\Controller;
use catchapp\Models\Club;
use catchapp\Models\ClubStream;
use catchapp\Models\DJ;
use catchapp\Models\Insight;
use catchapp\Models\StreamListeners;
use catchapp\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class WebStreamingController extends Controller
{
    public function createStream(Request $request)
    {
        $response = [];
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'club_id' => 'required|exists:clubs,id',
                'aspect_ratio_height' => 'required|integer',
                'aspect_ratio_width' => 'required|integer',
                'billing_mode' => 'required|in:pay_as_you_go',
                'broadcast_location' => 'required',
                'encoder' => 'required|in:wowza_gocoder,other_webrtc',
                'transcoder_type' => 'required',
            ]);
            if ($validator->fails()) {
                $response['error'] = true;
                $response['status_code'] = '401';
                $response['message'] = $validator->errors()->first();
            } else {
                $club = Club::query()->find($request->club_id);
                $club_id = $request->club_id;
                $aspect_ratio_height = $request->aspect_ratio_height;
                $aspect_ratio_width = $request->aspect_ratio_width;
                $aspect_ratio_height=135;
                $aspect_ratio_width=240;
                $name =preg_replace('/\s+/', '_', ('id_'.$club_id.'_n_'.$club->name));

                $data = array(
                    'live_stream' => array(
                        "aspect_ratio_height" => $aspect_ratio_height,
                        "aspect_ratio_width" => $aspect_ratio_width,
                        "billing_mode" => $request->billing_mode,
                        "broadcast_location" => $request->broadcast_location,
                        "encoder" => $request->encoder,
                        "name" => $name,
                        "transcoder_type" => 'transcoded',
                        "low_latency" => true,
                        "player_responsive" => true,
                    ),
                );

                $club_stream = ClubStream::query()->where('club_id','=', $club_id)->first();
                if ($club_stream)
                {
                    $stream_id = $club_stream->stream_id;
                    /** GENERATE A CONNECTION CODE THEN RETURN STREAM DETAILS */
                    $url = "https://api.cloud.wowza.com/api/v1.3/live_streams/$stream_id/regenerate_connection_code";
                    $method = "PUT";
                    $response = CurlHelper::CurlCommand($url, $method, '');


                    if ($response['error'] == false){
                        /** FETCH THE OLD STREAM IF THERE IS ANY OLD STREAM */

                        $url = "https://api.cloud.wowza.com/api/v1.3/live_streams/$stream_id";
                        $method = "GET";
                        $response = CurlHelper::CurlCommand($url, $method, '');
                        $r = json_decode($response['data']);
                        if ($r->live_stream) {
                            $club_stream->stream_id = $r->live_stream->id;
                            $club_stream->stream_url = $r->live_stream->player_hls_playback_url;
                            $club_stream->connection_code = $r->live_stream->connection_code;
                            $club_stream->save();
                        }
                    }
                }
                else
                {
                    /** CREATE A STREAM IF THERE IS NO OLD STREAM */

                    $url = "https://api.cloud.wowza.com/api/v1.3/live_streams";
                    $method = "POST";
                    $response = CurlHelper::CurlCommand($url, $method, $data);
                    $r = json_decode($response['data']);
                    if ($r->live_stream->id != null && $r->live_stream->id != '') {
                        $club_stream = new ClubStream();
                        $club_stream->club_id = $club_id;
                        $club_stream->stream_id = $r->live_stream->id;
                        $club_stream->stream_url = $r->live_stream->player_hls_playback_url;
                        $club_stream->connection_code = $r->live_stream->connection_code;
                        $club_stream->save();
                    }
                }
                if ($response['error'] == false){
                    return json_decode($response['data'], 200);
                }
                else
                {

                    $err_response=[
                        'error' => true,
                        'message' => $response['data']
                    ];
                    return json_encode($err_response, 200);
                }

                DB::commit();
                $response['error'] = false;
                $response['status_code'] = '200';
                $response['message'] = 'DJ Clubs';
                $response['clubs'] = $assigned_clubs;
                $response['recent_clubs'] = $recent_clubs;
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $response['error'] = true;
            $response['status_code'] = '400';
            $response['message'] = $e->getMessage();
        } finally {
            return response()->json($response);
        }
    }
    public function fetchAllStreams()
    {
        $url = "https://api.cloud.wowza.com/api/v1.3/live_streams";
        $method = "GET";
        $response = CurlHelper::CurlCommand($url, $method, '');

        if ($response['error'] == false){
            return json_decode($response['data'], 200);
        }
        else
        {
            $err_response=[
                'error' => true,
                'message' => $response['data']
            ];
            return json_encode($err_response, 200);
        }
    }
    public function fetchStream(Request $request)
    {
        $stream_id = $request->input('stream_id');
        if (empty($stream_id)) {
            $response = [
                'error' => true,
                'message' => 'Please, Fill stream_id (required field).'
            ];
            return json_encode($response, 200);
        }
        $url = "https://api.cloud.wowza.com/api/v1.3/live_streams/$stream_id";
        $method = "GET";
        $response = CurlHelper::CurlCommand($url, $method, '');
        if ($response['error'] == false){
            return json_decode($response['data'], 200);
        }
        else
        {
            $err_response=[
                'error' => true,
                'message' => $response['data']
            ];
            return json_encode($err_response, 200);
        }    }
    public function deleteStream(Request $request)
    {
        $stream_id = $request->input('stream_id');
        if (empty($stream_id)) {
            $response = [
                'error' => true,
                'message' => 'Please, Fill stream_id (required field).'
            ];
            return json_encode($response, 200);
        }
        $url = "https://api.cloud.wowza.com/api/v1.3/live_streams/$stream_id";
        $method = "DELETE";
        $response = CurlHelper::CurlCommand($url, $method, '');
        if ($response['error'] == false)
        {
            return json_decode($response['data'], 200);
        }
        else
        {
            $err_response=[
                'error' => true,
                'message' => $response['data']
            ];
            return json_encode($err_response, 200);
        }
    }
    public function updateStream(Request $request)
    {
        $stream_id = $request->input('stream_id');
        $name  = $request->input('name');
        $aspect_ratio_width   = $request->input('aspect_ratio_width');
        $aspect_ratio_height   = $request->input( 'aspect_ratio_height' );
        if (empty($stream_id)) {
            $response = [
                'error' => true,
                'message' => 'Please, Fill stream_id (required field).'
            ];
            return json_encode($response, 200);
        }
        if (empty($aspect_ratio_height)) {
            $aspect_ratio_height ='1080';
        }
        if (empty($aspect_ratio_width)) {
            $aspect_ratio_width='1920';
        }
        if (empty($name)) {
            $response = [
                'error' => true,
                'message' => 'Please, Fill name (required field).'
            ];
            return json_encode($response, 200);
        }

        $encoder ='wowza_gocoder';
        $data = array(
            'live_stream' => array(
                "encoder" => $encoder,
                "name" => $name,
                "aspect_ratio_height" => $aspect_ratio_height,
                "aspect_ratio_width" => $aspect_ratio_width,
            ),
        );

        $url = "https://api.cloud.wowza.com/api/v1.3/live_streams/$stream_id";
        $method = "PATCH";
        $response = CurlHelper::CurlCommand($url, $method, $data);

        if ($response['error'] == false){
            return json_decode($response['data'], 200);
        }
        else
        {
            $err_response=[
                'error' => true,
                'message' => $response['data']
            ];
            return json_encode($err_response, 200);
        }    }
    public function startStream(Request $request)
    {
        $stream_id = $request->input('stream_id');
        if (empty($stream_id)) {
            $response = [
                'error' => true,
                'message' => 'Please, Fill stream_id (required field).'
            ];
            return json_encode($response, 200);
        }
        $url = "https://api.cloud.wowza.com/api/v1.3/live_streams/$stream_id/start/";
        $method = "PUT";
        $response = CurlHelper::CurlCommand($url, $method, '');
        if ($response['error'] == false){
            return json_decode($response['data'], 200);
        }
        else
        {
            $err_response=[
                'error' => true,
                'message' => $response['data']
            ];
            return json_encode($err_response, 200);
        }
    }
    public function generateCode(Request $request)
    {
        $stream_id = $request->input('stream_id');
        if (empty($stream_id)) {
            $response = [
                'error' => true,
                'message' => 'Please, Fill stream_id (required field).'
            ];
            return json_encode($response, 200);
        }
        $url = "https://api.cloud.wowza.com/api/v1.3/live_streams/$stream_id/regenerate_connection_code";
        $method = "PUT";
        $response = CurlHelper::CurlCommand($url, $method, '');
        if ($response['error'] == false){
            return json_decode($response['data'], 200);
        }
        else
        {
            $err_response=[
                'error' => true,
                'message' => $response['data']
            ];
            return json_encode($err_response, 200);
        }
    }
    public function stopStream(Request $request)
    {
        $stream_id = $request->input('stream_id');

        if (empty($stream_id)) {
            $response = [
                'error' => true,
                'message' => 'Please, Fill stream_id (required field).'
            ];
            return json_encode($response, 200);
        }
        $club_stream = ClubStream::query()->where('stream_id','=', $stream_id)->first();
        if (empty($club_stream))
        {
            $response =[
                'error' => true,
                'message' => 'No stream exists for this club.'
            ];
            return json_encode($response, 200);
        }

        $url = "https://api.cloud.wowza.com/api/v1.3/live_streams/$stream_id/stop";
        $method = "PUT";
        $response = CurlHelper::CurlCommand($url, $method, '');
        if ($response['error'] == false){

            if ($club_stream)
            {
                $club_stream->female_listeners=0;
                $club_stream->male_listeners=0;
                $club_stream->traffic='Slow';
                $club_stream->updated_by_dj=0;
                $club_stream->save();

                // delete previous listeners logs
                $fetched_logs = StreamListeners::query()->where('club_stream_id','=',$club_stream->stream_id)->delete();
//                if ($fetched_logs->count()>0)
//                {
//                    foreach ($fetched_logs as $log)
//                    {
//                        $log->delete();
//                    }
//                }
            }

            return json_decode($response['data'], 200);
        }
        else
        {
            $err_response=[
                'error' => true,
                'message' => $response['data']
            ];
            return json_encode($err_response, 200);
        }    }
    public function resetStream(Request $request)
    {
        $stream_id = $request->input('stream_id');
        if (empty($stream_id)) {
            $response = [
                'error' => true,
                'message' => 'Please, Fill stream_id (required field).'
            ];
            return json_encode($response, 200);
        }
        $url = "https://api.cloud.wowza.com/api/v1.3/live_streams/$stream_id/reset";
        $method = "PUT";
        $response = CurlHelper::CurlCommand($url, $method, '');
        if ($response['error'] == false){
            return json_decode($response['data'], 200);
        }
        else
        {
            $err_response=[
                'error' => true,
                'message' => $response['data']
            ];
            return json_encode($err_response, 200);
        }    }
    public function regenerateConnectionCode(Request $request)
    {
        $stream_id = $request->input('stream_id');
        if (empty($stream_id)) {
            $response = [
                'error' => true,
                'message' => 'Please, Fill stream_id (required field).'
            ];
            return json_encode($response, 200);
        }
        $url = "https://api.cloud.wowza.com/api/v1.3/live_streams/$stream_id/regenerate_connection_code";
        $method = "PUT";
        $response = CurlHelper::CurlCommand($url, $method, '');
        if ($response['error'] == false){
            return json_decode($response['data'], 200);
        }
        else
        {
            $err_response=[
                'error' => true,
                'message' => $response['data']
            ];
            return json_encode($err_response, 200);
        }    }
    public function fetchThumbnail(Request $request)
    {
        $stream_id = $request->input('stream_id');
        if (empty($stream_id)) {
            $response = [
                'error' => true,
                'message' => 'Please, Fill stream_id (required field).'
            ];
            return json_encode($response, 200);
        }
        $url = "https://api.cloud.wowza.com/api/v1.3/live_streams/$stream_id/thumbnail_url";
        $method = "GET";
        $response = CurlHelper::CurlCommand($url, $method, '');
        if ($response['error'] == false){
            return json_decode($response['data'], 200);
        }
        else
        {
            $err_response=[
                'error' => true,
                'message' => $response['data']
            ];
            return json_encode($err_response, 200);
        }    }
    public function fetchLStreamState(Request $request)
    {
        $stream_id = $request->input('stream_id');
        if (empty($stream_id)) {
            $response = [
                'error' => true,
                'message' => 'Please, Fill stream_id (required field).'
            ];
            return json_encode($response, 200);
        }
        $url = "https://api.cloud.wowza.com/api/v1.3/live_streams/$stream_id/state";
        $method = "GET";
        $response = CurlHelper::CurlCommand($url, $method, '');
        if ($response['error'] == false){
            return json_decode($response['data'], 200);
        }
        else
        {
            $err_response=[
                'error' => true,
                'message' => $response['data']
            ];
            return json_encode($err_response, 200);
        }    }
    public function fetchLStreamMetrics(Request $request)
    {
        $stream_id = $request->input('stream_id');
        if (empty($stream_id)) {
            $response = [
                'error' => true,
                'message' => 'Please, Fill stream_id (required field).'
            ];
            return json_encode($response, 200);
        }
        $url = "https://api.cloud.wowza.com/api/v1.3/live_streams/$stream_id/stats";
        $method = "GET";
        $response = CurlHelper::CurlCommand($url, $method, '');
        if ($response['error'] == false){
            return json_decode($response['data'], 200);
        }
        else
        {
            $err_response=[
                'error' => true,
                'message' => $response['data']
            ];
            return json_encode($err_response, 200);
        }
    }
    public function logStream(Request $request)
    {
        $female_count = $request->input('female_count');
        $male_count = $request->input('male_count');
        $traffic = $request->input('traffic');
        $start_time = $request->input('start_time');
        $club_id = $request->input('club_id');
        $dj_id = $request->input('dj_id');
        if (empty($club_id))
        {
            $response =[
                'error' => true,
                'message' => 'Please send stream club_id. (required field).'
            ];
            return json_encode($response, 200);
        }
        $club = Club::query()->find($club_id);
        if (empty($club))
        {
            $response =[
                'error' => true,
                'message' => 'Please send a valid club_id.'
            ];
            return json_encode($response, 200);
        }

        if (empty($dj_id))
        {
            $response =[
                'error' => true,
                'message' => 'Please send dj_id. (required field).'
            ];
            return json_encode($response, 200);
        }
        $dj = DJ::query()->find($dj_id);
        if (empty($dj))
        {
            $response =[
                'error' => true,
                'message' => 'Please send a valid dj_id.'
            ];
            return json_encode($response, 200);
        }

        if ($club)
        {
            $club_stream = ClubStream::query()->where('club_id','=', $club_id)->first();
            if (empty($club_stream))
            {
                $response =[
                    'error' => true,
                    'message' => 'No stream exists for this club.'
                ];
                return json_encode($response, 200);
            }
        }

        if (empty($start_time))
        {
            $response =[
                'error' => true,
                'message' => 'Please send stream start time in timestamp format. (required field).'
            ];
            return json_encode($response, 200);
        }
        if (empty($female_count) && $female_count!=0)
        {
            $response =[
                'error' => true,
                'message' => 'Please send female_count. (required field).'
            ];
            return json_encode($response, 200);
        }
        if (empty($male_count)&& $male_count!=0)
        {
            $response =[
                'error' => true,
                'message' => 'Please send male_count. (required field).'
            ];
            return json_encode($response, 200);
        }
        if (empty($traffic))
        {
            $response =[
                'error' => true,
                'message' => 'Please send traffic. (required field).'
            ];
            return json_encode($response, 200);
        }
        else
        {
            $arr_traffic = array("Slow", "Normal", "Hype");

            if (!(in_array($traffic, $arr_traffic)))
            {
                $response =[
                    'error' => true,
                    'message' => 'Please send valid traffic value one among (\'Slow\',\'Normal\',\'Hype\').'
                ];
                return json_encode($response, 200);
            }
        }
        if (isset($club_stream))
        {

//            $total = $male_count+$female_count;

//            $insight = Insight::query()->first();
//
//            if ($total >= $insight->hype_count)
//            {
//                $traffic = 'Hype';
//            }
//            if ($total < $insight->hype_count && $total > $insight->slow_count)
//            {
//                $traffic = 'Normal';
//            }
//            if ($total <= $insight->slow_count)
//            {
//                $traffic = 'Slow';
//            }
            if(empty($female_count))
            {
                $female_count=0;
            }
            if(empty($male_count))
            {
                $male_count=0;
            }

            $club_stream->stream_time =Carbon::createFromTimestamp($start_time)->toDateTimeString();
            ;
            $club_stream->female_listeners = $female_count;
            $club_stream->male_listeners = $male_count;
            $club_stream->traffic = $traffic;
            $club_stream->updated_by_dj = $dj_id;


            $club_stream->save();
            $response =[
                'error' => false,
                'message' => 'Club\'s streaming detail logged successfully.'
            ];
            return json_encode($response, 200);
        }
        else
        {
            $response =[
                'error' => true,
                'message' => 'No stream exists for this club.'
            ];
            return json_encode($response, 200);
        }
    }
    public function logStreamUser(Request $request)
    {
        $user_id = $request->input('user_id');
        $club_id = $request->input('club_id');
        $device_token = $request->input('device_token');
        if (empty($device_token))
        {
            $response =[
                'error' => true,
                'message' => 'Please send device_token. (required field).'
            ];
            return json_encode($response, 200);
        }
        if (empty($club_id))
        {
            $response =[
                'error' => true,
                'message' => 'Please send stream club_id. (required field).'
            ];
            return json_encode($response, 200);
        }
        $club = Club::query()->find($club_id);
        if (empty($club))
        {
            $response =[
                'error' => true,
                'message' => 'Please send a valid club_id.'
            ];
            return json_encode($response, 200);
        }

        if ($club)
        {
            $club_stream = ClubStream::query()->where('club_id','=', $club_id)->first();
            if (empty($club_stream))
            {
                $response =[
                    'error' => true,
                    'message' => 'No stream exists for this club.'
                ];
                return json_encode($response, 200);
            }
        }

        if (empty($user_id))
        {
            $response =[
                'error' => true,
                'message' => 'Please send user_id. (required field).'
            ];
            return json_encode($response, 200);
        }
        $user = User::query()->find($user_id);
        if (empty($user) || $user->deleted_at != null)
        {
            $response =[
                'error' => true,
                'message' => 'Please provide a valid user id as no user exists with this user id. (required field).'
            ];
            return json_encode($response, 200);
        }

        if (isset($user) && $user->deleted_at == null)
        {
            $fetch_log = StreamListeners::query()->where('user_id','=',$user_id)->where('club_stream_id','=',$club_stream->stream_id)->get();
            if ($fetch_log->count()>0)
            {
                foreach ($fetch_log as $log)
                {
                    $log->delete();
                }
            }
            $new_log = new StreamListeners();
            $new_log->user_id = $user_id;
            $new_log->club_stream_id = $club_stream->stream_id;
            $new_log->device_token = $device_token;
            $new_log->save();

            if ($user->gender == 'female')
            {
                $club_stream->female_listeners++;
            }
            else
            {
                $club_stream->male_listeners++;
            }
            $club_stream->save();
            $response =[
                'error' => false,
                'message' => 'Listener count logged successfully.'
            ];
            return json_encode($response, 200);
        }
        else
        {
            $response =[
                'error' => true,
                'message' => 'No stream exists for this club.'
            ];
            return json_encode($response, 200);
        }
    }
    public function logLeftUser(Request $request)
    {
        $user_id = $request->input('user_id');
        $club_id = $request->input('club_id');
        if (empty($club_id))
        {
            $response =[
                'error' => true,
                'message' => 'Please send stream club_id. (required field).'
            ];
            return json_encode($response, 200);
        }
        $club = Club::query()->find($club_id);
        if (empty($club))
        {
            $response =[
                'error' => true,
                'message' => 'Please send a valid club_id.'
            ];
            return json_encode($response, 200);
        }

        if ($club)
        {
            $club_stream = ClubStream::query()->where('club_id','=', $club_id)->first();
            if (empty($club_stream))
            {
                $response =[
                    'error' => true,
                    'message' => 'No stream exists for this club.'
                ];
                return json_encode($response, 200);
            }
        }

        if (empty($user_id))
        {
            $response =[
                'error' => true,
                'message' => 'Please send user_id. (required field).'
            ];
            return json_encode($response, 200);
        }
        $user = User::query()->find($user_id);
        if (empty($user) || $user->deleted_at != null)
        {
            $response =[
                'error' => true,
                'message' => 'Please provide a valid user id as no user exists with this user id. (required field).'
            ];
            return json_encode($response, 200);
        }

        if (isset($user) && $user->deleted_at == null)
        {

            $fetch_log = StreamListeners::query()->where('user_id','=',$user_id)->where('club_stream_id','=',$club_stream->stream_id)->first();
            if ($fetch_log)
            {
                $fetch_log->delete();
            }

            if ($user->gender == 'female')
            {
                if ($club_stream->female_listeners>0) {
                    $club_stream->female_listeners--;
                }
            }
            else
            {
                if ($club_stream->male_listeners>0) {
                    $club_stream->male_listeners--;
                }
            }
            $club_stream->save();
            $response =[
                'error' => false,
                'message' => 'Listener count logged successfully.'
            ];
            return json_encode($response, 200);
        }
        else
        {
            $response =[
                'error' => true,
                'message' => 'No stream exists for this club.'
            ];
            return json_encode($response, 200);
        }
    }
}
