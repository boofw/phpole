<?php
/**
 * 全局配置
 *
 * @author pole
 */
class PCfg
{
	static $cfg = array();

	static function init($cfg = NULL)
	{
		if (is_array($cfg)) {
			self::$cfg = $cfg;
		} elseif ($cfg && is_string($cfg) && file_exists($cfg)) {
			self::$cfg = require $cfg;
		}

		// Init config of libs
		if (is_array(self::$cfg['libcfg'])) {
			foreach (self::$cfg['libcfg'] as $class=>$ccfg) {
				foreach ($ccfg as $k=>$v) {
					$class::$$k = $v;
				}
			}
		}
		unset(self::$cfg['libcfg']);
	}
}