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
		self::$cfg['lib_cfg_data'] = self::$cfg['lib'];
	}
	
	/**
	 * use after self::init, before <class> init
	 * 
	 * @param $class
	 */
	static function apply($class)
	{
		if (is_array(self::$cfg['lib_cfg_data'][$class])) {
			foreach (self::$cfg['lib_cfg_data'][$class] as $k=>$v) {
				$class::$$k = $v;
			}
		}
		unset(self::$cfg['lib_cfg_data'][$class]);
	}
	
	/**
	 * use after self::init
	 */
	static function applyAll()
	{
		if (is_array(self::$cfg['lib_cfg_data'])) {
			$keys = array_keys(self::$cfg['lib_cfg_data']);
			foreach ($keys as $class) {
				self::apply($class);
			}
		}
		unset(self::$cfg['lib_cfg_data']);
	}
}