<?php
/**
 * Created by PhpStorm.
 * User: marmo
 * Date: 2017/7/30
 * Time: 17:28
 */

namespace app\lib\exception;


class WeChatException extends BaseException{
    public $code = 400;
    public $msg = "微信服务器接口调用失败";
    public $errorCode = 999;
}