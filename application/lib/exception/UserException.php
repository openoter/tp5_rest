<?php
/**
 * Created by PhpStorm.
 * User: marmo
 * Date: 2017/8/3
 * Time: 0:01
 */

namespace app\lib\exception;


class UserException extends BaseException{
    public $code = 401;
    public $msg = "用户不存在";
    public $errorCode = 60000;
}