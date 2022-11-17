<?php


namespace catchapp\Http\Controllers\API;


use catchapp\Http\Controllers\Controller;
use catchapp\Mail\SendMailable;
use catchapp\Models\DJ;
use catchapp\Models\EmailConfiguration;
use catchapp\Models\EmailType;
use catchapp\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class EmailController extends Controller
{
    public function requestEmail(Request $request)
    {
        $response = [];
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:djs,id',
                'text' => 'required'
            ]);

            if ($validator->fails()) {
                $response['error'] = true;
                $response['status_code'] = '401';
                $response['message'] = $validator->errors()->first();
            }
            else {
                $user_id = $request->input('user_id');
                $text = $request->input('text');
                $dj  = DJ::query()->find($user_id);
                if (!$dj || $dj->deleted_at != null)
                {
                    throw new \Exception("Sorry, This DJ can't send request email to join Club as this DJ has been deleted by Super Admin. Contact Administration for help!.");
                }

                //              SEND EMAIL
                $subject = 'Request from DJ to join Club!';
                $mail_to = env('HELP_MAIL_TO');
                $content = 'Hi Super Admin, Here is a request mail from a DJ '
                    . $dj->first_name.
                    ' whose email address is '.$dj->email.' to assign him his/her first Club on CatchApp. Here is the message from DJ attached to it:- <br><b>"'. $text.'"</b><br> Thank you!';
                $type = EmailType::query()
                    ->join('email_addresses', 'email_types.id', '=',
                        'email_addresses.email_type', 'inner')->select('email_addresses.email_address as mail_from', 'email_types.*')
                    ->where('name', 'LIKE', '%' . 'Super Admin' . '%')->first();

                $mail = new EmailConfiguration();
                $mail->mail_to = $mail_to;

                if ($type) {
                    $mail->email_type = $type->id;
                    $mail->mail_from = $dj->email;
                    $mail->mail_to = $type->mail_from;
                } else {
                    $mail->email_type = 0;
                    $mail->mail_from = $dj->email;
                }
                $mail->mail_subject = $subject;
                $mail->mail_content = $content;
                $mail->is_sent = 0;
                $mail->save();

//              SENDING MAIL
                if (isset($mail)) {
                    Mail::to($mail->mail_to)
                        ->queue(new SendMailable($mail));
                    // check for failures
                    if (Mail::failures()) {
                        throw new \Exception("Sorry, Your E-mail couldn't be delivered because some error occurred!");
                    } else {
                        $mail->is_sent = 1;
                        $mail->save();
                    }
                }
                DB::commit();
//              SEND EMAIL
               $response['error'] = false;
                $response['status_code'] = '200';
                $response['message'] = 'Email has been sent to Administration. Thank you!';
                return response()->json($response);
            }
        }
        catch
        (\Exception $e) {
            DB::rollBack();
            $response['error'] = true;
            $response['status_code'] = '400';
            $response['message'] = $e->getMessage();
        } finally {
            return response()->json($response);
        }

    }
}
