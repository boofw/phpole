<?php
class PStorage
{
	/**
	 * @var string 配置存储方式
	 */
	static $driver = 'local';
	/**
	 * @var array 对应存储驱动的详细配置，具体参考相应驱动
	 * 驱动为 'local' 需配 array('basepath'=>'', 'baseurl'=>'')
	 * 驱动为 'qiniu' 需配 array('ak'=>'','sk'=>'','bucket'=>'')
	 * 驱动为 'bcs' 需配 array('ak'=>'','sk'=>'','bucket'=>'', 'host'=>'')
	 */
	static $cfg = array();

	public $name;
	public $ext;
	public $dir;
	
	private static $driverObj;

	protected function __construct()
	{
		if (class_exists('PCfg')) {
			PCfg::apply(__CLASS__);
		}
	}

	static function init($filename)
	{
		$o = new self();
		$d = pathinfo($filename);
		$o->name = $d['filename'];
		$o->ext = $d['extension'];
		$o->dir = $o->mkDirHash();
		return $o;
	}

	static function upload($fileobj)
	{
		$o = new self();
		$o->name = uniqid();
		$o->ext = PFile::path2ext($fileobj['name']);
		if (!$o->ext) $o->ext = PFile::mime2ext($fileobj['type']);
		$o->dir = $o->mkDirHash();

		$o->saveFile($fileobj['tmp_name']);
		return $o;
	}

	static function putFile($localfile, $filename)
	{
		$o = self::init($filename);
		$o->saveFile($localfile);
		return $o;
	}

	protected function mkDirHash()
	{
		return substr($this->name, -3);
	}

	function getFullName()
	{
		$fullname = $this->name;
		if ($this->ext) $fullname .= '.'.$this->ext;
		return $fullname;
	}

	function getPath()
	{
		$dir = $this->dir;
		if ($dir) $dir .= '/';
		return $dir.$this->getFullName();
	}

	function getUrl()
	{
		$this->initDriver();
		return self::$driverObj->getUrl($this);
	}

	protected function saveFile($filepath)
	{
		$this->initDriver();
		self::$driverObj->save($filepath, $this->getPath());
	}
	
	protected function initDriver()
	{
		$driver = ucfirst(strtolower(self::$driver)).'Storage';
		if (!class_exists($driver)) {
			require dirname(__FILE__).'/storage/'.$driver.'.php';
		}
		if (!class_exists($driver)) {
			throw new Exception('Storage Driver `'.$driver.'` not found');
		}
		self::$driverObj = new $driver();
		foreach (self::$cfg as $k=>$v) {
			self::$driverObj->$k = $v;
		}
	}
}