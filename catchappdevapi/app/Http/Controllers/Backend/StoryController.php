<?php
/**
 * Created by PhpStorm.
 * User: iapp
 * Date: 12/6/19
 * Time: 11:18 AM
 */

namespace catchapp\Http\Controllers\Backend;


use App\Libraries\AjaxModal;
use Carbon\Carbon;
use catchapp\Http\Controllers\Controller;
use catchapp\Models\User;
use catchapp\Models\UserStory;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class StoryController extends Controller
{
    public function index16(Request $request)
    {

        $story_users_ids = UserStory::query()->distinct()
            ->pluck('user_id');

        $query = User::query()->whereIn('id', $story_users_ids);
        $data = $query->get();
        $stories = UserStory::query()->whereIn('user_id', $story_users_ids)
            ->where('status', '=', 1)
            ->where('is_active', '=', 1)->orderBy('created_at', 'DESC')->get();
        foreach ($stories as $item) {
            if ($item->created_at >= Carbon::now()->subDays(1)->toDateTimeString() && $item->status == 1) {
                $item->is_active = 1;
            } else {
                $item->is_active = 0;
            }
            $item->save();
        }
        $data->map(function ($item) {
            $activeCount = UserStory::query()->where('user_id', '=', $item->id)
                ->where('status', '=', 1)
                ->count();
            $blockedCount = UserStory::query()->where('user_id', '=', $item->id)
                ->where('status', '=', 0)
                ->count();
            $appearingCount = UserStory::query()->where('user_id', '=', $item->id)
                ->where('is_active', '=', 1)
                ->where('status', '!=', 0)
                ->where('created_at', '>=', Carbon::now()->subDays(1)->toDateTimeString())
                ->count();
            $status = "Active";
            if ($activeCount == 0) {
                $status = "Blocked";
            }
            $item['user_name'] = $item->first_name . " " . $item->last_name;
            $item['total_stories'] = "<b>" . $appearingCount . "</b> Appearing , <b>" . $blockedCount . "</b> Blocked";
            $item['status'] = $status;
            $latestStory = UserStory::query()
                ->where('user_id', '=', $item->id)
                ->latest()->first();
            $item['added_on'] = Carbon::parse($latestStory->created_at)->isoFormat('MMMM Do YYYY, h:mm:ss a');
            return $item;
        });
        if ($request->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('block/unblock', function ($row) {
                    if ($row->status == "Blocked") {
                        $btn = '<label class="switch"> <input type="checkbox" id="is_active"
                                                   class="toggle_status " name="is_active"
                                                   value=' . $row->id . ' data-toggle="toggle"> <span class="slider"></span> </label>
                                                   <span>  <small class="label label-danger"> Blocked</small></span>
                                                   ';
                    } else {
                        $btn = '<label class="switch"> <input type="checkbox" id="is_active" checked
                                                   class="toggle_status" name="is_active"
                                                   value=' . $row->id . ' data-toggle="toggle"> <span class="slider"></span> </label>';
                    }

                    return $btn;
                })
                ->addColumn('action', function ($row) {
                    $btn = ' <a 
                        href="' . env('APP_URL') . '/dashboard/userStories?user_id=' . $row->id . '"
                        data-userId="' . $row->id . '" id="viewStories"
                        class="btn btn-xs btn-danger mr-5px viewStories">
                        <i class="glyphicon glyphicon-eye-open"></i>
                        <span> </span>View Story List';
                    return $btn;
                })
                ->rawColumns(['action', 'total_stories', 'appearing_stories', 'block/unblock', 'modal'])
                ->make(true);
        }
        return view('backend.users.stories.list16');
    }

    public function changeUserStatus(Request $request)
    {
        $user_id = $request->input('user_id');
        $stories = UserStory::query()->where('user_id', $user_id)->get();
        foreach ($stories as $story) {
            $story->status = $request->input('is_active');
            $story->is_active = $request->input('is_active');
            $story->save();
        }
        $story_users_ids = UserStory::query()->distinct()
            ->pluck('user_id');

        $stories = UserStory::query()->whereIn('user_id', $story_users_ids)->get();
        return response()->json(['success' => 'User\'s Stories Status Is Changed!', 'stories' => $stories]);
    }

    public function userStories(Request $request)
    {
        $user_id = $request->get('user_id');
        $query = UserStory::query()
            ->where('user_id', '=', $user_id)->orderBy('created_at', 'DESC');

        $data = $query->get();
        foreach ($data as $item) {
            if ($item->created_at >= Carbon::now()->subDays(1)->toDateTimeString()) {
                $item->is_active = 1;
            } else {
                $item->is_active = 0;
            }
            $item->save();
        }
        $data->map(function ($item) {
            $status = "Active";
            if ($item->status == 0) {
                $status = "Blocked";
            }
            if ($item->is_active == 1 && $item->status == 1 && $item->created_at >= Carbon::now()->subDays(1)->toDateTimeString()) {
                $in_24hour = 'Appearing';
            } elseif ($item->status == 0 && $item->is_active == 1 || $item->status == 0 && $item->is_active == 0) {
                $in_24hour = "Blocked";
            } else {
                $in_24hour = 'Disappeared';
            }
            $item['story_type'] = UserStory::$story_type{$item->story_type};
//            $item['story_status'] = $status;
            $item['appear_status'] = $in_24hour;
            $item['added_on'] = Carbon::parse($item->created_at)->isoFormat('MMMM Do YYYY, h:mm:ss a');
            return $item;
        });

        if ($request->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('block/unblock', function ($row) {
                    if ($row->status == 0) {
                        $btn = '<label class="switch"> <input type="checkbox" id="is_active"
                                                   class="toggle_status " name="is_active"
                                                   value=' . $row->id . ' data-toggle="toggle"> <span class="slider"></span> </label>';
                    } else {
                        $btn = '<label class="switch"> <input type="checkbox" id="is_active" checked
                                                   class="toggle_status" name="is_active"
                                                   value=' . $row->id . ' data-toggle="toggle"> <span class="slider"></span> </label>';
                    }

                    return $btn;
                })
                ->addColumn('action', function ($row) {
                    $btn = '';
                    if ($row->story != '') {
                        $btn .= '<a href="#" class="btn btn-xs btn-primary mr-5px"
                        data-storyId="' . $row->id . '"
                        data-toggle="modal" data-target="#view_story" >
                        <i class="glyphicon glyphicon-eye-open"></i>
                        <span> View</span></a>';
                    } else {
                        $btn .= " <br><i><b>No Story Uploaded!</b></i>";
                    }
                    $btn .= '<a href="' . env('APP_URL') . '/dashboard/user-story/delete-story/' . $row->id . '"
                                            onclick="return confirm(\'Do you really want to delete this Story?\')"
                                            class="btn btn-xs btn-danger"> <i class="fa fa-trash-o"></i><span> Delete</span></a>';
                    return $btn;
                })
                ->rawColumns(['action', 'block/unblock', 'modal'])
                ->make(true);
        }
        return view('backend.users.stories.list1', ['user_id' => $user_id]);
    }


    public function index(Request $request)
    {
        $query = UserStory::query();
        $data = $query->get();

        $data->map(function ($item) {

            $user = User::withTrashed()->find($item->user_id);

            $status = "Active";
            if ($item->status == 0) {
                $status = "Blocked";
            }
            if ($user->deleted_at!='')
            {
                $item['user_name'] = $user->first_name." (Deleted User)";
            }
            else{$item['user_name'] = $user->first_name;
            }
            $item['story_type'] = UserStory::$story_type{$item->story_type};
            $item['story_status'] = $status;
            $item['added_on'] = Carbon::parse($item->created_at)->format('d F, Y');
            return $item;
        });
        if ($request->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('block/unblock', function ($row) {
                    if ($row->status == 0) {
                        $btn = '<label class="switch"> <input type="checkbox" id="is_active"
                                                   class="toggle_status " name="is_active"
                                                   value=' . $row->id . ' data-toggle="toggle"> <span class="slider"></span> </label>';
                    } else {
                        $btn = '<label class="switch"> <input type="checkbox" id="is_active" checked
                                                   class="toggle_status" name="is_active"
                                                   value=' . $row->id . ' data-toggle="toggle"> <span class="slider"></span> </label>';
                    }

                    return $btn;
                })
                ->addColumn('action', function ($row) {
                    $btn = '';
                    if ($row->story != '') {
                        $btn .= '<a href="#" class="btn btn-xs btn-primary mr-5px"
data-storyId="' . $row->id . '"
                data-toggle="modal" data-target="#view_story" >  <i class="glyphicon glyphicon-eye-open"></i><span> View</span></a>';
                    } else {
                        $btn .= " <br><i><b>No Story Uploaded!</b></i>";
                    }
                    $btn .= '<a href="' . env('APP_URL') . '/dashboard/user-story/delete-story/' . $row->id . '"
                                            onclick="return confirm(\'Do you really want to delete this Story?\')"
                                            class="btn btn-xs btn-danger"> <i class="fa fa-trash-o"></i><span> Delete</span></a>';
                    return $btn;
                })
                ->rawColumns(['action', 'block/unblock', 'modal'])
                ->make(true);
        }
        return view('backend.users.stories.list');
    }
    public function addStory(Request $request)
    {
//        echo "hello";
//        die();
//        $rules = array(
//            'story_type' => 'required',
//            'user_id' => 'required',
//        );
//
//        $validator = Validator::make(Input::all(), $rules);
//
//        $validator->after(function ($validator) use ($request) {
//            $story_type = $request->input('story_type');
//            if ($story_type == 'Text' && $request->hasFile('text_story') == '') {
//                $validator->errors()->add('text_story', 'Please, Add A Story!');
//            }
//
//            if ($story_type == 'Image' || $story_type == 'Video') {
//                if ($request->hasFile('file_story') == false) {
//                    $validator->errors()->add('file_story', 'Please, Select An Image/Video!');
//                } else {
//                    if ($story_type == 'Image') {
//                        $rules['file_story'] = 'required|mimes:jpeg,bmp,png|size:5000';
//                    }
//                    if ($story_type == 'Video') {
//                        $rules['file_story'] = 'required|mimes:m4v,avi,flv,mp4,mov|size:10000';
//                    }
//                }
//            }
//
//            if ($story_type == 'Video') {
//                if ($request->hasFile('video_story') == false) {
//                    $validator->errors()->add('video_story', 'Please, Select A File To Upload!');
//                } else {
//                    $rules['video_story'] = 'required|mimetypes:video/*';
//                }
//            }
//        });
//
//        if ($validator->fails()) {
//            return redirect()->back()->withErrors($validator)->withInput();
//        }

        $result = "Error!";
        $userStory = new UserStory();
        $story_type = $request->input('story_type');
        $userStory->user_id = $request->input('user_id');
        $userStory->status = 1;

        if ($story_type == 'Text') {
            $userStory->story_type = 1;
            $userStory->story = $request->input('text_story');
            $userStory->save();
            $result = "Text Story Is Saved!";
        }
        if ($story_type == 'Image' || $story_type == 'Video') {
            if ($request->hasFile('file_story')) {
                $file = $request->file('file_story');
                $file_extension = $file->getClientOriginalExtension();
                $image_extensions = ["jpg", "jpeg", "bmp", "png"];
                $video_extensions = ["m4v", "avi", "flv", "mp4", "mov"];
                if (in_array($file_extension, $image_extensions)) {
                    $userStory->story_type = 2;
                    $image = $request->file('file_story');
                    $filename = 'user-photo-story-' . time() . '.' . $file_extension;
                    $path = $image->move(base_path('public/uploads/PhotoStory/'), $filename);
                    $userStory->story = $filename;
                    $userStory->save();
                    $result = "Video Story Is Saved";
                } else {
                    $result = "This file format isn't supported";
                }

                if (in_array($file_extension, $video_extensions)) {
                    $userStory->story_type = 3;
                    $video = $request->file('file_story');

                    $filename = 'user-video-story-' . time() . '.' . $file_extension;
                    $path = $video->move(base_path('public/uploads/VideoStory/'), $filename);
                    $userStory->story = $filename;
                    $userStory->save();
                    $result = "Photo Story Saved!";
                } else {
                    $result = "This file format isn't supported";
                }
            } else {
                $result = "File Type Is Invalid!";
            }
        }
        print_r($result);
//        if ($story_type == 'Image') {
//            if ($request->hasFile('image_story')) {
//                $userStory->story_type = 2;
//
//                $photo = $request->file('image_story');
//                $extension = $photo->getClientOriginalExtension();
//                $filename = 'user-story-' . time() . '.' . $extension;
//                $path = $photo->move(base_path('public/uploads/UserStory/'), $filename);
//                $userStory->story = $filename;
//                $userStory->save();
//            }
//        }
//        if ($story_type == 'Video') {
//            $userStory->story_type = 3;
//
//        }

    }

    public function changeStatus(Request $request)
    {
        $status = $request->input('is_active');

        $id = $request->input('id');
        $story = UserStory::query()->find($id);
        $story->status = $status;
        if ($status == 0) {
            $story->is_active = 0;
        } else {
            $story->is_active = 1;
        }
        $story->save();
        $stories = UserStory::query()->get();
        return response()->json(['success' => 'Story Status Is Changed!', 'stories' => $stories]);
    }

    public function viewStory(Request $request)
    {

        $story_id = $request->input('story_id');
        $story = UserStory::query()->find($story_id);
        if ($story->story_type == 1) {
            $response_html = "<h5 style=\"text-align: center;\">
<p style=\"text-align: center;\"><label>Text: </label><p id=\"text_content\" style=\" color: $story->text_color;font-family: $story->font \">$story->story</p></p></h5>";
        } elseif ($story->story_type == 2) {
            $response_html = "<h4 style=\"text-align: center; \"><b>Image Not Found!</b></h4>";
            $old_image = base_path('public/uploads/PhotoStory/' . $story->story);
            if (is_file($old_image) && file_exists($old_image)) {
                $response_html = '<label> Image: </label>
                <img style="width: 100%;" class=" img img-bordered"
                src="' . env('APP_URL') . '/uploads/PhotoStory/' . $story->story . '" 
                alt="Image Story">';
            }
        } else {
            $response_html = "<h4 style=\"text-align: center;\"><b>Video Not Found!</b></h4>";
            $video = base_path('public/uploads/VideoStory/' . $story->story);
            if (is_file($video) && file_exists($video)) {




                $response_html = '<label>Video: </label>
                <video width="100%" controls>
                <source src="' . env('APP_URL') . '/uploads/VideoStory/' . $story->story . '">
                Your browser does not support the video tag. </video>';
            }
        }
        return response()->json(['success' => 'View Story!', 'response_html' => $response_html]);
    }

    public function deleteStory($id)
    {
        $story = UserStory::query()->find($id);
        if ($story) {
            if ($story->story_type == 2 || $story->story_type == 3) {
                if ($story->story_type == 2) {
                    if ($story->story != '') {
                        $path = base_path('public/uploads/PhotoStory/' . $story->story);
                        if (file_exists($path) && is_file($path)) {
                            unlink($path);
                        }
                    }
                } else {
                    if ($story->story != '') {
                        $path = base_path('public/uploads/VideoStory/' . $story->story);
                        if (file_exists($path)&& is_file($path)) {
                            unlink($path);
                        }
                    }
                }
            }

            $story->delete();
        }
        return back()->with('success', 'User Story has been deleted successfully.');
    }

    public function openModal(AjaxModal $ajaxModal)
    {
        return $ajaxModal->view('backend.emails.testModal')->send();
    }


}
