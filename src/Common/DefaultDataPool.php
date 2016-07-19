<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/6/16
 * Time: 15:52
 */

namespace Wwtg99\DataPool\Common;


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
        }
        $this->init($conf);
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
     * @inheritdoc
     */
    public function config($config)
    {
        $def_conf = [
            'root_path'=>realpath(implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..'])),
            'mapper_path'=>'',
            'log_dir'=>__DIR__,
            'debug'=>false,
            'connections'=>[]
        ];
        $conf = array_merge($def_conf, $config);
        $root_path = $conf['root_path'];
        $map_path = $conf['mapper_path'];
        $log_dir = $conf['log_dir'];
        $debug = boolval($conf['debug']);
        foreach ($conf['connections'] as $connection) {
            $name = $connection['name'];
            $cls = $connection['class'];
            $connection['debug'] = $debug;
            if (!isset($connection['root_path'])) {
                $connection['root_path'] = $root_path;
            }
            if (!isset($connection['mapper_path'])) {
                $connection['mapper_path'] = $map_path;
            }
            if (isset($connection['logger'])) {
                if (!isset($connection['logger']['log_dir'])) {
                    $connection['logger']['log_dir'] = $log_dir;
                }
            }
            $this->configs[$name] = $connection;
            $this->connections[$name] = new \ReflectionClass($cls);
        }
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function init($conf)
    {
        if (is_array($conf)) {
            return $this->config($conf);
        } else {
            if (file_exists($conf)) {
                $f = file_get_contents($conf);
                $obj = json_decode($f, true);
                return $this->config($obj);
            }
        }
        return $this;
    }

}