<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/6/16
 * Time: 17:13
 */

namespace DataPool\Engines;


use DataPool\Common\IDataEngine;

class MedooEngine extends HandlerEngine
{

    /**
     * @var bool
     */
    protected $debug = false;

    /**
     * @var MedooPlus
     */
    protected $medoo;

    /**
     * @var string
     */
    protected $last_sql = '';

    /**
     * @var array
     */
    protected $last_error = [];

    /**
     * MedooEngine constructor.
     * @param array $config
     */
    public function __construct($config)
    {
        $this->init($config);
    }

    /**
     * @param $name
     * @param $select
     * @param $where
     * @return mixed
     */
    public function select($name, $select, $where)
    {
        $tb = $this->formatTable($name);
        if (count($tb) == 2) {
            $re = $this->medoo->select($tb[0], $tb[1], $select, $where);
        } else {
            $re = $this->medoo->select($tb[0], $select, $where);
        }
        $this->last_sql = $this->medoo->getLastSql();
        $this->last_error = $this->medoo->getLastError();
        if ($re === false) {

        }
        $re = $this->handle('select', $re);
        return $re;
    }

    /**
     * @param $name
     * @param $select
     * @param $where
     * @return mixed
     */
    public function get($name, $select, $where)
    {
        // TODO: Implement get() method.
    }

    /**
     * @param $name
     * @param $data
     * @return mixed
     */
    public function insert($name, $data)
    {
        // TODO: Implement insert() method.
    }

    /**
     * @param $name
     * @param $data
     * @param $where
     * @return mixed
     */
    public function update($name, $data, $where)
    {
        // TODO: Implement update() method.
    }

    /**
     * @param $name
     * @param $where
     * @return mixed
     */
    public function delete($name, $where)
    {
        // TODO: Implement delete() method.
    }

    /**
     * @param $name
     * @param $where
     * @return mixed
     */
    public function has($name, $where)
    {
        // TODO: Implement has() method.
    }

    /**
     * @param $name
     * @param $select
     * @param $where
     * @return mixed
     */
    public function count($name, $select, $where)
    {
        // TODO: Implement count() method.
    }

    /**
     * @param $query
     * @return mixed
     */
    public function query($query)
    {
        if (is_array($query)) {
            $sql = $query[0];
            $data = isset($query[1]) ? $query[1] : [];
            $one = isset($query[2]) ? $query[2] : false;
            if ($one) {
                $re = $this->medoo->queryOne($sql, $data);
            } else {
                $re = $this->medoo->queryAll($sql, $data);
            }
        } else {
            $re = $this->medoo->query($query);
        }
        return $re;
    }

    /**
     * @param array $config
     * @return IDataEngine
     */
    public function init($config)
    {
        $c = $config['database'];
        $db_conf = [
            'database_type' => $c['driver'],
            'database_name' => $c['dbname'],
            'server' => isset($c['host']) ? $c['host'] : null,
            'username' => isset($c['username']) ? $c['username'] : null,
            'password' => isset($c['password']) ? $c['password'] : null,
            'port' => isset($c['port']) ? $c['port'] : null,
            'charset' => isset($c['charset']) ? $c['charset'] : 'utf8',
        ];
        if (array_key_exists('database_file', $c)) {
            $db_conf['database_file'] = $c['database_file'];
        }
        if (array_key_exists('option', $c)) {
            $db_conf['option'] = $c['option'];
        }
        if (array_key_exists('prefix', $c)) {
            $db_conf['prefix'] = $c['prefix'];
        }
        if (array_key_exists('debug', $c)) {
            $this->debug = $config['debug'];
        }
        $this->medoo = new MedooPlus($db_conf);
    }

    /**
     * @return array
     */
    public function getLastError()
    {
        return $this->medoo->getLastError();
    }

    /**
     * @return string
     */
    public function getLastQuery()
    {
        return $this->medoo->getLastSql();
    }


    /**
     * @param string|array $name
     * @return array
     */
    private function formatTable($name)
    {
        if (is_array($name)) {
            $table = $name['table'];
            if (array_key_exists('join', $name) && $name['join']) {
                $join = $name['join'];
                return [$table, $join];
            }
            return [$table];
        } else {
            return [$name];
        }
    }
}