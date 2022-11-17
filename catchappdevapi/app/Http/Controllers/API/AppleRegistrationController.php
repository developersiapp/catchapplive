<?php


namespace catchapp\Http\Controllers\API;


use catchapp\Http\Controllers\Controller;
use catchapp\Models\AppleUsers;
use catchapp\Models\AppleDjs;
use catchapp\Models\DJ;
use catchapp\Models\User;
use catchapp\Models\UserDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AppleRegistrationController extends Controller
{
    public function userAppleRegister(Request $request)
    {
        $response = [];
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'email' => 'unique:users',
                'client_id' => 'required',
                'device_token' => 'required',
                'registeration_type' => 'required|in:6',
            ]);

            $validator->after(function ($validator) use ($request) {
                if ($request->has('email') && $request->input('email') != ' ' && $request->input('email') != null)
                {
                    $user = AppleUsers::query()->where('email', '=', $request->email)->first();
                    if ($user) {
                        $validator->errors()->add('email', 'The email has already been taken.');
                    }
                }
            });

            if ($validator->fails()) {
                $response['error']= true;
                $response['status_code'] = '401';
                $response['message'] = $validator->errors()->first();
            }
            else
            {
                $client_id = $request->input('client_id');
                $device_token = $request->input('device_token');
                $registeration_type = $request->input('registeration_type');
                if ($request->has('email') && $request->input('email') !=' ' && $request->input('email')!= null)
                {
                    $email = $request->input('email');
                }
                if ($request->has('first_name') && $request->input('first_name')!=' ' && $request->input('first_name')!=null)
                {
                    $first_name = $request->input('first_name');
                }
                if ($request->has('last_name') && $request->input('last_name')!=' ' && $request->input('last_name')!=null)
                {
                    $last_name = $request->input('last_name');
                }
                $user = User::query()->where('client_id','=',$client_id)
                    ->where('registeration_type','=',6)
                    ->first();
                $status = 1; /*** NEW USER ***/
                $message = 'Please proceed completing your profile.'; /*** NEW USER ***/
                if (!$user)
                {
                    $user = AppleUsers::query()->where('client_id','=',$client_id)->first();
                    if (!$user) {
                        $user = new AppleUsers();
                        $user->client_id = $client_id;
                    }
                    if ($user->email == ' ' || $user->email == null)
                    {
                        $user->email = isset($email)?$email:' ';
                    }
                    if ($user->first_name == ' ' || $user->first_name == null)
                    {
                        $user->first_name = isset($first_name)?$first_name:' ';
                    }
                    if ($user->last_name == ' ' || $user->last_name == null)
                    {
                        $user->last_name = isset($last_name)?$last_name:' ';
                    }
                    $user->device_token = $device_token;
                    $user->save();
                    $array=['created_at','updated_at'];
                }
                else
                {
                    $user->is_active =1;
                    $user->save();

                    $user['user_id'] = $user->id;
                    $array=['id','password' ,'created_at','updated_at','deleted_at'];


                    $user_device =UserDevice::query()
                        ->where('user_id','=', $user->id)
                        ->where('device_token','=', $device_token)->first();
                    if (!$user_device) {
                        $user_device = new UserDevice();
                        $user_device->user_id = $user->id;
                        $user_device->device_token = $device_token;
                        $user_device->save();
                    }

                    $status = 2; /*** Complete Profile USER ***/
                    $message = 'User registered successfully.';
                }



                foreach ($array as $var) {
                    unset($user[$var]);
                }
                $user['status']= $status;
                DB::commit();

                $response['error'] = false;
                $response['status_code'] = '200';
                $response['message'] = $message;
                $response['data'] =$user;
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

    public function userAppleUpdate(Request $request)
    {
        $response = [];
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'first_name' => 'required',
                'user_name' => 'required|string|max:255|unique:users',
                'client_id' => 'required',
                'email' => 'email|required|unique:users',
                'date_of_birth' => 'date',
                'device_token' => 'required',
            ]);


            $validator->after(function ($validator) use ($request) {
                if (($request->has('email') && $request->input('email') != ' ' && $request->input('email') != null)
                    && (($request->has('client_id') && $request->input('client_id') != ' ' && $request->input('client_id') != null)))
                {
                    $user = User::query()
                        ->where('client_id', '=', $request->client_id)
                        ->where('registeration_type', '=', 6)
                        ->first();
                    if ($user) {
                        $validator->errors()->add('client_id', 'User is already registered with us.');
                    }

                    $apple_user = AppleUsers::query()
                        ->where('client_id', '!=', $request->client_id)
                        ->where('email', '=', $request->email)->first();
                    if ($apple_user) {
                        $validator->errors()->add('email', 'The email has already been taken.');
                    }


                }

                if ($request->hasFile('profile_image')) {
                    $profile_image = $request->file('profile_image');
                    $allowedMimeTypes = ['image/jpeg', 'image/gif', 'image/png', 'image/bmp', 'image/svg+xml'];
                    $contentType = $profile_image->getMimeType();

                    if (!in_array($contentType, $allowedMimeTypes)) {
                        $validator->errors()->add('profile_image', 'File format isn\'t supported for profile picture.');
                    }
                }


            });


            if ($validator->fails()) {
                $response['error']= true;
                $response['status_code'] = '401';
                $response['message'] = $validator->errors()->first();
            }
            else
            {
                $device_token = $request->input('device_token');
                $client_id = $request->input('client_id');
                $email = $request->input('email');
                $first_name = $request->input('first_name');
                $user_name = $request->input('user_name');

                /*** optional fields ***/
                if ($request->has('last_name') && $request->input('last_name')!=' ' && $request->input('last_name') != null) {
                    $last_name = $request->input('last_name');
                }
                if ($request->has('date_of_birth') && $request->input('date_of_birth')!=' ' && $request->input('date_of_birth') != null) {
                    $date_of_birth = $request->input('date_of_birth');
                }
                if ($request->has('password') && $request->input('password')!=' ' && $request->input('password') != null) {
                    $password= $request->input('password');
                }
                if ($request->has('location') && $request->input('location')!=' ' && $request->input('location') != null) {
                    $location= $request->input('location');
                }

                if ($request->hasFile('profile_image') && !empty($request->file('profile_image')) ) {
                    $profile_image = $request->file('profile_image');

                    $filename = '';
                    if ($request->hasFile('profile_image')) {
                        $file_extension = $profile_image->getClientOriginalExtension();
                        $filename = 'user-profile-photo-' . time() . '.' . $file_extension;
                    }

                    $profile_image->move(base_path('public/uploads/users/'), $filename);
                    $prof_image = "";

                    $image_path = base_path('public/uploads/users/' . $filename);
                    if (file_exists($image_path)  && is_file($image_path)) {
                        $prof_image = env('APP_URL') . '/uploads/users/' . $filename;
                    }
                }
                /*** optional fields ***/

                $apple_user = AppleUsers::query()->where('client_id','=',$client_id)->first();

                if ($apple_user)
                {
                    $user = new User();
                    $user->email = isset($email)?$email:$apple_user->email;
                    $user->registeration_type = 6;
                    $user->first_name = isset($first_name)?$first_name:$apple_user->first_name;
                    $user->last_name = isset($last_name)?$last_name:$apple_user->last_name;
                    isset($date_of_birth)?($user->birth_date=$date_of_birth):'';
//                    $user->birth_date = isset($date_of_birth)?$date_of_birth:'';
                    $user->password = isset($password)?Hash::make($password):'';
                    $user->location = isset($location)?$location:'';
                    $user->user_name =$user_name;
                    $user->device_token =$device_token;
                    $user->profile_image =isset($filename)?$filename:'';
                    $user->client_id =$client_id;

                    $user->save();

                    $apple_user->delete();

                    $user_device =UserDevice::query()
                        ->where('device_token','=',$device_token)
                        ->where('user_id','=', $user->id)->first();
                    if (!$user_device) {
                        $user_device = new UserDevice();
                        $user_device->user_id = $user->id;
                        $user_device->device_token = $device_token;
                        $user_device->save();
                    }
                    $user['profile_image'] = isset($prof_image)?$prof_image:'';

                    $user['user_id'] = $user->id;
                    $array=['id','password' ,'created_at','updated_at','deleted_at'];

                    foreach ($array as $var) {
                        unset($user[$var]);
                    }

                    DB::commit();

                    $response['error'] = false;
                    $response['status_code'] = '200';
                    $response['message'] = 'User registered successfully';
                    $response['data'] =$user;
                }
                else
                {
                    throw new \Exception('Login with Apple first.');
                }


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


    public function djAppleRegister(Request $request)
    {
        $response = [];
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'email' => 'unique:djs',
                'client_id' => 'required',
//                'device_token' => 'required',
                'registeration_type' => 'required|in:6',
            ]);

            $validator->after(function ($validator) use ($request) {
                if ($request->has('email') && $request->input('email') != ' ' && $request->input('email') != null)
                {
                    $dj = AppleDjs::query()->where('email', '=', $request->email)->first();
                    if ($dj) {
                        $validator->errors()->add('email', 'The email has already been taken.');
                    }
                }
            });

            if ($validator->fails()) {
                $response['error']= true;
                $response['status_code'] = '401';
                $response['message'] = $validator->errors()->first();
            }
            else
            {
                $client_id = $request->input('client_id');
//                $device_token = $request->input('device_token');
                $registeration_type = $request->input('registeration_type');
                if ($request->has('email') && $request->input('email') !=' ' && $request->input('email')!= null)
                {
                    $email = $request->input('email');
                }
                if ($request->has('name') && $request->input('name')!=' ' && $request->input('name')!=null)
                {
                    $name = $request->input('name');
                }

                $dj = DJ::query()->where('client_id','=',$client_id)
                    ->where('registeration_type','=',$registeration_type)
                    ->first();
                $status = 1; /*** NEW DJ ***/
                $message = 'Please proceed completing your profile.'; /*** NEW DJ ***/
                if (!$dj)
                {
                    $dj = AppleDjs::query()->where('client_id','=',$client_id)->first();
                    if (!$dj) {
                        $dj = new AppleDjs();
                        $dj->client_id = $client_id;
                    }
                    if ($dj->email == ' ' || $dj->email == null)
                    {
                        $dj->email = isset($email)?$email:' ';
                    }
                    if ($dj->name == ' ' || $dj->name == null)
                    {
                        $dj->name = isset($name)?$name:' ';
                    }
                    if ($request->has('device_token') && $request->input('device_token')!=' ' && $request->input('device_token')!=null)
                    {
                        $device_token = $request->input('device_token');
                        $dj->device_token = $device_token;
                    }
                    $dj->save();
                    $array=['created_at','updated_at'];
                }
                else
                {
                    $dj->flag =1;
                    $dj->save();

                    $dj['dj_id'] = $dj->id;
                    $array=['id','password' ,'created_at','updated_at','deleted_at'];


                    $status = 2; /*** Complete Profile USER ***/
                    $message = 'User registered successfully.';
                }



                foreach ($array as $var) {
                    unset($dj[$var]);
                }
                $dj['status']= $status;
                DB::commit();

                $response['error'] = false;
                $response['status_code'] = '200';
                $response['message'] = $message;
                $response['data'] =$dj;
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

    public function djAppleUpdate(Request $request)
    {
        $response = [];
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'user_name' => 'required|string|max:255|unique:djs',
                'client_id' => 'required',
                'email' => 'email|required|unique:djs',
                'date_of_birth' => 'date',
            ]);


            $validator->after(function ($validator) use ($request) {
                if (($request->has('email') && $request->input('email') != ' ' && $request->input('email') != null)
                    && (($request->has('client_id') && $request->input('client_id') != ' ' && $request->input('client_id') != null)))
                {
                    $dj = DJ::query()
                        ->where('client_id', '=', $request->client_id)
                        ->where('registeration_type', '=', 6)
                        ->first();
                    if ($dj) {
                        $validator->errors()->add('client_id', 'DJ is already registered with us.');
                    }

                    $apple_dj = AppleDjs::query()
                        ->where('client_id', '!=', $request->client_id)
                        ->where('email', '=', $request->email)->first();
                    if ($apple_dj) {
                        $validator->errors()->add('email', 'The email has already been taken.');
                    }


                }

                if ($request->hasFile('profile_image')) {
                    $profile_image = $request->file('profile_image');
                    $allowedMimeTypes = ['image/jpeg', 'image/gif', 'image/png', 'image/bmp', 'image/svg+xml'];
                    $contentType = $profile_image->getMimeType();

                    if (!in_array($contentType, $allowedMimeTypes)) {
                        $validator->errors()->add('profile_image', 'File format isn\'t supported for profile picture.');
                    }
                }


            });


            if ($validator->fails()) {
                $response['error']= true;
                $response['status_code'] = '401';
                $response['message'] = $validator->errors()->first();
            }
            else
            {
                $client_id = $request->input('client_id');
                $email = $request->input('email');
                $name = $request->input('name');
                $user_name = $request->input('user_name');

                /*** optional fields ***/
                if ($request->has('date_of_birth') && $request->input('date_of_birth')!=' ' && $request->input('date_of_birth') != null) {
                    $date_of_birth = $request->input('date_of_birth');
                }
                if ($request->has('password') && $request->input('password')!=' ' && $request->input('password') != null) {
                    $password= $request->input('password');
                }
                if ($request->has('location') && $request->input('location')!=' ' && $request->input('location') != null) {
                    $location= $request->input('location');
                }

                if ($request->hasFile('profile_image') && !empty($request->file('profile_image')) ) {
                    $profile_image = $request->file('profile_image');

                    $filename = '';
                    if ($request->hasFile('profile_image')) {
                        $file_extension = $profile_image->getClientOriginalExtension();
                        $filename = 'dj-profile-photo-' . time() . '.' . $file_extension;
                    }

                    $profile_image->move(base_path('public/uploads/djs/'), $filename);
                    $prof_image = "";

                    $image_path = base_path('public/uploads/djs/' . $filename);
                    if (file_exists($image_path)  && is_file($image_path)) {
                        $prof_image = env('APP_URL') . '/uploads/djs/' . $filename;
                    }
                }
                /*** optional fields ***/

                $apple_dj = AppleDjs::query()->where('client_id','=',$client_id)->first();

                if ($apple_dj)
                {
                    $dj = new DJ();
                    $dj->email = isset($email)?$email:$apple_dj->email;
                    $dj->registeration_type = 6;
                    $dj->name = isset($name)?$name:$apple_dj->name;
                    isset($date_of_birth)?($dj->birth_date=$date_of_birth):'';
                    $dj->password = isset($password)?$password:'';
                    $dj->locatione = isset($location)?$location:'';
                    $dj->user_name =$user_name;
                    $dj->profile_image =isset($filename)?$filename:'';
                    $dj->client_id =$client_id;

                    $dj->save();

                    $apple_dj->delete();

                    $dj['profile_image'] = isset($prof_image)?$prof_image:'';

                    $dj['dj_id'] = $dj->id;
                    $array=['id','password' ,'created_at','updated_at','deleted_at'];

                    foreach ($array as $var) {
                        unset($dj[$var]);
                    }

                    DB::commit();

                    $response['error'] = false;
                    $response['status_code'] = '200';
                    $response['message'] = 'DJ registered successfully';
                    $response['data'] =$dj;
                }
                else
                {
                    throw new \Exception('Login with Apple first.');
                }


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
}
