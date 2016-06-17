<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/6/16
 * Time: 15:51
 */

namespace DataPool\Common;


interface IDataPool
{
    /**
     * Get DataConnection
     *
     * @param string $name
     * @return IDataConnection
     */
    public function getConnection($name = '');

    /**
     * Config pool.
     *
     * @param array $config
     * @return IDataPool
     */
    public function config($config);

    /**
     * Initialize pool with conf file.
     *
     * @param $conf
     * @return IDataPool
     */
    public function init($conf);
}