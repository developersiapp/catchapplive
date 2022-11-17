<?php
namespace catchapp\Http\Controllers\Frontend;
use catchapp\Http\Controllers\Controller;
use catchapp\Models\AdminUser;
use catchapp\Models\User;

/**
 * Created by PhpStorm.
 * User: iapp
 * Date: 5/6/19
 * Time: 11:36 AM
 */
class HomeController extends Controller
{

    public function index()
    {
        if (!session('admin_id')) {
        return view('frontend.admin.login');}
    else
        {
            $user = AdminUser::query()->find(session('admin_id'));
            return view('backend.dashboard.index', ['user' => $user])->with('message', 'Logged In Successfully!');

        }
    }
}