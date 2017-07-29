<?php

namespace app\api\validate;

/**
 * Class IDCollection
 *
 * @package app\api\validate
 */
class IDCollection extends BaseValidate{
    protected $rule = [
        "ids" => "require|checkIDs"
    ];

    protected $message = [
        "ids"=>"ids必须是以逗号分隔的多个正整数"
    ];

    /**
     * 检查ids是否符合规范，id1,id2...
     * 1. 必须为正整数
     * 2. 用逗号分隔
     *
     * @param $value
     * @return bool
     */
    protected function checkIDs($value){
        $value = explode(",", $value);
        if(empty($value)){
            return false;
        }
        foreach ($value as $id){
            if(!$this->isPositiveInteger($id)){
                return false;
            }
        }
        return true;
    }
}