<?php
/**
 * Created by PhpStorm.
 * User: tarin
 * Date: 15-09-2018
 * Time: 04:28 PM
 */

namespace App\Helpers;


use Illuminate\Database\Eloquent\Collection;

class SelectHelper
{

    public static function select($value, $selectedValue = null)
    {
        if (is_numeric($value)) {
            $str = " value=" . htmlspecialchars($value) . " ";
        } else {
            $str = ' value="' . htmlspecialchars($value) . '" ';
        }

        if (is_object($selectedValue)) {
            if (get_class($selectedValue) == Collection::class ||
                get_class($selectedValue) == \Illuminate\Support\Collection::class) {
                $selectedValue = $selectedValue->toArray();
            }
        }
        if (is_array($selectedValue)) {
            if (in_array($value, $selectedValue)) {
                $str .= " selected ";
            }
        } else if ($value == $selectedValue) {
            $str .= " selected ";
        }
        return $str;
    }


}