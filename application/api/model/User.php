<?php

namespace app\api\model;


class User extends BaseModel{

    /**
     * 定义关系：1:1
     * @return \think\model\relation\HasOne
     */
    public function address(){
        return $this->hasOne("UserAddress", "user_id", "id");
    }
    public static function getByOpenId($openId) {
        $user = self::where("openid", $openId)
            ->find();
        return $user;
    }
}