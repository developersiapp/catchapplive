<?php


namespace catchapp\Helpers;


class MaskHelper
{
    public static function maskNumber($var, $percent = 70)
    {
        $len = strlen($var);
        $charToReplace = intval($len * ($percent / 100));
        if ($charToReplace <= 0) {
            return $var;
        }
        $xvar = substr_replace($var, str_repeat("X", $charToReplace - 1), 1, $charToReplace - 1);
        return $xvar;
    }

    public static function maskEmail($email)
    {

        $pos = stripos($email, '@');
        if ($pos === false) {
            return $email;
        }
        $em = explode("@", $email);
        $name = implode(array_slice($em, 0, count($em) - 1), '@');
        $len = floor(strlen($name) / 3);

        return substr($name, 0, $len) . str_repeat('*', $len * 2) . "@" . end($em);
    }

    public static function parsePhoneNumber($number)
    {
        $number = trim($number);
        $number = ltrim($number, '0');
        if (strlen($number) > 10) {
            if (stripos($number, "+91") === 0) {
                $number = str_ireplace("+91", "", $number);
            }
        }

        if (strlen($number) > 10) {
            if (stripos($number, "91") === 0) {
                $number = str_ireplace("91", "", $number);
            }
        }
        return $number;
    }

    public static function parseWebsiteUrl($website)
    {
        $website = trim($website);
        $website = str_ireplace("https://", "", $website);
        $website = str_ireplace("http://", "", $website);
        $website = str_ireplace("http//", "", $website);
        $website = str_ireplace("https//", "", $website);

        return "http://" . $website;
    }
}
