<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/6/16
 * Time: 15:58
 */

namespace DataPool\Connections;


use DataPool\Common\IDataConnection;
use DataPool\Engines\MedooEngine;

class DatabaseConnection extends MapperConnection
{
    /**
     * @param $query
     * @return mixed
     */
    public function query($query)
    {
        return $this->engine->query($query);
    }


    /**
     * @return mixed
     */
    public function connect()
    {
        return $this;
    }

    /**
     * @param array $config
     * @return IDataConnection
     * @throws \Exception
     */
    public function init($config)
    {
        parent::init($config);
        if (!$this->engine) {
            $this->engine = new MedooEngine($config);
            $this->engine->init($config);
        }
        $debug = $this->debug;
        $log_func = function($data, $context) use ($debug)  {
            if ($data === false) {
                $context['error'] = $this->engine->getLastError();
                $this->logger->error('Error for query ' . $this->engine->getLastQuery(), $context);
            }
            if ($debug) {
                $this->logger->info('Query ' . $this->engine->getLastQuery(), $context);
            }
            return $data;
        };
        $this->engine->registerHandler('select', $log_func);
        $this->engine->registerHandler('get', $log_func);
        $this->engine->registerHandler('insert', $log_func);
        $this->engine->registerHandler('update', $log_func);
        $this->engine->registerHandler('delete', $log_func);
        $this->engine->registerHandler('has', $log_func);
        $this->engine->registerHandler('count', $log_func);
        $this->engine->registerHandler('query', $log_func);
        return $this;
    }

    /**
     * @return mixed
     */
    public function close()
    {
        return $this;
    }

}