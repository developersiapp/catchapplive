<?php

namespace catchapp\Http\Controllers\API;
use Carbon\Carbon;
use catchapp\Helpers\MediaHelper;
use catchapp\Models\SeenStory;
use catchapp\Models\User;
use catchapp\Models\UserStory;
use catchapp\Models\ReportedStory;
use catchapp\Models\ReportedDj;
use Illuminate\Http\Request;
use catchapp\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use phpDocumentor\Reflection\DocBlock\Tags\See;

class StoryController extends Controller
{

    //      ADD STORY API

    public function addStory(Request $request)
    {
        $story_type = $request->input('story_type');
        $user_id = $request->input('user_id');
        $textStory = $request->input('text_story');
        $textColor = $request->input('text_color');
        $textFont = $request->input('text_font');
        $mediaStory = $request->file('media_story');
        if (empty($user_id)) {
            $response = [
                'error' => true,
                'message' => 'Please fill user_id required field!'
            ];
            return json_encode($response, 200);
        }
        if (!empty($user_id)) {
            $user = User::query()->find($user_id);
            if (empty($user)) {
                $response = [
                    'error' => true,
                    'message' => 'No record found with this user_id. Please provide a correct one!'
                ];
                return json_encode($response, 200);
            }
        }
        if (empty($story_type)) {
            $response = [
                'error' => true,
                'message' => 'Please fill story type required field!'
            ];
            return json_encode($response, 200);
        } else {
            $userStory = new UserStory();
            $userStory->status = 1;
            $userStory->user_id = $user_id;
            $userStory->story_type = $story_type;

            if ($story_type == 1) {
                if (empty($textStory)) {
                    $response = [
                        'error' => true,
                        'message' => 'Please fill text story required field!'
                    ];
                    return json_encode($response, 200);
                }

                if (empty($textColor)) {
                    $response = [
                        'error' => true,
                        'message' => 'Please fill text color required field!'
                    ];
                    return json_encode($response, 200);
                }
                if (empty($textFont)) {
                    $response = [
                        'error' => true,
                        'message' => 'Please fill text font required field!'
                    ];
                    return json_encode($response, 200);
                }
                else {
                    $userStory->story = $textStory;
                    $userStory->text_color = $textColor;
                    $userStory->font = $textFont;
                    $userStory->save();
                    $user->is_active=1;
                    $user->save();
                    $response = [
                        'error' => false,
                        'message' => 'User\'s text story is uploaded!',
                        'data' => [
                            'user_id' => $user_id,
                            'story_id' => $userStory->id,
                            'content' => $userStory->story,
                            'text_color' => $userStory->text_color,
                            'text_font' => $userStory->font
                        ],
                    ];
                    return json_encode($response, 200);
                }

            }
            if ($story_type == 2 || $story_type == 3) {
                if ($request->hasFile('media_story')) {
                    $file_extension = $mediaStory->getClientOriginalExtension();
                    $image_extensions = ["jpg", "jpeg", "bmp", "png"];
                    $allowedImageMimeTypes = ['image/jpeg', 'image/gif', 'image/png', 'image/bmp', 'image/svg+xml'];
                    $allowedVideoMimeTypes = ['video/3gpp', 'video/mp4', 'video/mpeg', 'video/ogg', 'video/quicktime', 'video/webm', 'video/x-m4v', 'video/ms-asf', 'video/x-ms-wmv', 'video/x-msvideo'];

                    $video_extensions = ["m4v", "avi", "flv", "mp4", "mov"];
                    if ($story_type == 2) {
                        $contentType = $mediaStory->getMimeType();

                        if (in_array($contentType, $allowedImageMimeTypes)) {


//                        if (in_array($file_extension, $image_extensions)) {
                            $file_size_mb = (number_format((float)$mediaStory->getSize() / 1048576, 2, '.', '') . ' MB');
                            $file_size = (number_format((float)$mediaStory->getSize() / 1000, 1, '.', '') . ' KB');
//                            if ($file_size > 3072) {
//                                $msg = "You tried uploading a file of size " . $file_size . "(" . $file_size_mb . "), which exceeds our file upload size limit of 2 MB";
//                                $response = [
//                                    'error' => true,
//                                    'message' => $msg
//                                ];
//                                return json_encode($response, 200);
//                            }

                            $filename = 'user-photo-story-' . time() . '.' . $file_extension;
                            $mediaStory->move(base_path('public/uploads/PhotoStory/'), $filename);
                            $userStory->story = $filename;
                            $userStory->save();
                            $user->is_active=1;
                            $user->save();
                            $response = [
                                'error' => false,
                                'message' => 'User\'s photo story is uploaded!',
                                'data' => [
                                    'user_id' => $user_id,
                                    'story_id' => $userStory->id,
                                    'content' => env('APP_URL') . '/uploads/PhotoStory/' . $userStory->story
                                ],
                            ];
                            return json_encode($response, 200);
                        } else {
                            $response = [
                                'error' => true,
                                'message' => 'Such file format isn\'t supported for this story_type!'
                            ];
                            return json_encode($response, 200);
                        }
                    }
                    if ($story_type == 3) {
                        $contentType = $mediaStory->getMimeType();
                        if (in_array($contentType, $allowedVideoMimeTypes)) {
//                        if (in_array($file_extension, $video_extensions)) {
                            $file_size = (number_format((float)$mediaStory->getSize() / 1048576, 2, '.', '') . ' MB');
//                            if ($file_size > 6) {
//                                $msg = "You tried uploading a file of size " . $file_size . ", which exceeds our file upload size limit of 5 MB";
//                                $response = [
//                                    'error' => true,
//                                    'message' => $msg
//                                ];
//                                return json_encode($response, 200);
//                            }
                            $filename = 'user-video-story-' . time() . '.' . $file_extension;
                            $mediaStory->move(base_path('public/uploads/VideoStory/'), $filename);
                            $video_path   = base_path().'/public/uploads/VideoStory/'.$filename;
                            $thumbnail_url = MediaHelper::generateThumbnail($video_path, $filename);

                            $userStory->story = $filename;
                            $userStory->save();



                            $user->is_active=1;
                            $user->save();
                            $response = [
                                'error' => false,
                                'message' => 'User\'s video story is uploaded!',
                                'data' => [
                                    'user_id' => $user_id,
                                    'story_id' => $userStory->id,
                                    'content' => env('APP_URL') . '/uploads/VideoStory/' . $userStory->story,
                                    'thumnbail' => $thumbnail_url
                                ],
                            ];
                            return json_encode($response, 200);
                        } else {
                            $response = [
                                'error' => true,
                                'message' => 'Such file format isn\'t supported for this story_type!'
                            ];
                            return json_encode($response, 200);
                        }
                    }
                } else {
                    $response = [
                        'error' => true,
                        'message' => 'Please fill media story required field!'
                    ];
                    return json_encode($response, 200);
                }
            } else {
                $response = [
                    'error' => true,
                    'message' => 'Invalid story type value.'
                ];
                return json_encode($response, 200);
            }
        }
    }



    //      LOG SEEN STORY

    public function logSeenStory(Request $request)
    {
        $user_id = $request->input('user_id');
        $story_id = $request->input('story_id');
        if (empty($user_id))
        {
            $response =[
                'error' => true,
                'message' =>'Please send user_id. (required field)'
            ];
            return json_encode($response, 200);
        }
        $user = User::query()->find($user_id);
        if (empty($user))
        {
            $response =[
                'error' => true,
                'message' =>'Please send a valid user_id. (required field)'
            ];
            return json_encode($response, 200);
        }

        if (!empty($user->deleted_at))
        {
            $response =[
                'error' => true,
                'message' => 'Sorry, this user has been deleted by Super Admin.'
            ];
            return json_encode($response, 200);
        }

        if (empty($story_id))
        {
            $response =[
                'error' => true,
                'message' =>'Please send story_id. (required field)'
            ];
            return json_encode($response, 200);
        }

        $story = UserStory::query()->find($story_id);

        if (empty($story))
        {
            $response =[
                'error' => true,
                'message' =>'Please send a valid story id. (required field)'
            ];
            return json_encode($response, 200);
        }

        if (!empty($story->deleted_at))
        {
            $response =[
                'error' => true,
                'message' => 'Sorry, this Story has been deleted by Super Admin.'
            ];
            return json_encode($response, 200);
        }

        if ($story->user_id != $user_id) {

            $seenLog = SeenStory::query()->where('user_id','=', $user_id)
                ->where('story_id','=', $story_id)->first();

            if (!$seenLog) {
                $seenLog = new SeenStory();
                $seenLog->user_id = $user_id;
                $seenLog->story_id = $story_id;
                $seenLog->save();

                $response =[
                    'error' => false,
                    'message' =>'User\'s seen story has been logged successfully!'
                ];
                return json_encode($response, 200);
            }
            else
            {
                $response =[
                    'error' => false,
                    'message' => 'User\'s seen story is already logged!'
                ];
                return json_encode($response, 200);
            }
        }
        $response =[
            'error' => false,
            'message' =>'User\'s own story can\'t be logged!'
        ];
        return json_encode($response, 200);
    }




    //      User Stories

    public function userStories(Request $request)
    {
        define("ENCRYPTION_KEY", "!@#$%^&*");

        $user_id = $request->input('user_id');
        if (empty($user_id)) {
            $response = [
                'error' => true,
                'message' => 'Please fill user_id is required.'
            ];
            return json_encode($response, 200);
        } else {
            $user = User::query()->find($user_id);
            if (empty($user)) {
                $response = [
                    'error' => true,
                    'message' => 'No user is found with provided user_id.'
                ];
                return json_encode($response, 200);
            } else {
                $otuse_id= $user_id;
                $reported_stories = ReportedStory::query()->where('user_id','=', $user_id)->pluck('story_id')->toArray();

                $story_users_ids = UserStory::query()
                    ->whereNotIn('id',$reported_stories)
                    ->distinct()
                    ->pluck('user_id')->toArray();
                $position= array_search($user_id,$story_users_ids);
                unset($story_users_ids[$position]);

                $query = User::query()->whereIn('id', $story_users_ids);

                $limit = $request->input('limit', 0);
                if ($limit == "") {
                    $limit = 10;
                }
                if ($query->count()>0) {
                    $pageCount = ($query->count() / $limit);
                    if (is_float($pageCount)) {
                        $pageCount = round($pageCount) + 1;
                    }
                }
                else
                {
                    $pageCount=1;
                }
                if ($query->count() > 0) {
                    //Pagination //
                    $offset = $request->input('offset', 0);
                    if ($offset == "") {
                        $offset = 0;
                    }
                    $limit = $request->input('limit', 0);
                    if ($limit == "") {
                        $limit = 10;
                    }
                    $query->take($limit);
                    $query->skip($offset * $limit);
                    $storyUsers = $query->get();


                    $allstories = [];
                    $endstories = [];

                    if (isset($otuse_id)) {
                        if ($otuse_id!='') {

                            $user_stories = UserStory::query()
                                ->where('status', '=', 1)
                                ->where('user_id', '=', $otuse_id)
                                ->orderBy('created_at','DESC')
                                ->whereNotIn('id',$reported_stories)
                                ->get();
                            $user_stories_ids = UserStory::query()
                                ->where('status', '=', 1)
                                ->whereNotIn('id',$reported_stories)
                                ->where('user_id', '=', $otuse_id)->distinct()->pluck('id');
                            $user_stories_count = $user_stories->count();
                            $total_seen = SeenStory::query()->where('user_id','=', $user_id)
                                ->whereIn('story_id', $user_stories_ids)->count();

                            $ele['name'] = $user->first_name . " " . $user->last_name;
                            $ele['user_id'] = $otuse_id;

                            if (!empty($user->profile_image) || $user->profile_image!= null || $user->profile_image != " ") {
                                $prof_image = "";
                                $image_path = base_path('public/uploads/users/' . $user->profile_image);
                                if (file_exists($image_path)&& is_file($image_path) ) {
                                    $prof_image = env('APP_URL') . '/uploads/users/' . $user->profile_image;
                                }
                                $ele['user_picture'] = $prof_image;
                            } else {
                                $ele['user_picture'] = '';
                            }
                            $ele['total_seen'] = $total_seen;
                            $stories = [];
                            foreach ($user_stories as $story) {
                                $single_story = [];
                                $is_seen = SeenStory::query()
                                    ->where([['user_id', '=', $user_id],['story_id', '=', $story->id]])
                                    ->first();
                                $single_story['story_id'] = $story->id;

                                if ($is_seen) {
                                    $single_story['story_seen'] = 1;
                                } else {
                                    $single_story['story_seen'] = 0;
                                }
                                if ($story->story_type == 1) {
                                    $single_story['media_type'] = 'text';
                                    $single_story['content'] = $story->story;
                                    $single_story['text_color'] = $story->text_color;
                                    $single_story['text_font'] = $story->font;
                                    array_push($stories, $single_story);
                                }
                                elseif ($story->story_type == 2) {
                                    $single_story['media_type'] = 'image';
                                    $single_story['content'] = (env('APP_URL') . '/uploads/PhotoStory/' . $story->story);
                                    if ($story->story != '') {
                                        $image_path = base_path('public/uploads/PhotoStory/' . $story->story);
                                        $image_file_headers = @get_headers($image_path);

                                        if ($image_file_headers[0] != 'HTTP/1.0 404 Not Found' &&
                                            $image_file_headers[0] != 'HTTP/1.0 302 Found' &&
                                            $image_file_headers[7] != 'HTTP/1.0 404 Not Found') {
                                            array_push($stories, $single_story);
                                        }
                                    }
                                }
                                elseif ($story->story_type == 3) {
                                    $v_story = env('APP_URL') . '/uploads/VideoStory/' . $story->story;
//                                    $v_story = base_path('public/uploads/VideoStory/' . $story->story);
                                    $headers = @get_headers($v_story);

                                    if($headers && strpos( $headers[0], '200')) {
                                        $single_story['media_type'] = 'video';
                                        $single_story['content'] = (env('APP_URL') . '/uploads/VideoStory/' . $story->story);
                                        $thumb = $story->story . ".jpg";
                                        $thumbnail_url = env('APP_URL') . '/uploads/VideoThumbnail/' . $thumb;
                                        $single_story['thumbnail'] = "";
                                        $thumbnail = base_path('public/uploads/VideoThumbnail/' . $thumb);
                                        if (file_exists($thumbnail) && is_file($thumbnail)) {
                                            $single_story['thumbnail'] = $thumbnail_url;
                                        } else {
                                            $video_path = base_path('public/uploads/VideoStory/' . $story->story);
                                            $thumbnail_url = MediaHelper::generateThumbnail($video_path, $story->story);
                                            $thumbnail = base_path('public/uploads/VideoThumbnail/' . $story->story . ".jpg");
                                            if (file_exists($thumbnail) && is_file($thumbnail)) {
                                                $single_story['thumbnail'] = $thumbnail_url;
                                            }
                                        }

                                        if ($story->story != '') {
                                            $video_path = base_path('public/uploads/VideoStory/' . $story->story);
                                            if (file_exists($video_path) && is_file($video_path)) {
                                                array_push($stories, $single_story);
                                            }
                                        }
                                    }
                                }
                            }
                            $ele['stories'] = $stories;

                            array_push($allstories, $ele);
                        }
                        unset($otuse_id);
                    }

                    foreach ($storyUsers as $user) {
                        $user_stories = UserStory::query()
                            ->where('status', '=', 1)
                            ->where('user_id', '=', $user->id)
                            ->whereNotIn('id',$reported_stories)
                            ->orderBy('created_at','DESC')
                            ->get();
                        $user_stories_ids = UserStory::query()
                            ->whereNotIn('id',$reported_stories)
                            ->where('status', '=', 1)
                            ->where('user_id', '=', $user->id)->distinct()->pluck('id');
                        $user_stories_count = $user_stories->count();
                        $total_seen = SeenStory::query()->where('user_id','=', $user_id)
                            ->whereIn('story_id', $user_stories_ids)->count();

                        $ele['name'] = $user->first_name . " " . $user->last_name;
                        $ele['user_id'] = $user->id;

                        if (!empty($user->profile_image) || $user->profile_image!= null || $user->profile_image != " ") {
                            $prof_image = "";
                            $image_path = base_path('public/uploads/users/' . $user->profile_image);
                            if (file_exists($image_path)&& is_file($image_path) ) {
                                $prof_image = env('APP_URL') . '/uploads/users/' . $user->profile_image;
                            }
                            $ele['user_picture'] = $prof_image;
                        } else {
                            $ele['user_picture'] = '';
                        }
                        $ele['total_seen'] = $total_seen;
                        $stories = [];
                        foreach ($user_stories as $story) {
                            if (!empty($story->story))
                            {
                                $single_story = [];
                                $is_seen = SeenStory::query()
                                    ->where([['user_id', '=', $user_id],['story_id', '=', $story->id]])
                                    ->first();
                                $single_story['story_id'] = $story->id;

                                if ($is_seen) {
                                    $single_story['story_seen'] = 1;
                                } else {
                                    $single_story['story_seen'] = 0;
                                }
                                if ($story->story_type == 1) {
                                    $single_story['media_type'] = 'text';
                                    $single_story['content'] = $story->story;
                                    $single_story['text_color'] = $story->text_color;
                                    $single_story['text_font'] = $story->font;
                                    array_push($stories, $single_story);
                                } elseif ($story->story_type == 2) {
                                    $single_story['media_type'] = 'image';
                                    $single_story['content'] = (env('APP_URL') . '/uploads/PhotoStory/' . $story->story);
                                    if ($story->story != '') {
                                        $image_path = base_path('public/uploads/PhotoStory/' . $story->story);
                                        $image_file_headers = @get_headers($image_path);

                                        if ($image_file_headers[0] != 'HTTP/1.0 404 Not Found' &&
                                            $image_file_headers[0] != 'HTTP/1.0 302 Found' &&
                                            $image_file_headers[7] != 'HTTP/1.0 404 Not Found') {
                                            array_push($stories, $single_story);
                                        }
//                                        else
//                                        {
//                                            $story->forceDelete();
//                                        }
                                    }
                                }
                                elseif ($story->story_type == 3) {
//                                    $v_story = (env('APP_URL') . 'public/uploads/VideoStory/' . $story->story);
                                    $v_story = (env('APP_URL') . '/uploads/VideoStory/' . $story->story);
//                                    $v_story = base_path('public/uploads/VideoStory/' . $story->story);
                                    $headers = @get_headers($v_story);

                                    if($headers && strpos( $headers[0], '200')) {
                                        $single_story['media_type'] = 'video';
                                        $single_story['content'] = (env('APP_URL') . '/uploads/VideoStory/' . $story->story);
                                        $thumb = $story->story . ".jpg";
                                        $thumbnail_url = env('APP_URL') . '/uploads/VideoThumbnail/' . $thumb;
                                        $single_story['thumbnail'] = "";
                                        $thumbnail = base_path('public/uploads/VideoThumbnail/' . $thumb);
                                        if (file_exists($thumbnail) && is_file($thumbnail)) {
                                            $single_story['thumbnail'] = $thumbnail_url;
                                        } else {
                                            $video_path = base_path('public/uploads/VideoStory/' . $story->story);
                                            $thumbnail_url = MediaHelper::generateThumbnail($video_path, $story->story);
                                            $thumbnail = base_path('public/uploads/VideoThumbnail/' . $story->story . ".jpg");
                                            if (file_exists($thumbnail) && is_file($thumbnail)) {
                                                $single_story['thumbnail'] = $thumbnail_url;
                                            }
                                        }
                                        if ($story->story != '') {
                                            $video_path = base_path('public/uploads/VideoStory/' . $story->story);
                                            if (file_exists($video_path) && is_file($video_path)) {
                                                array_push($stories, $single_story);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        $ele['stories'] = $stories;
                        if ($total_seen >= $user_stories_count )
                        {
                            array_push($endstories, $ele);
                        }
                        else
                        {
                            array_push($allstories, $ele);
                        }
                    }
                    $merge = array_merge($allstories, $endstories);

                    $response = [
                        'error' => false,
                        'message' => 'All Stories.',
                        'data' => $merge,
                        'pageCount' => $pageCount
                    ];
                    return json_encode($response, 200);
                } else {
                    $response = [
                        'error' => true,
                        'message' => 'No story is uploaded yet.'
                    ];
                    return json_encode($response, 200);
                }
            }
        }
    }

    public function userStoriesForWeb(Request $request)
    {
        $response = [];
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:users,id',
            ]);
            if ($validator->fails()) {
                $response['success']= false;
                $response['status_code'] = '401';
                $response['message'] = $validator->errors()->first();
            } else {
                $id = $request->input('id');

                $reported_stories = ReportedStory::query()->where('user_id','=', $id)->pluck('story_id')->toArray();
                $story_users_ids = UserStory::query()
//                    ->whereDate('created_at', '>=', $date->toDateString())
                    ->whereNotIn('id',$reported_stories)
                    ->distinct()
                    ->pluck('user_id')->toArray();
                /** Fetching user's ids who uploaded stories */

//                $position= array_search($id,$story_users_ids);
//                unset($story_users_ids[$position]);

                $Users_stories=[];
                $storyUsers = User::query()->whereIn('id', $story_users_ids)->orderBy('id')->get();
                if ($storyUsers->count()>0) {
                    foreach ($storyUsers as $user) {

                        $single_user =[];
                        $single_user['user_id']= $user->id;
                        $single_user['name']= $user->first_name . " " . $user->last_name;

                        $obj_story = [];
                        $obj_story2['header']['heading'] = $user->first_name . " " . $user->last_name;
                        if (!empty($user->profile_image) || $user->profile_image != null || $user->profile_image != " ") {
                            $prof_image = "";
                            $image_path = base_path('public/uploads/users/' . $user->profile_image);
                            if (file_exists($image_path) && is_file($image_path)) {
                                $prof_image = env('APP_URL') . '/uploads/users/' . $user->profile_image;
                            }
                            $single_user['profile_picture']=$prof_image;
                            $obj_story2['header']['profileImage'] = $prof_image;
                        } else {
                            if (!empty($user->profile_picture_url)) {
                                if (file_exists($user->profile_picture_url) && is_file($user->profile_picture_url)) {
                                    $obj_story2['header']['profileImage'] = $user->profile_picture_url;
                                    $single_user['profile_picture'] = $user->profile_picture_url;
                                } else {
                                    $obj_story2['header']['profileImage'] = '';
                                    $single_user['profile_picture'] = '';
                                }
                            }
                        }

                        $stories=[];
                        $user_stories = UserStory::query()
                            ->whereNotIn('id',$reported_stories)
                            ->where('status', '=', 1)
                            ->where('user_id', '=', $user->id)
                            ->orderBy('created_at', 'DESC')
                            ->get();
                        if ($user_stories->count() > 0) {
                            foreach ($user_stories as $story) {
                                $obj_story=[];
                                $obj_story1=[];
                                $is_seen = SeenStory::query()
                                    ->where([['user_id', '=', $id], ['story_id', '=', $story->id]])
                                    ->first();

                                if ($story->story_type == 1) {
//                                    $obj_story1['id'] = $story->id;
                                    $obj_story1['content'] = '<p>'.$story->story.'</p>';
                                    $obj_story1['text_color'] = $story->text_color;
                                    $obj_story1['text_font'] = $story->font;
                                    $obj_story2['header']['subheading'] = Carbon::parse($story->created_at)->isoFormat('MMMM Do YYYY, h:mm:ss a');                                                $thumb = $story->story . ".jpg";
//                                    $obj_story1['type'] = 'text';
//                                    if ($is_seen) {
//                                        $obj_story1['seen'] = 1;
//                                    } else {
//                                        $obj_story1['seen'] = 0;
//                                    }
                                    $obj_story= array_merge($obj_story1, $obj_story2);
//                                    array_push($stories, $obj_story);
                                } elseif ($story->story_type == 2) {
                                    if ($story->story != '') {
                                        $image_path = base_path('public/uploads/PhotoStory/' . $story->story);
                                        $image_file_headers = @get_headers($image_path);
                                        if ($image_file_headers[0] != 'HTTP/1.0 404 Not Found' &&
                                            $image_file_headers[0] != 'HTTP/1.0 302 Found' &&
                                            $image_file_headers[7] != 'HTTP/1.0 404 Not Found') {

                                            $obj_story1['url'] = (env('APP_URL') . '/uploads/PhotoStory/' . $story->story);
//                                            $obj_story1['type'] = 'image';
//                                            $obj_story1['id'] = $story->id;
                                            $obj_story2['header']['subheading'] = Carbon::parse($story->created_at)->isoFormat('MMMM Do YYYY, h:mm:ss a');                                                $thumb = $story->story . ".jpg";
//                                            if ($is_seen) {
//                                                $obj_story1['seen'] = 1;
//                                            } else {
//                                                $obj_story1['seen'] = 0;
//                                            }
                                            $obj_story= array_merge($obj_story1, $obj_story2);
                                            array_push($stories, $obj_story);
                                        }
                                    }
                                } elseif ($story->story_type == 3) {
                                    if ($story->story != '') {
                                        $v_story = (env('APP_URL') . '/uploads/VideoStory/' . $story->story);
                                        $file_headers = @get_headers($v_story);
                                        if ($file_headers[0] != 'HTTP/1.0 404 Not Found'
                                            && $file_headers[0] != 'HTTP/1.0 302 Found'
//                                            && $file_headers[7] != 'HTTP/1.0 404 Not Found'
                                        ) {
                                            $video_path = base_path('public/uploads/VideoStory/' . $story->story);
                                            if (file_exists($video_path) && is_file($video_path)) {
                                                $obj_story1['url'] = (env('APP_URL') . '/uploads/VideoStory/' . $story->story);
                                                $obj_story1['type'] = 'video';
                                                $obj_story1['duration'] = 25000;
//                                                $obj_story1['id'] = $story->id;
                                                $obj_story2['header']['subheading'] = Carbon::parse($story->created_at)->isoFormat('MMMM Do YYYY, h:mm:ss a');                                                $thumb = $story->story . ".jpg";
                                                $thumb = $story->story . ".jpg";
                                                $thumbnail_url = env('APP_URL') . '/uploads/VideoThumbnail/' . $thumb;
//                                                $obj_story1['thumbnail'] = "";
//                                                $thumbnail = base_path('public/uploads/VideoThumbnail/' . $thumb);
//                                                if (file_exists($thumbnail) && is_file($thumbnail)) {
//                                                    $obj_story1['thumbnail'] = $thumbnail_url;
//                                                } else {
//                                                    $video_path = base_path('public/uploads/VideoStory/' . $story->story);
//                                                    $thumbnail_url = MediaHelper::generateThumbnail($video_path, $story->story);
//                                                    $thumbnail = base_path('public/uploads/VideoThumbnail/' . $story->story . ".jpg");
//                                                    if (file_exists($thumbnail) && is_file($thumbnail)) {
//                                                        $obj_story1['thumbnail'] = $thumbnail_url;
//                                                    }
//                                                }
//                                                if ($is_seen) {
//                                                    $obj_story1['seen'] = 1;
//                                                } else {
//                                                    $obj_story1['seen'] = 0;
//                                                }
                                                $obj_story= array_merge($obj_story1, $obj_story2);
                                                array_push($stories, $obj_story);
                                            }
                                        }
                                    }
                                }
                            }
                            $single_user['stories']=$stories;
                        }
                        array_push($Users_stories, $single_user);
                    }
                }
                DB::commit();
                $response['success'] = true;
                $response['status_code'] = '200';
                $response['message'] = 'Active Stories';
                $response['data'] =$Users_stories;
            }
        }
        catch (\Exception $e) {
            DB::rollBack();
            $response['success']= false;
            $response['status_code'] = '400';
            $response['message'] = $e->getMessage();
        } finally {
            return response()->json($response);
        }
    }
    public function reportStory(Request $request)
    {
        $response = [];
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:user_stories,id',
                'user_id' => 'required|exists:users,id',
                'reason' => 'required|in:1,2',
            ]);
            if ($validator->fails()) {
                $response['success']= false;
                $response['status_code'] = '401';
                $response['message'] = $validator->errors()->first();
            } else {
                $id = $request->input('id');
                $user_id = $request->input('user_id');
                $exists = ReportedStory::query()->where('user_id','=',$user_id)->where('story_id','=', $id)->first();
                if (!empty($exists))
                {
                    throw new \Exception('Story already reported by this user.');
                }
                $report = new ReportedStory();
                $report->user_id= $user_id;
                $report->story_id= $id;
                $report->reason= $request->reason;
                $report->save();

                DB::commit();
                $response['success'] = true;
                $response['status_code'] = '200';
                $response['message'] = 'Story reported.';
            }
        }
        catch (\Exception $e) {
            DB::rollBack();
            $response['success']= false;
            $response['status_code'] = '400';
            $response['message'] = $e->getMessage();
        } finally {
            return response()->json($response);
        }
    }
	
	
	 public function reportDj(Request $request)
    {
        $response = [];
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:clubs,id',
                'user_id' => 'required|exists:users,id',
                'reason' => 'required|in:1,2',
            ]);
            if ($validator->fails()) {
                $response['success']= false;
                $response['status_code'] = '401';
                $response['message'] = $validator->errors()->first();
            } 
			else {
                $id = $request->input('id');
                $user_id = $request->input('user_id');
                $exists = ReportedDj::query()
				->where('user_id','=',$user_id)
				->where('dj_id','=', $id)
				->first();
				if (!empty($exists))
                {
					throw new \Exception('Club has already been reported by this user.');
                }
                $report = new ReportedDj();
                $report->user_id= $user_id;
                $report->dj_id= $id;
                $report->reason= $request->reason;
                $report->save();

                DB::commit();
                $response['success'] = true;
                $response['status_code'] = '200';
                $response['message'] = 'Your report has been submitted successfully. Admin will take action within 24 hrs.';
            }
        }
        catch (\Exception $e) {
            DB::rollBack();
            $response['success']= false;
            $response['status_code'] = '400';
            $response['message'] = $e->getMessage();
        } finally {
            return response()->json($response);
        }
    }

}
