<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/6/16
 * Time: 17:05
 */

namespace Wwtg99\DataPool\Common;


interface IDataEngine
{

    const KEY_FIELD = 'KEY';
    const KEYDATA_FIELD = 'KEYDATA';
    const FIELDS_FIELD = 'FIELDS';
    const PAGE_FIELD = 'PAGE';

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
     * @return mixed
     */
    public function has($name, $where);

    /**
     * @param $name
     * @param $select
     * @param $where
     * @return mixed
     */
    public function count($name, $select, $where);

    /**
     * @param $query
     * @return mixed
     */
    public function query($query);

    /**
     * @param string $name
     * @param callable $handler
     * @return IDataEngine
     */
    public function registerHandler($name, $handler);

    /**
     * @param string $name
     * @return IDataEngine
     */
    public function removeHandler($name);

    /**
     * @param array $config
     * @return IDataEngine
     */
    public function init($config);

    /**
     * @return IDataEngine
     */
    public function close();

    /**
     * @return array
     */
    public function getLastError();

    /**
     * @return string
     */
    public function getLastQuery();
}