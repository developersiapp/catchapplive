<?php

namespace catchapp\Http\Controllers\API;

use catchapp\Helpers\CurlHelper;
use catchapp\Helpers\TokenHelper;
use Carbon\Carbon;
use catchapp\Helpers\MaskHelper;
use catchapp\Http\Controllers\Controller;
use catchapp\Mail\SendMailable;
use catchapp\Models\City;
use catchapp\Models\Club;
use catchapp\Models\ClubStream;
use catchapp\Models\ClubWebStream;
use catchapp\Models\Country;
use catchapp\Models\DJ;
use catchapp\Models\EmailConfiguration;
use catchapp\Models\EmailType;
use catchapp\Models\Insight;
use catchapp\Models\Page;
use catchapp\Models\Pivot_Dj_Club;
use catchapp\Models\RecentSearch;
use catchapp\Models\State;
use catchapp\Models\User;
use catchapp\Models\UserDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;


class UserProfileController extends Controller
{

    //      USER REGISTRATION

    public function userRegister(Request $request)
    {
        define("ENCRYPTION_KEY", "!@#$%^&*");
        $first_name = $request->input('first_name');
        $last_name = $request->input('last_name');
        $email = $request->input('email');
        $user_name = $request->input('user_name');
        $password = $request->input('password');
        $gender = $request->input('gender');
        $location = $request->input('location');
        $oauth_key = $request->input('oauth_key');
        $profile_image = $request->file('profile_image');
        $device_token='ABCDEF';
        if ($request->has('device_token') && !empty($request->device_token))
        {
            $device_token = $request->input('device_token');
        }
        $device_type =1;
        if ($request->has('device_type') && !empty($request->device_type))
        {
            $device_type= $request->device_type;
        }
        $register_type = $request->input('register_type');
        if (empty($first_name)) {
            $response = [
                'error' => true,
                'message' => 'Please fill first name required field.'
            ];
            return json_encode($response, 200);
        }
        if (empty($email)) {
            $response = [
                'error' => true,
                'message' => 'Please fill email required field.'
            ];
            return json_encode($response, 200);
        }
        if (empty($user_name)) {
            $response = [
                'error' => true,
                'message' => 'Please fill user name required field.So that, You can login by using your user name or email.'
            ];
            return json_encode($response, 200);
        }
        if (!empty($email)) {
            $user = User::query()->where('email', '=', $email)->first();
            if ($user) {
                if($request->has('client_id') && !empty($request->client_id))
                {
                    $user->client_id = $request->client_id;
                    $user->registeration_type = $request->register_type;
                    if ($request->has('gender') && $request->gender != null && $request->gender != '')
                    {
                        $user->gender = $gender;
                    }
                    $user->is_active = 1;
                    $logged_in = false;
                    if ($user->is_active==1)
                    {
                        $logged_in= true;
                    }

                    $get_notification = false;
                    if ($user->get_notification==1)
                    {
                        $get_notification= true;
                    }

                    //<---------------------- device token saving ------------->
                    $user_device = UserDevice::query()->where('user_id', '=', $user->id)
                        ->where('device_token', '=', $device_token)
                        ->where('device_type','=',$device_type)
                        ->first();

                    if (!$user_device && $device_token!='ABCDEF') {
                        $user_device = new UserDevice();
                        $user_device->user_id = $user->id;
                        $user_device->device_token = $device_token;
                        $user_device->device_type = $device_type;
                        $user_device->save();
                    }
                    $prev_device_token = User::query()->where('device_token','=', $device_token)->get();
                    if($prev_device_token->count()>0)
                    {
                        foreach ($prev_device_token as $prev_user)
                        {
                            $prev_user->device_token = '';
                            $prev_user->device_type = 1;
                            $prev_user->save();
                        }
                    }
                    if ($device_token!= 'ABCDEF') {
                        $user->device_token = $device_token;
                        $user->device_type = $device_type;
                    }
                    //<---------------------- device token saved ------------->

                    if ($request->has('profile_picture_url') && !empty($request->profile_picture_url))
                    {
                        $user->profile_picture_url = $request->profile_picture_url;
                    }
                    $user->save();
                    $response = [
                        'error' => false,
                        'message' => 'You\'re logged in successfully!',
                        'data' => [
                            'user_id' => $user->id,
                            'first_name' => $user->first_name,
                            'email' => $user->email,
                            'user_name' => $user->user_name,
                            'date_of_birth' => $user->birth_date,
                            'gender' => $user->gender,
                            'logged_in' => $logged_in,
                            'get_notification' => $get_notification,
                            'login_type' => (int)$user->registeration_type,
                            'client_id' => $user->client_id,
                            'location' => $user->location,
                            'profile_image' => ''
                        ]
                    ];
                    if (!empty($user->last_name) && $user->last_name!= null)
                    {
                        $response['data']['last_name'] = isset($user->last_name)?(!empty($user->last_name)?$user->last_name:''):'';
                    }

                    if (isset($device_token) && $device_token!= 'ABCDEF')
                    {
                        $response['data']['device_token'] = $device_token;
                    }
                    if (empty($user->profile_image)) {
                        if (!empty($user->profile_picture_url)) {
                            $response['data']['profile_image'] = $user->profile_picture_url;
                        }
                    }
                    else
                    {
                        $response['data']['profile_image'] = env('APP_URL') . '/uploads/users/' . $user->profile_image;
                    }
                    return json_encode($response, 200);
                }
                else
                {
                    $response = [
                        'error' => true,
                        'message' => 'Sorry, This email is already taken.Try another!'
                    ];
                    return json_encode($response, 200);
                }
            }

        }
        if (!empty($user_name)) {
            if (strpos($user_name, ' ') !== false) {
                $response = [
                    'error' => true,
                    'message' => 'User name can\'t contain blank space. Please try again with valid user name.'
                ];
                return json_encode($response, 200);
            }
            $user = User::query()->where('user_name', '=', $user_name)->first();
            if ($user) {
                $response = [
                    'error' => true,
                    'message' => 'Sorry, This user name is already taken.Try another!'
                ];
                return json_encode($response, 200);
            }
        }

        if ($request->has('date_of_birth') && $request->input('date_of_birth')!=' ' && $request->input('date_of_birth') != null) {
            $dob = Carbon::parse($request->input('date_of_birth'))->format('Y-m-d');
        }
        if ($request->has('gender') && $request->input('gender')!=' ' && $request->input('gender') != null) {
            $gender = $request->input('gender');
        }
        if ($request->hasFile('profile_image')) {
            $allowedMimeTypes = ['image/jpeg', 'image/gif', 'image/png', 'image/bmp', 'image/svg+xml'];
            $contentType = $profile_image->getMimeType();

            if (!in_array($contentType, $allowedMimeTypes)) {
                $response = [
                    'error' => true,
                    'message' => 'File format isn\'t supported for profile picture.'
                ];
                return json_encode($response, 200);
            }
        }
        if (empty($register_type)) {
            $response = [
                'error' => true,
                'message' => 'Please fill register type required field.'
            ];
            return json_encode($response, 200);
        }
        if (empty($password) && ($register_type==7 || $register_type==1)) {
            $response = [
                'error' => true,
                'message' => 'Please fill password required field.'
            ];
            return json_encode($response, 200);
        }
//        if (empty($device_token)) {
//            $response = [
//                'error' => true,
//                'message' => 'Please send device_token required field.'
//            ];
//            return json_encode($response, 200);
//        }

        if (($register_type != 1 && $register_type <= 5) && empty($oauth_key)) {
            $response = [
                'error' => true,
                'message' => 'Please send oauth key required field.'
            ];
            return json_encode($response, 200);
        }
        else {
            $filename = '';
            if ($request->hasFile('profile_image')) {
                $file_extension = $profile_image->getClientOriginalExtension();
                $filename = 'user-profile-photo-' . time() . '.' . $file_extension;
            }
            if ($register_type == 1) {
                $info = [
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'email' => $email,
                    'user_name' => $user_name,
                    'password' => encrypt($password, ENCRYPTION_KEY),
                    'registeration_type' => (int)$register_type,
                    'location' => $location,
                    'device_token' => $device_token,
                    'device_type' => $device_type,
                    'profile_image' => $filename
                ];

                if (isset($device_token) && $device_token!= 'ABCDEF')
                {
                    $info['device_token'] = $device_token;
                    $info['device_type'] = $device_type;
                }

                if (isset($dob)) {
                    $info['birth_date'] = $dob;
                }
                if (isset($gender)) {
                    $info['gender'] = $gender;
                }
                $newUser = User::create($info);
                if ($device_token != 'ABCDEF')
                {
                    $user_device = new UserDevice();
                    $user_device->user_id = $newUser->id;
                    $user_device->device_token = $device_token;
                    $user_device->device_type = $device_type;
                    $user_device->save();
                }

//                  SEND AN WELCOME EMAIL TO NEW USER
                $subject = 'Welcome to CatchApp!';
                $mail_to = $newUser->email;
                $content = 'Hi ' . $newUser->first_name .  isset($newUser->last_name)?(!empty($newUser->last_name)?$newUser->last_name:''):'' . ' (User) ! Welcome to CatchApp.';
                $type = EmailType::query()
                    ->join('email_addresses', 'email_types.id', '=',
                        'email_addresses.email_type', 'inner')->select('email_addresses.email_address as mail_from', 'email_types.*')
                    ->where('name', 'LIKE', '%' . 'new user' . '%')->first();

                $mail = new EmailConfiguration();
                if ($type) {
                    $mail->email_type = $type->id;
                    $mail->mail_from = $type->mail_from;
                } else {
                    $mail->email_type = 0;
                    $mail->mail_from = 'newuser@catchapp.com';
                }
                $mail->mail_to = $mail_to;
                $mail->mail_subject = $subject;
                $mail->mail_content = $content;
                $mail->is_sent = 0;
                $mail->save();

//                  SENDING MAIL
                if (isset($mail)) {
                    Mail::to($mail->mail_to)
                        ->queue(new SendMailable($mail));
                    // check for failures
                    if (Mail::failures()) {
                        return back()->with('error', 'Email is not sent.');
                    } else {
                        $mail->is_sent = 1;
                        $mail->save();
                    }
                }
//              SEND EMAIL

                if ($request->hasFile('profile_image')) {
                    $profile_image->move(base_path('public/uploads/users/'), $filename);
                    $prof_image = "";

                    $image_path = base_path('public/uploads/users/' . $filename);
                    if (file_exists($image_path)  && is_file($image_path)) {
                        $prof_image = env('APP_URL') . '/uploads/users/' . $filename;
                    }

                    $response = [
                        'error' => false,
                        'message' => 'User registered Successfully',
                        'data' => [
                            'user_id' => $newUser->id,
                            'first_name' => $first_name,
                            'email' => $email,
                            'user_name' => $user_name,
                            'date_of_birth' => $newUser->birth_date,
                            'gender' => $newUser->gender,
                            'profile_image' => $prof_image,
                            'registeration_type' => (int)$register_type,
                            'location' => $location,
                        ]
                    ];
                    if (!empty($newUser->last_name) && $newUser->last_name!= null)
                    {
                        $response['data']['last_name'] = isset($newUser->last_name)?(!empty($newUser->last_name)?$newUser->last_name:''):'';
                    }
                    if (isset($device_token) && $device_token!= 'ABCDEF')
                    {
                        $response['data']['device_token'] = $device_token;
                        $response['data']['device_type'] = $device_type;
                    }

                }
                else {
                    $response = [
                        'error' => false,
                        'message' => 'User registered Successfully',
                        'data' => [
                            'user_id' => $newUser->id,
                            'first_name' => $first_name,
                            'last_name' => $last_name,
                            'email' => $email,
                            'user_name' => $user_name,
                            'date_of_birth' => $newUser->birth_date,
                            'gender' => $newUser->gender,
                            'profile_image' => '',
                            'registeration_type' => (int)$register_type,
                            'location' => $location,
                        ]
                    ];
                    if (!empty($newUser->last_name) && $newUser->last_name!= null)
                    {
                        $response['data']['last_name'] = isset($newUser->last_name)?(!empty($newUser->last_name)?$newUser->last_name:''):'';
                    }
                    if (isset($device_token) && $device_token!= 'ABCDEF')
                    {
                        $response['data']['device_token'] = $device_token;
                    }
                }
                return json_encode($response, 200);
            }
            elseif ($register_type !=6 && $register_type !=1) {
                $client_id = $request->input('client_id');
                if (empty($client_id) && $register_type!=7) {
                    $response = [
                        'error' => true,
                        'message' => 'Please, Enter client_id (required field).'
                    ];
                    return json_encode($response, 200);
                }
                $infor = [
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'email' => $email,
                    'user_name' => $user_name,
                    'password' => encrypt($password, ENCRYPTION_KEY),
                    'registeration_type' => (int)$register_type,
                    'client_id' => $client_id,
                    'oauth_key' => $oauth_key,
                    'location' => $location,
                ];
                $prof_image="";
                if ($request->has('profile_picture_url') && !empty($request->profile_picture_url))
                {
                    $infor['profile_picture_url'] = $request->profile_picture_url;
                    $infor['profile_image'] = '';
                    $prof_image = $request->profile_picture_url;
                }
                if (!empty($filename))
                {
                    $infor['profile_image'] = $filename;
                    $infor['profile_picture_url'] = '';
                    $prof_image = env('APP_URL') . '/uploads/users/' . $filename;
                }
                $infor['device_type'] = 0;

                if ($device_token!= 'ABCDEF')
                {
                    $infor['device_token'] = $device_token;
                    $infor['device_type'] = $device_type;
                }
                if (isset($dob))
                {
                    $infor['birth_date']= $dob;
                }
                if (isset($last_name))
                {
                    $infor['last_name']= $last_name;
                }
                if (isset($gender))
                {
                    $infor['gender']= $gender;

                }

                $newUser = User::create($infor);
                if ($device_token!='ABCDEF') {
                    $user_device = new UserDevice();
                    $user_device->user_id = $newUser->id;
                    $user_device->device_token = $device_token;
                    $user_device->device_type = $device_type;
                    $user_device->save();
                }

                $response = [
                    'error' => false,
                    'message' => 'User registered Successfully',
                    'data' => [
                        'user_id' => $newUser->id,
                        'first_name' => $first_name,
                        'email' => $email,
                        'user_name' => $user_name,
                        'birth_date' => $newUser->birth_date,
                        'gender' => $newUser->gender,
                        'registeration_type' => (int)$register_type,
                        'profile_image' =>$prof_image,
                        'oauth_key' => $oauth_key,
                        'location' => $location,
                    ]
                ];
                if (!empty($newUser->last_name) && $newUser->last_name!= null)
                {
                    $response['data']['last_name'] = isset($newUser->last_name)?(!empty($newUser->last_name)?$newUser->last_name:''):'';
                }

                if (isset($device_token) && $device_token!= 'ABCDEF')
                {
                    $response['data']['device_token'] = $device_token;
                }
                return json_encode($response, 200);
            }
            else {
                $response = [
                    'error' => true,
                    'message' => 'Registration type isn\'t supported.'
                ];
                return json_encode($response, 200);
            }
        }
    }


//      USER UPDATE

    public function userUpdate(Request $request)
    {
        define("ENCRYPTION_KEY", "!@#$%^&*");

        $user_id = $request->input('user_id');
        $first_name = $request->input('first_name');
        $last_name = $request->input('last_name');
        $password = $request->input('password');
        $user_name = $request->input('user_name');
        $date_of_birth = $request->input('date_of_birth');
        $gender = $request->input('gender');
        $device_token=$request->input('device_token');
        $profile_image = $request->file('profile_image');
        $picture_url = $request->input('picture_url');
        $device_type =1;
        if ($request->has('device_type') && !empty($request->device_type))
        {
            $device_type= $request->device_type;
        }

        if (empty($user_id)) {
            $response = [
                'error' => true,
                'message' => 'Please fill user_id required field.'
            ];
            return json_encode($response, 200);
        }
        if ($request->hasFile('profile_image')) {
            $allowedMimeTypes = ['image/jpeg', 'image/gif', 'image/png', 'image/bmp', 'image/svg+xml'];
            $contentType = $profile_image->getMimeType();
            if (!in_array($contentType, $allowedMimeTypes)) {
                $response = [
                    'error' => true,
                    'message' => 'File format isn\'t supported for profile picture.'
                ];
                return json_encode($response, 200);
            }
        }
        $filename = '';
        $user = User::query()->find($user_id);

        if (!empty($user)) {
            if (!empty($first_name)) {
                $user->first_name = $first_name;
            }
            if (!empty($email)) {
                $response = [
                    'error' => true,
                    'message' => 'Sorry, You can\'t change your email address.'
                ];
                return json_encode($response, 200);
            }

            if (isset($last_name) && !empty($last_name)) {
                $user->last_name = $last_name;
            }
            else
            {
                $user->last_name = "";
            }
            if (!empty($user_name)) {
                if (strpos($user_name, ' ') !== false) {
                    $response = [
                        'error' => true,
                        'message' => 'User name can\'t contain blank space. Please try again with valid user name.'
                    ];
                    return json_encode($response, 200);
                }

                $user_exists = User::query()->where('user_name', '=', $user_name)->first();
                if ($user_exists && $user_exists->id != $user_id) {
                    $response = [
                        'error' => true,
                        'message' => 'User name is already taken. Please try another !'
                    ];
                    return json_encode($response, 200);
                }
                $user->user_name = $user_name;
            }
            if (!empty($password)) {
                $user->password = encrypt($password, ENCRYPTION_KEY);
            }
            if (!empty($date_of_birth)) {
                $user->birth_date = $date_of_birth;
            }
            if (!empty($gender)) {
                $user->gender = $gender;
            }

            if(!empty($device_token))
            {
                $prev_device_token = User::query()
                    ->where('device_token','=', $device_token)
                    ->where('device_type','=',$device_type)
                    ->get();
                if($prev_device_token->count()>0)
                {
                    foreach ($prev_device_token as $prev_user)
                    {
                        $prev_user->device_token = '';
                        $prev_user->save();
                    }
                }

                $user->device_token = $device_token;
                $user->device_type = $device_type;
                $user_device = UserDevice::query()
                    ->where('user_id', '=', $user->id)
                    ->where('device_token', '=', $device_token)
                    ->where('device_type', '=', $device_type)
                    ->first();
                if (!$user_device && $device_token!='ABCDEF') {
                    $user_device = new UserDevice();
                    $user_device->user_id = $user->id;
                    $user_device->device_token = $device_token;
                    $user_device->device_type = $device_type;
                    $user_device->save();
                }

            }


            // SAVE PROFILE IMAGE
            if ($request->hasFile('profile_image')) {

                if ($user->profile_image != '') {
                    $old_image = base_path('public/uploads/users/' . $user->profile_image);
                    if (is_file($old_image) && file_exists($old_image)) {
                        unlink($old_image);
                    }
                }
                $extension = $profile_image->getClientOriginalExtension();
                $filename = 'user-profile-photo-' . time() . '.' . $extension;
                $profile_image->move(base_path('public/uploads/users/'), $filename);
                $user->profile_image = $filename;
                $user->profile_picture_url ='';

            } else {
                if ((!empty($user->profile_image) || $user->profile_image != null || $user->profile_image != " ")
                    && (!($request->has('picture_url')) && empty($picture_url) )) {
                    $old_image = base_path('public/uploads/users/' . $user->profile_image);
                    if (is_file($old_image) && file_exists($old_image)) {
                        unlink($old_image);
                    }
                    $user->profile_image = " ";
                }
            }
            // END

            $user->is_active=1;
            $user->save();

            $logged_in = false;
            if ($user->is_active==1)
            {
                $logged_in= true;
            }
            $prof_image = "";

            if (!empty($user->profile_picture_url))
            {
                $prof_image= $user->profile_picture_url;
            }
            if (!empty($user->profile_image) || $user->profile_image!= null || $user->profile_image != " ") {

                $Profile_pic = base_path('public/uploads/users/' . $user->profile_image);
                if (is_file($Profile_pic) && file_exists($Profile_pic)) {
                    $prof_image = env('APP_URL') . '/uploads/users/' . $user->profile_image;
                }
                $response = [
                    'error' => false,
                    'message' => 'User details are updated successfully',
                    'data' => [
                        'user_id' => $user->id,
                        'first_name' => $user->first_name,
                        'last_name' => isset($user->last_name)?(!empty($user->last_name)?$user->last_name:''):'',
                        'email' => $user->email,
                        'user_name' => $user->user_name,
                        'date_of_birth' => $user->birth_date,
                        'gender' => $user->gender,
                        'logged_in' => $logged_in,
                        'profile_image' => $prof_image,
                        'device_token'=>$device_token,
                        'device_type'=> $device_type,
                        'location' => $user->location,
                    ]
                ];
            } else {
                $response = [
                    'error' => false,
                    'message' => 'User details are updated successfully',
                    'data' => [
                        'user_id' => $user->id,
                        'first_name' => $user->first_name,
                        'last_name' => isset($user->last_name)?(!empty($user->last_name)?$user->last_name:''):'',
                        'email' => $user->email,
                        'date_of_birth' => $user->birth_date,
                        'gender' => $user->gender,
                        'logged_in' => $logged_in,
                        'device_token'=> $device_token,
                        'device_type'=> $device_type,
                        'location' => $user->location,
                    ]
                ];
                if (!empty($user->profile_picture_url))
                {
                    $response['data']['profile_image'] = $user->profile_picture_url;
                }
                else
                {
                    $response['data']['profile_image'] = '';
                }
            }
            return json_encode($response, 200);
        } else {

            $response = [
                'error' => false,
                'message' => 'You provided an invalid user_id.'
            ];
            return json_encode($response, 200);

        }

    }

//      USER DETAILS

    public function userDetails(Request $request)
    {
        define("ENCRYPTION_KEY", "!@#$%^&*");
        $user_id = $request->input('user_id');
        if (empty($user_id)) {
            $response = [
                'error' => true,
                'message' => 'Please fill user_id required field.'
            ];
            return json_encode($response, 200);
        } else {
            $userdata = User::where(['id' => $user_id, 'deleted_at' => null])->first();
            if (!empty($userdata)) {
                $logged_in = false;
                if ($userdata->is_active==1)
                {
                    $logged_in= true;
                }
                $get_notification = false;
                if ($userdata->get_notification==1)
                {
                    $get_notification= true;
                }

                if (empty($userdata->profile_image) || $userdata->profile_image== null || $userdata->profile_image == " ") {
                    $response = [
                        'error' => false,
                        'message' => 'User Details.',
                        'data' => [
                            'first_name' => $userdata->first_name,
                            'last_name' => isset($userdata->last_name)?(!empty($userdata->last_name)?$userdata->last_name:''):'',
                            'email' => $userdata->email,
                            'user_name' => $userdata->user_name,
                            'date_of_birth' => $userdata->birth_date,
                            'gender' => $userdata->gender,
                            'logged_in' => $logged_in,
                            'get_notification' => $get_notification,
                            'location' => $userdata->location,
                        ],
                    ];
                    if (!empty($userdata->profile_picture_url))
                    {
                        $response['data']['profile_image'] = $userdata->profile_picture_url;
                    }
                    else
                    {
                        $response['data']['profile_image'] = '';
                    }
                    return json_encode($response, 200);

                } else {
                    $prof_image = '';
                    if (!empty($userdata->profile_picture_url))
                    {
                        $prof_image= $userdata->profile_picture_url;
                    }
                    if (!empty($userdata->profile_image) || $userdata->profile_image!= null || $userdata->profile_image != " ") {
                        $image_path = base_path('public/uploads/users/' . $userdata->profile_image);
                        if (file_exists($image_path) && is_file($image_path)) {
                            $prof_image = env('APP_URL') . '/uploads/users/' . $userdata->profile_image;
                        }
                    }

                    $response = [
                        'error' => false,
                        'message' => 'User Details.',
                        'data' => [
                            'first_name' => $userdata->first_name,
                            'last_name' => isset($userdata->last_name)?(!empty($userdata->last_name)?$userdata->last_name:''):'',
                            'email' => $userdata->email,
                            'user_name' => $userdata->user_name,
                            'date_of_birth' => $userdata->birth_date,
                            'gender' => $userdata->gender,
                            'logged_in' => $logged_in,
                            'get_notification' => $get_notification,
                            'location' => $userdata->location,
                            'profile_image' => $prof_image
                        ],
                    ];
                    return json_encode($response, 200);
                }
            }
            else {
                $response = [
                    'error' => true,
                    'message' => 'Invalid User_id .'
                ];
                return json_encode($response, 200);
            }
        }
    }

//      forgot password api

    public function userForgotPassword(Request $request)
    {
        define("ENCRYPTION_KEY", "!@#$%^&*");

        $email = $request->input('email');
        $user_name = $request->input('user_name');
        if (empty($email) && empty($user_name)) {
            $response = [
                'error' => true,
                'message' => 'Please enter email OR user name to proceed.'
            ];
            return json_encode($response, 200);
        }

        if (empty($email) && !empty($user_name)) {
            $user = User::query()->where('user_name', '=', $user_name)->first();
            if (!$user) {
                $response = [
                    'error' => true,
                    'message' => 'Sorry, We couldn\'t find any user with this user name!'
                ];
                return json_encode($response, 200);
            }
        }
        if (!empty($email) && empty($user_name)) {
            $user = User::query()->where('email', '=', $email)->first();
            if (!$user) {
                $response = [
                    'error' => true,
                    'message' => 'Sorry, We couldn\'t find any user with this email address!'
                ];
                return json_encode($response, 200);
            }
        }
        if (!empty($email) && !empty($user_name)) {
            $user = User::query()->where('email', '=', $email)->first();
            if (!$user) {
                $user = User::query()->where('user_name', '=', $user_name)->first();
                if (!$user) {
                    $response = [
                        'error' => true,
                        'message' => 'Sorry, We couldn\'t find any user with this email address!'
                    ];
                    return json_encode($response, 200);
                }
            }
        }

        if (isset($user)) {
            if (!empty($user)) {
                if ($user->deleted_at == null) {
                    $fullname = $user->first_name . ' ' . $user->last_name;
                    $user->remember_token =TokenHelper::alphaNumericTokenForUser(Str::random(15));
                    $user->save();
                    if($request->has('web') && $request->web == true)
                    {
                        $link = env('WEB_APP_URL') . '/reset-password/' . $user->remember_token;
                    }
                    else
                    {
                        $link = env('APP_URL') . '/user/reset-password/' . $user->remember_token;
                    }

                    $mail = new EmailConfiguration();
                    $mail->email_type = 0;
                    $mail->mail_from = 'developer.iapptechnologies@gmail.com';
                    $mail->mail_to = $user->email;
                    $mail->mail_subject = "[CatchApp] Password reset link";

                    $mail->mail_content = 'Hi ' . $fullname . '! Please visit this link to reset your CatchApp login password. <br><a href=' . $link . '>Click here!</a>';
                    $mail->is_sent = 0;
                    $mail->save();

                    // SENDING MAIL
                    if (isset($mail)) {
                        Mail::to($mail->mail_to)
                            ->queue(new SendMailable($mail));

                        // If EMAIL ISN'T SENT
                        if (Mail::failures()) {
                            $response = [
                                'error' => true,
                                'message' => 'Mail could\'nt be send.'
                            ];
                            return json_encode($response, 200);
                        } else {
                            $mail->is_sent = 1;
                            $mail->save();

                            $msg = "Password reset link is sent on ". MaskHelper::maskEmail($user->email).'. Check your inbox.';
                            $response = [
                                'error' => false,
                                'message' => $msg
                            ];
                            return json_encode($response, 200);
                        }
                    } else {
                        $response = [
                            'error' => false,
                            'message' => 'Sorry, Email couldn\'t be send on provided email address.'
                        ];
                        return json_encode($response, 200);
                    }
                }
                if ($user->deleted_at != null) {
                    $response = [
                        'error' => true,
                        'message' => 'Sorry, this user has been deleted by Super Admin so you can\'t recover the password!'
                    ];
                    return json_encode($response, 200);
                }
            } else {
                $response = [
                    'error' => true,
                    'message' => 'This user isn\'t registered with us!'

                ];

                return json_encode($response, 200);
            }
        } else {
            $response = [
                'error' => true,
                'message' => 'Sorry, We couldn\'t find any user with provided credentials!'
            ];
            return json_encode($response, 200);
        }
    }


//      update password for web

    public function updateUserPasswordForWeb(Request $request)
    {

        $response = [];
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'token' => 'required|exists:users,remember_token',
                'password' => 'required',
            ]);
            if ($validator->fails()) {
                $response['error']= true;
                $response['status_code'] = '401';
                $response['message'] = $validator->errors()->first();
            } else {
                define("ENCRYPTION_KEY", "!@#$%^&*");

                $token = $request->input('token');
                $password = $request->input('password');
                $user = User::query()->where('remember_token','=', $token)->first();
                if (!$user)
                {
                    throw new \Exception('Un-authorized attempt.');
                }

                $user->password = encrypt($password, ENCRYPTION_KEY);
                $user->remember_token =TokenHelper::alphaNumericTokenForUser(Str::random(15));
                $user->save();
                DB::commit();
                $response['error'] = false;
                $response['status_code'] = '200';
                $response['message'] = 'Password updated successfully.';
            }
        }
        catch (\Exception $e) {
            DB::rollBack();
            $response['error']= true;
            $response['status_code'] = '400';
            $response['message'] = $e->getMessage();
        } finally {
            return response()->json($response);
        }
    }



//      CLUB DETAIL API

    public function clubDetail(Request $request)
    {
        define("ENCRYPTION_KEY", "!@#$%^&*");
        $club_id = $request->input('club_id');
        if (empty($club_id)) {
            $response = [
                'error' => true,
                'message' => 'Please fill club_id required field.'
            ];
            return json_encode($response, 200);
        } else {
            $club = Club::query()->find($club_id);
            if (!empty($club)) {
                $dj_names=[];
                $dj_ids = Pivot_Dj_Club::query()->where('club_id','=', $club_id)->pluck('dj_id');
                $djs= DJ::query()->whereIn('id', $dj_ids)->get();
                foreach ($djs as $dj){
                    array_push($dj_names, $dj->name);
                }

                $prof_image = "";
                if (!empty($club->profile_image) || $club->profile_image!= null || $club->profile_image != " ") {

                    $image_path = base_path('public/uploads/clubs/' . $club->profile_image);
                    if (file_exists($image_path)&& is_file($image_path) ) {
                        $prof_image = env('APP_URL') . '/uploads/clubs/' . $club->profile_image;
                    }
                }
                $c_city = City::query()->find($club->city);
                $c_state = State::query()->find($club->state);
                $c_country = Country::query()->find($club->country);
                $location = $club->street_address;
                $location .= isset($c_city) ? ', ' . $c_city->name : ' ';
                $location .= isset($c_state) ? ', ' . $c_state->name : ' ';
                $location .= isset($c_country) ? ', ' . $c_country->name : ' ';

                $data =[
                    'club_id' => $club_id,
                    'location' => $location,
                    'club_name' => $club->name,
                    'club_image' => $prof_image,
                    'club_dj' => $dj_names,
                    'web_streaming'=>0
                ];

                $total = 0;
                $male =0;
                $female=0;
                $traffic='ns';

                /****** Add club stream details *******/
                $club_stream_detail = ClubStream::query()->where('club_id','=', $club_id)->first();

                if (!empty($club_stream_detail))
                {
                    $data['live_stream_id'] =$club_stream_detail->stream_id;
                    $data['live_stream_connection_code']=isset($club_stream_detail)?$club_stream_detail->connection_code:'';
                    $data['live_stream_url'] = $club_stream_detail->stream_url;
                    if ($club_stream_detail->updated_by_dj!= '')
                    {
                        $dj = DJ::query()->find($club_stream_detail->updated_by_dj);
                        if ($dj)
                        {
                            $dj_name= $dj->name;
                            $data['dj_name'] =$dj_name;
                            $data['dj_id'] =$dj->id;
                        }
                    }
                    $traffic = $club_stream_detail->traffic;
                    $female = $club_stream_detail->female_listeners;
                    $male = $club_stream_detail->male_listeners;
                }
                if ($request->has('web') && !empty($request->web) && $request->web == true)
                {
                    $web_club_stream = ClubWebStream::query()->where('club_id', '=', $club_id)->first();
                    if (!empty($web_club_stream1))
                    {
                        $data['live_stream_id'] =$web_club_stream1->stream_id;
                        $data['live_stream_connection_code']=isset($web_club_stream1)?$web_club_stream1->connection_code:'';
                        $data['live_stream_url'] = $web_club_stream1->stream_url;
                        if ($web_club_stream1->updated_by_dj!= '')
                        {
                            $dj = DJ::query()->find($web_club_stream1->updated_by_dj);
                            if ($dj)
                            {
                                $dj_name= $dj->name;
                                $data['dj_name'] =$dj_name;
								$data['dj_id'] =$dj->id;
                            }
                        }
                        $traffic = $web_club_stream1->traffic;
                        $female = $web_club_stream1->female_listeners;
                        $male = $web_club_stream1->male_listeners;
                    }
                }

                /*** Start check if there already someone streaming on WEB  ***/
                $web_club_stream = ClubWebStream::query()->where('club_id', '=', $club_id)->first();
                $data['web_streaming']= 0;
                if ($web_club_stream)
                {
                    $url = "https://api.cloud.wowza.com/api/v1.3/live_streams/$web_club_stream->stream_id/state";
                    $method = "GET";
                    $stream_state = CurlHelper::CurlCommand($url, $method, '');
                    $decoded_stream_state = json_decode($stream_state['data']);
                    if($decoded_stream_state)
                    {
                        if ($decoded_stream_state->live_stream->state !='stopped')
                        {
                            $data['web_streaming']= 1;
                            $data['live_stream_url']= $web_club_stream->stream_url;
                            $club_stream_detail = $web_club_stream;
                            $female = $club_stream_detail->female_listeners;
                            $male = $club_stream_detail->male_listeners;
                        }
                    }
                }
                /*** End check if there already someone streaming on WEB  ***/
                if ($total== 0) {
                    $total = $male + $female;
                }
                $data['live_stream_url'] = isset($club_stream_detail)?$club_stream_detail->stream_url:'';
                $traffic = isset($club_stream_detail)?$club_stream_detail->traffic:$traffic;
                $data['male_listeners'] = $male;
                $data['female_listeners'] = $female;
                $data['total_listeners']= $total;


                $insight = Insight::query()->first();
                if ($traffic== 'ns'){
                    if ($total >= $insight->hype_count)
                    {
                        $traffic = 'Hype';
                    }
                    if ($total < $insight->hype_count && $total > $insight->slow_count)
                    {
                        $traffic = 'Normal';
                    }
                    if ($total <= $insight->slow_count)
                    {
                        $traffic = 'Slow';
                    }
                }
                $data['traffic'] = $traffic;
                /****** End adding club stream details *******/

                if ($request->has('user_id') && !empty($request->input('user_id'))  && $request->input('user_id') !=0)
                {
                    $user_id = $request->user_id;
                    $user = User::query()->find($user_id);
                    if (empty($user))
                    {
                        $response = [
                            'error' => true,
                            'message' => 'Invalid user_id.'
                        ];
                        return json_encode($response, 200);
                    }
                    else
                    {
                        $profile_image = "";
                        if (!empty($user->profile_image) || $user->profile_image != null || $user->profile_image != " ") {
                            $image_path1 = base_path('public/uploads/users/' . $user->profile_image);
                            if (file_exists($image_path) && is_file($image_path)) {
                                $profile_image = env('APP_URL') . '/uploads/users/' . $user->profile_image;
                            }
                        }
                    }
                }
                if ($request->has('dj_id') && !empty($request->input('dj_id'))
                    && $request->input('dj_id') !=0 )
                {
                    $dj = DJ::query()->find($request->dj_id);
                    if (!empty($dj))
                    {
                        $profile_image = "";
                        if (!empty($dj->profile_image) && $dj->profile_image != null && $dj->profile_image != " ") {
                            $image_path1 = base_path('public/uploads/djs/' . $dj->profile_image);
                            if (file_exists($image_path) && is_file($image_path)) {
                                $profile_image = env('APP_URL') . '/uploads/djs/' . $dj->profile_image;
                            }
                        }

                        if($request->has('searched') && $request->input('searched')== true) {
                            $history = RecentSearch::query()->where('user_id', '=', $request->dj_id)
                                ->where('club_id', '=', $request->club_id)->first();
                            if (!$history) {
                                $history = new RecentSearch();
                                $history->user_id = $request->dj_id;
                            }
                            $history->club_id = $request->club_id;
                            $history->save();
                        }
                    }
                    else
                    {
                        $response = [
                            'error' => true,
                            'message' => 'Invalid dj_id.'
                        ];
                        return json_encode($response, 200);
                    }
                }

                if($request->has('web') && $request->web == true)
                {
                    /****** Add club stream details for web *******/
                    $club_stream_detail = ClubWebStream::query()->where('club_id','=', $club_id)->first();

                    if (!empty($club_stream_detail))
                    {
                        $data['live_stream_id'] =$club_stream_detail->stream_id;
                        $data['live_stream_connection_code']=isset($club_stream_detail)?$club_stream_detail->connection_code:'';
                        $data['live_stream_url'] = $club_stream_detail->stream_url;
                        if ($club_stream_detail->updated_by_dj!= '')
                        {
                            $dj = DJ::query()->find($club_stream_detail->updated_by_dj);
                            if ($dj)
                            {
                                $dj_name= $dj->name;
                                $data['dj_name'] =$dj_name;
								$data['dj_id'] =$dj->id;
                            }
                        }
                        $traffic = $club_stream_detail->traffic;
                        $female = $club_stream_detail->female_listeners;
                        $male = $club_stream_detail->male_listeners;
                    }

                    /*** Start check if there already someone streaming on WEB  ***/
                    $IOS_club_stream = ClubStream::query()->where('club_id', '=', $club_id)->first();
                    $data['web_streaming']= 0;
                    if ($IOS_club_stream)
                    {
                        $url = "https://api.cloud.wowza.com/api/v1.3/live_streams/$IOS_club_stream->stream_id/state";
                        $method = "GET";
                        $stream_state = CurlHelper::CurlCommand($url, $method, '');
                        $decoded_stream_state = json_decode($stream_state['data']);
                        if($decoded_stream_state)
                        {
                            if ($decoded_stream_state->live_stream->state !='stopped')
                            {
                                $data['web_streaming']= 1;
                                $data['live_stream_url']= $IOS_club_stream->stream_url;
                                $data['live_stream_connection_code']=$IOS_club_stream->connection_code;
                                $club_stream_detail = $IOS_club_stream;
                                $female = $IOS_club_stream->female_listeners;
                                $male = $IOS_club_stream->male_listeners;
                            }
                        }
                    }
                    /*** End check if there already someone streaming on WEB  ***/
                    if ($total== 0) {
                        $total = $male + $female;
                    }
                    $data['live_stream_url'] = isset($club_stream_detail)?$club_stream_detail->stream_url:'';
                    $traffic = isset($club_stream_detail)?$club_stream_detail->traffic:$traffic;
                    $data['male_listeners'] = $male;
                    $data['female_listeners'] = $female;
                    $data['total_listeners']= $total;
                    $insight = Insight::query()->first();
                    if ($traffic== 'ns'){
                        if ($total >= $insight->hype_count)
                        {
                            $traffic = 'Hype';
                        }
                        if ($total < $insight->hype_count && $total > $insight->slow_count)
                        {
                            $traffic = 'Normal';
                        }
                        if ($total <= $insight->slow_count)
                        {
                            $traffic = 'Slow';
                        }
                    }
                    $data['traffic'] = $traffic;
                    /****** End adding club stream details *******/
                }

                $response = [
                    'success' => true,
                    'message' => 'Club Detail.',
                    'data' => $data,
                ];
                if (isset($profile_image))
                {
                    $response['profile_image'] = $profile_image;
                }
            }

            else {
                $response = [
                    'error' => true,
                    'message' => 'Invalid club_id .'

                ];
                return json_encode($response, 200);
            }
        }
        return json_encode($response, 200);
    }

//      HOME PAGE CLUBS

    public function homePageClubs(Request $request)
    {

        $city_name = $request->input('city_name');
        $state_name = $request->input('state_name');
        $country_name = $request->input('country_name');
        if (empty($city_name)) {
            $response = [
                'error' => true,
                'message' => 'Please provide a city name required field.'
            ];
            return json_encode($response, 200);
        }
        else {
            $city_id = '';
            $state_id = '';
            $cities = City::all();
            $states = State::all();
            foreach ($cities as $c_item) {
                if (strcasecmp($c_item->name, $city_name) == 0) {
                    $city_id = $c_item->id;
                    $state_id = $c_item->state_id;
                    $country_id = $c_item->country_id;
                }
            }

            /* FETCHING SUGGESTED CLUBS */
            $nearby_clubs = [];
            $sugQuery = Club::query()->where('country', '=', 1);
            if (isset($country_id) && !empty($country_id)) {
                $sugQuery = Club::query()->where('country', '=', $country_id);
            }
            $suggested_clubs = $sugQuery->limit(20)->inRandomOrder()->select('id as club_id','name','profile_image')->get();
            if ($suggested_clubs->count() > 0) {
                foreach ($suggested_clubs as $sugClub) {
                    if (!empty($sugClub->profile_image) || $sugClub->profile_image!= null || $sugClub->profile_image != " ") {
                        $image_path = base_path('public/uploads/clubs/' . $sugClub->profile_image);
                        if (file_exists($image_path)&& is_file($image_path) ) {
                            $sugClub['profile_image'] = env('APP_URL') . '/uploads/clubs/' . $sugClub->profile_image;
                        }
                        else
                        {
                            $sugClub['profile_image'] ="";
                        }
                    }
//                    $sugClub['live']=  CurlHelper::is_live($sugClub->club_id);
                }
            }

            /* FETCHING SUGGESTED CLUBS */

            if ($state_id != '') {
                $nearby_clubs = Club::query()->where('state', '=', $state_id)
                    ->select('id as club_id','name','profile_image')
                    ->inRandomOrder()->get();
                if ($nearby_clubs->count() > 0) {
                    foreach ($nearby_clubs as $nearby_club) {
                        if (!empty($nearby_club->profile_image) || $nearby_club->profile_image!= null || $nearby_club->profile_image != " ") {
                            $image_path = base_path('public/uploads/clubs/' . $nearby_club->profile_image);
                            if (file_exists($image_path)&& is_file($image_path) ) {
                                $nearby_club['profile_image'] = env('APP_URL') . '/uploads/clubs/' . $nearby_club->profile_image;
                            }
                            else{
                                $nearby_club['profile_image'] ="";
                            }
                        }
//                        $nearby_club['live']=  CurlHelper::is_live($nearby_club->club_id);
                    }
                }
            }

            /* FETCHING NEAR BY CLUBS */

            if ($city_id != '') {
                $query = Club::query()->where('city', '=', $city_id)
                    ->orderBy('city');

                //Pagination //
                $totalRecords = $query->count();

                $offset = $request->input('offset', 0);
                if ($offset == "") {
                    $offset = 0;
                }
                $limit = $request->input('limit', 0);
                if ($limit == "") {
                    $limit = 10;
                }
                $query->take($limit);
                $query->skip($offset * $limit);
                $clubs = $query->get();
                $city_clubs = $query->select('id','id as club_id','name','profile_image')->get();


                if ($city_clubs->count() > 0) {
                    foreach ($city_clubs as $club) {
                        if (!empty($club->profile_image) || $club->profile_image!= null || $club->profile_image != " ") {
                            $image_path = base_path('public/uploads/clubs/' . $club->profile_image);
                            if (file_exists($image_path)&& is_file($image_path) ) {
                                $club['profile_image'] = env('APP_URL') . '/uploads/clubs/' . $club->profile_image;
                            }
                            else{
                                $club['profile_image'] ="";
                            }
                        }
                        $club['live']=  CurlHelper::is_live($club->id);
                    }

                    $response = [
                        'error' => false,
                        'message' => 'City clubs',
                        'data' => $city_clubs,
                        'location' => $city_name,
                        'nearby_clubs' => $nearby_clubs,
                        'suggested_clubs' => $suggested_clubs,
                        'paginator' => [
                            'totalRecords' => $totalRecords,
                            'limit' => $limit,
                            'offset' => $offset,
                        ]
                    ];
                    return json_encode($response, 200);
                }
                else {
                    $response = [
                        'error' => true,
                        'message' => 'No club found in this city.',
                        'data' => [],
                        'location' => $city_name,
                        'nearby_clubs' => $nearby_clubs,
                        'suggested_clubs' => $suggested_clubs
                    ];
                    return json_encode($response, 200);
                }
            } else {
                $response = [
                    'error' => true,
                    'message' => 'No club found in this city.',
                    'nearby_clubs' => $nearby_clubs,
                    'suggested_clubs' => $suggested_clubs,
                    'location' => $city_name
                ];
                return json_encode($response, 200);
            }
        }
    }

//      USER LOGIN

    public function userLogin(Request $request)
    {

        define("ENCRYPTION_KEY", "!@#$%^&*");

        $email = $request->input('email');
        $device_token = $request->input('device_token');
        $device_type =1;
        if ($request->has('device_type') && !empty($request->device_type))
        {
            $device_type= $request->device_type;
        }
        $user_name = $request->input('user_name');
        $password = $request->input('password');
        if (empty($email) && empty($user_name)) {
            $response = [
                'error' => true,
                'message' => 'Please enter email or user name to proceed login.'
            ];
            return json_encode($response, 200);
        }

        if (empty($email) && !empty($user_name)) {
            $userdata = User::query()->where('user_name', '=', $user_name)->first();
            if (!$userdata) {
                $response = [
                    'error' => true,
                    'message' => 'Sorry, We couldn\'t find any user with this user name!'
                ];
                return json_encode($response, 200);
            }
        }
        if (!empty($email) && empty($user_name)) {
            $userdata = User::query()->where('email', '=', $email)->first();
            if (!$userdata) {
                $response = [
                    'error' => true,
                    'message' => 'Sorry, We couldn\'t find any user with this email address!'
                ];
                return json_encode($response, 200);
            }
        }
        if (!empty($email) && !empty($user_name)) {
            $userdata = User::query()->where('email', '=', $email)->first();
            if (!$userdata) {
                $userdata = User::query()->where('user_name', '=', $user_name)->first();
                if (!$userdata) {
                    $response = [
                        'error' => true,
                        'message' => 'Sorry, We couldn\'t find any user with this email address!'
                    ];
                    return json_encode($response, 200);
                }
            }
        }
//        if (empty($device_token)) {
//            $response = [
//                'error' => true,
//                'message' => 'Please send device_token to proceed login.'
//
//            ];
//            return json_encode($response, 200);
//
//        }
        if (empty($password)) {
            $response = [
                'error' => true,
                'message' => 'Please fill password field.'

            ];
            return json_encode($response, 200);

        } else {
            if (isset($userdata)) {
                if (!empty($userdata)) {
                    if ($userdata->deleted_at == null) {
                        $desPassword = decrypt($userdata->password, ENCRYPTION_KEY);
                        if ($password == $desPassword) {
                            if (!empty($device_token)) {
                                $userdata->device_token = $device_token;
                                $userdata->device_type = $device_type;
                            }
                            $userdata->is_active = 1;
                            $userdata->save();

                            $logged_in = false;
                            if ($userdata->is_active==1)
                            {
                                $logged_in= true;
                            }
                            $get_notification = false;
                            if ($userdata->get_notification==1)
                            {
                                $get_notification= true;
                            }

                            if (!empty($device_token)) {
                                $prev_user_device = UserDevice::query()
                                    ->where('device_token', '=', $device_token)
                                    ->where('device_type', '=', $device_type)
                                    ->first();
                                if ($prev_user_device) {
                                    $prev_user_device->user_id = $userdata->id;
                                    $prev_user_device->device_type = $device_type;
                                    $prev_user_device->save();
                                }

                                $user_device = UserDevice::query()
                                    ->where('user_id', '=', $userdata->id)
                                    ->where('device_token', '=', $device_token)
                                    ->where('device_type', '=', $device_type)
                                    ->first();
                                if (!$user_device && $device_token != 'ABCDEF') {
                                    $user_device = new UserDevice();
                                    $user_device->user_id = $userdata->id;
                                    $user_device->device_token = $device_token;
                                    $user_device->device_type = $device_type;
                                    $user_device->save();
                                }
                            }
                            if (empty($userdata->profile_image) || $userdata->profile_image == null || $userdata->profile_image == " ") {
                                $response = [
                                    'error' => false,
                                    'message' => 'You\'re logged in successfully!',
                                    'data' => [
                                        'user_id' => $userdata->id,
                                        'first_name' => $userdata->first_name,
                                        'last_name' => $userdata->last_name,
                                        'email' => $userdata->email,
                                        'user_name' => $userdata->user_name,
                                        'date_of_birth' => $userdata->birth_date,
                                        'gender' => $userdata->gender,
                                        'logged_in' => $logged_in,
                                        'get_notification' => $get_notification,
                                        'login_type' => (int)$userdata->registeration_type,
                                        'location' => $userdata->location,
                                        'device_token' => $device_token,
                                        'device_type' => $device_type,
                                        'profile_image' => "",
                                    ]
                                ];
                                if (!empty($userdata->profile_picture_url))
                                {
                                    $response['data']['profile_image'] = $userdata->profile_picture_url;
                                }

                            } else {
                                $response = [
                                    'error' => false,
                                    'message' => 'You\'re logged in successfully!',
                                    'data' => [
                                        'user_id' => $userdata->id,
                                        'first_name' => $userdata->first_name,
                                        'last_name' => $userdata->last_name,
                                        'email' => $userdata->email,
                                        'user_name' => $userdata->user_name,
                                        'date_of_birth' => $userdata->birth_date,
                                        'gender' => $userdata->gender,
                                        'logged_in' => $logged_in,
                                        'get_notification' => $get_notification,
                                        'login_type' => (int)$userdata->registeration_type,
                                        'location' => $userdata->location,
                                        'device_token' => $device_token,
                                        'device_type' => $device_type,
                                        'profile_image' => env('APP_URL') . '/uploads/users/' . $userdata->profile_image,
                                    ]
                                ];
                            }
                            return json_encode($response, 200);

                        } else {
                            $response = [
                                'error' => true,
                                'message' => 'You\'ve entered an incorrect password! Try again!'
                            ];

                            return json_encode($response, 200);
                        }
                    }
                } else {
                    $response = [
                        'error' => true,
                        'message' => 'This user isn\'t registered with us!'

                    ];

                    return json_encode($response, 200);
                }
            } else {
                $response = [
                    'error' => true,
                    'message' => 'Sorry, We couldn\'t find any user with provided credentials!'
                ];
                return json_encode($response, 200);
            }
        }
    }

//      USER LOGIN VIA SOCIAL PLATFORMS

    public function socialUserLogin(Request $request)
    {
        $client_id = $request->input('client_id');
        $email = $request->input('email');
        $registration_type = $request->input('registration_type');
        $device_token='ABCDEF';
        if($request->has('device_token') && !empty($request->device_token)) {
            $device_token = $request->input('device_token');
        }
        $device_type =1;
        if ($request->has('device_type') && !empty($request->device_type))
        {
            $device_type= $request->device_type;
        }
        if (empty($client_id)) {
            $response = [
                'error' => true,
                'message' => 'Please, send client_id (required fields).'
            ];
            return json_encode($response, 200);
        }

        if (empty($registration_type)) {
            $response = [
                'error' => true,
                'message' => 'Please, send registration_type (required field).'
            ];
            return json_encode($response, 200);
        }
//        if (empty($device_token)) {
//            $response = [
//                'error' => true,
//                'message' => 'Please, send device_token (required field).'
//            ];
//            return json_encode($response, 200);
//        }

        $user = User::query()->where('client_id', '=', $client_id)
            ->where('registeration_type', '=', $registration_type)->first();
        if (!$user) {
            $user = User::query()->where('email', '=', $email)->first();
            if ($user && $user->deleted_at == null && $request->has('client_id') && $request->has('registration_type')) {
                if ($client_id != '' && $registration_type != '') {
                    $prev_user_device = UserDevice::query()
                        ->where('device_token', '=', $device_token)
                        ->where('device_type', '=', $device_type)
                        ->first();
                    if ($prev_user_device) {
                        $prev_user_device->user_id = $user->id;
                        $prev_user_device->device_type = $device_type;
                        $prev_user_device->save();
                    }

                    $user_device = UserDevice::query()->where('user_id', '=', $user->id)
                        ->where('device_token', '=', $device_token)
                        ->where('device_type', '=', $device_type)
                        ->first();
                    if (!$user_device && $device_token != 'ABCDEF') {
                        $user_device = new UserDevice();
                        $user_device->user_id = $user->id;
                        $user_device->device_token = $device_token;
                        $user_device->device_type = $device_type;
                        $user_device->save();
                    }
                    $prev_device_token = User::query()
                        ->where('device_token', '=', $device_token)
                        ->where('device_type', '=', $device_type)
                        ->get();
                    if ($prev_device_token->count() > 0) {
                        foreach ($prev_device_token as $prev_user) {
                            $prev_user->device_token = '';
                            $prev_user->save();
                        }
                    }

                    $user->client_id = $client_id;
                    $user->registeration_type = (int)$registration_type;
                    $user->device_token = $device_token;
                    $user->device_type = $device_type;
                    $user->is_active = 1;
                    $user->save();
                    $logged_in = false;
                    if ($user->is_active == 1) {
                        $logged_in = true;
                    }

                    $get_notification = false;
                    if ($user->get_notification == 1) {
                        $get_notification = true;
                    }

                    if (empty($user->profile_image) || $user->profile_image == null || $user->profile_image == " ") {
                        $response = [
                            'error' => false,
                            'message' => 'You\'re logged in successfully!',
                            'data' => [
                                'user_id' => $user->id,
                                'first_name' => $user->first_name,
                                'last_name' => $user->last_name,
                                'email' => $user->email,
                                'user_name' => $user->user_name,
                                'date_of_birth' => $user->birth_date,
                                'gender' => $user->gender,
                                'logged_in' => $logged_in,
                                'get_notification' => $get_notification,
                                'login_type' => (int)$user->registeration_type,
                                'client_id' => $user->client_id,
                                'location' => $user->location,
                                'device_token' => $device_token,
                                'device_type' => $device_type,
                                'profile_image' => "",
                            ]
                        ];
                        if (!empty($user->profile_picture_url)) {
                            $response['data']['profile_image'] = $user->profile_picture_url;
                        }
                        return json_encode($response, 200);
                    }
                    else {
                        $response = [
                            'error' => false,
                            'message' => 'You\'re logged in successfully!',
                            'data' => [
                                'user_id' => $user->id,
                                'first_name' => $user->first_name,
                                'last_name' => $user->last_name,
                                'email' => $user->email,
                                'user_name' => $user->user_name,
                                'date_of_birth' => $user->birth_date,
                                'gender' => $user->gender,
                                'logged_in' => $logged_in,
                                'get_notification' => $get_notification,
                                'login_type' => (int)$user->registeration_type,
                                'client_id' => $user->client_id,
                                'location' => $user->location,
                                'device_token' => $device_token,
                                'profile_image' => env('APP_URL') . '/uploads/users/' . $user->profile_image,
                            ]
                        ];
                        return json_encode($response, 200);
                    }
                }
            }

            $user = User::query()->where('email', '=', $email)
                ->where('registeration_type', '=', $registration_type)->first();
        }
        if (!empty($user)) {
            if ($user->deleted_at == null) {
                // save user device //
                $user_device = UserDevice::query()->where('user_id', '=', $user->id)
                    ->where('device_token', '=', $device_token)
                    ->where('device_type', '=', $device_type)
                    ->first();
                if (!$user_device && $device_token!='ABCDEF') {
                    $user_device = new UserDevice();
                    $user_device->user_id = $user->id;
                    $user_device->device_token = $device_token;
                    $user_device->device_type = $device_type;
                    $user_device->save();
                }
                $prev_device_token = User::query()
                    ->where('device_token','=', $device_token)
                    ->where('device_type','=', $device_type)
                    ->get();
                if($prev_device_token->count()>0)
                {
                    foreach ($prev_device_token as $prev_user)
                    {
                        $prev_user->device_token = '';
                        $prev_user->save();
                    }
                }

                // save user device //

                $user->device_token = $device_token;
                $user->device_type = $device_type;
                $user->is_active = 1;
                $user->save();
                $logged_in = false;
                if ($user->is_active==1)
                {
                    $logged_in= true;
                }

                $get_notification = false;
                if ($user->get_notification==1)
                {
                    $get_notification= true;
                }

                if (empty($user->profile_image) || $user->profile_image == null || $user->profile_image == " ") {
                    $response = [
                        'error' => false,
                        'message' => 'You\'re logged in successfully!',
                        'data' => [
                            'user_id' => $user->id,
                            'first_name' => $user->first_name,
                            'last_name' => $user->last_name,
                            'email' => $user->email,
                            'user_name' => $user->user_name,
                            'date_of_birth' => $user->birth_date,
                            'gender' => $user->gender,
                            'logged_in' => $logged_in,
                            'get_notification' => $get_notification,
                            'login_type' => (int)$user->registeration_type,
                            'client_id' => $user->client_id,
                            'location' => $user->location,
                            'device_token' => $device_token,
                            'profile_image' => "",
                        ]
                    ];
                    if (!empty($user->profile_picture_url))
                    {
                        $response['data']['profile_image'] = $user->profile_picture_url;
                    }
                    return json_encode($response, 200);
                } else {
                    $response = [
                        'error' => false,
                        'message' => 'You\'re logged in successfully!',
                        'data' => [
                            'user_id' => $user->id,
                            'first_name' => $user->first_name,
                            'last_name' => $user->last_name,
                            'email' => $user->email,
                            'user_name' => $user->user_name,
                            'date_of_birth' => $user->birth_date,
                            'gender' => $user->gender,
                            'logged_in' => $logged_in,
                            'get_notification' => $get_notification,
                            'login_type' => (int)$user->registeration_type,
                            'client_id' => $user->client_id,
                            'location' => $user->location,
                            'device_token' => $device_token,
                            'profile_image' => env('APP_URL') . '/uploads/users/' . $user->profile_image,
                        ]
                    ];
                    return json_encode($response, 200);
                }
            } else {
                $response = [
                    'error' => true,
                    'message' => 'Sorry, Login denied. Because this user has been deleted by Super Admin. Contact administration for any help.'
                ];
                return json_encode($response, 200);
            }
        }
        else {
            $response = [
                'error' => true,
                'message' => 'Sorry, We could\'t find any user with provided login details.'
            ];
            return json_encode($response, 200);
        }
    }

//      USER LOCATION UPDATE

    public function userLocationUpdate(Request $request)
    {
        $user_id = $request->input('user_id');
        $location = $request->input('location');
        if (empty($user_id)) {
            $response = [
                'error' => true,
                'message' => 'Please, send user_id (required field).'
            ];
            return json_encode($response, 200);
        }
        if (empty($location)) {
            $response = [
                'error' => true,
                'message' => 'Please, send location (required field).'
            ];
            return json_encode($response, 200);
        }

        $user = User::query()->find($user_id);


        if (!empty($user)) {
            if ($user->deleted_at == null) {
                $user->location = $location;
                $user->is_active = 1;
                $user->save();
                $response = [
                    'error' => false,
                    'message' => 'Location updated successfully.',
                    'data' => [
                        'user_id' => $user->id,
                        'location' => $user->location,
                    ],
                ];
                return json_encode($response, 200);

            } else {
                $response = [
                    'error' => true,
                    'message' => 'Sorry, You can\'nt update location as this user has been deleted by Super Admin. Contact Administration for help!.'
                ];
                return json_encode($response, 200);
            }
        }else {
            $response = [
                'error' => true,
                'message' => 'Sorry, We couldn\'t find any user with provided information.'
            ];
            return json_encode($response, 200);
        }

    }

//      STATIC PAGES
    public function privacyPolicyPage()
    {
        $page = Page::query()->where('type', '=', 1)->first();
        if ($page) {
            return view('frontend.static-pages.page', ['page' => $page]);
        }
    }

    public function TnCPage()
    {
        $page = Page::query()->where('type', '=', 2)->first();
        if ($page) {
            return view('frontend.static-pages.page', ['page' => $page]);
        }
    }

//    USER LOG OUT
    public function logOut(Request $request)
    {
        $user_id = $request->input('user_id');
        $device_token = $request->input('device_token');
        if (empty($user_id)) {
            $response = [
                'error' => true,
                'message' => 'Please, send user_id (required field).'
            ];
            return json_encode($response, 200);
        }
        $device_token='ABCDEF';
        if ($request->has('device_token') && !empty($request->device_token))
        {
            $device_token = $request->input('device_token');
        }

//        if (empty($device_token)) {
//            $response = [
//                'error' => true,
//                'message' => 'Please, send device_token (required field).'
//            ];
//            return json_encode($response, 200);
//        }
        $user = User::query()->find($user_id);
        if ($user) {
            if ($user->deleted_at == null) {
                if ($user->is_active != 0) {
                    $user->is_active = 0;
                }
                if ($user->device_token == $device_token)
                {
                    $user->device_token ='';
                    $user->device_type =0;
                }
                $user->save();

                $user_device = UserDevice::query()
                    ->where('user_id','=', $user_id)
                    ->where('device_token','=', $device_token)
                    ->first();
                if($user_device)
                {
                    $user_device->delete();
                }
                $response = [
                    'error' => false,
                    'message' => 'You\'ve been logged out successfully!',
                ];
                return json_encode($response, 200);
            }
            else
            {
                $response = [
                    'error' => true,
                    'message' => 'Sorry, You can\'t make any changes as this user has been deleted by Super Admin. Contact Administration for help!.'
                ];
                return json_encode($response, 200);
            }
        } else {
            $response = [
                'error' => true,
                'message' => 'Sorry, We couldn\'t find any user with provided information.'
            ];
            return json_encode($response, 200);
        }

    }
}
