<?php
/**
 * Created by PhpStorm.
 * User: iapp
 * Date: 7/6/19
 * Time: 11:58 AM
 */

namespace catchapp\Http\Controllers\Backend;


use Carbon\Carbon;
use catchapp\Http\Controllers\Controller;
use catchapp\Models\AdminUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    public function adminProfile()
    {
        if (session('admin_id')) {
            $admin = AdminUser::query()->find(session('admin_id'));
            if ($admin) {
                return view('backend.settings.admin-profile', ['admin' => $admin]);
            }
        } else {
            return view('frontend.admin.login');
        }
    }

    public function saveAdminProfile(Request $request)
    {
        $request->flash();

        $id = $request->input('id');
        $admin = AdminUser::query()->find($id);
        if ($admin) {
            $rules = array(
                'name' => 'required',
                'email' => 'required|unique:admin_users,email,' . $admin->id,
                'old_password' => 'required'
            );

            $validator = Validator::make(Input::all(), $rules);

            $validator->after(function ($validator) use ($admin, $request) {
                if ($admin->password != $request->input('old_password')) {
                    $validator->errors()->add('old_password', 'You\'ve Entered A Wrong Password!');
                    /*return back()
                        ->with('error', 'You\'ve Entered A Wrong Password!');*/

                }
            });

            if ($validator->fails()) {
                $messages = $validator->messages();
                return redirect()->back()->withErrors($validator)->withInput();
            } else {
                $admin->name = $request->input('name');
                $admin->email = $request->input('email');
                if ($request->hasFile('admin_image')) {
                    if ($admin->profile_image!=''){
                        $image_path = base_path('public/uploads/admins/'.$admin->profile_image);
                        if(file_exists($image_path) && is_file($image_path)) {
                            unlink($image_path);
                        }}

                    $photo = $request->file('admin_image');
                    $extension = $photo->getClientOriginalExtension();
                    $filename = 'admin-profile-photo-' . time() . '.' . $extension;
                    $photo->move(base_path('public/uploads/admins/'), $filename);

                    $admin->profile_image = $filename;
                }

                if ($request->input('change_password') == true) {
                    $new_password = $request->input('new_password');
                    $repeat_password = $request->input('repeat_password');
                    if ($new_password != '' && $repeat_password != '') {
                        if ($new_password == $repeat_password) {
                            $admin->password = $new_password;
                        } else {
                            return back()->with('error', 'Sorry, Profile Could\'t Updated! New Password & Repeat Password Don\'t Match.');
                        }
                    }
                }
                $admin->save();
                return back()->with('success', 'Admin Profile Has Been Updated!');
            }
    }
}

public
function deleteProfilePic($id)
{
    $admin = AdminUser::query()->find($id);
    if ($admin) {

        $admin->profile_image = " ";
        $admin->save();
        if ($admin->profile_image!=''){
            $image_path = base_path('public/uploads/admins/'.$admin->profile_image);
            if(file_exists($image_path) && is_file($image_path)) {
                unlink($image_path);
            }
        }

        return back()->with('success', 'Profile Picture Has Been Deleted!');
    }
}

public
function editPassword($id)
{
    $admin = AdminUser::query()->find($id);
    if ($admin) {
        return view('backend.settings.changePassword', ['admin' => $admin]);
    }
}

public
function changePassword(Request $request)
{
    $id = $request->input('id');
    $admin = AdminUser::query()->find($id);
    $rules = array(
        'old_password' => 'required',
        'new_password' => 'required',
        'repeat_password' => 'required|same:new_password',
    );

    $validator = Validator::make(Input::all(), $rules);

    $validator->after(function ($validator) use ($admin, $request) {
        if ($admin->password != $request->input('old_password')) {
            $validator->errors()->add('old_password', 'You\'ve Entered A Wrong Password!');
        }
        if ($admin->password == $request->input('new_password')) {
            $validator->errors()
                ->add('new_password', 'Your current password is same. Please, Enter a new password');
        }
    });

    if ($validator->fails()) {
        $messages = $validator->messages();
        return redirect()->back()->withErrors($validator)->withInput();
    }
    $admin->password = $request->input('new_password');
    $admin->save();
    return redirect()
        ->back()
        ->with('success', 'Password is changed successfully!');
}
}
