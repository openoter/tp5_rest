<?php

namespace app\lib\exception;

/**
 * Class SuccessMessage
 * 删除、更新、增加成功后消息
 * @package app\lib\exception
 */
class SuccessMessage extends BaseException{
    /**
     * 201: 创建、更新成功
     * 202: 需要一个异步的处理才能完成请求
     */
    public $code = 201;
    public $msg = "ok";
    public $errorCode = 0;
}