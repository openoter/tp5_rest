<?php

namespace app\api\controller\v1;


use app\api\validate\Count;
use app\api\validate\IDMustBePositiveInt;
use app\lib\exception\ProductException;
use think\Controller;
use app\api\model\Product as ProductModel;
class Product{

    /**
     * 查询最近的商品
     * @url  /product/:id[?count=num]
     * @param int $count
     * @return \think\response\Json
     * @throws ProductException
     * @throws \app\lib\exception\ParameterException
     */
    public function getRecent($count=15){
        (new Count())->goCheck();
        $res = ProductModel::getMostRecent($count);
        if(!$res){
            throw new ProductException();
        }

       // $res = collection($res)->hidden(["summary"])->toArray();
        return json($res);
    }

    /**
     * 根据分类id获取获取商品
     * @url /product/by_category
     * @eg  http://zerg.com/api/v1/product/by_category?id=3
     * @param $id
     * @return \think\response\Json
     * @throws ProductException
     * @throws \app\lib\exception\ParameterException
     */
    public function getAllByCategoryID($id){
        (new IDMustBePositiveInt())->goCheck();
        $res = ProductModel::getProductByCategoryId($id);
        if(!$res){
            throw new ProductException();
        }
//        临时隐藏字段summary
       // $res = collection($res)->hidden(["summary"])->toArray();
        return json($res);
    }

    public function getOne($id){
        (new IDMustBePositiveInt())->goCheck();

        $res = ProductModel::getProductDetail($id);
        if(!$res){
            throw new ProductException();
        }

        return json($res);
    }
}