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
	static $httpRemoteIp = '';

	static function init($cfg = NULL)
	{
		(self::$httpHost = $_SERVER['HTTP_X_REAL_HOST']) || (self::$httpHost = $_SERVER['HTTP_HOST']);
		(self::$httpRemoteIp = $_SERVER['HTTP_X_REAL_IP']) || (self::$httpRemoteIp = $_SERVER['REMOTE_ADDR']);

		if (class_exists('PCfg')) {
			PCfg::init($cfg);
			PCfg::apply(__CLASS__);
		}

		if (!self::$approot) {
			self::$approot = $_SERVER['DOCUMENT_ROOT'];
		}
		
		$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
		$root = dirname($_SERVER['DOCUMENT_URI']);
		if ($root != '/') {
			$uri = substr($uri, strlen($root));
		}
		if (!is_array(self::$route)) {
			self::$route = array();
		}
		self::$route['/^\/([\w]+)\/([\w]+)([\/\?]{1}.*)?$/'] = 'c=$1&a=$2&v=$3';
		self::$route['/^\/([\w]+)([\/\?]{1}.*)?$/'] = 'c=$1&a=index&v=$2';
		foreach (self::$route as $rk=>$rv) {
			if (preg_match($rk, $uri, $m)) {
				foreach ($m as $mk=>$mv) {
					if ($mk>0) $rv = str_replace('$'.$mk, $mv, $rv);
				}
				parse_str($rv, $args);
				if (is_array($args)) {
					self::$r['c'] = $args['c'];
					self::$r['a'] = $args['a'];
					unset($args['c']);
					unset($args['a']);
					if ($args['v']) {
						$moreArgs = explode('/', trim($args['v'], '/'));
						for ($i=0; $i<count($moreArgs); $i=$i+2) {
							if (!is_numeric($moreArgs[$i]) && !(preg_match('/^\$[1-9]$/', $moreArgs[$i]) && !$moreArgs[$i+1])) {
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
		if (!self::$r['c']) self::$r['c'] = 'index';
		if (!self::$r['a']) self::$r['a'] = 'index';
		$c = ucfirst(self::$r['c']) . 'Controller';
		$a = 'action' . ucfirst(self::$r['a']);
		if (!class_exists($c) && file_exists(self::$approot.'/controller/'.$c.'.php')) {
			require self::$approot.'/controller/'.$c.'.php';
		}
		if (class_exists($c)) {
			$c = new $c();
		} else {
			self::$r['c_real'] = 'p';
			$c = new PController();
		}
		try {
			$c->$a();
		} catch (Exception $e) {
			if ($e->getCode()==404) {
				self::$r['a_real'] = 'error';
				$c->actionError();
			} else {
				throw $e;
			}
		}
		
	}
}