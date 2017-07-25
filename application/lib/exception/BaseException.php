<?php


namespace app\lib\exception;


use think\Exception;

/**
 * Class BaseException
 * 自定义异常的基类，用于描述错误码、错误信息
 * @package app\lib\exception
 */
class BaseException extends Exception{
    public $code = 400; //HTTP状态码，如404、202
    public $msg = "参数错误"; //错误的具体信息
    public $errorCode = 10000; //自动错误码


    /**
     * BaseException constructor.
     * 构造函数
     * @param array $params 关联数组
     */
    public function __construct($params = []){

        if(!is_array($params)){
            return ;
        }
        if(array_key_exists("code", $params)){
            $this->code = $params["code"];
        }
        if(array_key_exists("msg", $params)){
            $this->msg = $params["msg"];
        }
        if(array_key_exists("errorCode", $params)){
            $this->errorCode = $params["errorCode"];
        }
    }
}