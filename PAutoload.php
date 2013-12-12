<?php
class PAutoload {
	
	static $libmaps = array();
	static $libdirs = array();
	
	static function importMap($libmaps) {
		self::$libmaps = array_merge($libmaps, self::$libmaps);
		self::registerAutoloader();
	}
	
	static function importDir($dir) {
		if (!is_array($dir)) {
			$dir = array($dir);
		}
		self::$libdirs = array_merge($dir, self::$libdirs);
		self::registerAutoloader();
	}
	
	static function autoload($class) {
		if (isset(self::$libmaps[$class]) && file_exists(self::$libmaps[$class])) {
			return require self::$libmaps[$class];
		}
		self::importDir(dirname(__FILE__));
		self::$libdirs = array_unique(self::$libdirs);
		foreach (self::$libdirs as $v) {
			$v .= '/'.$class.'.php';
			if (file_exists($v)) {
				return require $v;
			}
		}
	}
	
	static function registerAutoloader($callback=null) {
		spl_autoload_unregister(array('PAutoload','autoload'));
		if (!is_null($callback)) spl_autoload_register($callback);
		spl_autoload_register(array('PAutoload','autoload'));
	}
}

spl_autoload_register(array('PAutoload','autoload'));