<?php

namespace app\lib\exception;

/**
 * Class ThemeException
 * 主题相关异常
 * @package app\lib\exception
 */
class ThemeException extends BaseException{
    public $code = 404;
    public $msg = "请求的主题不存在";
    public $errorCode = 30000;
}