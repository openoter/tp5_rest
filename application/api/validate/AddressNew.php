<?php

namespace app\api\validate;

/**
 * Class AddressNew
 * 新增地址
 * @package app\api\validate
 */
class AddressNew extends BaseValidate{
    protected $rule = [
        "name"      => "require|isNotEmpty",
        "mobile"    => "require|isMobile",
        "province"  => "require|isNotEmpty",
        "city"      => "require|isNotEmpty",
        "country"    => "require|isNotEmpty",
        "detail"    => "require|isNotEmpty"
//        为什么不要uid，uid是自动增长的，不安全，我们从缓存中获取（登录时已经将uid缓存了）
    ];

}