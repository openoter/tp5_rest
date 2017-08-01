<?php
/**
 * Created by PhpStorm.
 * User: marmo
 * Date: 2017/7/30
 * Time: 20:43
 */

namespace app\api\service;

/**
 * Class Token
 * UserToken的基类
 * @package app\api\service
 */
class Token {
    /**
     * 获取key
     */
    public static function generateToken(){

        /**
         * 加强安全性的方法
         * 使用3组字符串用md5加密
         */
        /**
         * 32位字符
         */
        $randChars = getRandChar(32);
        $timestamp = $_SERVER["REQUEST_TIME_FLOAT"];
        //salt 盐
        $salt = config("secure.token_salt");

        return md5($randChars.$timestamp.$salt);
    }

//    private function getR
}