<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/6/16
 * Time: 15:52
 */

namespace DataPool\Common;


class DefaultDataPool implements IDataPool
{

    /**
     * @var array
     */
    protected $connections = [];

    /**
     * @var string
     */
    protected $cur = 'main';

    /**
     * @var array
     */
    protected $configs = [];

    /**
     * DefaultDataPool constructor.
     * @param string|array $conf
     * @param string $root_path
     */
    public function __construct($conf = null, $root_path = null)
    {
        if (!$root_path) {
            $root_path = __DIR__;
        }
        if (is_array($conf)) {
            $conf['root_path'] = $root_path;
            $this->config($conf);
        } else {
            $this->init($conf);
        }
    }

    /**
     * Get DataConnection
     *
     * @param string $name
     * @return IDataConnection
     */
    public function getConnection($name = '')
    {
        if (!$name) {
            $name = $this->cur;
        }
        if (array_key_exists($name, $this->connections)) {
            $conn = $this->connections[$name];
            if ($conn instanceof IDataConnection) {
                $this->cur = $name;
                $conn->connect();
                return $conn;
            } elseif ($conn instanceof \ReflectionClass) {
                if (isset($this->configs[$name])) {
                    $conf = $this->configs[$name];
                    $c = $conn->newInstance();
                    if ($c instanceof IDataConnection) {
                        $c->init($conf);
                        $this->connections[$name] = $c;
                        $this->cur = $name;
                        $c->connect();
                        return $c;
                    }
                }

            }
        }
        return null;
    }

    /**
     * @param array $config
     * @return IDataPool
     * @throws \Exception
     */
    public function config($config)
    {
        $root_path = isset($config['root_path']) ? $config['root_path'] : realpath(implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..']));
        $map_path = isset($config['mapper_path']) ? $config['mapper_path'] : '';
        $log_dir = isset($config['log_dir']) ? $config['log_dir'] : __DIR__;
        $debug = isset($config['debug']) ? boolval($config['debug']) : false;
        if (array_key_exists('connections', $config)) {
            foreach ($config['connections'] as $connection) {
                $name = $connection['name'];
                $cls = $connection['class'];
                $connection['debug'] = $debug;
                $connection['root_path'] = $root_path;
                if (isset($connection['logger'])) {
                    $connection['logger']['log_dir'] = $log_dir;
                }
                if (!isset($connection['mapper_path'])) {
                    $connection['mapper_path'] = $map_path;
                }
                $this->configs[$name] = $connection;
                $this->connections[$name] = new \ReflectionClass($cls);
            }
        } else {
            $msg = Message::messageList(1);
            throw $msg->getException();
        }
        return $this;
    }

    /**
     * @param $conf
     * @return IDataPool
     */
    public function init($conf)
    {
        $f = file_get_contents($conf);
        $obj = json_decode($f, true);
        $this->config($obj);
        return $this;
    }

}