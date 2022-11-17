<?php

namespace catchapp\Http\Controllers\Auth;

use catchapp\Http\Controllers\Controller;
use catchapp\Models\AdminUser;
use catchapp\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function adminLogin(Request $request)
    {
        if (!session('admin_id')) {
            $email = $request->input('email');
            $password =$request->input('password');
            $user = AdminUser::query()->where('email', '=', $email)->first();
            if ($user) {
                if ($user->password == $password) {
                    session(['admin_id' => $user->id]);
                    session(['admin_email' => $user->email]);
                    return redirect()->route('dashboard.index')->with('message', 'Logged In Successfully!');
//                    return view('backend.dashboard.index', ['user' => $user])->with('message', 'Logged In Successfully!');
                } else {
                    return redirect()->back()
                        ->with('message', "Incorrect credentials! Try again.");
                }

            } else {
                return redirect()->back()
                    ->with('message', 'Incorrect credentials! Try again.');

            }
        } else {
            $user = AdminUser::query()->find(session('admin_id'));
            return redirect()->route('dashboard.index')->with('message', 'Logged In Successfully!');

//            return view('backend.dashboard.index',
//                ['user' => $user])
//                ->with('message', 'Logged In Successfully!');
        }
    }

    public function adminLogout()
    {
        if (session()->has('admin_id')) {
            session()->forget('admin_id');
            return redirect('/');
        }
        return redirect('/');
    }
}
