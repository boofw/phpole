<?php
class LocalStorage
{
	public $basepath;
	public $baseurl;

	function save($filepath, $savepath)
	{
		$fullpath = $this->basepath.'/'.$savepath;
		PUtil::mkdir(dirname($fullpath));
		$r = move_uploaded_file($filepath, $fullpath);
		if (!$r) {
			copy($filepath, $fullpath);
		}
	}
	
	function getUrl($storageObj)
	{
		return $this->baseurl.'/'.$storageObj->getPath();
	}
}