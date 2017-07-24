<?php
/**
 * Created by PhpStorm.
 * User: marmo
 * Date: 2017/7/23
 * Time: 12:51
 */

namespace app\sample\validate;


use think\Validate;

class UserV extends Validate{
    protected $rule = [
        'name'=>"require|max:20",
        'email'=>"email"
    ];
}