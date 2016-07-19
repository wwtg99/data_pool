<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/6/17
 * Time: 9:49
 */

namespace Wwtg99\DataPool\Common;


class Message
{

    /**
     * @var int
     */
    protected $code = 0;

    /**
     * @var string
     */
    protected $msg = '';

    /**
     * Message constructor.
     * @param int $code
     * @param string $msg
     */
    public function __construct($code, $msg)
    {
        $this->code = $code;
        $this->msg = $msg;
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param int $code
     * @return Message
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return string
     */
    public function getMsg()
    {
        return $this->msg;
    }

    /**
     * @param string $msg
     * @return Message
     */
    public function setMsg($msg)
    {
        $this->msg = $msg;
        return $this;
    }

    /**
     * @return \Exception
     */
    public function getException()
    {
        return new \Exception($this->getMsg(), $this->getCode());
    }

    /**
     * @param int $code
     * @return Message
     */
    public static function messageList($code)
    {
        switch ($code) {
            case 1: $msg = 'No connections defined!'; break;
            case 2: $msg = 'Not supported engine!'; break;
            case 3: $msg = 'No name for connection!'; break;
            case 4: $msg = 'No mapper found!'; break;
            case 5: $msg = 'Invalid key data!'; break;
            case 6: $msg = 'Invalid key database'; break;
            default: $msg = ''; $code = 0; break;
        }
        return new Message($code, $msg);
    }
}