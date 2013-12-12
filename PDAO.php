<?php
/**
 * PDAO DAO基类，提供常用数据库操作函数
 *
 * @property string $pk 数据表主键
 * @property PDO $db 数据库 PDO
 *
 * @example PDAO::init(dbName.tableName);
 */
class PDAO
{
	/**
	 * @var array 数据库连接相关配置
	 */
	static $cfg = array();
	/**
	 * @var string 自定义DAO目录
	 */
	static $daoroot = '';
	/**
	 * @var string 数据表主键(可继承并覆盖)
	 */
	protected $pk = 'id';
	/**
	 * @var PDO 数据库连接(从子类覆盖无效)
	 */
	protected $db;
	/**
	 * @var string 表前缀(从子类覆盖无效)
	 */
	protected $pre = '';
	/**
	 * @var string 数据表名(不带前缀)(从子类覆盖无效)
	 */
	protected $name = '';
	/**
	 * @var string 完整数据表名(带前缀)(从子类覆盖无效)
	 */
	protected $fullname = '';

	private static $_obj;
	private static $_pdopool = array();

	public static function init($name)
	{
		if (!$name) {
			throw new Exception('TableName is empty');
		}
		$d = explode('.', $name);
		if (isset($d[1])) {
			$db = $d[0];
			$tblname = $d[1];
		} else {
			$db = 'default';
			$tblname = $d[0];
		}

		if (!(self::$_obj instanceof self)) {
			self::$_obj = new self();
		}

		if (!isset(self::$cfg[$db]) || !isset(self::$cfg[$db]['dsn'])) {
			throw new Exception('No such DB in config');
		}

		if (!isset(self::$_pdopool[$db]) || !(self::$_pdopool[$db] instanceof PDO)) {
			self::$_pdopool[$db] = new PDO(self::$cfg[$db]['dsn'], self::$cfg[$db]['username'], self::$cfg[$db]['password'], self::$cfg[$db]['opts']);
		}

		self::$_obj->db = self::$_pdopool[$db];
		self::$_obj->pre = self::$cfg[$db]['prefix'];
		self::$_obj->name = $tblname;
		self::$_obj->fullname = self::$_obj->pre . self::$_obj->name;

		return self::$_obj;
	}

	private function __construct()
	{
	}

	/**
	 * 获取一条数据
	 * @param mixed $where 条件数组或主键值
	 * @param array $order 排序
	 * @return array 一行数据数组或false
	 *
	 * @example $obj->get($id); 获取主键为$id的数据行
	 * @example $obj->get(array("id=? and name=?", array($id,$name))); 获取 id为$id，name为$name 的第一行数据
	 * @example $obj->get(array("id=:id and name=:name", array(':id'=>$id,':name'=>$name))); 获取 id为$id，name为$name 的第一行数据
	 * @example $obj->get(array(), $order); 获取数据表中第一行数据，$order示例参见 $this->getAll()
	 */
	public function get($where, $order = array()) {
		$res = array();
		if (is_array($where)) {
			$r = $this->getAll($where, $order, 1);
			if (isset($r[0])) $res = $r[0];
		} else {
			$q = $this->db->prepare("select * from `{$this->fullname}` where `{$this->pk}`=?");
			$q->execute(array($where));
			$res = $q->fetch(PDO::FETCH_ASSOC);
		}
		return $res;
	}

	/**
	 * 获取一个字段值
	 * @param string $column 要获取的字段
	 * @param mixed $where 条件数组或主键值
	 * @param array $order 排序
	 * @return string
	 */
	public function getColumn($column, $where, $order = array()) {
		$r = $this->get($where, $order);
		return $r[$column];
	}

	/**
	 * 更新或新增数据
	 * @param array $data 数据数组
	 * @param mixed $where 条件数组或主键值或NULL(为NULL是为新增数据)
	 * @return array (0=>#last_id#, 1=>#影响的行数#)
	 */
	public function set($data, $where = NULL) {
		$autoid = $frows = 0;
		if (is_array($data) && $data) {
			foreach ($data as $k=>$v) {
				if (is_null($v)) $data[$k] = '';
			}
			$where = $this->fixWhere($where);
			if (is_null($where)) {
				foreach ($data as $k => $v) {
					$cols[] = $k;
					$placers[] = '?';
					$vals[] = $v;
				}
				$q = $this->db->prepare("insert into `{$this->fullname}` (`".implode('`,`', $cols)."`) values (".implode(',', $placers).")");
				$q->execute($vals);
				$frows = $q->rowCount();
				$autoid = $this->db->lastInsertId();
			} elseif (is_array($where[1]) && $where[0]) {
				foreach ($data as $k => $v) {
					$setvs[] = "`$k`=:$k";
					$where[1][':'.$k] = $v;
				}
				$q = $this->db->prepare("update `{$this->fullname}` set ".implode(',', $setvs)." where {$where[0]}");
				$q->execute($where[1]);
				$frows = $q->rowCount();
			}
		}
		return array($autoid, $frows);
	}

	/**
	 * 新增数据
	 * @param array $data 数据数组
	 * @return array (0=>#last_id#, 1=>#影响的行数#)
	 */
	public function add($data) {
		return $this->set($data);
	}

	/**
	 * 新增或更新数据
	 * @param array $data 数据数组
	 * @param array $keys 检索键
	 * @return array (0=>#last_id#, 1=>#影响的行数#)
	 *
	 * @example $obj->replace(array('id'=>$id, 'type'=>$type, 'name'=>$name), 'id,type')
	 */
	public function replace($data, $keys = array()) {
		if (!$keys) $keys = array($this->pk);
		if (!is_array($keys)) {
			$keys = explode(',', $keys);
		}
		$i=0;
		foreach ($keys as $v) {
			$where[0][] = "`$v`=:wk_$i";
			$where[1][':wk_'.$i] = $data[$v];
			$i++;
		}
		$r = $this->get($where, array());
		if ($r) $res = $this->set($data, $where);
		else $res = $this->set($data);
		return $res;
	}

	/**
	 * 处理计数字段
	 * @param $columns
	 * @param $where
	 * @return string
	 */
	public function plus($columns, $where) {
		$where = $this->fixWhere($where);
		foreach ($columns as $k=>$v) {
			$set .= ",`$k`=`$k`+$v";
		}
		$set = trim($set, ',');
		return $this->db->prepare("update `{$this->fullname}` set $set where {$where[0]}")->execute($where[1]);
	}

	/**
	 * 删除一条数据
	 * @param mixed $where 条件数组或主键值
	 * @return int 影响的行数
	 */
	public function del($where) {
		$res = 0;
		$where = $this->fixWhere($where);
		if (is_array($where[1])) {
			$q = $this->db->prepare("delete from `{$this->fullname}` where {$where[0]}");
			$q->execute($where[1]);
			$res = $q->rowCount();
		}

		return $res;
	}

	/**
	 * 获取多条数据
	 * @param array $where
	 * @param array $order
	 * @param int $num
	 * @param int $offset
	 * @return array 结果数据数组
	 *
	 * @example $obj->getAll(array("id=? and name=?", array($id,$name))); 获取 id为$id，name为$name 的全部数据行
	 * @example $obj->getAll(array("id>:id or name=:name", array(':id'=>$id,':name'=>$name))); 获取 id>$id或name为$name 的全部数据行
	 * @example $obj->getAll(array('id'=>$id,'name'=>$name)); 获取 id为$id，name为$name 的全部数据行
	 * @example $obj->getAll(array("id>10","name='pole'")); 获取 id>10，name为'pole'的全部数据行[!已知条件数据，不可使用变量]
	 * @example $obj->getAll(array()); 获取数据表中全部数据，主键倒序
	 * @example $obj->getAll(array(), array()); 获取数据表中全部数据，默认排序
	 * @example $obj->getAll(array(), array('id desc', 'name')); 获取数据表中全部数据，id倒序，name升序
	 */
	public function getAll($where = array(), $order = array(), $num = 0, $offset = 0) {
		if (is_null($order)) $order = array("{$this->pk} desc");
		if (!is_array($order)) $order = array();
		$where = $this->fixWhere($where);
		$sql = "select * from `{$this->fullname}`";
		if ($where[0]) $sql .= " where {$where[0]}";
		if ($order) $sql .= " order by ".implode(',', $order);
		if ($num) $sql .= " limit $offset,$num";
		$q = $this->db->prepare($sql);
		$q->execute($where[1]);
		return $q->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * where $column in ($values) 查询
	 * @param array $values 查询匹配值
	 * @param string $column 查询相关字段
	 * @return array 结果集
	 */
	public function getIn($values, $column = NULL) {
		if (is_null($column)) $column = $this->pk;
		if (is_array($values) && $num = count($values)) {
			$placers = array_fill(0, $num, '?');
			$where = array("`$column` in (".implode(',', $placers).")", $values);
			return $this->getAll($where);
		}
	}

	/**
	 * 查询结果行数
	 * @param array $where
	 * @param array $order
	 * @param int $num
	 * @param int $offset
	 * @return int 结果总行数
	 */
	public function getCount($where = array(), $order = array(), $num = 0, $offset = 0) {
		if (is_null($order)) $order = array("{$this->pk} desc");
		if (!is_array($order)) $order = array();
		$where = $this->fixWhere($where);
		$sql = "select count(*) from `{$this->fullname}`";
		if ($where[0]) $sql .= " where {$where[0]}";
		if ($order) $sql .= " order by ".implode(',', $order);
		if ($num) $sql .= " limit $offset,$num";
		$q = $this->db->prepare($sql);
		$q->execute($where[1]);
		return $q->fetchColumn();
	}

	/**
	 * 获取分页数据
	 * @param array $where
	 * @param array $order
	 * @param int $page
	 * @param int $pagesize
	 * @return array {pager:{total:总数, page:当前页号, pagesize:每页条数, pagetotal:总页数}, data:{#内容#}}
	 */
	public function getPage($where = array(), $order = array(), $page = 1, $pagesize = 20) {
		if ($page<1) $page = 1;
		$offset = ($page-1) * $pagesize;
		$total = $this->getCount($where);
		$data = $this->getAll($where, $order, $pagesize, $offset);
		return array('data'=>$data, 'pager'=>array('total'=>$total, 'page'=>$page, 'pagesize'=>$pagesize, 'pagemax'=>ceil($total/$pagesize)));
	}

	/**
	 * 执行一次查询并返回一个已执行的 PDOStatement
	 * @param array $where
	 * @param array $order
	 * @param int $num
	 * @param int $offset
	 * @param int $fetchMode
	 * @return PDOStatement
	 */
	public function query($where = array(), $order = array(), $num = 0, $offset = 0, $fetchMode = PDO::FETCH_ASSOC) {
		if (is_null($order)) $order = array("{$this->pk} desc");
		if (!is_array($order)) $order = array();
		$where = $this->fixWhere($where);
		$sql = "select * from `{$this->fullname}`";
		if ($where[0]) $sql .= " where {$where[0]}";
		if ($order) $sql .= " order by ".implode(',', $order);
		if ($num) $sql .= " limit $offset,$num";
		$q = $this->db->prepare($sql);
		$q->execute($where[1]);
		$q->setFetchMode($fetchMode);
		return $q;
	}

	/**
	 * 执行一条SQL语句并返回一个已执行的 PDOStatement
	 * @param string $sql
	 * @param int $fetchMode
	 * @return PDOStatement
	 */
	public function querySQL($sql, $fetchMode = PDO::FETCH_ASSOC) {
		$q = $this->db->query($sql);
		$q->setFetchMode($fetchMode);
		return $q;
	}

	/**
	 * 从 PDOStatement 中读取一条数据
	 * @param PDOStatement $query
	 * @return array
	 */
	public function fetch($query) {
		return $query->fetch();
	}

	function seq() {
		$this->db->exec("replace into `{$this->fullname}` (`v`) values ('1')");
		return $this->db->lastInsertId();
	}

	private function fixWhere($where) {
		if (!$where) return $where;
		if (!is_array($where)) $where = array("`{$this->pk}`=:{$this->pk}", array(':'.$this->pk=>$where));
		$ks = array_keys($where);
		if (is_string($ks[0])) {
			foreach ($where as $k=>$v) {
				$where0[] = "`$k`=:$k";
				$where1[':'.$k] = $v;
			}
			$where = array($where0, $where1);
		}
		if (!is_array($where[1])) $where = array($where, array());
		if (is_array($where[0])) $where[0] = implode(' and ', $where[0]);
		return $where;
	}

}
/*

*/