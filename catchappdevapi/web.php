<?php

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
    Route::get('', 'Frontend\HomeController@index');
    Route::post('admin-login', 'Auth\LoginController@adminLogin');
    Route::get('admin-log-out', 'Auth\LoginController@adminLogout');
});

//Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth']], function () {
//
//});

Route::post('/addStory','Backend\StoryController@addStory');

Route::group(['prefix' => 'dashboard'], function () {

    Route::get('/data-table', ['uses'=>'Backend\NewController@index','as'=>'users.index']);


    Route::get('/', 'Backend\DashboardController@index');
    Route::get('/users', ['uses' => 'Backend\UserController@index', 'as' => 'users.index']);

    Route::get('/users/add-new', 'Backend\UserController@addNew');
    Route::post('/users/save-user', 'Backend\UserController@saveUser');
    Route::get('/users/edit-user/{id}', 'Backend\UserController@editUser');
    Route::get('/users/delete-user/{id}', 'Backend\UserController@deleteUser');
    Route::get('/users/remove-profile-image/{id}', 'Backend\UserController@deleteProfilePic');

    Route::get('/clubs', 'Backend\ClubController@index');
    Route::get('/clubs/add-new', 'Backend\ClubController@addNew');
    Route::post('/clubs/save-club', 'Backend\ClubController@saveClub');
    Route::get('/clubs/edit-club/{id}', 'Backend\ClubController@editClub');
    Route::get('/clubs/delete-club/{id}', 'Backend\ClubController@deleteClub');
    Route::get('/clubs/remove-profile-image/{id}', 'Backend\ClubController@deleteProfilePic');
    Route::post('club/change-states','Backend\ClubController@changeStates');
    Route::post('club/change-cities','Backend\ClubController@changeCities');

    Route::get('/djs', 'Backend\DJController@index');
    Route::get('/djs/add-new', 'Backend\DJController@addNew');
    Route::post('/djs/save-dj', 'Backend\DJController@saveDj');
    Route::get('/djs/edit-dj/{id}', 'Backend\DJController@editDj');
    Route::get('/djs/delete-dj/{id}', 'Backend\DJController@deleteDj');
    Route::get('/djs/change-password/{id}', 'Backend\DJController@editPassword');
    Route::post('/djs/update-password', 'Backend\DJController@updatePassword');
    Route::get('/djs/assign-club/{id}', 'Backend\DJController@editClub');
    Route::post('/djs/update-club', 'Backend\DJController@updateClub');
    Route::get('/djs/remove-profile-image/{id}', 'Backend\DJController@deleteProfilePic');

    Route::get('/admin-profile', 'Backend\SettingController@adminProfile');
    Route::get('/email-configuration', 'Backend\SettingController@emails');
    Route::get('/settings/new-email', 'Backend\SettingController@emailConfiguration');
    Route::get('/insights', 'Backend\SettingController@insights');

    Route::get('/admin/remove-profile-image/{id}', 'Backend\SettingController@deleteProfilePic');
    Route::post('/admin/save-admin-profile', 'Backend\SettingController@saveAdminProfile');
    Route::post('/insight/save-insights', 'Backend\SettingController@saveInsights');
    Route::post('/settings/save-email', 'Backend\SettingController@saveEmail');
    Route::get('/settings/delete-mail/{id}', 'Backend\SettingController@deleteMail');
    Route::get('/settings/edit-mail/{id}', 'Backend\SettingController@editMail');
    Route::get('/admin/change-password/{id}', 'Backend\SettingController@editPassword');
    Route::post('admin/update-password', 'Backend\SettingController@changePassword');

    Route::get('/email/send-mail/{id}', 'Backend\SettingController@sendMail');


    Route::get('stories','Backend\StoryController@index');
    Route::post('/user/user-story','Backend\StoryController@changeStatus');
});
//
//Route::get('session/get','Auth\SessionController@accessSessionData');
//Route::get('session/set','Auth\SessionController@storeSessionData');
//Route::get('session/remove','Auth\SessionController@deleteSessionData');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
//Route::get('/dashboard', 'DashboardController@index')->name('home');
//
//
//Route::get('admin-login', 'Auth\AdminLoginController@showLoginForm');
//Route::post('admin-login', ['as'=>'admin-login','uses'=>'Auth\AdminLoginController@login']);
