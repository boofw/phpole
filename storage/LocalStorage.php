<?php
class LocalStorage
{
	public $basepath;
	public $baseurl;

	function save($filepath, $savepath)
	{
		$fullpath = $this->basepath.'/'.$savepath;
		PUtil::mkdir(dirname($fullpath));
		move_uploaded_file($filepath, $fullpath);
	}
	
	function getUrl($storageObj)
	{
		return $this->baseurl.'/'.$storageObj->getPath();
	}
}