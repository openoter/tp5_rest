<?php

namespace app\api\validate;


use app\lib\exception\ParameterException;
use think\Validate;

class OrderPlace extends BaseValidate{

    /*
    //模拟数据
    protected $data = [
        [
            "productId" => 1,
            "count" => 3,
        ],
        [
            "productId" => 2,
            "count" => 3,
        ]
    ];*/
    protected $rule = [
        "products" => "checkProducts"
    ];

    /**
     * 单个商品的验证规则
     * @type array
     */
    protected $singRule = [
        "product_id" => "require|isPositiveInteger",
        "count" => "require|isPositiveInteger"
    ];

    /**
     * 检测订单列表的数据是否符合规范
     * @param $value
     * @return bool
     * @throws ParameterException
     */
    protected function checkProducts($value){
        //判断是否为数组
        if(!is_array($value)){
            throw new ParameterException([
                "msg" => "商品参数不正确"
            ]);
        }
        //判断是否为空
        if(empty($value)){
            throw new ParameterException([
                "msg" => "商品列表不能为空"
            ]);
        }
//        if(is_array($value)){
            foreach($value as $val){
                $this->checkProduct($val);
            }
//        }

        return true;
    }


    /**
     * 检查单个商品的合法性
     * @param $val
     * @throws ParameterException
     */
    private function checkProduct($val){
        $validate = new BaseValidate($this->singRule);
        $result = $validate->check($val);
        if(!$result){
            throw new ParameterException([
                "msg" => "商品列表参数错误"
            ]);
        }
    }
}