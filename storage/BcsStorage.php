<?php
require __DIR__.'/../../../baidu/bcs/bcs.class.php';

class BcsStorage extends PStorage
{
	public $ak;
	public $sk;
	public $bucket;
	public $host = NULL;
	
	protected function saveFile($filepath)
	{
		$baiduBCS = new BaiduBCS($this->ak, $this->sk, $this->host);
		$response = $baiduBCS->create_object($this->bucket, '/'.$this->getPath(), $filepath);
		return $response->isOK();
	}
	
	protected function getUrl()
	{
		return 'http://bcs.duapp.com/'.$this->bucket.'/'.$this->getPath();
	}
	
// 	function cache($name, $bcsLocalFilePath)
// 	{
// 		if (!$this->bucket) return NULL;
// 		$bcsObj = '/'.$name;
// 		PUtil::mkdir(dirname($bcsLocalFilePath));
// 		$baiduBCS = new BaiduBCS($this->ak, $this->sk, $this->host);
// 		$response = $baiduBCS->get_object($this->bucket, $bcsObj, array('fileWriteTo'=>$bcsLocalFilePath));
// 		return $response->isOK();
// 	}
}