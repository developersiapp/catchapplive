<?php

namespace catchapp\Http\Controllers\Backend;

use Carbon\Carbon;
use catchapp\Models\Club;
use catchapp\Models\DJ;
use catchapp\Models\Feedback;
use catchapp\Models\Page;
use catchapp\Models\Pivot_Dj_Club;
use catchapp\Models\User;
use Illuminate\Http\Request;
use catchapp\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class PageController extends Controller
{
    public function tncPage()
    {
        $page = Page::query()->where('type', '=', 2)->first();
        if ($page) {
            return view('backend.static-pages.tnc', ['page' => $page]);
        }
        return view('backend.static-pages.tnc');
    }

    public function privacyPolicyPage()
    {
        $page = Page::query()->where('type', '=', 1)->first();
        if ($page) {
            return view('backend.static-pages.privacy-policy', ['page' => $page]);
        }
        return view('backend.static-pages.privacy-policy');
    }


    public function savePrivacyPolicy(Request $request)
    {
        $rules = array(
            'title' => 'required',
            'page_content' => 'required'
        );
        $attributeNames = array(
            'page_content' => 'pages\'s content'
        );

        $validator = Validator::make(Input::all(), $rules);
        $validator->setAttributeNames($attributeNames);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $id = $request->input('id');
        $page = Page::query()->find($id);
        if (!$page) {
            $page = new Page();
            $page->type=1;
        }
        $page->title= ($request->input('title'));
        $page->page_content= ($request->input('page_content'));
        $page->save();
        return back()->with('success', 'Privacy policies are updated.');

    }
    public function saveTnc(Request $request)
    {
        $rules = array(
            'title' => 'required',
            'page_content' => 'required'
        );
        $attributeNames = array(
            'page_content' => 'pages\'s content'
        );

        $validator = Validator::make(Input::all(), $rules);
        $validator->setAttributeNames($attributeNames);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $id = $request->input('id');
        $page = Page::query()->find($id);
        if (!$page) {
            $page = new Page();
            $page->type=2;
        }
        $page->title= ($request->input('title'));
        $page->page_content= $request->input('page_content');
        $page->save();
        return back()->with('success', 'Terms & Conditions are updated.');

    }


    public function feedbacks(Request $request)
    {

        $query = Feedback::query()->orderBy('created_at', 'DESC');
        $data = $query->get();
        $data->map(function($item){
            $user = User::query()->find($item->user_id);
            $item['user_name'] = $user->first_name.' '.$user->last_name;
            $item['added_on'] = Carbon::parse($item->created_at)->format('d F, Y');
            return $item;
        });
        if ($request->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
//                ->addColumn('action', function ($row) {
//                    $btn = '<a href="' . env('APP_URL') . '/dashboard/clubs/edit-club/' . $row->id . '" class="btn btn-xs btn-primary mr-5px">
//                                            <i class="fa fa-pencil"></i><span> Edit</span>
//                                        </a><a href="' . env('APP_URL') . '/dashboard/clubs/delete-club/' . $row->id . '"
//                                            onclick="return confirm(\'Do you really want to delete this Club?\')"
//                                            class="btn btn-xs btn-danger"> <i class="fa fa-trash-o"></i><span> Delete</span></a>';
//
//                    return $btn;
//                })
//                ->rawColumns(['action', 'logo'])
                ->make(true);
        }
        return view('backend.feedbacks.list');
    }
}
