<?php
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::prefix('/')->group(function () {

    Route::get('test', 'TestController@index');
    Route::get('', ['uses' =>'Frontend\HomeController@index', 'as' => 'admin.index']);
    Route::post('admin-login', 'Auth\LoginController@adminLogin');
    Route::get('admin-log-out', 'Auth\LoginController@adminLogout');
    Route::get('user/reset-password/{token}', ['uses' => 'Frontend\UserController@index', 'as' => 'user.resetPassword']);
    Route::get('notice', ['uses' => 'Frontend\UserController@notice', 'as' => 'user.notice']);
    Route::get('dj/reset-password/{token}', ['uses' => 'Frontend\UserController@djindex', 'as' => 'dj.resetPassword']);
    Route::post('/user/update-password', ['uses' => 'Frontend\UserController@savePassword', 'as' => 'user.savePassword']);
    Route::get('privacy-policy', 'API\UserProfileController@privacyPolicyPage');
    Route::get('terms-and-conditions', 'API\UserProfileController@TnCPage');
});


Route::post('/addStory','Backend\StoryController@addStory');

Route::group(['prefix' => 'dashboard'], function () {

    //    DASHBOARD

    Route::get('/', ['uses'=>'Backend\DashboardController@index','as'=>'dashboard.index']);
    Route::get('/getData', ['uses'=>'Backend\DashboardController@getData','as'=>'dashboard.getData']);
    Route::post('/getfilteredData', ['uses'=>'Backend\DashboardController@getFilteredData','as'=>'dashboard.filterData']);



    //    -------------------------------------        //
    Route::get('session/get','Auth\SessionController@accessSessionData');
    Route::get('session/set','Auth\SessionController@storeSessionData');
    Route::get('session/remove','Auth\SessionController@deleteSessionData');

    //    USERS ROUTES

    Route::get('/users', ['uses' => 'Backend\UserController@index', 'as' => 'users.index']);
    Route::get('/users/add-new', 'Backend\UserController@addNew');
    Route::post('/users/save-user', 'Backend\UserController@saveUser');
    Route::get('/users/edit-user/{id}', 'Backend\UserController@editUser');
    Route::get('/users/delete-user/{id}', 'Backend\UserController@deleteUser');
    Route::get('/users/remove-profile-image/{id}', 'Backend\UserController@deleteProfilePic');
    Route::post('/editUser',['uses'=>'Backend\UserController@editUser','as'=>'user.editUser']);
    Route::get('/modal', 'Backend\StoryController@openModal');


    //    USER STORY ROUTES

    Route::get('stories', ['uses' => 'Backend\StoryController@index16', 'as' => 'user-stories.index']);
    Route::get('userStories', ['uses' => 'Backend\StoryController@userStories', 'as' => 'stories.userStories']);
    Route::post('/user/user-story',['uses'=>'Backend\StoryController@changeStatus', 'as'=>'user.userStory']);
    Route::post('/user/block-user', ['uses' => 'Backend\StoryController@changeUserStatus', 'as' => 'user.userStatus']);
    Route::post('/view-story',['uses'=>'Backend\StoryController@viewStory','as'=>'user.viewStory']);
    Route::get('user-story/delete-story/{id}','Backend\StoryController@deleteStory');
    Route::get('user-story/view/{id}','Backend\StoryController@viewStory');

    //    CLUB ROUTES

    Route::get('/clubs', ['uses'=>'Backend\ClubController@index','as'=>'clubs.index']);
    Route::get('/clubs/add-new', 'Backend\ClubController@addNew');
    Route::post('/clubs/save-club', 'Backend\ClubController@saveClub');
    Route::get('/clubs/edit-club/{id}', 'Backend\ClubController@editClub');
    Route::get('/clubs/delete-club/{id}', 'Backend\ClubController@deleteClub');
    Route::get('/clubs/remove-profile-image/{id}', 'Backend\ClubController@deleteProfilePic');
    Route::post('/club/change-states', ['uses' => 'Backend\ClubController@changeStates', 'as' => 'clubs.changeStates']);
    Route::post('/club/change-cities',['uses'=>'Backend\ClubController@changeCities', 'as' =>'clubs.changeCities']);

    //    DJS ROUTES

    Route::get('/djs', ['uses'=>'Backend\DJController@index','as'=>'djs.index']);
    Route::get('/djs/add-new', 'Backend\DJController@addNew');
    Route::post('/djs/save-dj', 'Backend\DJController@saveDj');
    Route::get('/djs/edit-dj/{id}', 'Backend\DJController@editDj');
    Route::get('/djs/delete-dj/{id}', 'Backend\DJController@deleteDj');
    Route::get('/djs/change-password/{id}', 'Backend\DJController@editPassword');
    Route::post('/djs/update-password', 'Backend\DJController@updatePassword');
    Route::get('/djs/assign-club/{id}', 'Backend\DJController@editClub');
    Route::post('/djs/update-club', 'Backend\DJController@updateClub');
    Route::get('/djs/remove-profile-image/{id}', 'Backend\DJController@deleteProfilePic');

    //    SETTING ROUTES

    //    admin

    Route::get('/admin-profile', 'Backend\SettingController@adminProfile');
    Route::get('/admin/remove-profile-image/{id}', 'Backend\SettingController@deleteProfilePic');
    Route::post('/admin/save-admin-profile', 'Backend\SettingController@saveAdminProfile');
    Route::get('/admin/change-password/{id}', 'Backend\SettingController@editPassword');
    Route::post('admin/update-password', 'Backend\SettingController@changePassword');

    //    insights

    Route::get('/insights', 'Backend\InsightController@insights');
    Route::post('/insight/save-insights', 'Backend\InsightController@saveInsights');

    //    email configuration

    Route::get('/email-configuration', ['uses'=>'Backend\EmailController@emails','as'=>'emails.index']);
    Route::get('/settings/new-email', 'Backend\EmailController@emailConfiguration');
    Route::post('/settings/save-email', 'Backend\EmailController@saveEmail');
    Route::get('/settings/delete-mail/{id}', 'Backend\EmailController@deleteMail');
    Route::get('/settings/edit-mail/{id}', 'Backend\EmailController@editMail');
    Route::post('/email/send-mail', ['uses'=>'Backend\EmailController@sendMail','as'=>'email.send']);
    Route::post('/email/email-type/change-template', ['uses' => 'Backend\EmailController@getTemplate', 'as' => 'email-configuration.get-template']);

    //    email types

    Route::get('/email-types',['uses'=>'Backend\EmailController@emailTypes','as' =>'email-types.index']);
    Route::get('/email-type/edit/{id}','Backend\EmailController@editEmailType');
    Route::get('/email-type/add-new',['uses'=>'Backend\EmailController@newEmailType','as'=>'email-type.create'] );
    Route::post('/email-type/edit',['uses'=>'Backend\EmailController@editType','as'=>'email-type.edit'] );
    Route::post('/email-type/save',['uses'=>'Backend\EmailController@saveEmailType','as'=>'email-type.save']);
    Route::get('/email-type/delete/{id}','Backend\EmailController@deleteEmailType');

    //    email addresses

    Route::get('/email-addresses',['uses'=>'Backend\EmailController@emailAddresses','as' =>'email-addresses.index']);
    Route::get('/email-address/add-new',['uses'=>'Backend\EmailController@addEmailAddress','as' =>'email-addresses.addNew']);
    Route::get('/email-address/edit/{id}',['uses'=>'Backend\EmailController@editEmailAddress','as' =>'email-addresses.edit']);
    Route::post('/email-addresses/save',['uses'=>'Backend\EmailController@saveEmailAddress','as' =>'email-addresses.save']);
    Route::get('/email-address/delete/{id}',['uses'=>'Backend\EmailController@deleteEmailAddress','as'=>'email-addresses.delete']);

    //    static pages

    Route::get('/page/tnc-page',['uses'=>'Backend\PageController@tncPage','as' =>'staticPage.tnc']);
    Route::get('/page/privacy-policy-page',['uses'=>'Backend\PageController@privacyPolicyPage','as' =>'staticPage.privacyPolicy']);
    Route::post('/pages/save-privacy-policy',['uses'=>'Backend\PageController@savePrivacyPolicy','as' =>'staticPage.savePrivacyPolicy']);
    Route::post('/pages/save-tnc',['uses'=>'Backend\PageController@saveTnc','as' =>'staticPage.saveTnc']);

    //     feedbacks
    Route::get('/feedbacks', ['uses'=>'Backend\PageController@feedbacks','as'=>'feedbacks.index']);


});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
