<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/8/15
 * Time: 14:54
 */

namespace Wwtg99\DataPool\Mappers;

/**
 * Class ArrayPgInsertMapper
 * Array Mapper with insert return key for Postgresql
 * @package Wwtg99\DataPool\Mappers
 */
class ArrayPgInsertMapper extends ArrayMapper
{
    /**
     * @param $data
     * @return mixed
     */
    public function insert($data)
    {
        $db = $this->connection;
        $fields = [];
        $values = [];
        foreach ($data as $f => $d) {
            array_push($fields, $f);
            array_push($values, ":$f");
        }
        $re = $db->query(['insert into ' . $this->name . ' (' . implode(',', $fields) . ') values (' . implode(',', $values) . ') returning ' . $this->key, $data, true]);
        $this->setContext(null);
        if ($re) {
            $id = $re[$this->key];
            return $id;
        }
        return false;
    }

}