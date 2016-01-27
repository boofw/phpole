<?php namespace Boofw\Phpole\Database;

use Boofw\Phpole\Helper\Arr;

class Database
{
    /**
     * @var array $config
     * array(
            'dawn' => array(
                'driver' => 'mongo',
                'server' => 'mongodb://127.0.0.1',
                'options' => array(
                    'username' => false,
                ),
                'db' => 'config',
                'prefix' => '',
            ),
            'test' => array(
                'driver' => 'pdo',
                'dsn' => 'mysql:host=localhost;dbname=test',
                'username' => 'root',
                'passwd' => 'root',
                'options' => array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''),
                'prefix' => 'bs_',
            ),
       );
     */
    static $config = [];

    private static $pool = [];

    /**
     * @var \Boofw\Phpole\Database\Pdo\Collection
     */
    private $collection;

    private function __construct($name)
    {
        list($db, $table) = explode('.', $name);
        if (array_key_exists($db, self::$config)) {
            $config = self::$config[$db];
            if (array_key_exists('prefix', $config) && $config['prefix']) $table = $config['prefix'].$table;
            if ($config['driver'] === 'mongo') {
                $mongoClient = new \MongoClient($config['server'], $config['options']);
                $this->collection = $mongoClient->selectCollection($config['db'], $table);
            } elseif ($config['driver'] === 'pdo') {
                $pdo = new \PDO($config['dsn'], $config['username'], $config['passwd'], $config['options']);
                $this->collection = new \Boofw\Phpole\Database\Pdo\Collection($pdo, $table);
            } else {
                throw new AppException('Database driver <'.$config['driver'].'> not found!');
            }
        } else {
            throw new AppException('Database handle <'.$name.'> not found!');
        }
    }

    /**
     * Database init
     * @param $name
     * @return \Boofw\Phpole\Database\Database
     */
    static function init($name)
    {
        if (strpos($name, '.') === false) $name = 'default.'.$name;
        if (array_key_exists($name, self::$pool)) {
            return self::$pool[$name];
        }
        return self::$pool[$name] = new static($name);
    }

    function all($query = [], $fields = [], $sort = null, $limit = null, $skip = null)
    {
        $cursor = $this->collection->find($query, $fields);
        if ($sort) $cursor->sort($sort);
        if ($limit) $cursor->limit($limit);
        if ($skip) $cursor->skip($skip);
        return array_values(iterator_to_array($cursor));
    }

    function first($query = [], $fields = [], $sort = null, $skip = null)
    {
        list($r,) = $this->all($query, $fields, $sort, 1, $skip);
        return $r;
    }

    function in($key, $values)
    {
        $values = array_unique($values);
        $r = $this->all([$key => ['$in' => $values]]);
        return Arr::sortByArray($r, $key, $values);
    }

    function count($query = [])
    {
        return $this->collection->count($query);
    }

    function insert($a)
    {
        return $this->collection->insert($a);
    }

    function update($criteria, $new_object, $options = [])
    {
        if ( ! array_key_exists('multiple', $options)) $options['multiple'] = 1;
        return $this->collection->update($criteria, $new_object, $options);
    }

    function upsert($criteria, $new_object, $options = [])
    {
        return $this->update($criteria, $new_object, ['upsert' => 1]);
    }

    function remove($criteria, $options = [])
    {
        return $this->collection->remove($criteria, $options);
    }

    function page($query = [], $fields = [], $sort = null, $page = 1, $pagesize = 50)
    {
        if ($page < 1) $page = 1;
        if ($pagesize < 1) $pagesize = 50;
        $skip = ($page - 1) * $pagesize;
        $total = $this->count($query);
        $list = $this->all($query, $fields, $sort, $pagesize, $skip);
        $pagemax = ceil($total / $pagesize);
        return [$list, compact('total', 'page', 'pagesize', 'pagemax')];
    }
}