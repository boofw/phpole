<?php namespace Polev\Phpole\Database;

use PDO;
use Iterator;

class Cursor implements Iterator
{
    private $pdo;
    private $sql;
    private $params;
    private $limitsql = '';
    private $ordersql = '';

    /**
     * @var \PDOStatement
     */
    private $PDOStatement;
    private $position = 0;
    private $current;

    private $limit;
    private $skip;

    function __construct(PDO $pdo, $sql, $params)
    {
        $this->pdo = $pdo;
        $this->sql = $sql;
        $this->params = $params;
    }

    function sort($fields)
    {
        $this->ordersql = 'order by';
        foreach ($fields as $k=>$v) {
            if ($v==-1) {
                $this->ordersql .= " `$k` desc,";
            } else {
                $this->ordersql .= " `$k` asc,";
            }
        }
        $this->ordersql = trim($this->ordersql, ',');
        return $this;
    }

    function limit($num)
    {
        $this->limit = $num;
        $this->limitsql = "limit {$this->limit}";
        if ($this->skip) {
            $this->limitsql = "limit {$this->skip}, {$this->limit}";
        }
        return $this;
    }

    function skip($num)
    {
        $this->skip = $num;
        if ( ! $this->limit) {
            $this->limit = 1000;
        }
        $this->limitsql = "limit {$this->skip}, {$this->limit}";
        return $this;
    }

    function current ()
    {
        return $this->current;
    }

    function next ()
    {
        $this->position++;
    }

    function key ()
    {
        return $this->position;
    }

    function valid ()
    {
        $this->current = $this->PDOStatement->fetch();
        return (bool) $this->current;
    }

    function rewind ()
    {
        $this->PDOStatement = $this->pdo->prepare($this->sql.$this->ordersql.$this->limitsql);
        $this->PDOStatement->execute($this->params);
        $this->PDOStatement->setFetchMode(PDO::FETCH_ASSOC);
        $this->position = 0;
    }
}