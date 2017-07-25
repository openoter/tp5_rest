<?php

namespace app\lib\exception;


/**
 * Class ParameterException
 * 参数异常错误
 * @package app\api\validate
 */
class ParameterException extends BaseException{
    public $code = 400;
    public $msg = "参数错误";
    public $errorCode = 10000;
}