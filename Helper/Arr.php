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
        $array2 = self::dot($array2);
        foreach ($array2 as $k => $v) {
            self::set($array1, $k, $v);
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
        if ( ! is_array($array)) $array = array();
        foreach ($array as $v) {
            $r[$v[$column]] = $v;
        }
        return $r;
    }

    static function format($array, $keys)
    {
        return array_intersect_key($array, array_flip($keys));
    }

    /**
     * 数组$array的$column按照$orderby排序
     * @param array $array
     * @param string $column
     * @param array $orderby
     */
    static function sortByArray($array, $column, $orderby) {
        $array = self::resetKey($array, $column);
        $data = array();
        if ( ! is_array($orderby)) $orderby = array();
        foreach ($orderby as $id) {
            if ($array[$id]) {
                $data[] = $array[$id];
                unset($array[$id]);
            }
        }
        $array = array_values($array);
        $array = array_merge($data, $array);
        return $array;
    }
}