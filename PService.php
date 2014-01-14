<?php
class PService
{
	/**
	 * @var string 配置应用service的根目录
	 */
	static $apiroot = null;

	protected $appid = null;

	private static $_svcpool = array();

	public static function init($name, $appid)
	{
		if (isset(self::$_svcpool[$name]) && (self::$_svcpool[$name] instanceof self)) {
			$svc = self::$_svcpool[$name];
		} else {
			if (class_exists('PCfg')) {
				PCfg::apply(__CLASS__);
			}
			if (!self::$apiroot) {
				throw new Exception('self::$apiroot is empty');
			}
			$svc_class_dir = self::$apiroot;
			if (strpos($name, '.')) {
				$d = explode('.', $name);
				$svc_class_name = ucfirst($d[1]).'Service';
				if ($d[0]) {
					$svc_class_dir .= '/'.$d[0];
				}
			} else {
				$svc_class_name = ucfirst($name).'Service';
			}
			$svc_class_file = $svc_class_dir.'/'.$svc_class_name.'.php';
			if (!class_exists($svc_class_name) && file_exists($svc_class_file)) {
				require $svc_class_file;
			}
			$svc = new $svc_class_name();
		}
		$svc->appid = $appid;
		return $svc;
	}

	private function __construct()
	{
		$this->before();
	}
	
	function before()
	{
	}

}