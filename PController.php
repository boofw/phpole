<?php
class PController
{
	function __construct()
	{
		$this->before();
	}

	function __destruct()
	{
		$this->after();
	}

	function __call($func, $args)
	{
		if (substr($func, 0, 6)=='action') {
			throw new Exception('Action '.substr($func, 6).' not found!', 404);
		} else {
			throw new Exception('Method '.__CLASS__.'::'.$func.' not found!');
		}
	}

	function before()
	{}

	function after()
	{}

	function actionError()
	{
		return $this->cmsg(1, '404');
	}

	function addError($a, $column, $form)
	{
		if ($this->ajax) die(json_encode($a));
		else PMVC::$e[$form][$column] = $a;
	}
}