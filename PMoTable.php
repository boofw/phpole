<?php
class PMoTable
{
	private $tablename = 'xiciuser';
	
	function count()
	{
	
	}
	
	function find($query=array(), $fields=array())
	{
		$db = new PDO('mysql:host=localhost;dbname=test', 'root', 'root', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));
		$fieldstr = '';
		foreach ($fields as $k=>$v) {
			if ($v) {
				$fieldstr .= "`$k`,";
			}
		}
		$fieldstr = rtrim($fieldstr, ',');
		if (!$fieldstr) {
			$fieldstr = '*';
		}
		foreach ($query as $k=>$v) {
			if ($k=='$or' && is_array($v)) {
				foreach ($v as $ck=>$cv) {
					
				}
			} elseif (is_array($v)) {
				foreach ($v as $ck=>$cv) {
					if ($ck=='$lt') {
						
					} elseif ($ck=='$lte') {
						
					} elseif ($ck=='$gt') {
						
					} elseif ($ck=='$gte') {
						
					}
				}
			}
		}
		$sql = "SELECT $fieldstr FROM `{$this->tablename}`";
		return $db->prepare($sql)->execute($input_parameters)->fetchAll(PDO::FETCH_ASSOC);
	}
	
	function insert()
	{
		
	}
	
	function remove()
	{
		
	}
	
	function update()
	{
	
	}
}