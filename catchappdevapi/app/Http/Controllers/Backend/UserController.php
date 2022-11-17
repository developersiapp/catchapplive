<?php
/**
 * Created by PhpStorm.
 * User: iapp
 * Date: 5/6/19
 * Time: 2:32 PM
 */

namespace catchapp\Http\Controllers\Backend;

use Carbon\Carbon;
use catchapp\Http\Controllers\Controller;
use catchapp\Mail\SendMailable;
use catchapp\Models\EmailConfiguration;
use catchapp\Models\EmailType;
use catchapp\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    public function index2(Request $request)
    {
        $query = User::query();
        $data = $query->get();
        $data->map(function ($item) {
            $item['gender'] = ucfirst($item->gender);
            $item['birthdate'] =isset($item->birth_date)?$item->birth_date!=null? Carbon::parse($item->birth_date)->format('d F, Y'):'':'';
            $item['reg_type'] = User::$registeration_type{$item->registeration_type};
            $item['added_on'] = Carbon::parse($item->created_on)->format('d F, Y');
            return $item;
        });
        if ($request->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a href="#" class="show-modal btn btn-success btn-xs edit-user"
                     data-id=' . $row->id . '>
<i class="glyphicon glyphicon-pencil"></i><span> Edit</span>
</a>
                                        <a href="' . env('APP_URL') . '/dashboard/users/edit-user/' . $row->id . '" class="btn btn-xs btn-primary mr-5px">
                                            <i class="fa fa-pencil"></i><span> Edit</span>
                                        </a>
                                        <a href="' . env('APP_URL') . '/dashboard/users/delete-user/' . $row->id . '"
                                            onclick="return confirm(\'Do you really want to delete this User?\')"
                                            class="btn btn-xs btn-danger"> <i class="fa fa-trash-o"></i><span> Delete</span></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('backend.users.list25');
    }

    public function editUser2(Request $request)
    {
        $id = $request->input('id');
        $user = User::query()->find($id);
        $view = \Illuminate\Support\Facades\View::make('backend.users.editUser', $user);
        $data = [];
        $data['modal_id'] = time() . 'A' . rand(1000, 9999);
        $data['view'] = $view->render();
        return response()->json($data);
    }

    public function index(Request $request)
    {
        $query = User::query();
        $data = $query->orderBy('created_at','desc')->get();
        $data->map(function ($item) {

            $item['gender'] = ucfirst($item->gender);
            $item['birthdate'] =isset($item->birth_date)?$item->birth_date!=null? Carbon::parse($item->birth_date)->format('d F, Y'):'':'';
            $item['reg_type'] = isset($item->registeration_type) ? ($item->registeration_type != ' ' ? User::$registeration_type{$item->registeration_type} : '-') : 'Admin';
            $item['added_on'] = Carbon::parse($item->created_at)->format('d F, Y');
            return $item;
        });
        if ($request->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('logo', function ($row) {
                    $logo = '<img src="' . env('APP_URL') . '/dist/img/user.png" class="user-image img-sm img-circle" alt="User Image">';

                    if (!empty($row->profile_image) && $row->profile_image!= null && $row->profile_image != " "){
                        $image = base_path('public/uploads/users/' . $row->profile_image);
                        if (is_file($image) && file_exists($image)) {
                            $logo = '<img src="' . env('APP_URL') . '/uploads/users/' . $row->profile_image . '" class="user-image img-sm img-circle" alt="User Image">';
                        }
                    }

                    return $logo;
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . env('APP_URL') . '/dashboard/users/edit-user/' . $row->id . '" class="btn btn-xs btn-primary mr-5px">
                                            <i class="fa fa-pencil"></i><span> Edit</span> </a>
                                            <a href="' . env('APP_URL') . '/dashboard/users/delete-user/' . $row->id . '"
                                            onclick="return confirm(\'Do you really want to delete this User?\')"
                                            class="btn btn-xs btn-danger"> <i class="fa fa-trash-o"></i><span> Delete</span></a>';
                    return $btn;
                })
                ->rawColumns(['action', 'logo'])
                ->make(true);
        }
        return view('backend.users.list');
    }

    public function addNew()
    {
        return view('backend.users.create');
    }

    public function editUser($id)
    {
        $user = User::query()->find($id);
        return view('backend.users.create', ['user' => $user]);
    }

    public function deleteUser($id)
    {
        $user = User::query()->find($id);
        $user->forceDelete();
        return redirect()->route('users.index');
//        $users = User::query()->get();
//        return view('backend.users.list', ['users' => $users]);
    }

    public function saveUser(Request $request)
    {
        define("ENCRYPTION_KEY", "!@#$%^&*");

        $id = $request->input('id');
        $user = User::query()->find($id);
        if ($user) {
            $rules = array(
                'first_name' => 'required',
                'gender' => 'in:male,female',
//                'birthDate' => 'required',
                'user_name' => 'required',
                'email' => 'required|unique:users,email,' . $user->id,
            );
        } else {
            $rules = array(
                'first_name' => 'required',
                'gender' => 'in:male,female',
//                'birthDate' => 'required',
                'user_name' => 'required|unique:users,user_name',
                'email' => 'required|unique:users,email',
                'password' => 'required'
            );
        }
        $validator = Validator::make(Input::all(), $rules);

        $validator->after(function ($validator) use ($request) {
            $user_name = $request->input('user_name');
            $user_name_exists = User::query()->where('user_name', '=', $user_name)->first();
            if ($user_name_exists && $user_name_exists->id != $request->input('id')) {
                $validator->errors()->add('user_name', 'This user name has already been taken. Please, Try another!.');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        if (!$user) {
            $user = new User();
            $user->registeration_type = 1;
//            SAVE EMAIL TO SEND LATER
            $subject = 'Welcome to CatchApp!';
            $mail_to = $request->input('email');
            $content = 'Hi ' . $request->input('first_name') . $request->input('last_name') . ' (User) ! Welcome to CatchApp.';
            $type = EmailType::query()
                ->join('email_addresses', 'email_types.id', '=',
                    'email_addresses.email_type','inner')->select('email_addresses.email_address as mail_from', 'email_types.*')
                ->where('name','LIKE','%'.'new user'.'%')->first();

            $mail = new EmailConfiguration();
            if ($type) {
                $mail->email_type = $type->id;
                $mail->mail_from = $type->mail_from;
            }
            else
            {
                $mail->email_type = 0;
                $mail->mail_from = 'catchApp.com';
            }
            $mail->mail_to = $mail_to;
            $mail->mail_subject = $subject;
            $mail->mail_content = $content;
            $mail->is_sent = 0;
            $mail->save();

            if ($request->has('password') && $request->password != '' && $request->password != null) {
                $user->password = encrypt($request->input('password'), ENCRYPTION_KEY);
            }
        } else {
            if ($request->has('password') && $request->password != '' && $request->password != null) {
                $user->password = encrypt($request->input('password'), ENCRYPTION_KEY);
            }
        }
        $user->user_name = preg_replace('/\s+/', ' ', $request->input('user_name'));
        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        if ($request->has('gender') && $request->gender != null && $request->gender != '') {
            $user->gender = $request->input('gender');
        }
        if ($request->has('birthDate') && $request->birthDate != '' && $request->birthDate != null) {
            $user->birth_date =Carbon::parse($request->input('birthDate'))->format('Y-m-d');
        }
        $user->email = $request->input('email');
        if ($request->hasFile('user_image')) {
            if ($user->profile_image != '') {
                $image_path = base_path('public/uploads/users/' . $user->profile_image);  // Value is not URL but directory file path
                if (file_exists($image_path)&& is_file($image_path)) {
                    unlink($image_path);
                }
            }

            $photo = $request->file('user_image');
            $extension = $photo->getClientOriginalExtension();
            $filename = 'user-profile-photo-' . time() . '.' . $extension;
            $photo->move(base_path('public/uploads/users/'), $filename);

            $user->profile_image = $filename;
        }
        $user->save();
//            SENDING MAIL
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


        return redirect(env('APP_URL') . '/dashboard/users')->with('success', 'User details are saved!');
    }

    public function deleteProfilePic($id)
    {
        $user = User::query()->find($id);
        if ($user) {
            $image_path = base_path('public/uploads/users/' . $user->profile_image);  // Value is not URL but directory file path
            if (file_exists($image_path)&& is_file($image_path)) {
                unlink($image_path);
            }

            $user->profile_image = "";
            $user->save();
            return back()->with('success', 'Profile Picture Has Been Deleted!');
        }
    }
    
}
