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
     * @param $name
     * @param $select
     * @param $where
     * @return mixed
     */
    public function select($name, $select, $where)
    {
        if ($this->getContext()) {
            //TODO
        }
        $re = $this->connnection->getEngine()->select($name, $select, $where);
        $this->setContext(null);
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
        if ($this->getContext()) {
            //TODO
        }
        $re = $this->connnection->getEngine()->get($name, $select, $where);
        $this->setContext(null);
        return $re;
    }

    /**
     * @param $name
     * @param $data
     * @return mixed
     */
    public function insert($name, $data)
    {
        if ($this->getContext()) {
            //TODO
        }
        $re = $this->connnection->getEngine()->insert($name, $data);
        $this->setContext(null);
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
        if ($this->getContext()) {
            //TODO
        }
        $re = $this->connnection->getEngine()->update($name, $data, $where);
        $this->setContext(null);
        return $re;
    }

    /**
     * @param $name
     * @param $where
     * @return mixed
     */
    public function delete($name, $where)
    {
        if ($this->getContext()) {
            //TODO
        }
        $re = $this->connnection->getEngine()->delete($name, $where);
        $this->setContext(null);
        return $re;
    }

    /**
     * @param $name
     * @param $where
     * @return bool
     */
    public function has($name, $where)
    {
        if ($this->getContext()) {
            //TODO
        }
        $re = $this->connnection->getEngine()->has($name, $where);
        $this->setContext(null);
        return $re;
    }

    /**
     * @param $name
     * @param $select
     * @param $where
     * @return int
     */
    public function count($name, $select, $where)
    {
        if ($this->getContext()) {
            //TODO
        }
        $re = $this->connnection->getEngine()->count($name, $select, $where);
        $this->setContext(null);
        return $re;
    }


}