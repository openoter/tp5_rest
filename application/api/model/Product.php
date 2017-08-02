<?php

namespace app\api\model;

class Product extends BaseModel {
    //pivot：表示多对多的中间表属性
    protected $hidden = ["delete_time", "update_time", "from",
        "category_id", "create_time", "pivot", "main_img_id"];

    public function imgs(){
        return $this->hasMany("ProductImage", "product_id", "id");
    }

    public function properties(){
       return $this->hasMany("ProductProperty", "product_id", "id");
    }

    /**
     * 拼接全部图片地址
     * @param $value
     * @param $data
     * @return string
     */
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

    /**
     * 根据分类id获取商品数据
     * @param $categoryId
     * @return false|\PDOStatement|string|\think\Collection
     */
    public static function getProductByCategoryId($categoryId) {
        $res = self::where('category_id',"=", $categoryId)
            ->select();
        return $res;
    }


    /**
     * 查询商品的信息信息
     * @param $id
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public static function getProductDetail($id) {
        $res = self::with(["imgs"=>function($query){
            $query->with("imgUrl")
                ->order("order", "asc");
        }])
            ->with(["properties"])
            ->find($id);

        return $res;
    }
}