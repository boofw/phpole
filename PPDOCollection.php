<?php
/**
 * PDO to Mongo Interface
 */
class PPDOCollection
{
	private $db;
	private $tablename;
	
	function __construct(PDO $db, $tablename)
	{
		$this->db = $db;
		$this->tablename = $tablename;
	}
	
	function count()
	{
	
	}
	
	function find($query=array(), $fields=array())
	{
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
		$input_parameters = array();
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
		$sql = "SELECT $fieldstr FROM `{$this->tablename}` limit 2";
		$q = $this->db->prepare($sql);
		$q->execute($input_parameters);
		$q->setFetchMode(PDO::FETCH_ASSOC);
		return $q;
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