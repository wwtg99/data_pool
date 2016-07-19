<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/6/16
 * Time: 15:51
 */

namespace Wwtg99\DataPool\Common;


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
     * Initialize pool with conf file or array.
     *
     * @param string|array $conf
     * @return IDataPool
     */
    public function init($conf);
}