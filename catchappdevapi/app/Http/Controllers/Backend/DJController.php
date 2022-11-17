<?php
/**
 * Created by PhpStorm.
 * User: iapp
 * Date: 6/6/19
 * Time: 4:22 PM
 */

namespace catchapp\Http\Controllers\Backend;


use Carbon\Carbon;
use catchapp\Http\Controllers\Controller;
use catchapp\Mail\SendMailable;
use catchapp\Models\Club;
use catchapp\Models\DJ;
use catchapp\Models\EmailConfiguration;
use catchapp\Models\EmailType;
use catchapp\Models\Pivot_Dj_Club;
use catchapp\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class DJController extends Controller
{
    public function index(Request $request)
    {
        $query = DJ::query()
        ;
        $data = $query->orderBy('created_at','DESC')->get();

        $data->map(function($item){
            $club_names=[];
            $club_ids = Pivot_Dj_Club::query()->where('dj_id','=', $item->id)->pluck('club_id');
            $clubs = Club::query()->whereIn('id',$club_ids)->get();
            foreach ($clubs as $club){
                array_push($club_names, $club->name);
            }
            $item['gender'] = ucfirst($item->gender);
            $item['birth_date'] = isset($item->birth_date)?$item->birth_date!=null?Carbon::parse($item->birth_date)->format('d F, Y'):'':'';
            $item['reg_type'] = isset($item->registeration_type) ? ($item->registeration_type != ' ' ? User::$registeration_type{$item->registeration_type} : '-') : 'Admin';
            $item['club_names'] = '-';
//            $item['club_names'] = '<span class="text-muted"> No Club Assigned Yet</span>';

            if (count($club_names)>0) {
                $names ='<ul style="list-style: square;text-align: left;">';
                foreach ($clubs as $c)
                {
                    $names.='<li>'.$c->name.'</li>';
                }

                $names.='</ul>';
                $item['club_names'] = $names;
            }
            $item['added_on'] = Carbon::parse($item->created_at)->format('d F, Y');
            return $item;
        });
        if ($request->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('logo', function ($row) {
                    $logo = '<img src="' . env('APP_URL') . '/dist/img/user.png" class="dj-image img-sm img-circle" alt="Dj Image">';

                    if (!empty($row->profile_image) && $row->profile_image!= null && $row->profile_image != " "){
                        $image = base_path('public/uploads/djs/' . $row->profile_image);
                        if (is_file($image) && file_exists($image)) {
                            $logo = '<img src="' . env('APP_URL') . '/uploads/djs/' . $row->profile_image . '" class="dj-image img-sm img-circle" alt="Dj Image">';
                        }
                    }

                    return $logo;
                })
                ->addColumn('action', function($row){
                    $btn = '<a href="'.env('APP_URL').'/dashboard/djs/edit-dj/'.$row->id.'" class="btn btn-xs btn-primary mr-5px">
                                            <i class="fa fa-pencil"></i><span> Edit</span>

                                         <a href="'.env('APP_URL').'/dashboard/djs/delete-dj/'.$row->id.'"
                                            onclick="return confirm(\'Do you really want to delete this DJ?\')"
                                            class="btn btn-xs btn-danger"> <i class="fa fa-trash-o"></i><span> Delete</span></a>';

                    return $btn;
                })
                ->rawColumns(['action','club_names', 'logo'])
                ->make(true);
        }
        return view('backend.djs.list');

    }

    public function addNew()
    {
        $clubs = Club::query()->get();
        return view('backend.djs.create', ['clubs' => $clubs]);
    }

    public function saveDj(Request $request)
    {
        $id = $request->input('id');
        $dj = DJ::query()->find($id);
        if ($dj) {
            $rules = array(
                'name' => 'required',
//                'club' => 'required',
                'user_name' => 'required',
                'email' => 'required|unique:djs,email,' . $dj->id,
//                'password' => 'required',
//                'birth_date'=> 'required',
                'gender'=> 'in:male,female',
//                'gender'=> 'required|in:male,female',
                'dj_image'=> 'mimes:jpeg,gif,bmp,png,svg+xml',
            );
        } else {
            $rules = array(
                'name' => 'required',
//                'club' => 'required',
                'user_name' => 'required|unique:djs,user_name',
                'email' => 'required|unique:djs,email',
//                'password' => 'required',
//                'birth_date'=> 'required',
                'gender'=> 'in:male,female',
                'dj_image'=> 'mimes:jpeg,gif,bmp,png,svg+xml',
            );
        }
        $validator = Validator::make($request->all(), $rules);

        $validator->after(function ($validator) use ($request) {
            $user_name = $request->input('user_name');
            $user_name_exists = DJ::query()->where('user_name', '=', $user_name)->first();
            if ($user_name_exists && $user_name_exists->id != $request->input('id')) {
                $validator->errors()->add('user_name', 'This user name has already been taken. Please, Try another!.');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if (!$dj) {
            $dj = new DJ();
            $dj->registeration_type = 1;
            $subject = 'Welcome to CatchApp!';
            $mail_to = $request->input('email');
            $content = 'Hi ' . $request->input('name') . '(DJ) !. Welcome to CatchApp.';
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
                $mail->mail_from = 'catchApp.com';
            }
            $mail->mail_to = $mail_to;
            $mail->mail_subject = $subject;
            $mail->mail_content = $content;
            $mail->is_sent = 0;
            $mail->save();
        }
        $dob = Carbon::parse($request->input('birth_date'))->format('Y-m-d');
        $dj->name = $request->input('name');
        $dj->user_name = preg_replace('/\s+/', ' ', $request->input('user_name'));
        $dj->email = $request->input('email');
        if ($request->has('password') && $request->password !='' && $request->password != null ) {
            $dj->password = $request->password;
        }
        if ($request->has('birth_date') && $request->birth_date !='' && $request->birth_date != null ) {
            $dj->birth_date = $dob;
        }
        if ($request->has('gender') && $request->gender !='' && $request->gender != null ) {
            $dj->gender = $request->gender;
        }
        if ($request->has('club') && $request->input('club') != '') {
            $dj->assigned_clubs = implode(',', $request->input('club'));
        } else {
            $dj->assigned_clubs = '';
        }
        if ($request->hasFile('dj_image')) {
            if ($dj->profile_image != '') {
                $image_path = base_path('public/uploads/djs/'.$dj->profile_image);
                if(file_exists($image_path) && is_file($image_path)) {
                    unlink($image_path);
                }}

            $photo = $request->file('dj_image');
            $extension = $photo->getClientOriginalExtension();
            $filename = 'dj-profile-photo-' . time() . '.' . $extension;
            $photo->move(base_path('public/uploads/djs/'), $filename);

            $dj->profile_image = $filename;
        }
        $dj->save();

        //            SAVING DJ & CLUBS

        $clubs = $request->input('club');

        $dj_clubs = Pivot_Dj_Club::query()->where('dj_id','=', $dj->id)->get();
        if ($dj_clubs->count()>0) {
            if ($request->input('dj') == '') {
                Pivot_Dj_Club::query()
                    ->where('dj_id', '=', $dj->id)
                    ->delete();
            } else {
                Pivot_Dj_Club::query()
                    ->where('dj_id', '=', $dj->id)
                    ->whereNotIn('club_id', $clubs)
                    ->delete();
            }
        }
        if ($request->input('club')!='') {
            foreach ($clubs as $club) {
                $dj_club = Pivot_Dj_Club::query()->where('dj_id', '=', $dj->id)
                    ->where('club_id', '=', $club)->first();
                if (!$dj_club) {
                    $dj_club = new Pivot_Dj_Club();
                    $dj_club->club_id = $club;
                    $dj_club->dj_id = $dj->id;
                    $dj_club->save();
                }
            }
        }

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

        return redirect(env('APP_URL') . '/dashboard/djs')->with('success', 'DJ details are saved successfully!');

    }

    public function editDj($id)
    {
        $dj = DJ::query()->find($id);
        $clubs = Club::query()->get();
        if ($dj) {
            return view('backend.djs.create', ['dj' => $dj, 'clubs' => $clubs]);
        }
    }

    public function deleteDj($id)
    {
        $dj = DJ::query()->find($id);
        $dj->forceDelete();

        return redirect(env('APP_URL').'/dashboard/djs')->with('success','Dj is delted successfully!');
    }

    public function editPassword($id)
    {
        $dj = DJ::query()->find($id);
        if ($dj) {
            return view('backend.djs.changePassword', ['dj' => $dj]);
        }
    }

    public function updatePassword(Request $request)
    {
        $id = $request->input('id');
        $dj = DJ::query()->find($id);
        if ($dj) {
            $rules = array(
                'password' => 'required',
                'confirm_password' => 'required|same:password',
            );
            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()) {
                $messages = $validator->messages();
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $dj->password = $request->input('password');
            $dj->save();
            return redirect(env('APP_URL').'/dashboard/djs')->with('success','Dj password is updated successfully!');
        }
    }

    public function editClub($id)
    {
        $dj = DJ::query()->find($id);
        if ($dj) {
            $clubs = Club::query()->get();
            return view('backend.djs.changeClub', ['dj' => $dj,'clubs' => $clubs]);
        }
    }

    public function updateClub(Request $request)
    {
        $id = $request->input('id');
        $dj = DJ::query()->find($id);
        if ($dj) {
            $rules = array(
                'club' => 'required',
            );
            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()) {
                $messages = $validator->messages();
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $dj->assigned_clubs = $request->input('club');
            $dj->save();
            return redirect(env('APP_URL').'/dashboard/djs')->with('success','Dj is updated successfully!');


        }
    }

    public  function deleteProfilePic($id)
    {
        $dj =DJ::query()->find($id);
        if ($dj)
        {
            if ($dj->profile_image!=''){
                $image_path = base_path('public/uploads/djs/'.$dj->profile_image);
                if(file_exists($image_path)&& is_file($image_path)) {
                    unlink($image_path);
                }}

            $dj->profile_image = "";
            $dj->save();
            return back()->with('success','Profile Picture Has Been Deleted!');
        }
    }
}
