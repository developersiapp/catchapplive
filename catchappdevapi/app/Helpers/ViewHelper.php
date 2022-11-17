<?php
/**
 * Created by PhpStorm.
 * User: tarin
 * Date: 15-11-2018
 * Time: 03:03 PM
 */

namespace App\Helpers;


use Illuminate\Support\Facades\View;

class ViewHelper
{
    /**
     * Get the evaluated view contents for the given view.
     *
     * @param  string $view
     * @param  array $data
     * @return string
     */
    public static function getHtml($view, $data = [])
    {
        $view = View::make($view, $data);
        $contents = $view->render();
        return $contents;
    }

}