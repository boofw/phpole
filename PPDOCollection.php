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
	
	function find($query=array(), $fields=array(), $limit=0, $skip=0)
	{
		$fieldstr = '';
		if (!is_array($fields)) {
			$fields = array();
		}
		foreach ($fields as $k=>$v) {
			if ($v) {
				$fieldstr .= "`$k`,";
			}
		}
		$fieldstr = rtrim($fieldstr, ',');
		if (!$fieldstr) {
			$fieldstr = '*';
		}
		$wherestr = '';
		$input_parameters = array();
		$psk = ':ppdosqlkey';
		$i = 0;
		foreach ($query as $k=>$v) {
			if ($k=='$or' && is_array($v)) {
				$wcorstr = '';
				foreach ($v as $ck=>$cv) {
					foreach ($cv as $cck=>$ccv) {
						if ($wcorstr) $wcorstr .= ' or ';
						$wcorstr .= "`$cck`={$psk}{$i}";
						$input_parameters[$psk.$i] = $ccv;
						$i++;
					}
// 					if ($wcorstr) $wcorstr .= ' or ';
// 					$wcorstr .= "`$ck`={$psk}{$i}";
// 					$input_parameters[$psk.$i] = $v;
				}
				if ($wherestr) $wherestr .= ' and ';
				$wherestr .= '('.$wcorstr.')';
			} elseif (is_array($v)) {
				foreach ($v as $ck=>$cv) {
					if ($ck=='$lt') {
						
					} elseif ($ck=='$lte') {
						
					} elseif ($ck=='$gt') {
						
					} elseif ($ck=='$gte') {
						
					}
				}
			} else {
				if ($wherestr) $wherestr .= ' and ';
				$wherestr .= "`$k`={$psk}{$i}";
				$input_parameters[$psk.$i] = $v;
			}
			$i++;
		}
		$limit = intval($limit);
		$skip = intval($skip);
		$limitstr = '';
		if ($limit) {
			$limitstr = " limit $limit";
			if ($skip) {
				$limitstr = " limit $skip , $limit";
			}
		}
		if ($wherestr) $wherestr = ' where '.$wherestr;
		$sql = "SELECT $fieldstr FROM `{$this->tablename}` $wherestr $limitstr";
		var_dump($sql);
		var_dump($input_parameters);
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