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
     * @param $select
     * @param $where
     * @return mixed
     */
    public function select($select = null, $where = null);

    /**
     * @param $key
     * @param $select
     * @param $where
     * @return mixed
     */
    public function get($key, $select = null, $where = null);

    /**
     * @param $data
     * @return mixed
     */
    public function insert($data);

    /**
     * @param $data
     * @param $where
     * @return mixed
     */
    public function update($data, $where);

    /**
     * @param $key
     * @param $where
     * @return mixed
     */
    public function delete($key, $where = null);

    /**
     * @param $where
     * @return bool
     */
    public function has($where);

    /**
     * @param $select
     * @param $where
     * @return int
     */
    public function count($select = null, $where = null);

    /**
     * @param $term
     * @param $select
     * @param array $fields
     * @return mixed
     */
    public function search($term, $select = null, $fields = []);
}