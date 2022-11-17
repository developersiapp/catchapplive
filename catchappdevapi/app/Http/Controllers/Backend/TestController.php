<?php
/**
 * Created by PhpStorm.
 * User: iapp
 * Date: 7/6/19
 * Time: 11:58 AM
 */

namespace catchapp\Http\Controllers\Backend;


use catchapp\Http\Controllers\Controller;
use catchapp\Mail\SendMailable;
use catchapp\Models\AdminUser;
use catchapp\Models\EmailConfiguration;
use catchapp\Models\Insight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class SettingzzzzController extends Controller
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

    public function insights()
    {
        if (session('admin_id')) {
            $admin = AdminUser::query()->find(session('admin_id'));
            if ($admin) {
                return view('backend.settings.insights');
            }
        } else {
            return view('frontend.admin.login');
        }
    }

    public function saveAdminProfile(Request $request)
    {
        $id = $request->input('id');
        $admin = AdminUser::query()->find($id);
        if ($admin) {
            $rules = array(
                'name' => 'required',
                'email' => 'required|unique:admin_users,email,' . $admin->id,
                'old_password' => 'required'
            );

            $validator = Validator::make(Input::all(), $rules);

            $validator->after(function ($validator) use($admin, $request) {
                if ($admin->password != $request->input('old_password') ) {
                    $validator->errors()->add('old_password', 'You\'ve Entered A Wrong Password!');
                }
            });

            if ($validator->fails()) {
                $messages = $validator->messages();
                return redirect()->back()->withErrors($validator)->withInput();
            } else {
                $admin->name = $request->input('name');
                $admin->email = $request->input('email');
                if ($request->hasFile('admin_image')) {
                    $photo = $request->file('admin_image');
                    $extension = $photo->getClientOriginalExtension();
                    $filename = 'admin-profile-photo-' . time() . '.' . $extension;
                    $path = $photo->move(base_path('public/uploads/admins/'), $filename);

                    $admin->profile_image = $filename;
                }
                $admin->save();
                return view('backend.settings.admin-profile', ['admin' => $admin]);
            }
        }
    }

    public function deleteProfilePic($id)
    {
        $admin = AdminUser::query()->find($id);
        if ($admin) {
            $admin->profile_image = "";
            $admin->save();
            return back()->with('success', 'Profile Picture Has Been Deleted!');
        }
    }

    public function saveInsights(Request $request)
    {
        $rules = array(
            'slow_count' => 'required',
            'normal_count' => 'required',
            'hype_count' => 'required',
        );
        $attributeNames = array(
            'slow_count' => 'Slow Count',
            'normal_count' => 'Normal Count',
            'hype_count' => 'Hype Count',
        );

        $validator = Validator::make(Input::all(), $rules);
        $validator->setAttributeNames($attributeNames);

        $validator->after(function ($validator) use ($request) {
            if ($request->input('hype_count') < $request->input('normal_count')
                || $request->input('hype_count') < $request->input('slow_count')
            ) {
                $validator->errors()->add('hype_count', 'Hype Count Should Be Most Among Slow, Normal & Hype Count!');
            }
            if ($request->input('normal_count') < $request->input('slow_count')
                || $request->input('normal_count') > $request->input('hype_count')
            ) {
                $validator->errors()->add('normal_count', 'Normal Count Should Be A Value Between Slow & Hype Count!');
            }
            if ($request->input('slow_count') > $request->input('normal_count')
                || $request->input('slow_count') > $request->input('hype_count')
            ) {
                $validator->errors()->add('slow_count', 'Slow Count Should Be Least Among Slow, Normal & Hype Count!');
            }
        });
        if ($validator->fails()) {
            $messages = $validator->messages();
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $id = $request->input('id');
        $insight = Insight::query()->find($id);
        if (!$insight)
        {
            $insight = new Insight();
        }
        $insight->hype_count = $request->input('hype_count');
        $insight->normal_count = $request->input('normal_count');
        $insight->slow_count = $request->input('slow_count');
        $insight->save();
        return view('backend.settings.insights');

    }

    public function emails()
    {
        $emails = EmailConfiguration::query()->get();
        return view('backend.settings.emails',['emails' => $emails]);
    }

    public  function emailConfiguration()
    {
        return view('backend.settings.email-configuration');
//        return view('backend.settings.test');
    }

    public function saveEmail(Request $request)
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

        if (!$mail)
        {
            $mail = new EmailConfiguration();
        }
        $mail->mail_to = $request->input('mail_to');
        $mail->mail_subject= $request->input('mail_subject');
        $mail->mail_content =$request->input('email_message');
        $mail->email_type =$request->input('email_type');
        $mail->save();
//        return response()->json(['success'=>'Email Has Been Updated Successfully.']);

        $emails = EmailConfiguration::query()->get();
        return view('backend.settings.emails',['emails' => $emails]);
    }

    public function deleteMail($id)
    {
        $email = EmailConfiguration::query()->find($id);
        if ($email)
        {
            $email->delete();
        }
        $emails =EmailConfiguration::query()->get();
        return view('backend.settings.emails',['emails'=> $emails]);

    }

    public function editMail($id)
    {
        $email = EmailConfiguration::query()->find($id);
        if ($email)
        {
            return view('backend.settings.email-configuration',['email' => $email]);
        }
    }

    public function sendMail($id)
    {
        $mail = EmailConfiguration::query()->find($id);
        if ($mail)
        {

            // Configuration
            $smtpAddress = 'smtp.zoho.com';
            $port = 465;
            $encryption = 'ssl';
            $yourEmail = 'developer.iapptechnologies@gmail.com';
            $yourPassword = '48+r4#gapC4P';
            $swift_smtpTransport = new \Swift_SmtpTransport();
            // Prepare transport
//            $transport =$swift_smtpTransport->($smtpAddress, $port, $encryption)
            $transport =$swift_smtpTransport->setPort($port)->setEncryption($encryption)
                ->setHost($smtpAddress)
                ->setUsername($yourEmail)
                ->setPassword($yourPassword);
            $mailer = new \Swift_Mailer($transport);

//            // Prepare content
//            $view = View::make('email_template', [
//                'message' => '<h1>Hello World !</h1>'
//            ]);
//
//            $html = $view->render();
            $swift_message = new \Swift_Message();
            // Send email
            $message = $swift_message->setDescription('test')
                ->setFrom(['mymail@zoho.com' => 'Our Code World'])
                ->setTo(["mail@email.com" => "mail@mail.com"])
                // If you want plain text instead, remove the second paramter of setBody
                ->setBody($mail->mail_content);
            if($mailer->send($message)){
                return "Check your inbox";
            }
//            Mail::to($mail->mail_to)
//                ->send(new SendMailable($mail));
//
//            $mail->is_sent=1;
//            $mail->save();
        }
        return "Something went wrong :(";
    }

    public function editPassword($id)
    {
        $admin = AdminUser::query()->find($id);
        if ($admin) {
            return view('backend.settings.changePassword', ['admin' => $admin]);
        }
    }

    public function changePassword(Request $request)
    {
        $id = $request->input('id');
        $admin = AdminUser::query()->find($id);
        $rules = array(
            'old_password' => 'required',
            'new_password' => 'required',
            'repeat_password' => 'required|same:new_password',
        );

        $validator = Validator::make(Input::all(), $rules);

        $validator->after(function ($validator) use($admin, $request) {
            if ($admin->password != $request->input('old_password') ) {
                $validator->errors()->add('old_password', 'You\'ve Entered A Wrong Password!');
            }
            if ($admin->password == $request->input('new_password') ) {
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
