<?php

namespace catchapp\Http\Controllers\API;

use Carbon\Carbon;
use catchapp\Helpers\CurlHelper;
use catchapp\Helpers\MaskHelper;
use catchapp\Helpers\TokenHelper;
use catchapp\Http\Controllers\Controller;
use catchapp\Mail\SendMailable;
use catchapp\Models\City;
use catchapp\Models\Club;
use catchapp\Models\ClubStream;
use catchapp\Models\Country;
use catchapp\Models\DJ;
use catchapp\Models\EmailConfiguration;
use catchapp\Models\EmailType;
use catchapp\Models\Pivot_Dj_Club;
use catchapp\Models\RecentSearch;
use catchapp\Models\State;
use catchapp\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use mysql_xdevapi\Exception;
use phpseclib\Crypt\Hash;
use catchapp\Helpers\MediaHelper;



class DjController extends Controller
{
    //      DJ REGISTRATION
    public function djRegister(Request $request)
    {
        $response = [];
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email|unique:djs,email',
                'user_name' => 'required|unique:djs,user_name',
                'registeration_type' => 'required|in:1,2,3,4,5,6,7',
                'password' => 'required_if:registeration_type,1|required_if:registeration_type,7',
                'birth_date' => 'date',
                'gender' => 'sometimes|in:male,female,other',
                'profile_image' => 'sometimes|mimes:jpeg,gif,bmp,png,svg+xml',
            ]);

            $validator->sometimes('client_id', 'required', function ($request) {
                return $request->registeration_type > 1 && $request->registeration_type < 6 && $request->registeration_type !=7;
            });
            $validator->sometimes('oauth_key', 'required', function ($request) {
                return $request->registeration_type > 1 && $request->registeration_type < 6 && $request->registeration_type !=7;
            });
            if ($validator->fails()) {
                $response['error'] = true;
                $response['status_code'] = '401';
                $response['message'] = $validator->errors()->first();
            }
            else {
                if ($request->registeration_type > 1 && $request->registeration_type < 6) {
                    if ($request->has('client_id') && $request->has('email')) {
                        $email = $request->has('email');
                        $dj = DJ::query()->where('email', '=', $email)->first();
                        if ($dj) {
                            if ($request->has('profile_picture_url') && $request->profile_picture_url
                                && !empty($request->profile_picture_url)) {
                                $dj->profile_picture_url = $request->profile_picture_url;
                            }
                            $dj->registeration_type = $request->registeration_type;
                            $dj->client_id = $request->client_id;
                            $dj->flag = 1;
                            $dj->save();
                            $logged_in = false;
                            if ($dj->flag == 1) {
                                $logged_in = true;
                            }
                            $dj['logged_in'] = $logged_in;
                            $dj_prof_image = "";
                            if (!empty($dj->profile_picture_url))
                            {
                                $dj_prof_image = $dj->profile_picture_url;
                            }
                            if (!empty($dj->profile_image) || $dj->profile_image != null || $dj->profile_image != " ") {
                                $image_path = base_path('public/uploads/djs/' . $dj->profile_image);
                                if (file_exists($image_path) && is_file($image_path)) {
                                    $dj_prof_image = env('APP_URL') . '/uploads/djs/' . $dj->profile_image;
                                }
                            }

                            $dj['registeration_type'] = User::$registeration_type{$dj->registeration_type};
                            unset($dj['created_at']);
                            unset($dj['updated_at']);
                            unset($dj['deleted_at']);
                            unset($dj['profile_image']);
                            unset($dj['password']);
                            unset($dj['flag']);
                            unset($dj['assigned_clubs']);
                            unset($dj['client_id']);
                            unset($dj['oauth_key']);
                            unset($dj['locatione']);
                            if ($dj->registeration_type == 1) {
                                unset($dj['client_id']);
                                unset($dj['oauth_key']);
                            }
                            $dj['profile_image'] = $dj_prof_image;
                            $dj['user_type'] = 'dj';

                            $response['error'] = false;
                            $response['status_code'] = '200';
                            $response['message'] = 'You\'re logged in now!';
                            $response['data'] = $dj;
                            return response()->json($response);
                        }
                    }
                }

                $filename = '';
                if ($request->hasFile('profile_image')) {
                    $file_extension = $request->profile_image->getClientOriginalExtension();
                    $filename = 'dj-profile-photo-' . time() . '.' . $file_extension;
                }
                if ($request->has('birth_date') && $request->input('birth_date')!=' ' && $request->input('birth_date') != null) {
                    $dob = Carbon::parse($request->input('birth_date'))->format('Y-m-d');
                }

                $dj = new DJ();
                $dj->name = $request->name;
                $dj->email = $request->email;
                $dj->user_name = $request->user_name;
                $dj->password = $request->password;
                if ($request->has('profile_picture_url') && $request->profile_picture_url
                    && !empty($request->profile_picture_url)) {
                    $dj->profile_picture_url = $request->profile_picture_url;
                }
                isset($dob)?($dj->birth_date=$dob):'';
                if ($request->has('gender') && $request->input('gender')!=' ' && $request->input('gender') != null) {
                    $dj->gender = $request->gender;
                }
                $dj->locatione = $request->location;
                $dj->registeration_type = $request->registeration_type;
                if ($request->registeration_type > 1 && $request->registeration_type < 6) {
                    $dj->client_id = $request->client_id;
                    $dj->oauth_key = $request->oauth_key;
                }
                $dj->profile_image = $filename;
                $dj->save();
                $prof_image = "";
                if ($request->hasFile('profile_image')) {
                    $request->profile_image->move(base_path('public/uploads/djs/'), $filename);
                    $image_path = base_path('public/uploads/djs/' . $filename);
                    if (file_exists($image_path) && is_file($image_path)) {
                        $prof_image = env('APP_URL') . '/uploads/djs/' . $filename;
                    }
                }
                if (!empty($dj->email)) {
                    //  SEND AN WELCOME EMAIL TO NEW DJ

                    $subject = 'Welcome to CatchApp!';
                    $mail_to = $dj->email;
                    $content = 'Hi ' . $dj->name . ' (DJ) ! Welcome to CatchApp.';
                    $type = EmailType::query()
                        ->join('email_addresses', 'email_types.id', '=',
                            'email_addresses.email_type', 'inner')->select('email_addresses.email_address as mail_from', 'email_types.*')
                        ->where('name', 'LIKE', '%' . 'new dj' . '%')->first();

                    $mail = new EmailConfiguration();
                    if ($type) {
                        $mail->email_type = $type->id;
                        $mail->mail_from = $type->mail_from;
                    } else {
                        $mail->email_type = 0;
                        $mail->mail_from = 'newdj@catchapp.com';
                    }
                    $mail->mail_to = $mail_to;
                    $mail->mail_subject = $subject;
                    $mail->mail_content = $content;
                    $mail->is_sent = 0;
                    $mail->save();

                    //      SENDING MAIL
                    if (isset($mail)) {
                        Mail::to($mail->mail_to)
                            ->queue(new SendMailable($mail));
                        // check for failures
                        if (Mail::failures()) {

                        } else {
                            $mail->is_sent = 1;
                            $mail->save();
                        }
                    }
                }
                unset($dj['created_at']);
                unset($dj['updated_at']);
                unset($dj['deleted_at']);
                unset($dj['profile_image']);
                unset($dj['password']);
                if ($dj->registeration_type == 1) {
                    unset($dj['client_id']);
                    unset($dj['oauth_key']);
                }
                $dj['profile_image'] = $prof_image;
                $dj['user_type'] = 'dj';

                $data = $dj;
                DB::commit();
                $response['error'] = false;
                $response['status_code'] = '200';
                $response['message'] = 'DJ registered successfully.';
                $response['data'] = $data;
            }
        }
        catch (\Exception $e) {
            DB::rollBack();
            $response['error'] = true;
            $response['status_code'] = '400';
            $response['message'] = $e->getMessage();
        } finally {
            return response()->json($response);
        }
    }

    //    UPDATE DJ DETAILS
    public function updateDj(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $user_name = $request->input('user_name');
        $password = $request->input('password');
        $gender = $request->input('gender');
        $location = $request->input('location');
        $profile_image = $request->file('profile_image');
        $picture_url = $request->input('picture_url');
        if (empty($id)) {
            $response = [
                'error' => true,
                'message' => 'Please fill id required field.',
            ];
            return json_encode($response, 200);
        }

        if ($request->hasFile('profile_image')) {
            $allowedMimeTypes = ['image/jpeg', 'image/gif', 'image/png', 'image/bmp', 'image/svg+xml'];
            $contentType = $profile_image->getMimeType();
            if (!in_array($contentType, $allowedMimeTypes)) {
                $response = [
                    'error' => true,
                    'message' => 'File format isn\'t supported for profile picture.',
                ];
                return json_encode($response, 200);
            }
        }
        $filename = '';
        $dj = DJ::query()->find($id);

        if (!empty($dj)) {
            if (!empty($name)) {
                $dj->name = $name;
            }
            if (!empty($email)) {
                $response = [
                    'error' => true,
                    'message' => 'Sorry, You can\'t change your email address.',
                ];
                return json_encode($response, 200);
            }
            if (!empty($user_name)) {
                if (strpos($user_name, ' ') !== false) {
                    $response = [
                        'error' => true,
                        'message' => 'User name can\'t contain blank space. Please try again with a valid user name.',
                    ];
                    return json_encode($response, 200);
                }

                $dj_exists = DJ::query()->where('user_name', '=', $user_name)->first();
                if ($dj_exists && $dj_exists->id != $id) {
                    $response = [
                        'error' => true,
                        'message' => 'User name is already taken. Please try another !',
                    ];
                    return json_encode($response, 200);
                }
                $dj->user_name = $user_name;
            }
            if (!empty($password)) {
                $dj->password = $password;
            }
            if (!empty($location)) {
                $dj->locatione = $location;
            }
            if ($request->has('birth_date') && $request->input('birth_date')!=' ' && $request->input('birth_date') != null) {
                $dj->birth_date = Carbon::parse($request->input('birth_date'))->format('Y-m-d');
            }

            if (!empty($gender)) {
                $dj->gender = $gender;
            }
            // SAVE PROFILE IMAGE
            if ($request->hasFile('profile_image')) {

                if (!empty($dj->profile_image)) {
                    $old_image = base_path('public/uploads/djs/' . $dj->profile_image);
                    if (is_file($old_image) && file_exists($old_image)) {
                        unlink($old_image);
                    }
                }
                $extension = $profile_image->getClientOriginalExtension();
                $filename = 'dj-profile-photo-' . time() . '.' . $extension;
                $profile_image->move(base_path('public/uploads/djs/'), $filename);
                $dj->profile_image = $filename;
                $dj->profile_picture_url = "";
            } else {
                if ((!empty($dj->profile_image) || $dj->profile_image != null || $dj->profile_image != " ")
                    && (!($request->has('picture_url')) && empty($picture_url) )) {
                    $old_image = base_path('public/uploads/djs/' . $dj->profile_image);
                    if (is_file($old_image) && file_exists($old_image)) {
                        unlink($old_image);
                    }
                    $dj->profile_image = " ";
                }
            }
            // END

            $dj->save();
            $prof_image = "";
            if (!empty($dj->profile_image) || $dj->profile_image != null || $dj->profile_image != " ") {
                $image_path = base_path('public/uploads/djs/' . $dj->profile_image);
                if (file_exists($image_path) && is_file($image_path)) {
                    $prof_image = env('APP_URL') . '/uploads/djs/' . $dj->profile_image;
                }
            }
            unset($dj['registeration_type']);
            unset($dj['client_id']);
            unset($dj['oauth_key']);
            unset($dj['assigned_clubs']);
            if (empty($request->location)) {
                unset($dj['locatione']);
            }
            $dj['logged_in'] = $dj['flag'];
            unset($dj['flag']);
            unset($dj['created_at']);
            unset($dj['updated_at']);
            unset($dj['deleted_at']);
            unset($dj['profile_image']);
            if ($dj->registeration_type == 1) {
                unset($dj['client_id']);
                unset($dj['oauth_key']);
            }
            $dj['profile_image'] = $prof_image;

            $data = $dj;

            $response = [
                'error' => false,
                'message' => 'Dj details are updated successfully',
                'data' => $data,
            ];

            return json_encode($response, 200);
        } else {
            $response = [
                'error' => true,
                'message' => 'You provided an invalid id.',
            ];
            return json_encode($response, 200);
        }

    }

    //      FETCH DJ DETAILS
    public function djDetails(Request $request)
    {
        $response = [];
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'id' => 'required',
            ]);
            if ($validator->fails()) {
                $response['error'] = true;
                $response['status_code'] = '401';
                $response['message'] = $validator->errors()->first();
            } else {
                $id = $request->id;
                $dj = DJ::query()->find($id);
                if (empty($dj)) {
                    throw new \Exception("Sorry, No dj exists with provided info.");
                }
                $club_names = [];
                $club_ids = Pivot_Dj_Club::query()->where('dj_id', '=', $id)->pluck('club_id');
                $clubs = Club::query()->whereIn('id', $club_ids)->get();
                $assigned_clubs = [];
                if ($clubs->count() > 0) {
                    foreach ($clubs as $c_item) {
                        $c = [];
                        $c['id'] = $c_item->id;
                        $c['club_name'] = $c_item->name;

                        $prof_image = "";
                        if (!empty($c_item->profile_image) || $c_item->profile_image != null || $c_item->profile_image != " ") {

                            $image_path = base_path('public/uploads/clubs/' . $c_item->profile_image);
                            if (file_exists($image_path) && is_file($image_path)) {
                                $prof_image = env('APP_URL') . '/uploads/clubs/' . $c_item->profile_image;
                            }
                        }
                        $c['club_image'] = $prof_image;
                        array_push($assigned_clubs, $c);
                    }
                }

                foreach ($clubs as $club) {
                    array_push($club_names, $club->name);
                }
                $data['id'] = $dj->id;
                $data['name'] = $dj->name;
                $data['user_name'] = $dj->user_name;
                $data['email'] = $dj->email;
                $data['gender'] = $dj->gender;
                $dj_prof_image = "";
                if (!empty($dj->profile_picture_url))
                {
                    $dj_prof_image = $dj->profile_picture_url;
                }
                if (!empty($dj->profile_image) || $dj->profile_image != null || $dj->profile_image != " ") {

                    $image_path = base_path('public/uploads/djs/' . $dj->profile_image);
                    if (file_exists($image_path) && is_file($image_path)) {
                        $dj_prof_image = env('APP_URL') . '/uploads/djs/' . $dj->profile_image;
                    }
                }

                $data['profile_image'] = $dj_prof_image;
                $data['user_type'] = 'dj';
                $data['clubs'] = $assigned_clubs;

                DB::commit();
                $response['error'] = false;
                $response['status_code'] = '200';
                $response['message'] = 'DJ Details';
                $response['data'] = $data;
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

    //    DJ LOGIN
    public function djLogin(Request $request)
    {
        $response = [];
        try {
            $email = $request->email;
            $user_name = $request->user_name;
            $password = $request->password;
            if (empty($email) && empty($user_name)) {
                throw new \Exception("'Please enter email or user name to proceed login.'");
            }

            if (empty($email) && !empty($user_name)) {
                $djData = DJ::query()->where('user_name', '=', $user_name)->first();
                if (!$djData) {
                    throw new \Exception('Sorry, We couldn\'t find any Dj with this user name!');
                }
            }
            if (!empty($email) && empty($user_name)) {
                $djData = DJ::query()->where('email', '=', $email)->first();
                if (!$djData) {
                    throw new \Exception('Sorry, We couldn\'t find any Dj with this email address!!');
                }
            }

            if (!empty($email) && !empty($user_name)) {
                $djData = DJ::query()->where('email', '=', $email)->first();
                if (!$djData) {
                    $djData = DJ::query()->where('user_name', '=', $user_name)->first();
                    if (!$djData) {
                        throw new \Exception('Sorry, We couldn\'t find any Dj with this email address!');
                    }
                }
            }
            if (empty($password)) {
                throw new \Exception('Please, Fill password to login.');
            }
            if (isset($djData)) {
                if ($djData->password == $password) {
                    $club_names = [];
                    $club_ids = Pivot_Dj_Club::query()->where('dj_id', '=', $djData->id)->pluck('club_id');
                    $clubs = Club::query()
                        ->whereIn('id', $club_ids)
                        ->get();
                    $assigned_clubs = [];
                    if ($clubs->count() > 0) {
                        foreach ($clubs as $c_item) {
                            $c = [];
                            $c['id'] = $c_item->id;
                            $c['club_name'] = $c_item->name;

                            $prof_image = "";
                            if (!empty($c_item->profile_image) || $c_item->profile_image != null || $c_item->profile_image != " ") {

                                $image_path = base_path('public/uploads/clubs/' . $c_item->profile_image);
                                if (file_exists($image_path) && is_file($image_path)) {
                                    $prof_image = env('APP_URL') . '/uploads/clubs/' . $c_item->profile_image;
                                }
                            }
                            $c['club_image'] = $prof_image;
                            array_push($assigned_clubs, $c);
                        }
                    }

                    foreach ($clubs as $club) {
                        array_push($club_names, $club->name);
                    }
                    $data['id'] = $djData->id;
                    $data['name'] = $djData->name;
                    $data['user_name'] = $djData->user_name;
                    $data['email'] = $djData->email;

                    $dj_prof_image = "";
                    if (!empty($djData->profile_picture_url))
                    {
                        $dj_prof_image=$djData->profile_picture_url;
                    }
                    if (!empty($djData->profile_image) || $djData->profile_image != null || $djData->profile_image != " ") {

                        $image_path = base_path('public/uploads/djs/' . $djData->profile_image);
                        if (file_exists($image_path) && is_file($image_path)) {
                            $dj_prof_image = env('APP_URL') . '/uploads/djs/' . $djData->profile_image;
                        }
                    }

                    $djData->flag = true;
                    $djData->save();
                    DB::commit();
                    $data['profile_image'] = $dj_prof_image;
                    $data['logged_in'] = $djData->flag;
                    $data['clubs'] = $assigned_clubs;
                    $data['user_type'] = 'dj';

                    $response['error'] = false;
                    $response['status_code'] = '200';
                    $response['message'] = 'You\'re logged in now!';
                    $response['data'] = $data;
                } else {
                    throw new \Exception('You\'ve entered a wrong password!');
                }
            } else {
                throw new \Exception('Sorry, We couldn\'t find any DJ with provided information');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $response['error'] = true;
            $response['status_code'] = '401';
            $response['message'] = $e->getMessage();
        } finally {
            return response()->json($response);
        }
    }

    //      USER LOGIN VIA SOCIAL PLATFORMS
    public function socialDjLogin(Request $request)
    {
        $response = [];
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required_without:client_id|email',
                'client_id' => 'required_without:email',
                'registeration_type' => 'required',
            ],
                ['email.required_without' => 'The :attribute field can not be blank if you\'re not passing client id.',
                    'client_id.required_without' => 'The :attribute field can not be blank if you\'re not passing email..']

            );
            if ($validator->fails()) {
                $response['error'] = true;
                $response['status_code'] = '401';
                $response['message'] = $validator->errors()->first();
            } else {
                if ($request->has('client_id')) {
                    $dj = DJ::query()->where('client_id', '=', $request->client_id)
                        ->where('registeration_type', '=', $request->registeration_type)
                        ->first();
                }
                if ($request->has('email') && $request->email!='') {
                    $dj = DJ::query()->where('email','=', $request->email)->first();
                    if ($dj && $dj->deleted_at == null && $request->has('client_id') && $request->has('registeration_type'))
                    {
                        if ($request->client_id!='' && $request->registeration_type!='')
                        {

                            $dj->client_id = $request->client_id;
                            $dj->registeration_type = $request->registeration_type;
                            $dj->flag = 1;
                            $dj->save();
                            $logged_in = false;
                            if ($dj->flag == 1) {
                                $logged_in = true;
                            }
                            $dj['logged_in'] = $logged_in;
                            $dj_prof_image = "";
                            if (!empty($dj->profile_picture_url))
                            {
                                $dj_prof_image=$dj->profile_picture_url;
                            }
                            if (!empty($dj->profile_image) || $dj->profile_image != null || $dj->profile_image != " ") {
                                $image_path = base_path('public/uploads/djs/' . $dj->profile_image);
                                if (file_exists($image_path) && is_file($image_path)) {
                                    $dj_prof_image = env('APP_URL') . '/uploads/djs/' . $dj->profile_image;
                                }
                            }
                            $dj['registeration_type'] = User::$registeration_type{$dj->registeration_type};
                            unset($dj['created_at']);
                            unset($dj['updated_at']);
                            unset($dj['deleted_at']);
                            unset($dj['profile_image']);
                            unset($dj['password']);
                            unset($dj['flag']);
                            unset($dj['assigned_clubs']);
                            unset($dj['client_id']);
                            unset($dj['oauth_key']);
                            unset($dj['locatione']);
                            if ($dj->registeration_type == 1) {
                                unset($dj['client_id']);
                                unset($dj['oauth_key']);
                            }
                            $dj['profile_image'] = $dj_prof_image;
                            $dj['user_type'] = 'dj';

                            $response['error'] = false;
                            $response['status_code'] = '200';
                            $response['message'] = 'You\'re logged in now!';
                            $response['data'] = $dj;
                        }
                    }

                    $dj = DJ::query()->where('email', '=', $request->email)
                        ->where('registeration_type', '=', $request->registeration_type)->first();
                }
                if (!$dj) {
                    throw new \Exception("Sorry, We couldn't find any user with provided login details.");
                }
                if (!empty($dj)) {
                    DB::beginTransaction();
                    if ($dj->deleted_at == null) {
                        $dj->flag = 1;
                        $dj->save();
                        DB::commit();

                        $logged_in = false;
                        if ($dj->flag == 1) {
                            $logged_in = true;
                        }
                        $dj['logged_in'] = $logged_in;
                        $dj_prof_image = "";
                        if (!empty($dj->profile_picture_url))
                        {
                            $dj_prof_image=$dj->profile_picture_url;
                        }
                        if (!empty($dj->profile_image) || $dj->profile_image != null || $dj->profile_image != " ") {
                            $image_path = base_path('public/uploads/djs/' . $dj->profile_image);
                            if (file_exists($image_path) && is_file($image_path)) {
                                $dj_prof_image = env('APP_URL') . '/uploads/djs/' . $dj->profile_image;
                            }
                        }
                        $dj['registeration_type'] = User::$registeration_type{$dj->registeration_type};
                        unset($dj['created_at']);
                        unset($dj['updated_at']);
                        unset($dj['deleted_at']);
                        unset($dj['profile_image']);
                        unset($dj['password']);
                        unset($dj['flag']);
                        unset($dj['assigned_clubs']);
                        unset($dj['client_id']);
                        unset($dj['oauth_key']);
                        unset($dj['locatione']);
                        if ($dj->registeration_type == 1) {
                            unset($dj['client_id']);
                            unset($dj['oauth_key']);
                        }
                        $dj['profile_image'] = $dj_prof_image;
                        $dj['user_type'] = 'dj';

                        $response['error'] = false;
                        $response['status_code'] = '200';
                        $response['message'] = 'You\'re logged in now!';
                        $response['data'] = $dj;

                    } else {
                        throw new \Exception("Sorry, Login denied. Because this user has been deleted by Super Admin. Contact administration for any help.");
                    }
                } else {
                    throw new \Exception("Sorry, We couldn't find any user with provided login details.");
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $response['error'] = true;
            $response['status_code'] = '401';
            $response['message'] = $e->getMessage();
        } finally {
            return response()->json($response);
        }
    }

    //    LOG OUT
    public function logOut(Request $request)
    {
        $response = [];
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'id' => 'required',
            ]);
            if ($validator->fails()) {
                $response['error'] = true;
                $response['status_code'] = '401';
                $response['message'] = $validator->errors()->first();
            } else {
                $user = DJ::query()->find($request->id);
                if ($user) {
                    if ($user->deleted_at == null) {
                        if ($user->flag != 0) {
                            $user->flag = 0;
                        }
                        $user->save();
                        DB::commit();
                        $response['error'] = false;
                        $response['status_code'] = '200';
                        $response['message'] = 'You\'ve been logged out successfully!';
                        $response['data'] = ['user_type' =>'dj'];

                    } else {
                        throw new \Exception('Sorry, You can\'nt make any changes as this DJ has been deleted by Super Admin. Contact Administration for help!.');
                    }
                } else {
                    throw new \Exception('Sorry, We couldn\'t find any DJ with provided information.');
                }
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

    //    DJ'S CLUBSRecentSearch
    public function djClubList(Request $request)
    {

        $response = [];
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'web' => 'sometimes|boolean',
//                'city' => 'required_if:web,1|string',
            ]);

            // echo "Here";
            if ($validator->fails()) {
                $response['error'] = true;
                $response['status_code'] = '401';
                $response['message'] = $validator->errors()->first();
            } else {



                $id = $request->id;
                $web = $request->web;
                $dj = DJ::query()->find($id);

                if (empty($dj)) {
                    throw new \Exception("Sorry, No dj exists with provided info.");
                }

                $club_names = [];
                $club_ids = Pivot_Dj_Club::query()->where('dj_id', '=', $id)->pluck('club_id');
                $clubs = Club::query()->whereIn('id', $club_ids)->get();
                $assigned_clubs = [];

                // 


                if ($clubs->count() > 0) {
                    foreach ($clubs as $c_item) {
                        $c = [];
                        $c['id'] = $c_item->id;
                        $c['name'] = $c_item->name;

                        $prof_image = "";
                        if (!empty($c_item->profile_image) || $c_item->profile_image != null || $c_item->profile_image != " ") {
                            $image_path = base_path('public/uploads/clubs/' . $c_item->profile_image);
                            if (file_exists($image_path) && is_file($image_path)) {
                                $prof_image = env('APP_URL') . '/uploads/clubs/' . $c_item->profile_image;
                            }
                        }
                        $c['live']=CurlHelper::is_live($c_item->id);
                        $c['profile_image'] = $prof_image;
                        array_push($assigned_clubs, $c);
                    }
                }
                foreach ($clubs as $club) {
                    array_push($club_names, $club->name);
                }

                $recent_clubs = [];
                $suggested_clubs = [];

                $r_club_ids = RecentSearch::query()->where('user_id', '=', $id)->orderBy('updated_at','DESC')->limit(4)->pluck('club_id');

                if ($r_club_ids->count() > 0) {
                    foreach ($r_club_ids as $r_item) {
                        $c_item = Club::query()->find($r_item);
                        if (isset($c_item)) {
                            $c = [];
                            $c['id'] = $c_item->id;
                            $c['name'] = $c_item->name;

                            $prof_image = "";
                            if (!empty($c_item->profile_image) || $c_item->profile_image != null || $c_item->profile_image != " ") {

                                $image_path = base_path('public/uploads/clubs/' . $c_item->profile_image);
                                if (file_exists($image_path) && is_file($image_path)) {
                                    $prof_image = env('APP_URL') . '/uploads/clubs/' . $c_item->profile_image;
                                }
                            }
                            $c['live']=CurlHelper::is_live($c_item->id);
                            $c['profile_image'] = $prof_image;
                            array_push($recent_clubs, $c);
                        }
                    }
                }
//                if ($request->has('web') && $web== true) {
//
//                    /* FETCHING SUGGESTED CLUBS */
//                    $city_name = $request->input('city');
//                    $state_id = 0;
//                    $city_id = 0;
//                    foreach (Club::$cities as $key => $val) {
//                        if (strcasecmp($val['Title'], $city_name) == 0) {
//                            $city_id = $key;
//                            $state_id = $val['State'];
//                        }
//                    }
//                    if ($state_id == 0) {
//                        foreach (Club::$states as $key => $val) {
//                            if (strcasecmp($val['Title'], $city_name) == 0) {
//                                $state_id = $key;
//                            }
//                        }
//                    }
//                    foreach (Club::$states as $key => $val) {
//                        if ($val['id'] == $state_id) {
//                            $country_id = $val['Country'];
//                        }
//                    }
//                    if (isset($country_id) && ($country_id != '' || $country_id != null)) {
//                        $country_clubs = Club::query()->where('country', '=', $country_id)->inRandomOrder();
//                    } else {
//                        $country_clubs = Club::query()->where('country', '=', 1)->inRandomOrder();
//                    }
//                    if ($country_clubs->count() > 0) {
//                        if ($country_clubs->count() > 20) {
//                            $country_clubs->limit(20);
//                        }
//                        $sugg_clubs = $country_clubs->get();
//                        foreach ($sugg_clubs as $country_club) {
//                            $ele_club['club_id'] = $country_club->id;
//                            $ele_club['name'] = $country_club->name;
//                            if (!empty($country_club->profile_image) || $country_club->profile_image != null || $country_club->profile_image != " ") {
//                                $image_path = base_path('public/uploads/clubs/' . $country_club->profile_image);
//                                if (file_exists($image_path) && is_file($image_path)) {
//                                    $ele_club['profile_image'] = env('APP_URL') . '/uploads/clubs/' . $country_club->profile_image;
//                                } else {
//                                    $ele_club['profile_image'] = "";
//                                }
//                            }
//                            array_push($suggested_clubs, $ele_club);
//                        }
//                    }
//
//                }

                $dj_prof_image = "";
                if (!empty($dj->profile_picture_url))
                {
                    $dj_prof_image = $dj->profile_picture_url;
                }
                if (!empty($dj->profile_image) || $dj->profile_image != null || $dj->profile_image != " ") {

                    $image_path = base_path('public/uploads/djs/' . $dj->profile_image);
                    if (file_exists($image_path) && is_file($image_path)) {
                        $dj_prof_image = env('APP_URL') . '/uploads/djs/' . $dj->profile_image;
                    }
                }
                DB::commit();
                $response['error'] = false;
                $response['status_code'] = '200';
                $response['message'] = 'DJ Clubs';
                $response['user_type'] = 'dj';
                $response['profile_image'] = $dj_prof_image;
                $response['clubs'] = $assigned_clubs;
                $response['recent_clubs'] = $recent_clubs;
//                $response['suggested_clubs'] = $suggested_clubs;

                // dd($response);
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

    //        FORGOT PASSWORD API
    public function forgotPassword(Request $request)
    {

        $response = [];
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'email' => 'required_without:user_name|exists:djs,email',
                'user_name' => 'required_without:email|exists:djs,user_name',
                'web' =>  'sometimes|boolean'
            ]);
            if ($validator->fails()) {
                $response['error'] = true;
                $response['status_code'] = '401';
                $response['message'] = $validator->errors()->first();
            } else {
                $email = $request->input('email');
                $user_name = $request->input('user_name');
                if (empty($email) && !empty($user_name)) {
                    $dj = DJ::query()->where('user_name', '=', $user_name)->first();
                    if (!$dj) {
                        throw new \Exception("Sorry, We couldn't find any DJ existing with this user name!");
                    } elseif ($dj->deleted_at != '') {
                        throw new \Exception('Sorry, You can\'t login by this user name as this DJ has been deleted by Super Admin. Contact Administration for more help!');
                    }
                }
                if (empty($user_name) && !empty($email)) {
                    $dj = DJ::query()->where('email', '=', $email)->first();
                    if (!$dj) {
                        throw new \Exception('Sorry, We couldn\'t find any DJ existing with this email!');
                    } elseif ($dj->deleted_at != '') {
                        throw new \Exception('Sorry, You can\'t login by this email as this DJ has been deleted by Super Admin. Contact Administration for more help!');
                    }
                }

                if (isset($dj) && ($dj->deleted_at == null || $dj->deleted_at == "")) {
                    $dj->reset_token =TokenHelper::alphaNumericToken(Str::random(15));
                    $dj->save();

                    $link = env('APP_URL') . '/dj/reset-password/' .$dj->reset_token;
                    if ($request->has('web') && !empty($request->web) && $request->web == true) {

                        $link = env('WEB_APP_URL').'/reset-password/' . $dj->reset_token;
                    }
                    $mail = new EmailConfiguration();
                    $mail->email_type = 0;
                    $mail->mail_from = 'developer.iapptechnologies@gmail.com';
                    $mail->mail_to = $dj->email;
                    $mail->mail_subject = "[CatchApp] DJ Password reset link";
                    $mail->mail_content = 'Hi ' . $dj->name . '! Please visit this link to reset your CatchApp login password. <br><a href=' . $link . '>Click here!</a>';
                    $mail->is_sent = 0;
                    $mail->save();
//
                    // SENDING MAIL
                    if (isset($mail)) {
                        Mail::to($mail->mail_to)
                            ->queue(new SendMailable($mail));

                        // If EMAIL ISN'T SENT
                        if (Mail::failures()) {
                            throw new \Exception('Mail could\'nt be send.');
                        } else {
                            $mail->is_sent = 1;
                            $mail->save();
                            $msg = "Password reset link is sent on " . MaskHelper::maskEmail($dj->email) . '. Check your inbox.';
                            DB::commit();

                            $response['error'] = false;
                            $response['status_code'] = '200';
                            $response['data'] = $msg;
                            $response['user_type'] = 'dj';
                        }
                    }

                } else {
                    throw new \Exception('Sorry, We couldn\'t find any DJ with provided info. Contact Administration for more help!');
                }
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

    public function updateDjPasswordForWeb(Request $request)
    {

        $response = [];
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'token' => 'required|exists:djs,reset_token',
                'password' => 'required',
            ]);
            if ($validator->fails()) {
                $response['error']= true;
                $response['status_code'] = '401';
                $response['message'] = $validator->errors()->first();
            } else {

                $token = $request->input('token');
                $dj = DJ::query()->where('reset_token','=', $token)->first();
                if (!$dj)
                {
                    throw new \Exception('Un-authorized attempt.');
                }

                $dj->password = $request->password;
                $dj->reset_token =TokenHelper::alphaNumericToken(Str::random(15));
                $dj->save();
                DB::commit();
                $response['error'] = false;
                $response['status_code'] = '200';
                $response['message'] = 'Password updated successfully.';
                $response['data'] = ['user_type' => 'dj'];
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

    //      SEARCH CLUB BY NAME
    public function searchClub(Request $request)
    {
        $response = [];
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'id' => 'sometimes|exists:djs,id',
                'user_id' => 'sometimes|exists:users,id',
                'text' => 'required',
                'city_name' => 'sometimes',
                'country' => 'sometimes'
            ]);
            if ($validator->fails()) {
                $response['error'] = true;
                $response['status_code'] = '401';
                $response['message'] = $validator->errors()->first();
            } else {
                $user_type= 'user';
                if ($request->has('id') && !empty($request->id)) {
                    $user = DJ::query()->find($request->id);
                    $user_type='dj';
                    $club_ids = Pivot_Dj_Club::query()->where('dj_id', '=', $request->id)->pluck('club_id');
                    $query= Club::query()
                        ->whereIn('id', $club_ids);
                }
                else
                {
                    $query= Club::query();
                }
                if ($request->has('user_id') && !empty($request->user_id)) {
                    $user = User::query()->find($request->id);
                    $user_type='user';
                }

                $country_id=0;
                if ($request->has('country') && !empty($request->country)) {
                    foreach (Club::$countries as $key => $val) {
                        if (strcasecmp($val, $request->country) == 0) {
                            $country_id = $key;
                        }
                    }
                }
                $city_id=0;
                $state_id=0;
                if ($request->has('city_name') && !empty($request->city_name)) {
                    $cities = City::query()->get();
                    foreach ($cities as $city) {
                        if (strtoupper($city->name) == strtoupper($request->city_name)) {
                            $city_id = $city->id;
                            $state_id = $city->state_id;
                            $country_id = $city->country_id;
                        }
                    }
                }

                $club_names = [];
                $query->where('name','LIKE','%' .$request->text.'%'  );
                if (!($request->has('id')) && empty($request->id) && $city_id!=0 && !empty($city_id))
                {
                    $query->where('city','=',$city_id);
                }
                if ($query->count()==0)
                {
                    $query->where('name','LIKE','%' .$request->text.'%'  );
                }
                $clubs= $query->get();
                $searched_clubs=[];
                if ($clubs->count() > 0) {
                    foreach ($clubs as $c_item) {
                        $c = [];
                        if ($request->has('id') && !empty($request->id)) {
                            $c['id'] = $c_item->id;
                        }
                        else
                        {
                            $c['club_id'] = $c_item->id;
                        }
                        $c['name'] = $c_item->name;

                        $prof_image = "";
                        if (!empty($c_item->profile_image) || $c_item->profile_image != null || $c_item->profile_image != " ") {

                            $image_path = base_path('public/uploads/clubs/' . $c_item->profile_image);
                            if (file_exists($image_path) && is_file($image_path)) {
                                $prof_image = env('APP_URL') . '/uploads/clubs/' . $c_item->profile_image;
                            }
                        }
                        $c['profile_image'] = $prof_image;
                        $c['live'] = CurlHelper::is_live($c_item->id);
                        array_push($searched_clubs, $c);
                    }
                }


                $nearby_clubs = [];
                $suggested_clubs = [];
                /* FETCHING NEAR BY CLUBS */
                if ($request->has('country') && !empty($request->country)) {
                    $countries = Country::all();
                    foreach ($countries as $c)
                    {
                        if (strpos(strtoupper($c->name), strtoupper($request->country))  !== false)
                        {
                            $main_country_id = $c->id;
                        }
                    }
                    $state_ids = [];

                    if (isset($main_country_id))
                    {
                        $state_ids = State::query()->where('country_id','=',$main_country_id)->pluck('id')->toArray();

                    }
                }
                $nearby_clubs = [];
                $suggested_clubs = [];
                $near_by_number = 0;

                if (isset($country_id) && ($country_id != '' && $country_id != null && $country_id!=0)) {
                    $country_clubs = Club::query()->where('country', '=', $country_id)->inRandomOrder();
                } else {
                    $country_clubs = Club::query()->where('country', '=', 1)->inRandomOrder();
                }
                if ($country_clubs->count() == 0) {
                    $country_clubs = Club::query()->where('country', '=', 1)->inRandomOrder();
                }
                if ($country_clubs->count() > 0) {
                    if ($country_clubs->count() > 20) {
                        $country_clubs->limit(20);
                    }
                    $sugg_clubs = $country_clubs->get();
                    foreach ($sugg_clubs as $country_club) {
                        $ele_club['club_id'] = $country_club->id;
                        $ele_club['name'] = $country_club->name;
                        if (!empty($country_club->profile_image) || $country_club->profile_image != null || $country_club->profile_image != " ") {
                            $image_path = base_path('public/uploads/clubs/' . $country_club->profile_image);
                            if (file_exists($image_path) && is_file($image_path)) {
                                $ele_club['profile_image'] = env('APP_URL') . '/uploads/clubs/' . $country_club->profile_image;
                            } else {
                                $ele_club['profile_image'] = "";
                            }
                        }
                        $ele_club['live'] = CurlHelper::is_live($country_club->id);

                        array_push($suggested_clubs, $ele_club);
                    }
                }

                if (isset($state_ids) && count($state_ids) > 0) {
                    foreach ($state_ids as $state_id) {
                        $query = Club::query();
                        $state_clubs = $query->where('state', '=', $state_id)->inRandomOrder()->get();
                        if ($state_clubs->count() > 0) {
                            foreach ($state_clubs as $state_club) {
                                if ($near_by_number < 20) {
                                    $ele_club['club_id'] = $state_club->id;
                                    $ele_club['name'] = $state_club->name;
                                    if (!empty($state_club->profile_image) || $state_club->profile_image != null || $state_club->profile_image != " ") {
                                        $image_path = base_path('public/uploads/clubs/' . $state_club->profile_image);
                                        if (file_exists($image_path) && is_file($image_path)) {
                                            $ele_club['profile_image'] = env('APP_URL') . '/uploads/clubs/' . $state_club->profile_image;
                                        } else {
                                            $ele_club['profile_image'] = "";
                                        }
                                    }
                                    $ele_club['live'] = CurlHelper::is_live($state_club->id);

                                    array_push($nearby_clubs, $ele_club);
                                    $near_by_number++;
                                }
                            }
                        }

                    }
                } else {
                    $nearby_clubs = $suggested_clubs;
                }
                /* FETCHING NEAR BY CLUBS */



                $user_prof_image = "";

                if(isset($user)) {
                    if (!empty($user->profile_picture_url))
                    {
                        $user_prof_image=$user->profile_picture_url;
                    }
                    if (!empty($user->profile_image) || $user->profile_image != null || $user->profile_image != " ") {
                        if ($request->has('id') && !empty($request->id)) {
                            $image_path = base_path('public/uploads/djs/' . $user->profile_image);
                        } else {
                            $image_path = base_path('public/uploads/users/' . $user->profile_image);
                        }
                        if (file_exists($image_path) && is_file($image_path)) {
                            if ($request->has('id') && !empty($request->id)) {
                                $user_prof_image = env('APP_URL') . '/uploads/djs/' . $user->profile_image;
                            } else {
                                $user_prof_image = env('APP_URL') . '/uploads/users/' . $user->profile_image;
                            }
                        }
                    }
                }
                DB::commit();
                if($user_type=='dj') {
                    $response['error'] = false;
                    $response['status_code'] = '200';
                    $response['message'] = 'Searched Clubs';
                    $response['clubs'] = $searched_clubs;
                    $response['profile_image'] = $user_prof_image;
                    $response['user_type'] = $user_type;
                }
                else
                {
                    $response['error'] = false;
                    $response['status_code'] = '200';
                    $response['message'] = 'Searched Clubs';
                    $response['data'] = $searched_clubs;
                    $response['nearby_clubs'] = $nearby_clubs;
                    $response['suggested_clubs'] = $suggested_clubs;
                    $response['location'] = $request->country;
                    $response['user_type'] = $user_type;
                }
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

    public function updateClub(Request $request)
    {
        $response = [];
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:clubs,id',
                'picture' => 'required|image|mimes:jpeg,gif,bmp,png,svg+xml',
            ]);
            if ($validator->fails()) {
                $response['error'] = true;
                $response['status_code'] = '401';
                $response['message'] = $validator->errors()->first();
            } else {
                $club = Club::query()->find($request->id);
                if ($club->profile_image!=''){
                    $image_path = base_path('public/uploads/clubs/'.$club->profile_image);
                    if(file_exists($image_path)&& is_file($image_path)) {
                        unlink($image_path);
                    }
                }

                $photo = $request->file('picture');
                $extension = $photo->getClientOriginalExtension();
                $filename = 'club-photo-' . time() . '.' . $extension;
//                $photo->move(base_path('public/uploads/clubs/'), $filename);




                /**** compress image before uploading ****/
                $extension = $photo->getClientOriginalExtension();
                $filename = 'club-photo-' . time() . '.' . $extension;

                $imageUploadPath = base_path().'/public/uploads/clubs/' . $filename;
                $fileType = pathinfo($imageUploadPath, PATHINFO_EXTENSION);

                // Allow certain file formats
                $allowTypes = array('jpg','png','jpeg','gif');
                if(in_array($fileType, $allowTypes)) {
                    // Image temp source
//                    $imageTemp = $_FILES["image"]["tmp_name"];

                    // Compress size and upload image
                    $compressedImage = MediaHelper::compressImage($photo, $imageUploadPath, 75);

                    if ($compressedImage) {
                        $club->profile_image = $filename;
                        $club->save();
                    }
                }
                /**** compress image before uploading ****/


                DB::commit();
                $response['error'] = false;
                $response['status_code'] = '200';
                $response['message'] = 'Searched Clubs';
                $response['data'] = $club;
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

    public function djCheckEmail(Request $request)
    {
        $response = [];
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'client_id' => 'required',
                'registration_type' => 'required',
            ]);
            if ($validator->fails()) {
                $response['error'] = true;
                $response['status_code'] = '401';
                $response['message'] = $validator->errors()->first();
            } else {
                $status = 0;
                $email = "";
                $message='Fresh User';
                $dj = DJ::query()->where('client_id', '=', $request->client_id)
                    ->where('registeration_type','=', $request->registration_type)->first();
                if ($dj) {
                    $status =1;
                    $message='Blank Email';
                    if (!empty($dj->email))
                    {
                        $status=2;
                        $email = $dj->email;
                        $message='Email Filled';
                    }
                }


                DB::commit();
                $response['error'] = false;
                $response['status_code'] = '200';
                $response['message'] = $message;
                $response['status'] = $status;
                $response['email'] = $email;

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

    public function userCheckEmail(Request $request)
    {
        $response = [];
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'client_id' => 'required',
                'registration_type' => 'required',
            ]);
            if ($validator->fails()) {
                $response['error'] = true;
                $response['status_code'] = '401';
                $response['message'] = $validator->errors()->first();
            } else {
                $status = 0;
                $email="";
                $message='Fresh User';
                $user = User::query()->where('client_id', '=', $request->client_id)
                    ->where('registeration_type','=', $request->registration_type)->first();
                if ($user) {
                    $status =1;
                    $message='Blank Email';
                    if (!empty($user->email))
                    {
                        $status=2;
                        $email =$user->email;
                        $message='Email Filled';
                    }
                }


                DB::commit();
                $response['error'] = false;
                $response['status_code'] = '200';
                $response['message'] = $message;
                $response['status'] = $status;
                $response['email'] = $email;
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

    public function djSocialLoginForWeb(Request $request)
    {
        $response = [];
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email',
                'user_name' => 'required',
                'registeration_type' => 'required|in:2,3',
                'birth_date' => 'date',
                'gender' => 'sometimes|in:male,female,other',
                'profile_image' => 'sometimes|mimes:jpeg,gif,bmp,png,svg+xml',
                'client_id' => 'required',
                'oauth_key' => 'required',
            ]);
            if ($validator->fails()) {
                $response['error'] = true;
                $response['status_code'] = '401';
                $response['message'] = $validator->errors()->first();
            } else {
                $client_id = $request->input('client_id');
                $dj = DJ::query()->where('client_id', '=', $client_id)
                    ->where('registeration_type','=', $request->registeration_type)->first();
                if (!$dj)
                {
                    $dj = DJ::query()->where('email','=', $request->email)->first();
                }
                if ($dj) {
                    if ($request->has('profile_picture_url') && !empty($request->profile_picture_url))
                    {
                        $dj->profile_picture_url = $request->profile_picture_url;
                    }
                    $dj->flag = 1;
                    $dj->client_id = $request->client_id;
                    $dj->registeration_type = $request->registeration_type;
                    $dj->oauth_key = $request->oauth_key;
                    $dj->save();
                    $logged_in = false;
                    if ($dj->flag == 1) {
                        $logged_in = true;
                    }
                    $dj['logged_in'] = $logged_in;
                    $dj_prof_image = "";
                    /*if (!empty($dj->profile_picture_url)) {
                        $dj_prof_image = $dj->profile_picture_url;
                    }
                    if (empty($dj->profile_image) || $dj->profile_image == null || $dj->profile_image == " ") {
                        $image_path = base_path('public/uploads/djs/' . $dj->profile_image);
                        if (file_exists($image_path) && is_file($image_path)) {
                            $dj_prof_image = env('APP_URL') . '/uploads/djs/' . $dj->profile_image;
                        }
                    }*/

                    if (empty($dj->profile_image) || $dj->profile_image== null || $dj->profile_image == " ") {
                        if (!empty($dj->profile_picture_url))
                        {
                            $dj_prof_image = $dj->profile_picture_url;
                        }
                    }
                    else{
                        if (!empty($dj->profile_picture_url))
                        {
                            $dj_prof_image= $dj->profile_picture_url;
                        }
                        if (!empty($dj->profile_image) || $dj->profile_image!= null || $dj->profile_image != " ") {
                            $image_path = base_path('public/uploads/djs/' . $dj->profile_image);
                            if (file_exists($image_path) && is_file($image_path)) {
                                $dj_prof_image = env('APP_URL') . '/uploads/djs/' . $dj->profile_image;
                            }
                        }
                    }


                    $dj['registeration_type'] = User::$registeration_type{$dj->registeration_type};
                    unset($dj['created_at']);
                    unset($dj['updated_at']);
                    unset($dj['deleted_at']);
                    unset($dj['profile_image']);
                    unset($dj['password']);
                    unset($dj['flag']);
                    unset($dj['assigned_clubs']);
                    unset($dj['client_id']);
                    unset($dj['oauth_key']);
                    unset($dj['locatione']);
                    unset($dj['profile_picture_url']);
                    if ($dj->registeration_type == 1) {
                        unset($dj['client_id']);
                        unset($dj['oauth_key']);
                    }
                    $dj['profile_image'] = $dj_prof_image;
                    $dj['user_type'] = 'dj';

                    DB::commit();
                    $response['error'] = false;
                    $response['status_code'] = '200';
                    $response['message'] = 'You\'re logged in now!';
                    $response['data'] = $dj;
                }
                else
                {
                    $dj = new DJ();
                    $dj->name = $request->name;
                    $dj->email = $request->email;
                    $filename = '';
                    if ($request->hasFile('profile_image')) {
                        $file_extension = $request->profile_image->getClientOriginalExtension();
                        $filename = 'dj-profile-photo-' . time() . '.' . $file_extension;
                    }
                    if ($request->has('birth_date') && !empty($request->input('birth_date'))) {
                        $dob = Carbon::parse($request->input('birth_date'))->format('Y-m-d');
                    }
                    $dj->user_name = $request->user_name.time();
                    if ($request->has('profile_picture_url') && $request->profile_picture_url
                        && !empty($request->profile_picture_url)) {
                        $dj->profile_picture_url = $request->profile_picture_url;
                    }
                    if (isset($dob) && !empty($dob))
                    {
                        $dj->birth_date=$dob;
                    }
                    if ($request->has('gender') && $request->input('gender')!=' ' && $request->input('gender') != null) {
                        $dj->gender = $request->gender;
                    }
                    $dj->locatione = $request->location;
                    $dj->registeration_type = $request->registeration_type;
                    $dj->client_id = $request->client_id;
                    $dj->oauth_key = $request->oauth_key;
                    $dj->profile_image = $filename;
                    $dj->save();
                    $prof_image = "";
                    if ($request->hasFile('profile_image')) {
                        $request->profile_image->move(base_path('public/uploads/djs/'), $filename);
                        $image_path = base_path('public/uploads/djs/' . $filename);
                        if (file_exists($image_path) && is_file($image_path)) {
                            $prof_image = env('APP_URL') . '/uploads/djs/' . $filename;
                        }
                    }
                    if (!empty($dj->email)) {
                        /**** SEND AN WELCOME EMAIL TO NEW DJ ****/
                        $subject = 'Welcome to CatchApp!';
                        $mail_to = $dj->email;
                        $content = 'Hi ' . $dj->name . ' (DJ) ! Welcome to CatchApp.';
                        $type = EmailType::query()
                            ->join('email_addresses', 'email_types.id', '=',
                                'email_addresses.email_type', 'inner')->select('email_addresses.email_address as mail_from', 'email_types.*')
                            ->where('name', 'LIKE', '%' . 'new dj' . '%')->first();

                        $mail = new EmailConfiguration();
                        if ($type) {
                            $mail->email_type = $type->id;
                            $mail->mail_from = $type->mail_from;
                        } else {
                            $mail->email_type = 0;
                            $mail->mail_from = 'newdj@catchapp.com';
                        }
                        $mail->mail_to = $mail_to;
                        $mail->mail_subject = $subject;
                        $mail->mail_content = $content;
                        $mail->is_sent = 0;
                        $mail->save();

                        //      SENDING MAIL
                        if (isset($mail)) {
                            Mail::to($mail->mail_to)
                                ->queue(new SendMailable($mail));
                            // check for failures
                            if (Mail::failures()) {

                            } else {
                                $mail->is_sent = 1;
                                $mail->save();
                            }
                        }
                    }

                    /**** SEND AN WELCOME EMAIL TO NEW DJ ****/

                    unset($dj['created_at']);
                    unset($dj['updated_at']);
                    unset($dj['deleted_at']);
                    unset($dj['profile_image']);
                    unset($dj['password']);
                    $dj['profile_image'] = $prof_image;
                    $dj['user_type'] = 'dj';

                    DB::commit();
                    $response['error'] = false;
                    $response['status_code'] = '200';
                    $response['message'] = 'DJ registered successfully.';
                    $response['data'] = $dj;
                }
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

    public function userSocialLoginForWeb(Request $request)
    {
        $response = [];
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'first_name' => 'required',
                'last_name' => 'string',
                'email' => 'required|email',
                'user_name' => 'required',
                'register_type' => 'required|in:2,3',
                'client_id' => 'required',
                'oauth_key' => 'required',
                'birth_date' => 'date',
                'gender' => 'sometimes|in:male,female,other',
                'location' => 'required',
                'profile_image' => 'sometimes|mimes:jpeg,gif,bmp,png,svg+xml',
            ]);
            if ($validator->fails()) {
                $response['error'] = true;
                $response['status_code'] = '401';
                $response['message'] = $validator->errors()->first();
            }
            else {
                $client_id = $request->input('client_id');
                $user = User::query()->where('client_id', '=', $client_id)
                    ->where('registeration_type','=', $request->register_type)->first();
                if (!$user)
                {
                    $user = User::query()->where('email','=', $request->email)->first();
                }
                if ($user) {
                    if ($request->has('profile_picture_url') && !empty($request->profile_picture_url))
                    {
                        $user->profile_picture_url = $request->profile_picture_url;
                    }
                    if ($request->has('last_name') && !empty($request->last_name))
                    {
                        $user->last_name = $request->last_name;
                    }
                    if ($request->has('location') && !empty($request->location))
                    {
                        $user->location = $request->location;
                    }
                    $user->client_id = $request->client_id;
                    $user->registeration_type = $request->register_type;
                    $user->oauth_key = $request->oauth_key;
                    $user->save();
                    $logged_in = false;
                    if ($user->is_active == 1) {
                        $logged_in = true;
                    }
                    $get_notification = false;
                    if ($user->get_notification==1)
                    {
                        $get_notification= true;
                    }

                    $user['logged_in'] = $logged_in;
                    $user['get_notification'] = $get_notification;
                    /*$user_prof_image = "";
                    if (!empty($user->profile_picture_url)) {
                        $user_prof_image = $user->profile_picture_url;
                    }
                    if (empty($user->profile_image) || $user->profile_image == null || $user->profile_image == " ") {
                        $image_path = base_path('public/uploads/users/' . $user->profile_image);
                        if (file_exists($image_path) && is_file($image_path)) {
                            $user_prof_image = env('APP_URL') . '/uploads/users/' . $user->profile_image;
                        }
                    }*/
                    $user_prof_image = '';
                    if (empty($user->profile_image) || $user->profile_image== null || $user->profile_image == " ") {
                        if (!empty($user->profile_picture_url))
                        {
                            $user_prof_image = $user->profile_picture_url;
                        }
                    }
                    else{
                        if (!empty($user->profile_picture_url))
                        {
                            $user_prof_image= $user->profile_picture_url;
                        }
                        if (!empty($user->profile_image) || $user->profile_image!= null || $user->profile_image != " ") {
                            $image_path = base_path('public/uploads/users/' . $user->profile_image);
                            if (file_exists($image_path) && is_file($image_path)) {
                                $user_prof_image = env('APP_URL') . '/uploads/users/' . $user->profile_image;
                            }
                        }
                    }

                    $user['registeration_type'] = User::$registeration_type{$user->registeration_type};
                    $user['user_id'] = $user->id;
                    unset($user['id']);
                    unset($user['created_at']);
                    unset($user['updated_at']);
                    unset($user['deleted_at']);
                    unset($user['profile_image']);
                    unset($user['password']);
                    unset($user['is_active']);
                    unset($user['client_id']);
                    unset($user['oauth_key']);
                    unset($user['profile_picture_url']);
//                    unset($user['location']);
                    $user['profile_image'] = $user_prof_image;

                    DB::commit();
                    $response['error'] = false;
                    $response['status_code'] = '200';
                    $response['message'] = 'You\'re logged in now!';
                    $response['data'] = $user;
                }
                else
                {
                    $user = new User();
                    $user->first_name = $request->first_name;
                    if ($request->has('last_name') && !empty($request->last_name))
                    {
                        $user->last_name = $request->last_name;
                    }
                    if ($request->has('location') && !empty($request->location))
                    {
                        $user->location = $request->location;
                    }
                    $user->email = $request->email;
                    $filename = '';
                    if ($request->hasFile('profile_image')) {
                        $file_extension = $request->profile_image->getClientOriginalExtension();
                        $filename = 'user-profile-photo-' . time() . '.' . $file_extension;
                    }
                    if ($request->has('birth_date') && !empty($request->input('birth_date'))) {
                        $dob = Carbon::parse($request->input('birth_date'))->format('Y-m-d');
                    }
                    $user->user_name = $request->user_name.time();
                    if ($request->has('profile_picture_url') && $request->profile_picture_url
                        && !empty($request->profile_picture_url)) {
                        $user->profile_picture_url = $request->profile_picture_url;
                    }
                    if (isset($dob) && !empty($dob))
                    {
                        $user->birth_date=$dob;
                    }
                    if ($request->has('gender') && $request->input('gender')!=' ' && $request->input('gender') != null) {
                        $user->gender = $request->gender;
                    }
                    $user->password = '';
                    $user->location = $request->location;
                    $user->registeration_type = $request->register_type;
                    $user->client_id = $request->client_id;
                    $user->oauth_key = $request->oauth_key;
                    $user->profile_image = $filename;
                    $user->save();
                    $prof_image = "";
                    if ($request->hasFile('profile_image')) {
                        $request->profile_image->move(base_path('public/uploads/users/'), $filename);
                        $image_path = base_path('public/uploads/users/' . $filename);
                        if (file_exists($image_path) && is_file($image_path)) {
                            $prof_image = env('APP_URL') . '/uploads/users/' . $filename;
                        }
                    }
                    if (!empty($user->email)) {
                        /**** SEND AN WELCOME EMAIL TO NEW USER ****/

                        $subject = 'Welcome to CatchApp!';
                        $mail_to = $user->email;
                        $content = 'Hi ' . $user->first_name . $user->last_name . ' (User) ! Welcome to CatchApp.';
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
                        /**** SEND AN WELCOME EMAIL TO NEW USER ****/
                    }

                    $user['registeration_type'] = User::$registeration_type{$user->registeration_type};

                    $user['user_id'] = $user->id;
                    unset($user['id']);
                    unset($user['created_at']);
                    unset($user['updated_at']);
                    unset($user['deleted_at']);
                    unset($user['profile_image']);
                    unset($user['password']);
                    unset($user['is_active']);
                    unset($user['client_id']);
                    unset($user['oauth_key']);
//                    unset($user['location']);
                    $user['profile_image'] = $prof_image;
                    DB::commit();
                    $response['error'] = false;
                    $response['status_code'] = '200';
                    $response['message'] = 'User registered successfully.';
                    $response['data'] = $user;
                }
            }
        }
        catch (\Exception $e) {
            DB::rollBack();
            $response['error'] = true;
            $response['status_code'] = '400';
            $response['message'] = $e->getMessage();
        } finally {
            return response()->json($response);
        }
    }

    public function liveDjList()
    {
        $response = [];
        try {

            $data=[];
            $clubs = Club::query()->get();
            if ($clubs->count()>0)
            {
                foreach ($clubs as $club)
                {
                    $liveDJ = CurlHelper::liveDj($club);
                    if ($liveDJ)
                    {
                        $dj = DJ::query()->find($liveDJ);
                        if (!empty($dj)) {
                            $tmp['dj_id'] = $liveDJ;
                            $tmp['club_id'] = $club->id;
                            $tmp['name'] = $club->name;

                            $club_prof_image = "";
                            if (!empty($club->profile_image) || $club->profile_image!= null || $club->profile_image != " ") {
                                $image_path = base_path('public/uploads/clubs/' . $club->profile_image);
                                if (file_exists($image_path)&& is_file($image_path) ) {
                                    $club_prof_image = env('APP_URL') . '/uploads/clubs/' . $club->profile_image;
                                }
                            }
                            $tmp['profile_image'] = $club_prof_image;
                            array_push($data,$tmp);
                        }
                    }
                }
            }
            DB::commit();
            $response['error'] = false;
            $response['status_code'] = '200';
            $response['message'] = 'Live Dj List';
            $response['data'] =$data;
        } catch (\Exception $e) {
            DB::rollBack();
            $response['error'] = true;
            $response['status_code'] = '400';
            $response['message'] = $e->getMessage();
        } finally {
            return response()->json($response);
        }
    }

}

