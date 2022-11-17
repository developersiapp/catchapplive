<?php
/**
 * Created by PhpStorm.
 * User: iapp
 * Date: 26/6/19
 * Time: 4:03 PM
 */

namespace catchapp\Http\Controllers\Backend;


use Carbon\Carbon;
use catchapp\Http\Controllers\Controller;
use catchapp\Models\EmailAddress;
use catchapp\Models\EmailConfiguration;
use catchapp\Models\EmailType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use PharIo\Manifest\Email;
use Yajra\DataTables\DataTables;
use catchapp\Mail\SendMailable;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    public function emails(Request $request)
    {
        $query = EmailConfiguration::query()
            ->join('email_types', 'email_configuration.email_type', '=',
                'email_types.id', 'left')->select('email_types.name as type', 'email_configuration.*');

        $data = $query->orderBy('created_at', 'desc')->get();

        $data->map(function ($item) {
            if($item->type =='')
            {
                $item['type']='CatchApp';
            }
            $item['added_on'] = Carbon::parse($item->created_at)->format('d F, Y');
            $item['content'] = $item->mail_content;
            return $item;
        });
        if ($request->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '';
                    if ($row->is_sent == 0) {
                        $btn .= '<a href="' . env('APP_URL') . '/dashboard/settings/edit-mail/' . $row->id . '"
                                            class="btn btn-xs btn-primary mr-5px"> <i class="fa fa-pencil"></i><span> Edit</span></a>
                                            <a  href="#" data-id="' . $row->id . '"
                                            onclick="return confirm(\'Send this Email?\')"
                                            class="btn btn-xs btn-success mr-5px send-email">
                                            <i class="fa fa-send"></i><span> Send</span></a>';}
                    $btn .= '<a href="' . env('APP_URL') . '/dashboard/settings/delete-mail/' . $row->id . '"
                                            onclick="return confirm(\'Do you really want to delete this Email?\')"
                                            class="btn btn-xs btn-danger"> <i class="fa fa-trash-o"></i><span> Delete</span></a>';

                    return $btn;
                })
//                ->escapeColumns([])
                ->rawColumns(['action', 'content'])
                ->make(true);
        }
        return view('backend.settings.emails');
    }

    public
    function emailss()
    {
        $emails = EmailConfiguration::query()->get();
        return view('backend.settings.emails-simple', ['emails' => $emails]);
    }

    public
    function emailConfiguration()
    {
        return view('backend.settings.email-configuration');
    }

    public
    function saveEmail(Request $request)
    {
        $id = $request->input('id');
        $mail = EmailConfiguration::query()->find($id);

        $rules = array(
            'mail_to' => 'required',
            'mail_subject' => 'required',
            'email_message' => 'required',
            'email_type' => 'required'
        );


        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return redirect()->back()->withErrors($validator)->withInput();
        }
        DB::beginTransaction();
        try {
            if (!$mail) {
                $mail = new EmailConfiguration();
            }
            $name = 'User';
            if ($request->input('name') != '') {
                $name = $request->input('name');
            }
            $auto_template = $request->input('email_message');
            $customized_template = str_replace("{name}", $name, $auto_template);

            $email_type = $request->input('email_type');
            $email_address = EmailAddress::query()->where('email_type', '=', $email_type)->first();
            $mail->mail_to = $request->input('mail_to');
            $mail->mail_from = $email_address->email_address;
            $mail->mail_subject = $request->input('mail_subject');
            $mail->mail_content = $customized_template;
            $mail->email_type = $email_type;
            $mail->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
        }

//        return response()->json(['success'=>'Email Has Been Updated Successfully.']);

//        $emails = EmailConfiguration::query()->get();
//        return view('backend.settings.emails', ['emails' => $emails]);
        return redirect()->to(route('emails.index'));
    }

    public
    function sendMail(Request $request)
    {
        $id= $request->input('id');
        $mail = EmailConfiguration::query()->find($id);
        if ($mail) {
            Mail::to($mail->mail_to)
                ->queue(new SendMailable($mail));
            // check for failures
            if (Mail::failures()) {
//                return redirect()
//                    ->back()->with('error', 'Sorry, Mail sending failed!');
                return back()->with('error', 'Email is not sent.');

            } else {
                $mail->is_sent = 1;
                $mail->save();
            }
        }
        return Response::json(['success' => 'Email Has Been Sent Successfully!'], 200);
//        return redirect(route('emails.index'))->with('success','Email Has Been Sent Successfully!');

//        return back()->with('success', 'Email Has Been Sent Successfully!');
    }

    public
    function deleteMail($id)
    {
        $email = EmailConfiguration::query()->find($id);
        if ($email) {
            $email->delete();
        }
        return redirect(route('emails.index'))->with('success','Email has been deleted successfully!');
    }

    public
    function editMail($id)
    {
        $email = EmailConfiguration::query()->find($id);
        if ($email) {
            return view('backend.settings.email-configuration', ['email' => $email]);
        }
    }


    public function getTemplate(Request $request)
    {
        $email_type_id = $request->input('email_type_id');
        $email_config = EmailAddress::query()->where('email_type', '=', $email_type_id)->first();
        return response()->json(['email_config' => $email_config]);

    }

//    EMAIL TYPE FUNCTIONS

    public function emailTypes(Request $request)
    {
        $query = EmailType::query();
        $data = $query->get();

        $data->map(function ($item) {
            $address = EmailAddress::query()->where('email_type', '=', $item->id)->first();
            if ($address) {
                $item['email_address'] = $address->email_address;
                $item['template'] = $address->template;
                $item['address_id'] = $address->id;
            } else {
                $item['email_address'] = '<span class="text-muted"> <i> NO EMAIL ADDRESS ADDED YET</i></span>';
                $item['template'] = '<span class="text-muted"> <i>NO TEMPLATE ADDED YET</i></span>';
            }
            $item['added_on'] = Carbon::parse($item->created_on)->format('d F, Y');
            return $item;
        });

        if ($request->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '
                    <a  href="#"
                data-aid="' . $row->address_id . '" data-id="' . $row->id . '" data-name="' . $row->name . '"
                class="btn btn-xs btn-primary mr-5px edit-email-type"> <i class="fa fa-pencil"></i><span> Edit</span></a>
                
                                            <a href="' . env('APP_URL') . '/dashboard/email-type/delete/' . $row->id . '"
                                            onclick="return confirm(\'Do you really want to delete this Email Type?\')"
                                            class="btn btn-xs btn-danger"> <i class="fa fa-trash-o"></i><span> Delete</span></a>';
                    return $btn;
                })
                ->rawColumns(['action', 'template', 'email_address'])
                ->make(true);
        }
        return view('backend.email-types.list');
    }

    public function newEmailType()
    {
        $view = \Illuminate\Support\Facades\View::make('backend.email-types.create');
        $data['modal_id'] = time() . 'A' . rand(1000, 9999);
        $data['view'] = $view->render();
        return response()->json($data);
    }

    public function editType(Request $request)
    {
        $aid = $request->input('aid');
        $view = \Illuminate\Support\Facades\View::make('backend.email-types.create');
        $data['id'] = $request->input('id');
        $data['name'] = $request->input('name');
        $data['aid'] = $aid;
        $address = EmailAddress::query()->find($aid);
        if ($address) {
            $data['template'] = $address->template;
            $data['email_address'] = $address->email_address;
        } else {
            $data['template'] = " NO";
            $data['email_address'] = " NO";
        }
        $data['modal_id'] = time() . 'A' . rand(1000, 9999);
        $data['view'] = $view->render();
        return response()->json($data);
    }

    public function deleteEmailType($id)
    {
        $emailType = EmailType::query()->find($id);
        if ($emailType) {
            $emailType->delete();
            return back()->with('success', 'Email type has been deleted successfully!');
        }
    }

    public function saveEmailType(Request $request)
    {

        $rules = array(
            'name' => 'required',
            'email_address' => 'required',
            'email_template' => 'required',
        );

        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()]);
        }
        $id = $request->input('id');
        $address_id = $request->input('aid');

        $address = EmailAddress::query()->find($address_id);
        $emailType = EmailType::query()->find($id);
        if (!$emailType) {
            $emailType = new EmailType();
        }
        $emailType->name = $request->input('name');
        $emailType->save();

        if (!$address) {
            $address = new EmailAddress();
        }

        $address->email_type = $emailType->id;
        $address->email_address = $request->input('email_address');
        $address->template = $request->input('email_template');
        $address->save();

        return back()->with(['success' => 'Email type is saved successfully.']);
//        return response()->json(['success'=>'Email type is saved successfully.']);
    }

//    EMAIL ADDRESSES FUNCTIONS

    public function emailAddresses(Request $request)
    {
        $query = EmailAddress::query()
            ->join('email_types', 'email_addresses.email_type', '=',
                'email_types.id', 'inner')->select('email_types.name as email_type_name', 'email_addresses.*');
        $data = $query->get();

        $data->map(function ($item) {
            $item['added_on'] = Carbon::parse($item->created_on)->format('d F, Y');
            return $item;
        });

        if ($request->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a  
                href="' . env('APP_URL') . '/dashboard/email-address/edit/' . $row->id . '"
                data-id="' . $row->id . '" class="btn btn-xs btn-primary mr-5px"> <i class="fa fa-pencil"></i><span> Edit</span></a>
                                            <a href="' . env('APP_URL') . '/dashboard/email-address/delete/' . $row->id . '"
                                            onclick="return confirm(\'Do you really want to delete this Email Address?\')"
                                            class="btn btn-xs btn-danger"> <i class="fa fa-trash-o"></i><span> Delete</span></a>';
                    return $btn;
                })
                ->rawColumns(['action', 'template'])
                ->make(true);
        }
        return view('backend.email-addresses.list');
    }

    public function addEmailAddress()
    {
        return view('backend.email-addresses.create');

    }

    public function editEmailAddress($id)
    {
        $email_address = EmailAddress::query()->find($id);
        if ($email_address) {
            return view('backend.email-addresses.create', ['email_address' => $email_address]);
        }

    }

    public function saveEmailAddress(Request $request)
    {
        $request->flash();
        $id = $request->input('id');
        $email_address = EmailAddress::query()->find($id);

        $rules = array(
            'email_type' => 'required',
            'email_address' => 'required',
            'email_template' => 'required'
        );

        $validator = Validator::make(Input::all(), $rules);

        $validator->after(function ($validator) use ($request) {
            $email_type = $request->input('email_type');
            $email_address = EmailAddress::query()->where('email_type', '=', $email_type)->first();
            if ($email_address && $email_address->id != $request->input('id')) {
                $validator->errors()->add('email_type', 'An email address already exists for this email type.');
            }
        });


        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if (!$email_address) {
            $email_address = new EmailAddress();
        }
        $email_address->email_type = $request->input('email_type');
        $email_address->email_address = $request->input('email_address');
        $email_address->template = $request->input('email_template');
        $email_address->save();
//        return response()->json(['success'=>'Email address details are saved.']);

        return redirect()->to(route('email-addresses.index'));
    }

    public function deleteEmailAddress($id)
    {
        $emailAddress = EmailAddress::query()->find($id);
        if ($emailAddress) {
            $emailAddress->delete();
            return back()->with('success', 'Email Address has been deleted successfully!');
        }
    }

}
