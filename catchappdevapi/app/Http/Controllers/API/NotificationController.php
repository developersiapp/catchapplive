<?php

namespace catchapp\Http\Controllers\API;

use catchapp\Http\Controllers\Controller;
use catchapp\Models\City;
use catchapp\Models\Club;
use catchapp\Models\ClubStream;
use catchapp\Models\DJ;
use catchapp\Models\Feedback;
use catchapp\Models\StreamListeners;
use catchapp\Models\User;
use catchapp\Models\UserDevice;
use catchapp\Models\UserListenedClubs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use function GuzzleHttp\json_encode;


class NotificationController extends Controller
{
    public function userClubLog(Request $request)
    {
        $user_id = $request->input('user_id');
        $club_id = $request->input('club_id');
        if (empty($user_id)) {
            $response = [
                'error' => true,
                'message' => "Please enter user_id (required field).",
            ];
            return json_encode($response, 200);

        }

        if (empty($club_id)) {
            $response = [
                'error' => true,
                'message' => "Please enter club_id (required field).",
            ];
            return json_encode($response, 200);

        }

        $log = UserListenedClubs::query()->where('user_id', '=', $user_id)->where('club_id', '=', $club_id)->first();
        if (!$log) {
//            UserListenedClubs::create($request->all());
            $log = new UserListenedClubs();
            $log->club_id = $club_id;
            $log->user_id = $user_id;
            $log->save();
            $response = [
                'error' => false,
                'message' => "User's listened club is logged successfully.",
            ];
            return json_encode($response, 200);

        }
        $response = [
            'error' => false,
            'message' => "Club is already added to user's listened clubs.",
        ];
        return json_encode($response, 200);
    }

    public function sendUserNotifications(Request $request)
    {
        $club_id = $request->input('club_id');
        if (empty($club_id)) {
            $response = [
                'error' => true,
                'message' => 'Please, send club_id (required field).'
            ];
            return json_encode($response, 200);
        }
        $club = Club::query()->find($club_id);
        if ($club) {

            $club_stream_detail = ClubStream::query()->where('club_id','=', $club_id)->first();

            if ($club->deleted_at == null) {
                $user_ids = UserListenedClubs::query()->where('club_id', '=', $club_id)->pluck('user_id');
                $users = User::query()->whereIn('id', $user_ids)
                    ->where('get_notification','=', 1)
                    ->get();

                $clubCity = City::query()->find($club->city)->pluck('name');
                $nearByUsers = User::query()->where('location','=', $clubCity)
                    ->whereNotIn('id', $user_ids)
                    ->get();
                if ($users->count() > 0 || $nearByUsers->count()>0) {
                    $count = 0;
                    if ($users->count()>0) {
                        foreach ($users as $user) {
                            if ($user->device_token != null && $user->device_token != 'ABCDEF') {
                                $token = $user->device_token;
                                $device_type = $user->device_type;
                                $fullname = $user->first_name . ' ' . $user->last_name;
                                $data = [];
                                $data['title'] = "Recommended for You";
                                $data['desc'] = "Hey $fullname! $club->name is live again. Listen now.";
                                $data['club_id'] = $club_id;

                                $data['desc'] = "Hey $fullname! $club->name is live again. Listen now.";
                                $data['club_id'] = $club_id;

                                $notification=array(
                                    'title' => "Recommended for You",
                                    'body' => "Hey $fullname! $club->name is live again. Listen now.",
                                    'club_id' => $club_id,
                                    'type' => 'club_live'
                                );

                                if ($device_type==1) {
                                    $message = $this->iOS($data, $token, $club_id);
                                }
                                else
                                {
                                    $message = $this->SendNotificationAndriod($data, $token);
                                }
                                if ($message == true) {
                                    $count++;
                                }

                                $ios_user_devices = UserDevice::query()
                                    ->where('user_id', '=', $user->id)
                                    ->where('device_type', '=', 1)
                                    ->pluck('device_token');
                                if ($ios_user_devices->count() > 0) {
                                    foreach ($ios_user_devices as $ios_user_device) {
                                        if ($ios_user_devices != null && $ios_user_devices != $token) {
                                            $message = $this->iOS($data, $ios_user_devices, $club_id);
                                            if ($message == true) {
                                                $count++;
                                            }
                                        }
                                    }
                                }

                                $android_user_devices = UserDevice::query()
                                    ->where('user_id', '=', $user->id)
                                    ->where('device_type', '=', 2)
                                    ->pluck('device_token');
                                if ($android_user_devices->count() > 0) {
                                    foreach ($android_user_devices as $android_user_device) {
                                        if (!empty($android_user_device) && $android_user_devices != $token) {
                                            $message = $this->SendNotificationAndriod($notification, $android_user_devices);
                                            if ($message == true) {
                                                $count++;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if ($nearByUsers->count()>0) {
                        foreach ($nearByUsers as $nearByUser) {
                            if ($nearByUser->device_token != null &&  $nearByUser->device_token != 'ABCDEF') {
                                $token = $nearByUser->device_token;
                                $device_type = $nearByUser->device_type;
                                $fullname = $nearByUser->first_name . ' ' . $nearByUser->last_name;
                                $data = [];
                                $data['title'] = "Recommended for You";
                                $data['desc'] = "Hey $fullname! A Club '$club->name' is live in your city. Listen to this club before it ends.";

                                $notification=array(
                                    'title' => "Recommended for You",
                                    'body' => "Hey $fullname! A Club '$club->name' is live in your city. Listen to this club before it ends.",
                                    'club_id' => $club_id,
                                    'type' => 'club_live'
                                );

                                if ($device_type==1) {
                                    $message = $this->iOS($data, $token, $club_id);
                                }
                                else
                                {
                                    $message = $this->SendNotificationAndriod($notification, $token);
                                }
                                if ($message == true) {
                                    $count++;
                                }
                                $ios_user_devices = UserDevice::query()
                                    ->where('user_id', '=', $nearByUser->id)
                                    ->where('device_type', '=', 1)
                                    ->pluck('device_token');
                                if ($ios_user_devices->count() > 0) {
                                    foreach ($ios_user_devices as $ios_user_device) {
                                        if ($ios_user_device != null && $ios_user_device != $token) {
                                            $message = $this->iOS($data, $ios_user_device, $club_id);
                                            if ($message == true) {
                                                $count++;
                                            }
                                        }
                                    }
                                }

                                $android_user_devices = UserDevice::query()
                                    ->where('user_id', '=', $nearByUser->id)
                                    ->where('device_type', '=', 2)
                                    ->pluck('device_token');
                                if ($android_user_devices->count() > 0) {
                                    foreach ($android_user_devices as $android_user_device) {
                                        if ($android_user_device != null && $android_user_device != $token) {
                                            $message = $this->SendNotificationAndriod($notification, $android_user_device);
                                            if ($message == true) {
                                                $count++;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $response = [
                        'error' => false,
                        'message' => 'Notification has been sent to '.$count.' app users.',
                    ];
                    return json_encode($response, 200);
                } else {
                    $response = [
                        'error' => false,
                        'message' => 'No user found who is nearby this club or has listened to this club before',
                    ];
                    return json_encode($response, 200);
                }
            } else {
                $response = [
                    'error' => true,
                    'message' => 'Sorry, This club has been deleted by Super Admin. Contact Administration for more help!',
                ];
                return json_encode($response, 200);

            }
        } else {
            $response = [
                'error' => true,
                'message' => 'Sorry, We couldn\'t find any club with provided club_id',
            ];
            return json_encode($response, 200);
        }
    }

    public function sendStreamStoppedNotification(Request $request)
    {
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

        if (isset($club_stream) && $club_stream->deleted_at == null) {

            $fetched_listeners_ids = StreamListeners::query()->where('club_stream_id', '=', $club_stream->stream_id)->pluck('user_id');
            $query = User::query()
                    ->join('stream_listener_log', 'users.id', '=',
                        'stream_listener_log.user_id','inner')->select(['users.id as user_id',
                    'users.first_name',
                    'users.last_name',
                    'users.email',
                    'stream_listener_log.club_stream_id',
                    'stream_listener_log.device_token'])
            ;
            $fetchedListeners= $query
               ->whereIn('user_id', $fetched_listeners_ids)
               ->where('club_stream_id','=', $club_stream->stream_id)
                ->get();


            $count = 0;

            if ($fetchedListeners->count() > 0) {
                if ($fetchedListeners->count() > 0) {
                    foreach ($fetchedListeners as $user) {
                        $fullname = $user->first_name . ' ' . $user->last_name;
                        $data = [];
                        $data['title'] = "DJ Stopped Streaming!";
                        $data['desc'] = "Hey $fullname! $club->name . $dj->name has stopped live streaming now!";

                        $notification=array(
                            'title' => "DJ Stopped Streaming!",
                            'body' => "Hey $fullname! $club->name . $dj->name has stopped live streaming now!",
                            'type' => 'stopped_stream'
                        );

                        if ($user->device_token != null && $user->device_token !='' && $user->device_token !='ABCDEF' )
                        {
                            $token = $user->device_token;
                            $device_type = $user->device_type;
                            if ($device_type==1) {
                                $message = $this->iOSNotificationForStopStreaming($data, $token);
                            }
                            else
                            {
                                $message = $this->SendNotificationAndriod($data, $token);
                            }
                            if ($message == true) {
                                $count++;
                            }
                        }
                    }
                }
            }
            $response = [
                'error' => false,
                'message' => $count. ' listeners have been informed about streaming status successfully.'
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




    public function iOSNotificationForStopStreaming($data, $devicetoken)
    {
        $deviceToken = $devicetoken;
        $ctx = stream_context_create();
        $passphrase = "";
        // Cert.pem is your certificate file

        $development=false;
        if ($development) {
            $apns_url = 'gateway.sandbox.push.apple.com';
//             $apns_cert = config_path() . "/Cert.pem";
            $apns_cert = config_path() . "/apns_Certificates.pem";
        } else {
            $apns_url = 'gateway.push.apple.com';
            $apns_cert = config_path() . "/apns_Certificates.pem";

        }

        stream_context_set_option($ctx, 'ssl', 'local_cert', $apns_cert);
        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
        // Open a connection to the APNS server
        $fp = stream_socket_client(
            'ssl://'.$apns_url.':2195', $err,
            $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
        if (!$fp)
            exit("Failed to connect: $err $errstr" . PHP_EOL);
        // Create the payload body
        $body['aps'] = array(
            'alert' => array(
                'title' => $data['title'],
                'body' => $data['desc'],
            ),
            'sound' => '',
        );
        // Encode the payload as JSON
        $payload = json_encode($body);
        // Build the binary notification
        $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
        // Send it to the server
        $result = fwrite($fp, $msg, strlen($msg));

        // Close the connection to the server
        fclose($fp);
        if (!$result)
            return false;
        else
            return true;
    }


    public function iOS($data, $devicetoken, $club_id)
    {
        $deviceToken = $devicetoken;
        $development=false;
        $ctx = stream_context_create();
        $passphrase = "";

        if ($development) {
            $apns_url = 'gateway.sandbox.push.apple.com';
            $apns_cert = config_path() . "/apns_Certificates.pem";
        } else {
            $apns_url = 'gateway.push.apple.com';
            $apns_cert = config_path() . "/apns_Certificates.pem";
        }

        stream_context_set_option($ctx, 'ssl', 'local_cert', $apns_cert);
        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

        // Open a connection to the APNS server
        $fp = stream_socket_client('ssl://'.$apns_url.':2195', $err,
            $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
        if (!$fp)
            exit("Failed to connect: $err $errstr" . PHP_EOL);
        // Create the payload body
        $body['aps'] = array(
            'alert' => array(
                'title' => $data['title'],
                'body' => $data['desc'],
            ),
            'sound' => 'default',
            'club_id' => $club_id
        );
        // Encode the payload as JSON
        $payload = json_encode($body);
        // Build the binary notification
        $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
        // Send it to the server
        $result = fwrite($fp, $msg, strlen($msg));

        // Close the connection to the server
        fclose($fp);
        if (!$result)
            return false;
        else
            return true;
    }


    public function SendNotificationAndriod($deviceToken,$notification)
    {
        if (!defined('API_ACCESS_KEY')) define('API_ACCESS_KEY', 'AAAAXg_sn1s:APA91bHBrmEUN5QZ9VFOj5PTvd5L_Lh2IB-D6ZsLN8vrspqvgWWqvNyQkmn2jiZV6M6yndjTX7GD1tsIpmClSRYINwsjuiThU8T6krP81xjIPB9QWHbeFBj-w6m_T-aBdiTl4zytueWz');
        $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
        $notification['sound'] = 'default';
        $extraNotificationData = ["message" => $notification, "extradata" => 'Here'];
        $fcmNotification = [
            'to' => $deviceToken, //single token
            'notification' => $notification,
            'data' => $extraNotificationData
        ];
        $headers = [
            'Authorization: key=' . API_ACCESS_KEY,
            'Content-Type: application/json'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $fcmUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
        if (!$result)
            return false;
        else
            return true;
    }

    public static function pushNotifications($token, $message, $badge)
    {
        ini_set("display_errors", 1);

        $sound = 'default';
        $development = false;//make it false if it is not in development mode
        $passphrase = ' ';//your passphrase
        $payload = array();
        $payload['aps'] = array('alert' => $message, 'badge' => intval($badge), 'sound' => $sound, 'mutable-content' => 1);
        $payload = json_encode($payload);

        $apns_url = NULL;
        $apns_cert = NULL;
        $apns_port = 2195;
//        $apns_cert = env('APP_URL') . '/pem/CertificatesapsCatch.pem';
        // $apns_cert = config_path() . "/Cert.pem";
        $apns_cert = config_path() . "/apns_Certificates.pem";
        if ($development) {
            $apns_url = 'gateway.sandbox.push.apple.com';

            //   $apns_cert = dirname(Yii::app()->request->scriptFile).'/Certificates_Push_fixit.pem';
        } else {
            $apns_url = 'gateway.push.apple.com';

            //   $apns_cert = dirname(Yii::app()->request->scriptFile).'/Certificates_Push_fixit.pem';

        }
        $stream_context = stream_context_create();
        stream_context_set_option($stream_context, 'ssl', 'local_cert', $apns_cert);
        stream_context_set_option($stream_context, 'ssl', 'passphrase', $passphrase);

        $apns = stream_socket_client('ssl://' . $apns_url . ':' . $apns_port, $error, $error_string, 10, STREAM_CLIENT_CONNECT, $stream_context);
        $device_tokens = str_replace("<", "", $token);
        $device_tokens1 = str_replace(">", "", $device_tokens);
        $device_tokens2 = str_replace(' ', '', $device_tokens1);
        $device_tokens3 = str_replace('-', '', $device_tokens2);

        $apns_message = chr(0) . pack('n', 32) . pack('H*', $device_tokens3) . chr(0) . chr(strlen($payload)) . $payload;
        $msg = fwrite($apns, $apns_message);

        if (!$msg) {
            $ms = 'Message not delivered' . PHP_EOL;
        } else {
            $ms = 'Message successfully delivered' . PHP_EOL;
        }
        @socket_close($apns);
        @fclose($apns);
        return $ms;
    }

    public function saveFeedback(Request $request)
    {
        $user_id = $request->input('user_id');
        $name = $request->input('name');
        $email = $request->input('email');
        $message = $request->input('message');
        if (empty($user_id))
        {
            $response =[
                'error' => true,
                'message' => ' Please send user_id (required field).'
            ];
            return json_encode($response, 200);
        }
        else {
            $user = User::withTrashed()->find($user_id);
            if (!$user) {
                $response = [
                    'error' => true,
                    'message' => 'Sorry, Feedback won\'t be posted as no user exists with provided user_id.',
                ];
                return json_encode($response, 200);
            }
            if ($user && $user->deleted_at != null) {
                $response = [
                    'error' => true,
                    'message' => 'Sorry, Feedback won\'t be posted as user has been deleted by Super Admin. Contact administration for more help!',
                ];
                return json_encode($response, 200);
            }
        }
        if (empty($name))
        {
            $response =[
                'error' => true,
                'message' => ' Please send name (required field).'
            ];
            return json_encode($response, 200);
        }
        if (empty($email))
        {
            $response =[
                'error' => true,
                'message' => ' Please send email (required field).'
            ];
            return json_encode($response, 200);
        }
        if (empty($message))
        {
            $response =[
                'error' => true,
                'message' => ' Please send message (required field).'
            ];
            return json_encode($response, 200);
        }
        $feedback = new Feedback();
        $feedback->user_id = $user_id;
        $feedback->name = $name;
        $feedback->email = $email;
        $feedback->message = $message;
        $feedback->save();

        $response =[
            'error' => false,
            'message' => 'Feedback is sent successfully.'
        ];
        return json_encode($response, 200);
    }

    public function getNotifications(Request $request)
    {
        $user_id = $request->user_id;
        if (empty($user_id))
        {
            $response =[
                'error' => true,
                'message' => 'Please send user id (required field).'
            ];
            return json_encode($response, 200);
        }
        $user = User::query()->find($user_id);
        if (!$user){
            $response =[
                'error' => true,
                'message' => 'No user found with provided user id.'
            ];
            return json_encode($response, 200);
        }
        $user->get_notification = !$user->get_notification;
        $user->save();
        if ($user->get_notification== false)
        {
            $msg = 'Your Notifications are deactivated now.';
        }
        else{
            $msg = 'Your Notifications are activated now.';
        }
        $response =[
            'error' => false,
            'message' => $msg
        ];
        return json_encode($response, 200);

    }

    public function test(Request $request)
    {

        if (!($request->has('device_token')) ||   empty($request->device_token))
        {
            return json_encode('Please send device_token.', 200);
        }

        $data = [];
        $data['title'] = "CatchApp Test";
        $data['desc'] = "Believe in yourself!.";

        $deviceToken = $request->device_token;
        $development=false;
        $ctx = stream_context_create();
        $passphrase = "";

        if ($development) {
            $apns_url = 'gateway.sandbox.push.apple.com';
            $apns_cert = config_path() . "/apns_Certificates.pem";
        } else {
            $apns_url = 'gateway.push.apple.com';
            $apns_cert = config_path() . "/apns_Certificates.pem";
        }

        stream_context_set_option($ctx, 'ssl', 'local_cert', $apns_cert);
        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

        // Open a connection to the APNS server
        $fp = stream_socket_client('ssl://'.$apns_url.':2195', $err,
            $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
        if (!$fp)
            exit("Failed to connect: $err $errstr" . PHP_EOL);
        // Create the payload body
        $body['aps'] = array(
            'alert' => array(
                'title' => $data['title'],
                'body' => $data['desc'],
            ),
            'sound' => 'default',
        );
        // Encode the payload as JSON
        $payload = json_encode($body);
        // Build the binary notification
        $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
        // Send it to the server
        $result = fwrite($fp, $msg, strlen($msg));

        // Close the connection to the server
        fclose($fp);
        if (!$result)
            return json_encode('error',200);
        else
            return json_encode('working',200);
    }





    public function sendTest(Request $request)
    {
        $response = [];
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'token' => 'required'
            ]);

            if ($validator->fails()) {
                $response['success'] = false;
                $response['status_code'] = '401';
                $response['message'] = $validator->errors()->first();
            }
            else {

                $message=array(
                    'title' => "Hi, Have a good day.",
                    'body' => "Stay blessed"
                );
                $result = $this->SendNotificationAndriod($request->token, $message);

                $msg = 'Notification sent '. $result;

                $response['success'] = true;
                $response['status_code'] = '200';
                $response['message'] = $msg;
            }
        }
        catch (\Exception $e) {
            DB::rollBack();
            $response['success'] = false;
            $response['status_code'] = '400';
            $response['message'] = $e->getMessage();
        }
        finally {
            return response()->json($response);
        }
    }


}
