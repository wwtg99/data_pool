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

    const CONTEXT_PAGE = 'page';
    const CONTEXT_PAGE_SIZE = 'page_size';
    const CONTEXT_TO_PAGE = 'to_page';
    const CONTEXT_LIMIT = 'limit';
    const CONTEXT_OFFSET = 'offset';
    const CONTEXT_ORDER = 'order';

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
            $p = $this->getPagination();
            if ($p) {
                if (is_null($where)) {
                    $where = [IDataEngine::PAGE_FIELD=>$p];
                } else {
                    $where[IDataEngine::PAGE_FIELD] = $p;
                }
            }
            $order = $this->getOrder();
            if ($order) {
                if (is_null($where)) {
                    $where = [IDataEngine::ORDER_FIELD=>$order];
                } else {
                    $where[IDataEngine::ORDER_FIELD] = $order;
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
            $p = $this->getPagination();
            if ($p) {
                $where[IDataEngine::PAGE_FIELD] = $p;
            }
            $order = $this->getOrder();
            if ($order) {
                $where[IDataEngine::ORDER_FIELD] = $order;
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

    /**
     * @return null|Pagination
     */
    protected function getPagination()
    {
        if (isset($this->context[self::CONTEXT_PAGE])) {
            $page = $this->context[self::CONTEXT_PAGE];
            $pageSize = isset($this->context[self::CONTEXT_PAGE_SIZE]) ? $this->context[self::CONTEXT_PAGE_SIZE] : 100;
            $topage = isset($this->context[self::CONTEXT_TO_PAGE]) ? $this->context[self::CONTEXT_TO_PAGE] : null;
            return Pagination::createFromPage($page, $pageSize, $topage);
        } elseif (isset($this->context[self::CONTEXT_LIMIT])) {
            $limit = $this->context[self::CONTEXT_LIMIT];
            $offset = isset($this->context[self::CONTEXT_OFFSET]) ? $this->context[self::CONTEXT_OFFSET] : 0;
            return new Pagination($limit, $offset);
        }
        return null;
    }

    /**
     * @return array
     */
    protected function getOrder()
    {
        $sort = [];
        if (isset($this->context[self::CONTEXT_ORDER])) {
            $order = $this->context[self::CONTEXT_ORDER];
            $ods = explode(',', $order);
            foreach ($ods as $sortf) {
                if (substr($sortf, 0, 1) == '+') {
                    $sort[substr($sortf, 1)] = 'ASC';
                } elseif (substr($sortf, 0, 1) == '-') {
                    $sort[substr($sortf, 1)] = 'DESC';
                }
            }
        }
        return $sort;
    }

}