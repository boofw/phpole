<?php namespace Polev\Phpole\Database;

class Db
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
     * @var \Polev\Phpole\Database\Pdo\Collection
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
            } else {
                $pdo = new \PDO($config['dsn'], $config['username'], $config['passwd'], $config['options']);
                $this->collection = new \Polev\Phpole\Database\Pdo\Collection($pdo, $table);
            }
        }
    }

    /**
     * Db init
     * @param $name
     * @return \Polev\Phpole\Database\Db
     */
    static function init($name)
    {
        if (strpos($name, '.') === false) $name = 'default.'.$name;
        if (array_key_exists($name, self::$pool)) {
            return self::$pool[$name];
        }
        return self::$pool[$name] = new self($name);
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
        if (array_key_exists('multiple', $options)) $options['multiple'] = 1;
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
        $skip = ($page - 1) * $pagesize;
        $total = $this->count($query);
        $data = $this->all($query, $fields, $sort, $pagesize, $skip);
        $pagemax = ceil($total / $pagesize);
        return ['data' => $data, 'pager' => compact('total', 'page', 'pagesize', 'pagemax')];
    }
}