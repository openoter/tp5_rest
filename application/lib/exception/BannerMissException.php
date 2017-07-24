<?php

namespace app\lib\exception;

/**
 * Class BannerMissException
 * Banner没有找到的异常
 * @package app\lib\exception
 */
class BannerMissException extends BaseException{
    public $code = 404;
    public $msg = "请求的Banner不存在";
    public $errorCode = 40000;
}