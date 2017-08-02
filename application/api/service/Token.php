<?php
/**
 * Created by PhpStorm.
 * User: marmo
 * Date: 2017/7/30
 * Time: 20:43
 */

namespace app\api\service;
use app\lib\exception\TokenException;
use think\Cache;
use think\Exception;
use think\Request;

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
    /**
     * 根据key获取Token
     * @param $key 类型：uid|scope
     * @return mixed
     * @throws Exception
     * @throws TokenException
     */
    public static function getCurrentTokenVar($key) {
        // http请求必须放在http头中
        $token = Request::instance()
            ->header("token");

        $vars = Cache::get($token);
//        print_r($vars);
        //判断缓存是否失效（或者缓存器是否有问题）
        if(!$vars){
            throw new TokenException();
        }else{
            //判断是否数组
            if(!is_array($vars)){
                $vars = json_decode($vars, true);
            }

            if(array_key_exists($key, $vars)){
                return $vars[$key];
            }else{
                throw new Exception("尝试获取的Token变量不存在");
            }
        }
    }

    /**
     * 获取当前的uid
     * @return mixed
     * @throws Exception
     * @throws TokenException
     */
    public static function getCurrentUid(){
        //token
        $uid = self::getCurrentTokenVar("uid");
        return $uid;
    }
}