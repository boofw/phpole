<?php
require __DIR__.'/../../../baidu/bcs/bcs.class.php';

class BcsStorage
{
	public $ak;
	public $sk;
	public $bucket;
	public $host = NULL;
	
	function save($filepath, $savepath)
	{
		$baiduBCS = new BaiduBCS($this->ak, $this->sk, $this->host);
		$response = $baiduBCS->create_object($this->bucket, '/'.$savepath, $filepath);
		return $response->isOK();
	}
	
	function getUrl($storageObj)
	{
		return 'http://bcs.duapp.com/'.$this->bucket.'/'.$storageObj->getPath();
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