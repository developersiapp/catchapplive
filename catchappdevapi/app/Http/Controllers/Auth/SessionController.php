<?php
/**
 * Created by PhpStorm.
 * User: iapp
 * Date: 7/6/19
 * Time: 1:15 PM
 */

namespace catchapp\Http\Controllers\Auth;


use catchapp\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SessionController extends  Controller
{
    public function accessSessionData(Request $request) {
        if($request->session()->has('admin_id'))
            echo $request->session()->get('admin_id');
        else
            echo 'Please, Login First!';
    }
    public function storeSessionData(Request $request) {
        $request->session()->put('admin_id',$request->input('admin_id'));
        echo "Admin Logged In Successfully!";
    }
    public function deleteSessionData(Request $request) {
        $request->session()->forget('admin_id');
        echo "Admin Logged Out.";
    }
}