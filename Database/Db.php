<?php namespace Polev\Phpole\Database;

class Db
{
    static $config = [];

    /**
     * @var \Polev\Phpole\Database\Pdo\Collection
     */
    private static $collection;
    private static $pool = [];

    static function init($name)
    {
        if (array_key_exists($name, self::$pool)) {
            return self::$collection = self::$pool[$name];
        }
        list($db, $table) = explode('.', $name);
        if (array_key_exists($db, self::$config)) {
            $config = self::$config[$db];
            if ($config['driver'] === 'mongo') {
                $mongoClient = new \MongoClient($config['server'], $config['options']);
                return self::$collection = self::$pool[$name] = $mongoClient->selectDB($name);
            } else {
                $pdo = new \PDO($config['dsn'], $config['username'], $config['passwd'], $config['options']);
                return self::$collection = self::$pool[$name] = new \Polev\Phpole\Database\Pdo\Collection($pdo, $table);
            }
        }
    }

    function all($query = [], $fields = [], $sort = null, $limit = null, $skip = null)
    {
        $cursor = self::$collection->find($query, $fields);
        if ($sort) $cursor->sort($sort);
        if ($limit) $cursor->limit($limit);
        if ($skip) $cursor->skip($skip);
        return iterator_to_array($cursor);
    }

    function first($query = [], $fields = [], $sort = null, $skip = null)
    {
        list($r,) = $this->all($query, $fields, $sort, 1, $skip);
        return $r;
    }

    function count($query = [])
    {
        return self::$collection->count($query);
    }

    function insert($a)
    {
        // @todo
    }

    function update($criteria, $new_object, $options = [])
    {
        // @todo
    }

    function upsert($criteria, $new_object, $options = [])
    {
        return $this->update($criteria, $new_object, ['upsert' => 1]);
    }

    function remove($criteria, $options = [])
    {
        // @todo
    }
}