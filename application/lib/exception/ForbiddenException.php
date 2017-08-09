<?php

namespace app\lib\exception;

/**
 * Class ForbiddenException
 * 禁止访问
 * @package app\lib\exception
 */
class ForbiddenException extends BaseException{
    public $code = 403;
    public $msg = "您没有权限访问";
    public $errorCode = 10001;
}