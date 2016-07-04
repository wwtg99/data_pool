<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/6/16
 * Time: 17:15
 */

namespace DataPool\Mappers;


use DataPool\Common\IDataConnection;
use DataPool\Common\IDataMapper;
use DataPool\Utils\Pagination;

abstract class ArrayMapper implements IDataMapper
{
    /**
     * @var IDataConnection
     */
    protected $connnection;

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
        $this->connnection = $environment;
        return $this;
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
                    $where = ['PAGE'=>$p];
                } else {
                    $where['PAGE'] = $p;
                }
            }
        }
        $re = $this->connnection->getEngine()->select($this->getTableName(), $select, $where);
        $this->setContext(null);
        return $re;
    }

    /**
     * @param $key
     * @param $select
     * @param $where
     * @return mixed
     */
    public function get($key, $select = null, $where = null)
    {
        if (!is_null($key)) {
            if (is_array($where)) {
                $where['KEY'] = $this->getTableKey();
                $where['KEYDATA'] = $key;
            } else {
                $where = ['KEY' => $this->getTableKey(), 'KEYDATA' => $key];
            }
        }
        $re = $this->connnection->getEngine()->get($this->getTableName(), $select, $where);
        $this->setContext(null);
        return $re;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function insert($data)
    {
        $re = $this->connnection->getEngine()->insert($this->getTableName(), $data);
        $this->setContext(null);
        return $re;
    }

    /**
     * @param $data
     * @param $where
     * @return mixed
     */
    public function update($data, $where)
    {
        $re = $this->connnection->getEngine()->update($this->getTableName(), $data, $where);
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
            $where = ['KEY' => $this->getTableKey(), 'KEYDATA'=>$key];
        }
        $re = $this->connnection->getEngine()->delete($this->getTableName(), $where);
        $this->setContext(null);
        return $re;
    }

    /**
     * @param $where
     * @return bool
     */
    public function has($where)
    {
        $re = $this->connnection->getEngine()->has($this->getTableName(), $where);
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
        $re = $this->connnection->getEngine()->count($this->getTableName(), $select, $where);
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
                $where['PAGE'] = $p;
            }
        }
        $where['KEYDATA'] = $term;
        if ($fields) {
            $where['FIELDS'] = $fields;
        } else {
            $where['FIELDS'] = $this->getTableKey();
        }
        $re = $this->connnection->getEngine()->select($this->getTableName(), $select, $where);
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