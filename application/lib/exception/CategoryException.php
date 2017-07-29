<?php
/**
 * Created by PhpStorm.
 * User: marmo
 * Date: 2017/7/30
 * Time: 1:29
 */

namespace app\lib\exception;


class CategoryException extends BaseException{
    public $code = 400;
    public $msg = "指定的分类不存在";
    public $errorCode = 50000;
}