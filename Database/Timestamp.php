<?php namespace Boofw\Phpole\Database;

class Timestamp extends Database
{
    function insert($a)
    {
        if ( ! isset($a['crts'])) $a['crts'] = $_SERVER['REQUEST_TIME'];
        if ( ! isset($a['upts'])) $a['upts'] = $_SERVER['REQUEST_TIME'];
        return parent::insert($a);
    }

    function update($criteria, $new_object, $options = [])
    {
        if ( ! isset($new_object['rmts']) &&  ! isset($new_object['upts'])) {
            $new_object['upts'] = $_SERVER['REQUEST_TIME'];
        }
        return parent::update($criteria, $new_object, $options);
    }
}