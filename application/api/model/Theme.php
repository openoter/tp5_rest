<?php

namespace app\api\model;

use think\Model;

class Theme extends BaseModel {
    protected $hidden = ["delete_time", "update_time", "topic_img_id", "head_img_id"];
    /**
     * 定义关联关系： 一对一
     * 要注意belongsTo和hasOne的区别
     * 带外键的表一般定义belongsTo，另外一方定义hasOne
     */
    public function topicImg(){
//        hasOne() //一对一
//        $this->hasOne()
       return $this->belongsTo("Image", "topic_img_id", "id");
    }

    public function headImg(){
       return $this->belongsTo("Image", "head_img_id", "id");
    }

//    多对多
    public function products(){
        return $this->belongsToMany("Product", 'theme_product', "product_id", "theme_id");
    }

    /**
     * 查询简单数据
     * @param $ids
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getSimpleList($ids){
        $ids = explode(",", $ids);
        $res = self::with("topicImg,headImg")
            ->select($ids);
        return $res;
    }

    /**
     * 查询没个主题下具体的内容
     * @param $id
     */
    public static function getThemeWithProducts($id){
        $res = self::with(["products", "topicImg", "headImg"])
            ->find($id);
        return $res;
    }

}
