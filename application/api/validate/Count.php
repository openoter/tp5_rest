<?php
/**
 * Created by PhpStorm.
 * User: marmo
 * Date: 2017/7/29
 * Time: 21:05
 */

namespace app\api\validate;


class Count extends BaseValidate{

    protected $rule = [
        'count'=>"isPositiveInteger|between:1,16" //必须为正整数
    ];
}