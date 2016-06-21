<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/6/16
 * Time: 17:13
 */

namespace DataPool\Engines;


use DataPool\Common\IDataEngine;
use DataPool\Common\Message;
use DataPool\Utils\Pagination;

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
        $where = $this->formatPage($this->formatKey($where));
        if (is_null($select)) {
            $select = '*';
        }
        if (count($tb) == 2) {
            $re = $this->medoo->select($tb[0], $tb[1], $select, $where);
        } else {
            $re = $this->medoo->select($tb[0], $select, $where);
        }
        $this->last_sql = $this->medoo->getLastSql();
        $this->last_error = $this->medoo->getLastError();
        $re = $this->handle('select', $re);
        if ($re === false) {
            return false;
        }
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
        $tb = $this->formatTable($name);
        $where = $this->formatKey($where);
        if (count($tb) == 2) {
            $re = $this->medoo->get($tb[0], $tb[1], $select, $where);
        } else {
            $re = $this->medoo->get($tb[0], $select, $where);
        }
        $this->last_sql = $this->medoo->getLastSql();
        $this->last_error = $this->medoo->getLastError();
        $re = $this->handle('get', $re);
        if ($re === false) {
            return false;
        }
        return $re;
    }

    /**
     * @param $name
     * @param $data
     * @return mixed
     */
    public function insert($name, $data)
    {
        $re = $this->medoo->insert($name, $data);
        $this->last_sql = $this->medoo->getLastSql();
        $this->last_error = $this->medoo->getLastError();
        $re = $this->handle('insert', $re);
        if ($re === false) {
            return false;
        }
        return $re;
    }

    /**
     * @param $name
     * @param $data
     * @param $where
     * @return mixed
     */
    public function update($name, $data, $where)
    {
        $re = $this->medoo->update($name, $data, $where);
        $this->last_sql = $this->medoo->getLastSql();
        $this->last_error = $this->medoo->getLastError();
        $re = $this->handle('update', $re);
        if ($re === false) {
            return false;
        }
        return $re;
    }

    /**
     * @param $name
     * @param $where
     * @return mixed
     */
    public function delete($name, $where)
    {
        $where = $this->formatKey($where);
        $re = $this->medoo->delete($name, $where);
        $this->last_sql = $this->medoo->getLastSql();
        $this->last_error = $this->medoo->getLastError();
        $re = $this->handle('delete', $re);
        if ($re === false) {
            return false;
        }
        return $re;
    }

    /**
     * @param $name
     * @param $where
     * @return mixed
     */
    public function has($name, $where)
    {
        $tb = $this->formatTable($name);
        if (count($tb) == 2) {
            $re = $this->medoo->has($tb[0], $tb[1], $where);
        } else {
            $re = $this->medoo->has($tb[0], $where);
        }
        $this->last_sql = $this->medoo->getLastSql();
        $this->last_error = $this->medoo->getLastError();
        $re = $this->handle('has', $re);
        if ($re === false) {
            return false;
        }
        return $re;
    }

    /**
     * @param $name
     * @param $select
     * @param $where
     * @return mixed
     */
    public function count($name, $select, $where)
    {
        $tb = $this->formatTable($name);
        if (count($tb) == 2) {
            $re = $this->medoo->count($tb[0], $tb[1], $select, $where);
        } else {
            $re = $this->medoo->count($tb[0], $where);
        }
        $this->last_sql = $this->medoo->getLastSql();
        $this->last_error = $this->medoo->getLastError();
        $re = $this->handle('count', $re);
        if ($re === false) {
            return false;
        }
        return $re;
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
        $this->last_sql = $this->medoo->getLastSql();
        $this->last_error = $this->medoo->getLastError();
        $re = $this->handle('query', $re);
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
     * @param array $where
     * @return array
     * @throws \Exception
     */
    protected function formatKey($where)
    {
        if (isset($where['KEY'])) {
            $key = $where['KEY'];
            unset($where['KEY']);
        }
        if (isset($where['KEYDATA'])) {
            $keydata = $where['KEYDATA'];
            unset($where['KEYDATA']);
        }
        if (isset($where['FIELDS'])) {
            $fields = $where['FIELDS'];
            unset($where['FIELDS']);
        }
        if (isset($key) && isset($keydata)) {
            if (is_array($key) && is_array($keydata)) {
                foreach ($key as $k) {
                    if (!array_key_exists($k, $keydata)) {
                        $keydata[$k] = null;
                    }
                }
                $where['AND'] = $keydata;
            } elseif (is_string($key)) {
                $where['AND'] = [$key => $keydata];
            } else {
                $msg = Message::messageList(5);
                throw $msg->getException();
            }
        } elseif (isset($fields) && isset($keydata)) {
            $wh = $this->formatSearch($fields, $keydata);
            $where['OR'] = $wh;
        }
        return $where;
    }

    /**
     * @param array|string $fields
     * @param array|string $data
     * @return array
     */
    protected function formatSearch($fields, $data)
    {
        if (is_array($fields)) {
            if (is_array($data)) {
                $wh = [];
                foreach ($data as $d) {
                    array_push($wh, $this->formatSearch($fields, $d));
                }
                return $wh;
            } else {
                $wh = [];
                foreach ($fields as $field) {
                    $f = $field . '[~]';
                    $wh[$f] = $data;
                }
                return $wh;
            }
        } else {
            $f = $fields . '[~]';
            if (is_array($data)) {
                $wh = [];
                foreach ($data as $d) {
                    $wh[$f] = $d;
                }
                return $wh;
            } else {
                return [$f => $data];
            }
        }
    }

    /**
     * @param $where
     * @return array
     */
    protected function formatPage($where)
    {
        if (isset($where['PAGE'])) {
            $page = $where['PAGE'];
            if ($page instanceof Pagination) {
                $where['LIMIT'] = [$page->getOffset(), $page->getLimit()];
            }
            unset($where['PAGE']);
        }
        return $where;
    }

    /**
     * @param string|array $name
     * @return array
     */
    protected function formatTable($name)
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