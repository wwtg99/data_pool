<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/6/16
 * Time: 17:13
 */

namespace Wwtg99\DataPool\Engines;


use Wwtg99\DataPool\Common\IDataEngine;
use Wwtg99\DataPool\Common\Message;
use Wwtg99\DataPool\Utils\Pagination;

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
    protected $lastSql = '';

    /**
     * @var array
     */
    protected $lastError = [];

    /**
     * @param $name: table name or ['table':'', 'join':'']
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
        $re = $this->handle('select', $re);
        if ($re === false) {
            return false;
        }
        return $re;
    }

    /**
     * @param $name: table name or ['table':'', 'join':'']
     * @param $select
     * @param $where
     * @return mixed
     */
    public function get($name, $select, $where)
    {
        if (is_null($select)) {
            $select = '*';
        }
        $tb = $this->formatTable($name);
        $where = $this->formatKey($where);
        if (count($tb) == 2) {
            $re = $this->medoo->get($tb[0], $tb[1], $select, $where);
        } else {
            $re = $this->medoo->get($tb[0], $select, $where);
        }
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
        $where = $this->formatKey($where);
        $re = $this->medoo->update($name, $data, $where);
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
        $re = $this->handle('query', $re);
        return $re;
    }

    /**
     * @param array $config
     * @return $this
     * @throws \Exception
     */
    public function init($config)
    {
        if (!isset($config['database'])) {
            $msg = Message::messageList(6);
            throw $msg->getException();
        }
        $c = $config['database'];
        $def_conf = [
            'driver'=>'',
            'dbname'=>'',
            'host'=>'',
            'username'=>'',
            'password'=>'',
            'charset'=>'utf8',
            'prefix'=>'',
            'option'=>null
        ];
        $c = array_merge($def_conf, $c);
        $db_conf = [
            'database_type' => $c['driver'],
            'database_name' => $c['dbname'],
            'server' => $c['host'],
            'username' => $c['username'],
            'password' => $c['password'],
            'port' => $c['port'],
            'charset' => $c['charset'],
            'prefix' => $c['prefix'],
            'option' => $c['option']
        ];
        if (array_key_exists('database_file', $c)) {
            $db_conf['database_file'] = $c['database_file'];
        }
        if (array_key_exists('debug', $c)) {
            $this->debug = $config['debug'];
        }
        $this->medoo = new MedooPlus($db_conf);
        return $this;
    }

    /**
     * @return IDataEngine
     */
    public function close()
    {
        return $this;
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
        if (isset($where[self::KEY_FIELD])) {
            $key = $where[self::KEY_FIELD];
            unset($where[self::KEY_FIELD]);
        }
        if (isset($where[self::KEYDATA_FIELD])) {
            $keydata = $where[self::KEYDATA_FIELD];
            unset($where[self::KEYDATA_FIELD]);
        }
        if (isset($where[self::FIELDS_FIELD])) {
            $fields = $where[self::FIELDS_FIELD];
            unset($where[self::FIELDS_FIELD]);
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
        if (isset($where[self::PAGE_FIELD])) {
            $page = $where[self::PAGE_FIELD];
            if ($page instanceof Pagination) {
                $where['LIMIT'] = [$page->getOffset(), $page->getLimit()];
            }
            unset($where[self::PAGE_FIELD]);
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