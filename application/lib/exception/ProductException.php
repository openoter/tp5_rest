<?php
/**
 * Created by PhpStorm.
 * User: marmo
 * Date: 2017/7/29
 * Time: 21:20
 */

namespace app\lib\exception;


class ProductException extends BaseException{
    public $code = 400;
    public $msg = "指定的商品不存在";
    public $errorCode = 20000;
}