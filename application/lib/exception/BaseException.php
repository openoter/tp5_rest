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
}