<?php namespace Polev\Phpole\Helper;

use Illuminate\Support\Arr as IlluminateArr;

class Arr extends IlluminateArr
{
    static function sum($array, $key)
    {
        return array_sum(self::fetch($array, $key));
    }

    static function merge($array1, $array2)
    {
        $array2 = Arr::dot($array2);
        foreach ($array2 as $k => $v) {
            Arr::set($array1, $k, $v);
        }
        return $array1;
    }
}