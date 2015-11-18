<?php
class PArray
{
	static function get($array, $key, $default=NULL) {
		return (isset($array[$key])) ? $array[$key] : $default;
	}

	static function getTrue($array, $key, $default=NULL) {
		return (isset($array[$key]) && $array[$key]) ? $array[$key] : $default;
	}

	/**
	 * 数组按指定的column过滤
	 * @param $array
	 * @param $column 指定的key
	 * @param $val 判断值
	 * @param $unset 符合条件的删除
	 */
	static function filterByKey($array, $column, $val=0, $unset=true) {
		$res = array();
		if (!is_array($array)) $array = array();
		foreach ($array as $k => $v) {
			if ($v[$column] == $val) {
				if ($unset) unset($array[$k]);
				else $res[] = $v;
			}
		}
		if ($unset) $res = $array;
		return $res;
	}

	/**
	 * 取得列表中指定字段的值的列表
	 * @param array $data 数组源
	 * @param string $column 要取得的字段
	 * @return array 一维数组
	 */
	static function getColumns($data=array(), $column='id') {
		$r=array();
		if (!is_array($data)) $data = array();
		foreach ($data as $v) {
			if ($v[$column]) $r[] = $v[$column];
		}
		$r = array_unique($r);
		return $r;
	}

	/**
	 * 将数组$array的key重置为$column的值
	 * @param $array
	 * @param $column
	 */
	static function resetKey($array, $column) {
		$r = array();
		if (!is_array($array)) $array = array();
		foreach ($array as $v) {
			$r[$v[$column]] = $v;
		}
		return $r;
	}

	/**
	 * 数组按指定字段排序
	 * @param $array
	 * @param $column
	 * @param $isdesc 是否倒序
	 */
	static function resort($array, $column, $isdesc=true) {
		$orderby = array();
		if (!is_array($array)) $array = array();
		foreach ($array as $v) {
			$orderby[] = $v[$column];
		}
		$order = $isdesc ? SORT_DESC : SORT_ASC;
		array_multisort($orderby, $order, SORT_REGULAR, $array);
		return $array;
	}

	/**
	 * 数组$array的$column按照$orderby排序
	 * @param array $array
	 * @param string $column
	 * @param array $orderby
	 */
	static function resortByArray($array, $column, $orderby) {
		$array = self::resetKey($array, $column);
		$data = array();
		if (!is_array($orderby)) $orderby = array();
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

	/**
	 * 数组按指定字段去重
	 * @param $array
	 * @param $column
	 */
	static function unique($array, $column) {
		$ids = array();
		if (!is_array($array)) $array = array();
		foreach ($array as $k => $v) {
			if (in_array($v[$column], $ids)) unset($array[$k]);
			$ids[] = $v[$column];
		}
		return $array;
	}

	/**
	 * 将数组$array按$column的值分组
	 * @param $array
	 * @param $column
	 * @param $rmcol
	 */
	static function colKey($array, $column, $rmcol=array()) {
		$r = array();
		if (!is_array($array)) $array = array();
		foreach ($array as $v) {
			if ($v) {
				if (!key_exists($v[$column], $r)) $r[$v[$column]] = array();
				$d = $v;
				foreach ($rmcol as $rm) {
					unset($d[$rm]);
				}
				$r[$v[$column]][] = $d;
			}
		}
		return $r;
	}

	/**
	 * 数组指定字段求和
	 * @param array $array
	 * @param mixed $columns
	 * @return mixed
	 */
	static function sumColumn($array, $columns) {
		if (is_array($columns)) {
			$r = array();
			foreach ($array as $v) {
				foreach ($columns as $col) {
					$r[$col] += $v[$col];
				}
			}
		} else {
			$r = 0;
			foreach ($array as $v) {
				$r += $v[$columns];
			}
		}
		return $r;
	}
}