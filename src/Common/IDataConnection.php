<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/6/16
 * Time: 15:54
 */

namespace DataPool\Common;


interface IDataConnection
{

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     * @return IDataConnection
     */
    public function setName($name);

    /**
     * @return mixed
     */
    public function connect();

    /**
     * @param array $config
     * @return IDataConnection
     */
    public function init($config);

    /**
     * @return mixed
     */
    public function close();

    /**
     * @param string $name
     * @return IDataMapper
     */
    public function getMapper($name);

    /**
     * @return IDataEngine
     */
    public function getEngine();

    /**
     * @param $query
     * @return mixed
     */
    public function query($query);
}