<?php

namespace app\api\validate;

/**
 * Class PageParameter
 * 分页参数
 * @package app\api\validate
 */
class PageParameter extends BaseValidate{
    protected $rule = [
        'page' => "isPositiveInteger",
        'size' => "isPositiveInteger",
    ];
    protected $message = [
        'page' => '分页参数必须是正整数',
        'size' => '分页参数必须是正整数',
    ];
}