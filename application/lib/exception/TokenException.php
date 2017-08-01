<?php

namespace app\lib\exception;


class TokenException extends BaseException{
    //下面的值表示的是默认值，可通过构造函数传入
    public $code = 401;
    public $msg = "Token已经过期或无效Token";
    public $errorCode = 10001;
}