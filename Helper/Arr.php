<?php namespace Boofw\Phpole\Helper;

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

    /**
     * 将数组$array的key重置为$column的值
     * @param $array
     * @param $column
     */
    static function resetKey($array, $column)
    {
        $r = array();
        if (!is_array($array)) $array = array();
        foreach ($array as $v) {
            $r[$v[$column]] = $v;
        }
        return $r;
    }

    static function format($array, $keys)
    {
        return array_intersect_key($array, array_flip($keys));
    }
}