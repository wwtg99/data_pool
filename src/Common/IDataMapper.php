<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/6/16
 * Time: 17:09
 */

namespace DataPool\Common;


interface IDataMapper
{

    /**
     * @param IDataConnection $environment
     * @return IDataMapper
     */
    public function setEnvironment($environment);

    /**
     * @param array $context
     * @return IDataMapper
     */
    public function setContext($context);

    /**
     * @return mixed
     */
    public function getContext();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return mixed
     */
    public function getKey();

    /**
     * @param $name
     * @param $select
     * @param $where
     * @return mixed
     */
    public function select($name, $select, $where);

    /**
     * @param $name
     * @param $select
     * @param $where
     * @return mixed
     */
    public function get($name, $select, $where);

    /**
     * @param $name
     * @param $data
     * @return mixed
     */
    public function insert($name, $data);

    /**
     * @param $name
     * @param $data
     * @param $where
     * @return mixed
     */
    public function update($name, $data, $where);

    /**
     * @param $name
     * @param $where
     * @return mixed
     */
    public function delete($name, $where);

    /**
     * @param $name
     * @param $where
     * @return bool
     */
    public function has($name, $where);

    /**
     * @param $name
     * @param $select
     * @param $where
     * @return int
     */
    public function count($name, $select, $where);
}