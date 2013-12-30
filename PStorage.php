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

	private function __construct()
	{
	}

	static function init()
	{
		if (class_exists('PCfg')) {
			PCfg::apply(__CLASS__);
		}
		$driver = ucfirst(strtolower(self::$driver)).'Storage';
		if (!class_exists($driver)) {
			require dirname(__FILE__).'/storage/'.$driver.'.php';
		}
		if (!class_exists($driver)) {
			throw new Exception('Storage Driver `'.$driver.'` not found');
		}
		$o = new $driver();
		foreach (self::$cfg as $k=>$v) {
			$o->$k = $v;
		}
		return $o;
	}

	function upload($fileobj)
	{
		$this->name = uniqid();
		$this->ext = PFile::path2ext($fileobj['name']);
		if (!$this->ext) $this->ext = PFile::mime2ext($fileobj['type']);
		$this->dir = $this->mkDirHash();

		$this->saveFile($fileobj['tmp_name']);

		return array(
				'fullname'=>$this->getFullName(),
				'name'=>$this->name,
				'ext'=>$this->ext,
				'dir'=>$this->dir,
				'url'=>$this->getUrl(),
		);
	}

	function info($filename)
	{
		$d = pathinfo($filename);
		$this->name = $d['filename'];
		$this->ext = $d['extension'];
		$this->dir = $this->mkDirHash();

		return array(
				'fullname'=>$filename,
				'name'=>$this->name,
				'ext'=>$this->ext,
				'dir'=>$this->dir,
				'url'=>$this->getUrl(),
		);
	}
	
	function putFile($localfile, $filename)
	{
		$d = pathinfo($filename);
		$this->name = $d['filename'];
		$this->ext = $d['extension'];
		$this->dir = $this->mkDirHash();

		$this->saveFile($localfile);
	
		return array(
				'fullname'=>$this->getFullName(),
				'name'=>$this->name,
				'ext'=>$this->ext,
				'dir'=>$this->dir,
				'url'=>$this->getUrl(),
		);
	}

	protected function mkDirHash()
	{
		return substr($this->name, -3);
	}

	protected function getFullName()
	{
		$fullname = $this->name;
		if ($this->ext) $fullname .= '.'.$this->ext;
		return $fullname;
	}

	protected function getPath()
	{
		$dir = $this->dir;
		if ($dir) $dir .= '/';
		return $dir.$this->getFullName();
	}

	protected function getUrl()
	{
	}

	protected function saveFile($filepath)
	{
	}
}