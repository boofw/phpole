<?php
/**
 * MVC 模式
 *
 * @author pole
 */
class PMVC
{
	/**
	 * @var string 配置应用controller与view的根目录
	 */
	static $approot = '';
	/**
	 * @var array 配置路由正则
	 */
	static $route = array();
	
	static $r = array('c'=>'', 'a'=>'');
	static $js = array();
	static $css = array();
	static $e = array();
	static $v = array();
	static $httpHost = '';

	static function init($cfg = NULL)
	{
		(self::$httpHost = $_SERVER['HTTP_X_REAL_HOST']) || (self::$httpHost = $_SERVER['HTTP_HOST']);

		PCfg::init($cfg);

		if (!self::$approot) {
			self::$approot = $_SERVER['DOCUMENT_ROOT'];
		}
		PAutoload::importDir(self::$approot.'/c');
		
		$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
		$root = dirname($_SERVER['DOCUMENT_URI']);
		if ($root != '/') {
			$uri = substr($uri, strlen($root));
		}
		if (!is_array(self::$route)) {
			self::$route = array();
		}
		self::$route['/^\/([\w]+)\/([\w]+)([\/\?]{1}.*)?$/'] = 'c=$1&a=$2&v=$3';
		foreach (self::$route as $rk=>$rv) {
			if (preg_match($rk, $uri, $m)) {
				foreach ($m as $mk=>$mv) {
					if ($mk>0) $rv = str_replace('$'.$mk, $mv, $rv);
				}
				parse_str($rv, $args);
				if (is_array($args)) {
					if ($args['v']) {
						$moreArgs = explode('/', trim($args['v'], '/'));
						for ($i=0; $i<count($moreArgs); $i=$i+2) {
							if (!is_numeric($moreArgs[$i])) {
								$args[$moreArgs[$i]] = $moreArgs[$i+1];
							}
						}
					}
					unset($args['v']);
					$_GET = array_merge($_GET, $args);
				}
				break;
			}
		}
		if ($_GET['c'] || $_GET['a']) {
			self::$r['c'] = $_GET['c'];
			self::$r['a'] = $_GET['a'];
		}
		if (!self::$r['c']) self::$r['c'] = 'index';
		if (!self::$r['a']) self::$r['a'] = 'index';
		$c = ucfirst(self::$r['c']) . 'Controller';
		$a = 'action' . ucfirst(self::$r['a']);
		$c = new $c();
		$c->$a();
	}
}