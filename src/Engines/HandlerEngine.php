<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/6/16
 * Time: 17:16
 */

namespace DataPool\Engines;


use DataPool\Common\IDataEngine;

abstract class HandlerEngine implements IDataEngine
{

    /**
     * @var array
     */
    protected $handlers = [];

    /**
     * @param string $name
     * @param callable $handler
     * @return IDataEngine
     */
    public function registerHandler($name, $handler)
    {
        if (array_key_exists($name, $this->handlers)) {
            if (is_array($this->handlers[$name])) {
                array_push($this->handlers[$name], $handler);
            } else {
                $h = [$this->handlers[$name], $handler];
                $this->handlers[$name] = $h;
            }
        } else {
            $this->handlers[$name] = [$handler];
        }
        return $this;
    }

    /**
     * @param string $name
     * @return IDataEngine
     */
    public function removeHandler($name)
    {
        unset($this->handlers[$name]);
        return $this;
    }

    /**
     * @param string $name
     * @param $data
     * @return mixed
     */
    protected function handle($name, $data)
    {
        if (array_key_exists($name, $this->handlers)) {
            $f = $this->handlers[$name];
            $context = ['event'=>$name, 'class'=>get_class($this)];
            if (is_array($f)) {
                foreach ($f as $item) {
                    if (is_callable($item)) {
                        $data = $item($data, $context);
                    }
                }
                return $data;
            } elseif (is_callable($f)) {
                return $f($data, $context);
            }
        }
        return $data;
    }
}