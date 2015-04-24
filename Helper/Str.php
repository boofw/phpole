<?php namespace Polev\Phpole\Helper;

use Illuminate\Support\Str as IlluminateStr;

class Str extends IlluminateStr
{
    static function e($value)
    {
        return htmlentities($value, ENT_QUOTES, 'UTF-8', false);
    }
}