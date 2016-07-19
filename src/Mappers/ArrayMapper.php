<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/6/16
 * Time: 17:15
 */

namespace Wwtg99\DataPool\Mappers;


use Wwtg99\DataPool\Common\IDataConnection;
use Wwtg99\DataPool\Common\IDataEngine;
use Wwtg99\DataPool\Common\IDataMapper;
use Wwtg99\DataPool\Utils\Pagination;

abstract class ArrayMapper implements IDataMapper
{
    /**
     * @var IDataConnection
     */
    protected $connection;

    /**
     * @var array
     */
    protected $context;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string|array
     */
    protected $key;

    /**
     * @param IDataConnection $environment
     * @return IDataMapper
     */
    public function setEnvironment($environment)
    {
        $this->connection = $environment;
        return $this;
    }

    /**
     * @return IDataConnection
     */
    public function getEnvironment()
    {
        return $this->connection;
    }

    /**
     * @param array $context
     * @return IDataMapper
     */
    public function setContext($context)
    {
        $this->context = $context;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param $select
     * @param $where
     * @return mixed
     */
    public function select($select = null, $where = null)
    {
        if ($this->getContext()) {
            $page = isset($this->context['page']) ? $this->context['page'] : null;
            $pageSize = isset($this->context['page_size']) ? $this->context['page_size'] : 100;
            $topage = isset($this->context['to_page']) ? $this->context['to_page'] : null;
            if ($page) {
                $p = Pagination::createFromPage($page, $pageSize, $topage);
                if (is_null($where)) {
                    $where = [IDataEngine::PAGE_FIELD=>$p];
                } else {
                    $where[IDataEngine::PAGE_FIELD] = $p;
                }
            }
        }
        $re = $this->connection->getEngine()->select($this->getTableName(), $select, $where);
        $this->setContext(null);
        return $re;
    }

    /**
     * Get one data by key or where.
     *
     * @param $key
     * @param $select
     * @param $where
     * @return mixed
     */
    public function get($key, $select = null, $where = null)
    {
        if (!is_null($key)) {
            if (is_array($where)) {
                $where[IDataEngine::KEY_FIELD] = $this->getTableKey();
                $where[IDataEngine::KEYDATA_FIELD] = $key;
            } else {
                $where = [IDataEngine::KEY_FIELD => $this->getTableKey(), IDataEngine::KEYDATA_FIELD => $key];
            }
        }
        $re = $this->connection->getEngine()->get($this->getTableName(), $select, $where);
        $this->setContext(null);
        return $re;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function insert($data)
    {
        $re = $this->connection->getEngine()->insert($this->getTableName(), $data);
        $this->setContext(null);
        return $re;
    }

    /**
     * @param $data
     * @param $where
     * @param $key
     * @return mixed
     */
    public function update($data, $where = null, $key = null)
    {
        if (!is_null($key)) {
            if (is_array($where)) {
                $where[IDataEngine::KEY_FIELD] = $this->getTableKey();
                $where[IDataEngine::KEYDATA_FIELD] = $key;
            } else {
                $where = [IDataEngine::KEY_FIELD => $this->getTableKey(), IDataEngine::KEYDATA_FIELD => $key];
            }
        }
        $re = $this->connection->getEngine()->update($this->getTableName(), $data, $where);
        $this->setContext(null);
        return $re;
    }

    /**
     * @param $key
     * @param $where
     * @return mixed
     */
    public function delete($key, $where = null)
    {
        if ($key) {
            $where = [IDataEngine::KEY_FIELD => $this->getTableKey(), IDataEngine::KEYDATA_FIELD=>$key];
        }
        $re = $this->connection->getEngine()->delete($this->getTableName(), $where);
        $this->setContext(null);
        return $re;
    }

    /**
     * @param $where
     * @return bool
     */
    public function has($where)
    {
        $re = $this->connection->getEngine()->has($this->getTableName(), $where);
        $this->setContext(null);
        return $re;
    }

    /**
     * @param $select
     * @param $where
     * @return int
     */
    public function count($select = null, $where = null)
    {
        $re = $this->connection->getEngine()->count($this->getTableName(), $select, $where);
        $this->setContext(null);
        return $re;
    }

    /**
     * @param $term
     * @param $select
     * @param array $fields
     * @return mixed
     */
    public function search($term, $select = null, $fields = [])
    {
        $where = [];
        if ($this->getContext()) {
            $page = isset($this->context['page']) ? $this->context['page'] : null;
            $pageSize = isset($this->context['page_size']) ? $this->context['page_size'] : 100;
            $topage = isset($this->context['to_page']) ? $this->context['to_page'] : null;
            if ($page) {
                $p = Pagination::createFromPage($page, $pageSize, $topage);
                $where[IDataEngine::PAGE_FIELD] = $p;
            }
        }
        $where[IDataEngine::KEYDATA_FIELD] = $term;
        if ($fields) {
            $where[IDataEngine::FIELDS_FIELD] = $fields;
        } else {
            $where[IDataEngine::FIELDS_FIELD] = $this->getTableKey();
        }
        $re = $this->connection->getEngine()->select($this->getTableName(), $select, $where);
        $this->setContext(null);
        return $re;
    }

    /**
     * @return string
     */
    protected function getTableName()
    {
        if (property_exists($this, 'name') && $this->name) {
            return $this->name;
        }
        $clsname = get_class($this);
        $sindex = strrpos($clsname, '\\');
        if ($sindex !== false) {
            $clsname = substr($clsname, $sindex + 1);
        }
        return strtolower($clsname);
    }

    /**
     * @return array|string
     */
    protected function getTableKey()
    {
        if (property_exists($this, 'key') && $this->key) {
            return $this->key;
        }
        return '';
    }

}