<?php
/**
 * Created by PhpStorm.
 * User: iapp
 * Date: 26/6/19
 * Time: 4:03 PM
 */

namespace catchapp\Http\Controllers\Backend;


use catchapp\Http\Controllers\Controller;
use catchapp\Models\AdminUser;
use catchapp\Models\Insight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class InsightController extends Controller
{
    public function insights()
    {
        if (session('admin_id')) {
            $admin = AdminUser::query()->find(session('admin_id'));
            if ($admin) {
                return view('backend.settings.insights');
            }
        } else {
            return view('frontend.admin.login');
        }
    }

    public
    function saveInsights(Request $request)
    {
        $rules = array(
            'slow_count' => 'required',
            'normal_count' => 'required',
            'hype_count' => 'required',
        );
        $attributeNames = array(
            'slow_count' => 'Slow Count',
            'normal_count' => 'Normal Count',
            'hype_count' => 'Hype Count',
        );

        $validator = Validator::make(Input::all(), $rules);
        $validator->setAttributeNames($attributeNames);

        $validator->after(function ($validator) use ($request) {
            if ($request->input('hype_count') < $request->input('normal_count')
                || $request->input('hype_count') < $request->input('slow_count')
            ) {
                $validator->errors()->add('hype_count', 'Hype Count Should Be Most Among Slow, Normal & Hype Count!');
            }
            if ($request->input('normal_count') < $request->input('slow_count')
                || $request->input('normal_count') > $request->input('hype_count')
            ) {
                $validator->errors()->add('normal_count', 'Normal Count Should Be A Value Between Slow & Hype Count!');
            }
            if ($request->input('slow_count') > $request->input('normal_count')
                || $request->input('slow_count') > $request->input('hype_count')
            ) {
                $validator->errors()->add('slow_count', 'Slow Count Should Be Least Among Slow, Normal & Hype Count!');
            }
        });
        if ($validator->fails()) {
            $messages = $validator->messages();
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $id = $request->input('id');
        $insight = Insight::query()->find($id);
        if (!$insight) {
            $insight = new Insight();
        }
        $insight->hype_count = $request->input('hype_count');
        $insight->normal_count = $request->input('normal_count');
        $insight->slow_count = $request->input('slow_count');
        $insight->save();
        return back()->with('success', 'Insights Has Been Updated!');

    }
}