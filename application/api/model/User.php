<?php

namespace app\api\model;


class User extends BaseModel{
    public static function getByOpenId($openId) {
        $user = self::where("openid", $openId)
            ->find();
        return $user;
    }
}