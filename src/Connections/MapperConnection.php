<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/6/16
 * Time: 17:31
 */

namespace DataPool\Connections;


use DataPool\Common\IDataConnection;
use DataPool\Common\IDataEngine;
use DataPool\Common\IDataMapper;
use DataPool\Common\Message;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

abstract class MapperConnection implements IDataConnection
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $mappers = [];

    /**
     * @var IDataEngine
     */
    protected $engine;

    /**
     * @var \Monolog\Logger
     */
    protected $logger;

    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var string
     */
    protected $mapper_path;

    /**
     * @var bool
     */
    protected $debug = false;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return IDataConnection
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param string $name
     * @return IDataMapper
     */
    public function getMapper($name)
    {
        if (array_key_exists($name, $this->mappers)) {
            $m = $this->mappers[$name];
            if ($m instanceof IDataMapper) {
                return $m;
            }
        } else {
            try {
                if ($this->mapper_path) {
                    $name = $this->mapper_path . '\\' . $name;
                }
                $rc = new \ReflectionClass($name);
                if ($rc->implementsInterface('\DataPool\Common\IDataMapper')) {
                    $ins = $rc->newInstance();
                    if ($ins instanceof IDataMapper) {
                        $ins->setEnvironment($this);
                        $this->mappers[$name] = $ins;
                        return $ins;
                    }
                }
            } catch (\ReflectionException $e) {
                $msg = Message::messageList(4);
                $this->logger->error($msg->getMsg() . '  ' . $e->getMessage());
            }
        }
        return null;
    }

    /**
     * @return IDataEngine
     */
    public function getEngine()
    {
        return $this->engine;
    }

    /**
     * @param array $config
     * @return $this
     * @throws \Exception
     */
    public function init($config)
    {
        if (array_key_exists('name', $config)) {
            $this->setName($config['name']);
        } else {
            $msg = Message::messageList(3);
            throw $msg->getException();
        }
        $this->initEngine($config);
        $this->initLog($config);
        $this->debug = isset($config['debug']) ? boolval($config['debug']) : false;
        $this->mapper_path = isset($config['mapper_path']) ? $config['mapper_path'] : '';
        $this->config = $config;
        return $this;
    }

    /**
     * @param array $config
     * @throws \Exception
     */
    protected function initEngine($config)
    {
        if (array_key_exists('engine', $config)) {
            $fc = new \ReflectionClass($config['engine']);
            if ($fc->implementsInterface('\DataPool\Common\IDataEngine')) {
                $this->engine = $fc->newInstance($config);
                $this->engine->init($config);
            } else {
                $msg = Message::messageList(2);
                throw $msg->getException();
            }
        }
    }

    /**
     * @param array $config
     */
    protected function initLog($config)
    {
        if (array_key_exists('logger', $config)) {
            $c = $config['logger'];
            $logger = new Logger($this->getName());
            $level = array_key_exists('level', $c) ? $c['level'] : '';
            $level = self::getLevel($level);
            $root_path = $config['root_path'];
            $logdir = $c['log_dir'];
            $name = array_key_exists('title', $c) ? $c['title'] : ($this->getName() . ".log");
            $max = array_key_exists('max_logfile', $c) ? $c['max_logfile'] : 10;
            $handler = new RotatingFileHandler(implode(DIRECTORY_SEPARATOR, [$root_path, $logdir, $name]), $max, $level, true, 0777);
            $logger->pushHandler($handler);
            $this->logger = $logger;
        }
    }

    /**
     * @param string $level
     * @return int
     */
    protected static function getLevel($level) {
        $l = strtoupper($level);
        switch ($l) {
            case 'DEBUG': return Logger::DEBUG;
            case 'INFO': return Logger::INFO;
            case 'NOTICE': return Logger::NOTICE;
            case 'WARNING': return Logger::WARNING;
            case 'ERROR': return Logger::ERROR;
            case 'CRITICAL': return Logger::CRITICAL;
            case 'ALERT': return Logger::ALERT;
            case 'EMERGENCY': return Logger::EMERGENCY;
            default: return Logger::ERROR;
        }
    }
}