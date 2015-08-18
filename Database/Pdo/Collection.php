<?php namespace Polev\Phpole\Database\Pdo;

use PDO;

class Collection
{
    private $pdo;
    private $table;

    function __construct(PDO $pdo, $table)
    {
        $this->pdo = $pdo;
        $this->table = $table;
    }

    function count($query = [], $limit = 0, $skip = 0)
    {
        $compiledSql = self::compileQuery($query);
        if ($compiledSql['sql']) $compiledSql['sql'] = ' where '.$compiledSql['sql'];
        $sql = "SELECT count(*) FROM `{$this->table}` {$compiledSql['sql']}";
        $q = $this->pdo->prepare($sql);
        $q->execute($compiledSql['params']);
        return $q->fetchColumn();
    }

    function find($query = [], $fields = [])
    {
        $fieldstr = '';
        if (!is_array($fields)) {
            $fields = [];
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

        if ($compiledSql['sql']) $compiledSql['sql'] = ' where '.$compiledSql['sql'];
        $sql = "SELECT $fieldstr FROM `{$this->table}` {$compiledSql['sql']}";
        return new Cursor($this->pdo, $sql, $compiledSql['params']);
    }

    function findOne($query = [], $fields = [], $options = [])
    {
        $cursor = $this->find($query, $fields)->limit(1);
        list($r,) = iterator_to_array($cursor);
        return $r;
    }

    function insert($a, $options = [])
    {
        foreach ($a as $k => $v) {
            $cols[] = $k;
            $placers[] = '?';
            $vals[] = $v;
        }
        $q = $this->pdo->prepare("insert into `{$this->table}` (`".implode('`,`', $cols)."`) values (".implode(',', $placers).")");
        $q->execute($vals);
        $frows = $q->rowCount();
        if (!$frows) return null;
        return $this->pdo->lastInsertId();
    }

    function remove($criteria, $options = [])
    {
        $limitSql = '';
        if (array_key_exists('justOne', $options) && $options['justOne']) {
            $limitSql = 'limit 1';
        }

        $compiledSql = self::compileQuery($criteria);
        if ($compiledSql['sql']) {
            $sql = "DELETE FROM `{$this->table}` where {$compiledSql['sql']} $limitSql";
            $q = $this->pdo->prepare($sql);
            $q->execute($compiledSql['params']);
            return $q->rowCount();
        }
        return 0;
    }

    function update($criteria, $new_object, $options = [])
    {
        if (is_array($new_object['$set'])) {
            $new_object = $new_object['$set'];
        }

        if (array_key_exists('upsert', $options) && $options['upsert']) {
            if ( ! $this->findOne($criteria)) {
                return $this->insert($new_object);
            }
        }

        $limitSql = 'limit 1';
        if (array_key_exists('multiple', $options) && $options['multiple']) {
            $limitSql = '';
        }

        $compiledSql = self::compileQuery($criteria);
        if ($compiledSql['sql']) $compiledSql['sql'] = ' where '.$compiledSql['sql'];
        foreach ($new_object as $k => $v) {
            $setvs[] = "`$k`=:csetk$k";
            $compiledSql['params'][':csetk'.$k] = $v;
        }
        $q = $this->pdo->prepare("update `{$this->table}` set ".implode(',', $setvs)." {$compiledSql['sql']} $limitSql");
        $q->execute($compiledSql['params']);
        return $q->rowCount();
    }

    static function compileQuery($query = [], $dep = 0)
    {
        $psk = ':k';
        $xwhere = [];
        $input_parameters = [];
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
                    } elseif ($ck=='$in') {
                        $inckarr = [];
                        foreach ($cv as $inck=>$incv) {
                            $inckarr[] = $psk.$dep.'d'.$i.'in'.$inck;
                            $input_parameters[$psk.$dep.'d'.$i.'in'.$inck] = $incv;
                        }
                        $xwhere[] = "`$k` in (".implode(',', $inckarr).")";
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