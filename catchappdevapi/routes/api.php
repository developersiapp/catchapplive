<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//      USER API'S
Route::post('apple-register', 'API\AppleRegistrationController@userAppleRegister');
Route::post('apple-update', 'API\AppleRegistrationController@userAppleUpdate');


Route::post('dj-apple-register', 'API\AppleRegistrationController@djAppleRegister');
Route::post('dj-apple-update', 'API\AppleRegistrationController@djAppleUpdate');



Route::post('user-register', 'API\UserProfileController@userRegister');
Route::post('user-login', 'API\UserProfileController@userLogin');
Route::post('user-logout', 'API\UserProfileController@logOut');
Route::post('social-user-login', 'API\UserProfileController@socialUserLogin');
Route::post('user-forgot-password', 'API\UserProfileController@userForgotPassword');
Route::post('user-update', 'API\UserProfileController@userUpdate');
Route::post('add-user-story', 'API\StoryController@addStory');
Route::post('user-details', 'API\UserProfileController@userDetails');
Route::post('update-location', 'API\UserProfileController@userLocationUpdate');
Route::post('log-user-clubs', 'API\NotificationController@userClubLog');
Route::post('save-feedback','API\NotificationController@saveFeedback');
Route::post('get-notifications','API\NotificationController@getNotifications');
Route::post('inform-listeners','API\NotificationController@sendStreamStoppedNotification');
Route::post('log-seen-story','API\StoryController@logSeenStory');
Route::post('send-request-email','API\EmailController@requestEmail');

//  STORY API'S
Route::post('/addStory', 'API\StoryController@addStory');
Route::post('user-stories', 'API\StoryController@userStories');

Route::post('user-web-stories', 'API\StoryController@userStoriesForWeb');
Route::post('report-story', 'API\StoryController@reportStory');
Route::post('report-dj', 'API\StoryController@reportDj');




//      CLUB API'S
Route::post('club-details', 'API\UserProfileController@clubDetail');
Route::post('home-clubs', 'API\UserProfileController@homePageClubs');
Route::post('send-notifications', 'API\NotificationController@sendUserNotifications');
Route::post('test-notification', 'API\NotificationController@test');

Route::post('log-stream','API\StreamingController@logStream');
Route::post('log-stream-listeners','API\StreamingController@logStreamUser');
Route::post('log-left-listeners','API\StreamingController@logLeftUser');

//      STREAMING API URLS
Route::post('create-live-stream', 'API\StreamingController@createStream');
Route::get('fetch-stream', 'API\StreamingController@fetchStream');
Route::get('fetch-all-streams', 'API\StreamingController@fetchAllStreams');
Route::get('fetch-thumbnail-url', 'API\StreamingController@fetchThumbnail');
Route::post('fetch-lstream-state', 'API\StreamingController@fetchLStreamState');
Route::post('fetch-lstream-metrics', 'API\StreamingController@fetchLStreamMetrics');
Route::patch('update-stream', 'API\StreamingController@updateStream');
Route::delete('delete-stream', 'API\StreamingController@deleteStream');
Route::put('start-stream', 'API\StreamingController@startStream');
Route::put('stop-stream', 'API\StreamingController@stopStream');
Route::put('reset-stream', 'API\StreamingController@resetStream');
Route::put('generate-connection-code', 'API\StreamingController@generateCode');

//      DJ API'S
Route::post('dj-register', 'API\DjController@djRegister');
Route::post('update-dj', 'API\DjController@updateDj');
Route::post('dj-details', 'API\DjController@djDetails');
Route::post('dj-login', 'API\DjController@djLogin');
Route::post('social-dj-login', 'API\DjController@socialDjLogin');
Route::post('dj-reset-password', 'API\DjController@forgotPassword');
Route::post('dj-logout', 'API\DjController@logOut');
Route::post('dj-clubs', 'API\DjController@djClubList');
Route::post('dj-search-clubs', 'API\DjController@searchClub');
Route::get('/logStory','API\StoryController@logStory');


Route::post('update-dj-password', 'API\DjController@updateDjPasswordForWeb');

Route::post('update-user-password', 'API\UserProfileController@updateUserPasswordForWeb');



//      SOCKETS

Route::get('update-story-status', 'API\DjController@storyStatus');
Route::post('update-club', 'API\DjController@updateClub');
Route::post('dj-check-email', 'API\DjController@djCheckEmail');
Route::post('user-check-email', 'API\DjController@userCheckEmail');
Route::post('dj-social-web', 'API\DjController@djSocialLoginForWeb');
Route::post('user-social-web', 'API\DjController@userSocialLoginForWeb');
Route::get('live-dj-list', 'API\DjController@liveDjList');



Route::get('cities-list', 'API\CityController@citiesList');
Route::get('top-cities', 'API\CityController@topCities');
Route::post('search-city', 'API\CityController@searchCity');


Route::post('sendTest','API\NotificationController@sendTest');

