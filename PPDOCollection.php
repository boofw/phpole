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

	function count($query=array())
	{
		$compiledSql = self::compileQuery($query);
		if ($compiledSql['sql']) $compiledSql['sql'] = ' where '.$compiledSql['sql'];
		$sql = "SELECT count(*) FROM `{$this->tablename}` {$compiledSql['sql']}";
		$q = $this->db->prepare($sql);
		$q->execute($compiledSql['params']);
		return $q->fetchColumn();
	}

	function find($query=array(), $fields=array(), $order=array(), $limit=0, $skip=0)
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
		$compiledSql = self::compileQuery($query);

		$orderstr = '';
		if ($order && is_array($order)) {
			$orderstr = 'order by';
			foreach ($order as $k=>$v) {
				if ($v==-1) {
					$orderstr .= " `$k` desc,";
				} else {
					$orderstr .= " `$k` asc,";
				}
			}
			$orderstr = trim($orderstr, ',');
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

		if ($compiledSql['sql']) $compiledSql['sql'] = ' where '.$compiledSql['sql'];
		$sql = "SELECT $fieldstr FROM `{$this->tablename}` {$compiledSql['sql']} $orderstr $limitstr";
		$q = $this->db->prepare($sql);
		$q->execute($compiledSql['params']);
		$q->setFetchMode(PDO::FETCH_ASSOC);
		return $q;
	}

	function findOne($query=array(), $fields=array(), $order=array())
	{
		$q = $this->find($query, $fields, $order, 1);
		return $q->fetch();
	}

	function insert($data)
	{
		foreach ($data as $k => $v) {
			$cols[] = $k;
			$placers[] = '?';
			$vals[] = $v;
		}
		$q = $this->db->prepare("insert into `{$this->tablename}` (`".implode('`,`', $cols)."`) values (".implode(',', $placers).")");
		$q->execute($vals);
		$frows = $q->rowCount();
		if (!$frows) return null;
		return $this->db->lastInsertId();
	}

	function remove($query)
	{
		$compiledSql = self::compileQuery($query);
		if ($compiledSql['sql']) {
			$sql = "DELETE FROM `{$this->tablename}` where {$compiledSql['sql']}";
			$q = $this->db->prepare($sql);
			$q->execute($compiledSql['params']);
			return $q->rowCount();
		}
		return 0;
	}

	function update($query, $data)
	{
		if (is_array($data['$set'])) {
			$data = $data['$set'];
		}
		$compiledSql = self::compileQuery($query);
		if ($compiledSql['sql']) $compiledSql['sql'] = ' where '.$compiledSql['sql'];
		foreach ($data as $k => $v) {
			$setvs[] = "`$k`=:csetk$k";
			$compiledSql['params'][':csetk'.$k] = $v;
		}
		$q = $this->db->prepare("update `{$this->tablename}` set ".implode(',', $setvs)." {$compiledSql['sql']}");
		$q->execute($compiledSql['params']);
		return $q->rowCount();
	}

	function upsert($query, $data)
	{
		$row = $this->findOne($query);
		if ($row) {
			$this->update($query, $data);
		} else {
			if (is_array($data['$set'])) {
				$data = $data['$set'];
			}
			$this->insert($data);
		}
		return 0;
	}

	static function compileQuery($query=[], $dep=0)
	{
		$psk = ':k';
		$xwhere = [];
		$input_parameters = array();
		$i = 0;
		foreach ($query as $k=>$v) {
			if ($k=='$or' && is_array($v)) {
				$orx = [];
				foreach ($v as $ck=>$cv) {
					$r = self::compileQuery($cv, ++$dep);
					$orx[] = $r['sql'];
					$input_parameters += $r['params'];
				}
				$xwhere[] = '('.implode(' or ', $orx).')';
			} elseif (is_array($v)) {
				foreach ($v as $ck=>$cv) {
					if ($ck=='$lt') {
						$xwhere[] = "`$k`<{$psk}{$dep}d{$i}";
						$input_parameters[$psk.$dep.'d'.$i] = $cv;
					} elseif ($ck=='$lte') {
						$xwhere[] = "`$k`<={$psk}{$dep}d{$i}";
						$input_parameters[$psk.$dep.'d'.$i] = $cv;
					} elseif ($ck=='$gt') {
						$xwhere[] = "`$k`>{$psk}{$dep}d{$i}";
						$input_parameters[$psk.$dep.'d'.$i] = $cv;
					} elseif ($ck=='$gte') {
						$xwhere[] = "`$k`>={$psk}{$dep}d{$i}";
						$input_parameters[$psk.$dep.'d'.$i] = $cv;
					}
					$i++;
				}
			} else {
				$xwhere[] = "`$k`={$psk}{$dep}d{$i}";
				$input_parameters[$psk.$dep.'d'.$i] = $v;
				$i++;
			}
		}
		$wherestr = implode(' and ', $xwhere);
		if ($wherestr) $wherestr = '('.$wherestr.')';
		$dep++;
		return ['sql'=>$wherestr, 'params'=>$input_parameters];
	}
}