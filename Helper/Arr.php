<?php namespace Polev\Phpole\Helper;

use Illuminate\Support\Arr as IlluminateArr;

class Arr extends IlluminateArr
{
    static function sum($array, $key)
    {
        return array_sum(self::fetch($array, $key));
    }
}