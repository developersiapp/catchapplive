<?php

namespace catchapp\Http\Controllers\Frontend;

use catchapp\Http\Controllers\Controller;
use catchapp\Models\DJ;
use catchapp\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class UserController extends Controller
{
    public function notice(Request $request)
    {
        return view('frontend.user.notice')->with('success', $request->message);
    }
    public function index($token)
    {
        $user = User::withTrashed()->where('remember_token', '=', $token)->first();
        if ($user) {
            if ($user->deleted_at == null) {
                $user['is_dj'] = 0;
                return view('frontend.user.resetPassword', ['user' => $user])->with('message', 'Logged In Successfully!');
            } else {
                return back()->with('error', 'Sorry, This user has been deleted by Super Admin. Contact administration!');
            }
        } else {
            return back()->with('error', 'Invalid token passed!');

        }

    }
    public function djindex($token)
    {
        $user = DJ::withTrashed()->where('reset_token', '=', $token)->first();
        if ($user) {
            if ($user->deleted_at == null) {
                $user['is_dj'] = 1;
                return view('frontend.user.resetPassword', ['user' => $user])->with('message', 'Logged In Successfully!');
            } else {
                return back()->with('error', 'Sorry, This DJ has been deleted by Super Admin. Contact administration for more help!');
            }
        } else {
            return back()->with('error', 'Invalid token passed!');
        }

    }

    public function savePassword(Request $request)
    {
        define("ENCRYPTION_KEY", "!@#$%^&*");

        $id = $request->input('id');
        $is_dj = $request->input('is_dj');
        if ($is_dj==0) {
            $user = User::query()->find($id);
            if ($user)
            {
                $user['is_dj'] =0;
            }
        }
        else {
            $user = DJ::query()->find($id);
            if ($user)
            {
                $user['is_dj'] =1;
            }
        }

        $rules = array(
            'new_password' => 'required|min:6',
            'confirm_password' => 'required|same:new_password'
        );
        $validator = \Illuminate\Support\Facades\Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        if ($user) {
            if ($user->is_dj==1)
            {
                $user->password = $request->input('new_password');
                $user->reset_token = '';
                $fullname = $user->name;
            }
            else
            {
                $user->password = encrypt($request->input('new_password'), ENCRYPTION_KEY);
                $user->remember_token = '';
                $fullname = $user->first_name . ' ' . $user->last_name;
            }
            unset($user['is_dj']);
            $user->save();
            $msg ='Congratulations, ' . $fullname . '!</br>You\'ve successfully set your new password.</br> You can proceed login to your account now!';
            return redirect()->route('user.notice',['message' => $msg]);
//            return redirect()->back()->with('success', 'Congratulations, ' . $fullname . '!</br>You\'ve successfully set your new password.</br> You can proceed login to your account now!');
        } else {
            return back()->with('error', 'Sorry, We couldn\'t find any user with provided link!');
        }
    }
}
