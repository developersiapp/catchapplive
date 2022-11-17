<?php


namespace catchapp\Helpers;


use catchapp\Models\DJ;
use catchapp\Models\User;
use Illuminate\Support\Str;

class TokenHelper
{
    /**
     * Generates an alphanumeric token
     * @param $length_of_string
     * @return false|int|string
     */
    public static function alphaNumericTokenForUser($token)
    {
//        // String of all alphanumeric character
//        $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
//
//        // Shuffle the $str_result and returns substring of specified length
//        $token= substr(str_shuffle($str_result),
//            0, $length_of_string);
//

        if (self::tokenExistsForUser($token) == true) {
            return self::alphaNumericTokenForUser(Str::random(15));
        }

        // otherwise, it's valid and can be used
        return $token;
    }



    /**
     * Generates an alphanumeric token
     * @param $length_of_string
     * @return false|int|string
     */
    public static function alphaNumericToken($token)
    {
//        // String of all alphanumeric character
//        $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
//
//        // Shuffle the $str_result and returns substring of specified length
//        $token= substr(str_shuffle($str_result),
//            0, $length_of_string);
//

        if (self::tokenExists($token) == true) {
            return self::alphaNumericToken(Str::random(15));
        }

        // otherwise, it's valid and can be used
        return $token;
    }

    /**
     * Checks if token already exists
     * @param $token
     * @return bool
     */
    public static function tokenExists($token) {

        $exists = DJ::query()->where('reset_token','=', $token)->first();
        if ($exists)
        {
            return true;
        }
        return false;
    }
    /**
     * Checks if token already exists
     * @param $token
     * @return bool
     */
    public static function tokenExistsForUser($token) {

        $exists = User::query()->where('remember_token','=', $token)->first();
        if ($exists)
        {
            return true;
        }
        return false;
    }
}
