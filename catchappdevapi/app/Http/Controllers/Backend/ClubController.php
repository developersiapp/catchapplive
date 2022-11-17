<?php
/**
 * Created by PhpStorm.
 * User: iapp
 * Date: 6/6/19
 * Time: 10:43 AM
 */

namespace catchapp\Http\Controllers\Backend;


use Carbon\Carbon;
use catchapp\Http\Controllers\Controller;
use catchapp\Mail\SendMailable;
use catchapp\Models\City;
use catchapp\Models\Club;
use catchapp\Models\Country;
use catchapp\Models\DJ;
use catchapp\Models\EmailConfiguration;
use catchapp\Models\EmailType;
use catchapp\Models\Pivot_Dj_Club;
use catchapp\Models\State;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class ClubController extends Controller
{

    public function index(Request $request)
    {
        $query = Club::query()->orderBy('created_at', 'DESC');
        $data = $query->get();
        $data->map(function($item){
            $dj_names=[];

            $dj_ids = Pivot_Dj_Club::query()->where('club_id','=', $item->id)->pluck('dj_id');

            $djs= DJ::query()->whereIn('id', $dj_ids)->get();
            foreach ($djs as $dj){
                array_push($dj_names, $dj->name);
            }
            $item['dj_names'] = implode(', ',$dj_names);
            $city = City::query()->find($item->city);
            $state = State::query()->find($item->state);
            $country = Country::query()->find($item->country);

            $item['city'] = isset($city)?$city->name:'-';
            $item['state'] = isset($state)?$state->name:'-';
            $item['country'] = isset($country)?$country->name:'-';
            $item['added_on'] = Carbon::parse($item->created_at)->format('d F, Y');
            return $item;
        });
        if ($request->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('logo', function ($row) {
                    $logo = '<img src="' . env('APP_URL') . '/dist/img/club.png" class="club-image img-sm img-circle" alt="Club Image">';
                    if (!empty($row->profile_image) && $row->profile_image!= null && $row->profile_image != " "){
                        $image = base_path('public/uploads/clubs/' . $row->profile_image);
                        if (is_file($image) && file_exists($image)) {
                            $logo = '<img src="' . env('APP_URL') . '/uploads/clubs/' . $row->profile_image . '" class="club-image img-sm img-circle" alt="Club Image">';
                        }
                    }

                    return $logo;
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . env('APP_URL') . '/dashboard/clubs/edit-club/' . $row->id . '" class="btn btn-xs btn-primary mr-5px">
                                            <i class="fa fa-pencil"></i><span> Edit</span>
                                        </a><a href="' . env('APP_URL') . '/dashboard/clubs/delete-club/' . $row->id . '"
                                            onclick="return confirm(\'Do you really want to delete this Club?\')"
                                            class="btn btn-xs btn-danger"> <i class="fa fa-trash-o"></i><span> Delete</span></a>';

                    return $btn;
                })
                ->rawColumns(['action', 'logo'])
                ->make(true);
        }
        return view('backend.clubs.list');
    }
    public function addNew()
    {
        return view('backend.clubs.create');
    }

    public function saveClub(Request $request)
    {
        $id = $request->input('id');
        $club = Club::query()->find($id);
        if ($club) {
            $rules = array(
                'name' => 'required',
                'email' => 'required|unique:clubs,email,' . $club->id,
                'address' => 'required',
                'country' => 'required',
                'state' => 'required',
                'city' => 'required',
                'zip' => 'required|alpha_dash|min:5|max:6',
                'password' => 'required',
            );
        }
        else
        {
            $rules = array(
                'name' => 'required',
                'email' => 'required|unique:clubs,email',
                'address' => 'required',
                'country' => 'required',
                'state' => 'required',
                'city' => 'required',
                'zip' => 'required|alpha_dash|min:5|max:6',
                'password' => 'required',
            );
        }


        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return redirect()->back()->withErrors($validator)->withInput();
        } else {
            $msg ="Club details are updated successfully!";
            if (!$club) {
                $club = new Club();
                $msg = "A new club is created successfully!";
                if ($club) {
                    $subject = 'Welcome to CatchApp!';
                    $mail_to = $request->input('email');
//                  $mail_to =  'developer.iapptechnologies@gmail.com';
                    $content ='Hi ' . $request->input('name') . '(Club) !. Welcome to CatchApp.';
                    $type = EmailType::query()
                        ->join('email_addresses', 'email_types.id', '=',
                            'email_addresses.email_type','inner')->select('email_addresses.email_address as mail_from', 'email_types.*')
                        ->where('name','LIKE','%'.'new club'.'%')->first();

                    $mail = new EmailConfiguration();
                    if ($type) {
                        $mail->email_type = $type->id;
                        $mail->mail_from = $type->mail_from;
                    } else
                    {
                        $mail->email_type = 0;
                        $mail->mail_from = 'catchApp.com';
                    }
                    $mail->mail_to = $mail_to;
                    $mail->mail_subject = $subject;
                    $mail->mail_content = $content;
                    $mail->is_sent = 0;
                    $mail->save();
                }
            }
            $club->email = $request->input('email');
            $club->name = $request->input('name');
            if ($request->has('dj') && $request->input('dj') != '') {
                $club->assigned_djs = implode(',', $request->input('dj'));
            } else {
                $club->assigned_djs = '';
            }
            $club->password = $request->input('password');
            $club->street_address = $request->input('address');

            $country = Country::query()->where('name','LIKE',"%".$request->input('country')."%")->first();
            if (!$country)
            {
                $country = new Country();
                $country->name = $request->input('country');
                $country->save();
            }
            $country_id = $country->id;

            $state = State::query()->where('name','LIKE',"%".$request->input('state')."%")->first();
            if (!$state)
            {
                $state = new State();
                $state->name = $request->input('country');
                $state->country_id = $country_id;
                $state->save();
            }
            $state_id = $state->id;

            $city = City::query()->where('name','LIKE',"%".$request->input('city')."%")->first();
            if (!$city)
            {
                $city = new City();
                $city->name = $request->input('city');
                $city->country_id = $country_id;
                $city->state_id = $state_id;
            }
            $city->zip = $request->input('zip');
            $city->save();
            $city_id = $city->id;

            $club->country = $country_id;
            $club->state = $state_id;
            $club->city =$city_id;
            $club->zip = $city->zip;

            if ($request->hasFile('club_image')) {
                if ($club->profile_image!=''){
                    $image_path = base_path('public/uploads/clubs/'.$club->profile_image);
                    if(file_exists($image_path)&& is_file($image_path)) {
                        unlink($image_path);
                    }
                }

                $photo = $request->file('club_image');
                $extension = $photo->getClientOriginalExtension();
                $filename = 'club-photo-' . time() . '.' . $extension;
                $photo->move(base_path('public/uploads/clubs/'), $filename);
                $club->profile_image = $filename;
            }
            $club->save();


//            SAVING DJ & CLUBS

            $djs = $request->input('dj');

            $club_djs = Pivot_Dj_Club::query()->where('club_id','=', $club->id)->get();
            if ($club_djs->count()>0)
            {
                if ($request->input('dj') == '') {
                    Pivot_Dj_Club::query()
                        ->where('club_id', '=', $club->id)
                        ->delete();
                }
                else {
                    Pivot_Dj_Club::query()
                        ->where('club_id', '=', $club->id)
                        ->whereNotIn('dj_id', $djs)
                        ->delete();
                }
            }
            if ($request->input('dj') != '') {

                foreach ($djs as $dj) {
                    $club_dj = Pivot_Dj_Club::query()->where('club_id', '=', $club->id)
                        ->where('dj_id', '=', $dj)->first();
                    if (!$club_dj) {
                        $club_dj = new Pivot_Dj_Club();
                        $club_dj->club_id = $club->id;
                        $club_dj->dj_id = $dj;
                        $club_dj->save();
                    }
                }
            }

            // SENDING MAIL
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
        }
        return redirect(env('APP_URL').'/dashboard/clubs')->with('success','Club details are saved successfully!');

    }

    public function deleteClub($id)
    {
        $club = Club::query()->find($id);
        $club->delete();
        return redirect(env('APP_URL').'/dashboard/clubs')->with('success','Club is deleted successfully!');
    }

    public function editClub($id)
    {
        $club = Club::query()->find($id);
        if ($club) {
            $locations['city']='';
            $locations['state']='';
            $locations['country']='';
            $city = City::query()->find($club->city);
            $state = State::query()->find($club->state);
            $country = City::query()->find($club->country);
            if (!empty($state))
            {
                $locations['state']= $state->name;
            }
            if (!empty($city))
            {
                $locations['city']= $city->name;
            }
            if (!empty($country))
            {
                $locations['country']= $country->name;
            }

            return view('backend.clubs.create', ['club' => $club,'locations'=>$locations]);
        }
    }

    public function deleteProfilePic($id)
    {
        $club = Club::query()->find($id);
        if ($club) {
            $image_path = base_path('public/uploads/clubs/'.$club->profile_image);
            if(file_exists($image_path) && is_file($image_path)) {
                unlink($image_path);
            }
            $club->profile_image = "";
            $club->save();
            return back()->with('success', 'Profile Picture Has Been Deleted!');
        }
    }

    public function changeStates(Request $request)
    {
        $country_id = $request->input('country_id');
        $states = State::query()->where('country_id','=',$country_id)->select('id','name')->get();
       /* $sel = [
            'id' => '',
            'Title' => 'Select'
        ];
        array_push($states, $sel);
        foreach (Club::$states as $s) {
            if ($s['Country'] == $country_id) {
                array_push($states, $s);
            }
        }*/
        return response()->json(['states' => $states]);

    }

    public function changeCities(Request $request)
    {
        $state_id = $request->input('state_id');
        $cities = City::query()->where('state_id','=',$state_id)->select('id','name')->get();
       /* $sel = [
            'id' => '',
            'Title' => 'Select'
        ];
        array_push($cities, $sel);
        foreach (Club::$cities as $c) {
            if ($c['State'] == $state_id) {
                array_push($cities, $c);
            }
        }*/
        return response()->json(['cities' => $cities]);

    }

}
