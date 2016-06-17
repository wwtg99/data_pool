<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/6/17
 * Time: 9:49
 */

namespace DataPool\Common;


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
            case 5: $msg = ['message'=>'login failed', 'type'=>'danger']; break;
            case 6: $msg = ['message'=>'password mismatch', 'type'=>'danger']; break;
            case 7: $msg = ['message'=>'password changed', 'type'=>'success']; break;
            case 8: $msg = ['message'=>'password not changed', 'type'=>'danger']; break;
            case 9: $msg = ['message'=>'empty input', 'type'=>'danger']; break;
            case 1001: $msg = ['message'=>'illegal oauth', 'type'=>'danger']; break;
            case 1002: $msg = ['message'=>'no code', 'type'=>'danger']; break;
            case 1003: $msg = ['message'=>'fail to get access_token', 'type'=>'danger']; break;
            default: $msg = ['message'=>'', 'type'=>'info']; $code = 0; break;
        }
        return new Message($code, $msg);
    }
}