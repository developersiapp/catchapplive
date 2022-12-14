<?php

/**
 * Created by PhpStorm.
 * User: iapp
 * Date: 17/6/19
 * Time: 10:10 AM
 */

namespace App\Libraries;

use Illuminate\Support\Facades\View;
use Illuminate\Validation\Validator;

class Ajax
{
    const MESSAGE_TYPE_SUCCESS = 'success';
    const MESSAGE_TYPE_ERROR = 'error';
    const MESSAGE_TYPE_INFO = 'info';


    private $arr;

    public function __construct()
    {
        $this->arr = [
            'message' => false,
            'nmessage' => false
        ];
    }

    public function success()
    {
        $this->arr['success'] = true;
        return $this;
    }

    public function fail()
    {
        $this->arr['success'] = false;
        return $this;
    }

    /**
     * @param Validator $validator
     * @return $this
     */
    public function form_errors($validator)
    {
        $this->arr['form_errors'] = $validator->errors();
        return $this;
    }

    public function js_callback($fn = "general_form")
    {
        $this->arr['completefn'] = $fn;
        return $this;
    }

    public function form_reset($fn = true)
    {
        $this->arr['form_reset'] = $fn;
        return $this;
    }

    public function page_reload($fn = true)
    {
        $this->arr['page_reload'] = $fn;
        return $this;
    }

    public function redirect_url($url)
    {
        $this->arr['redirect'] = true;
        $this->arr['redirectURL'] = $url;
        return $this;
    }

    public function param($key, $value)
    {
        $this->arr[$key] = $value;
        return $this;
    }

    public function message($messageTitle = "", $messageDescription = "", $messageType = "success")
    {
        $this->arr['message'] = true;
        $this->arr['messageTitle'] = $messageTitle;
        $this->arr['messageDescription'] = $messageDescription;
        $this->arr['messageType'] = $messageType;
        return $this;
    }

    public function notification($messageTitle = "", $messageDescription = "", $messageType = "success")
    {
        $this->arr['nmessage'] = true;
        $this->arr['nmessageTitle'] = $messageTitle;
        $this->arr['nmessageDescription'] = $messageDescription;
        $this->arr['nmessageType'] = $messageType;
        return $this;
    }

    /**
     * Get the evaluated view contents for the given view.
     *
     * @param  string $param
     * @param  string $view
     * @param  array $data
     * @return Ajax
     */
    public function loadView($param, $view, $data = [])
    {
        $view = View::make($view, $data);
        $contents = $view->render();
        $this->param($param, $contents);
        return $this;
    }

    public function send()
    {
        return response()->json($this->arr);
    }
}