<?php

namespace app\lib\exception;


class OrderException extends BaseException{
    public $code = 404; //HTTP状态码，如404、202
    public $msg = "订单不存在，请检查ID"; //错误的具体信息
    public $errorCode = 80000; //默认错误码
}