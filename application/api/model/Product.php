<?php

namespace app\api\model;

class Product extends BaseModel {
    //pivot：表示多对多的中间表属性
    protected $hidden = ["delete_time", "update_time", "from",
        "category_id", "create_time", "pivot", "main_img_id"];

    public function getMainImgUrlAttr($value, $data){
        return $this->prefixImgUrl($value, $data);
    }

    /**
     * 获取最近的商品
     * @param $count
     * @return false|\PDOStatement|string|\think\Collection
     */
    public static function getMostRecent($count){
        $res = self::limit($count)
            ->order("create_time desc")
            ->select();
        return $res;
    }

    public static function getProductByCategoryId($categoryId) {
        $res = self::where('category_id',"=", $categoryId)
            ->select();
        return $res;
    }
}