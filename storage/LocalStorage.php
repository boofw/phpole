<?php
class LocalStorage extends PStorage
{
	public $basepath;
	public $baseurl;

	protected function saveFile($filepath)
	{
		if ($this->dir) {
			$this->basepath .= '/'.$this->dir;
		}
		PUtil::mkdir($this->basepath);
		move_uploaded_file($filepath, $this->basepath.'/'.$this->getFullName());
	}
	
	protected function getUrl()
	{
		return $this->baseurl.'/'.$this->getPath();
	}
}