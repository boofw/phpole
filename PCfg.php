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
	}
	
	/**
	 * use after self::init, before <class> init
	 * 
	 * @param $class
	 */
	static function apply($class)
	{
		if (is_array(self::$cfg['libcfg'][$class])) {
			foreach (self::$cfg['libcfg'][$class] as $k=>$v) {
				$class::$$k = $v;
			}
		}
		unset(self::$cfg['libcfg'][$class]);
	}
	
	/**
	 * use after self::init
	 */
	static function applyAll()
	{
		if (is_array(self::$cfg['libcfg'])) {
			$keys = array_keys(self::$cfg['libcfg']);
			foreach ($keys as $class) {
				self::apply($class);
			}
		}
		unset(self::$cfg['libcfg']);
	}
}