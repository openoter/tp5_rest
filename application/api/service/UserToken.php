<?php

namespace app\api\service;
use app\api\model\User as UserModel;
use app\lib\enum\ScopeEnum;
use app\lib\exception\TokenException;
use app\lib\exception\WeChatException;
use think\Cache;
use think\Exception;

/**
 * Class UserToken
 *
 * 微信token
 * @package app\api\service
 */
class UserToken extends Token{
    protected $code; //从客户端传过来的code
    protected $wxAppID;
    protected $wxAppSecret;
    protected $wxLoginUrl;

    function __construct($code) {
        $this->code = $code;
        $this->wxAppID = config("wx.app_id");
        $this->wxAppSecret = config("wx.app_secret");
        $this->wxLoginUrl = sprintf(config("wx.login_url"),
            $this->wxAppID, $this->wxAppSecret, $this->code);
    }

    /**
     * 获取 token
     * @return string $token
     * @throws Exception
     * @throws WeChatException
     */
    public function get() {
        $res = curl_get($this->wxLoginUrl);
        $wxRes = json_decode($res, true);
        if(empty($wxRes)){
            throw new Exception("获取openid及session_key异常，微信内部错误");
        }else{
            $loginError = array_key_exists("errcode", $wxRes);
            if($loginError){
                $this->processLoginInError();
            }else{
                return $this->grantToken($wxRes);
            }
        }
    }

    /**
     * 授权令牌
     * @param $wxRes
     * @return string
     * @throws TokenException
     */
    private function grantToken($wxRes){

        /**
         * 1. 拿到openid
         * 2. 数据库里面查看，该openid是否存在（用户已经生产）
         * 3. 不存在则新增一条数据
         * 4. 生成令牌，将数据写入缓存
         *      key: 令牌; value: wxRes,uid,scope
         *
         *      scope: 证明用户的身份
         * 5. 把令牌返回给客户端
         */

        //1. 拿到openid
        $openid = $wxRes["openid"];
        //2. 数据库里面查看，该openid是否存在（用户已经生产）
        $user = UserModel::getByOpenId($openid);
        if($user){
            $uid = $user->id;
        }else{
            //3. 不存在则新增一条数据
            $uid = $this->newUser($openid);
        }

        //4. 生成令牌，将数据写入缓存
        $cache = $this->prepareCacheData($wxRes, $uid);
        $token = $this->saveCache($cache);


        //5. 返回
        return $token;
    }

    /**
     * 保存$key、$token、$scope到服务器
     * @param $cacheValue
     * @return string
     * @throws TokenException
     */
    private function saveCache($cacheValue){
        $key = self::generateToken();
        $value = json_encode($cacheValue);
        /**
         * 所谓令牌实效，其实就是缓存实效
         */
        $expire_in = config("setting.token_expire_in");
        $request = cache($key, $value, $expire_in);
        if(!$request){
            throw new TokenException([
                "msg" => "服务器缓存异常",
                "errorCode" => 10005
            ]);
        }

        $k = Cache::get($key);
        return $key;
    }


    /**
     * 创建一个新的用户
     * @param $openid
     * @return mixed
     */
    private function newUser($openid){
        $user = UserModel::create([
            "openid"=>$openid
        ]);
        return $user->id;
    }

    /**
     * 准备缓存的数据
     * @param $wxRes
     * @param $uid
     * @return mixed
     */
    private function prepareCacheData($wxRes, $uid){
        $cache = $wxRes;
        $cache['uid'] = $uid;
        /**
         * 数字越大权限越大，暂时赋值,16代表用户的权限值
         * 32表示CMS（管理员）的权限值
         */
        $cache['scope'] = ScopeEnum::USER;
        return $cache;
    }
    /**
     * 微信登录接口抛出异常
     * @param $wxRes
     * @throws WeChatException
     */
    private function processLoginInError($wxRes){
        throw  new WeChatException([
            "msg" => $wxRes["errmsg"],
            "errorCode" => $wxRes["errcode"]
        ]);
    }

}