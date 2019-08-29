<?php

namespace App\Service;

class Helper
{
    public static function slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }


    /**
     * @return string
     */
    public function getConfirmationCode()
    {
        $randomString = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $stringLength = strlen($randomString);
        $code = '';

        for ($i = 0; $i < $stringLength; $i++) {
            $code .= $randomString[rand(0, $stringLength - 1)];
        }

        return $code;
    }
}