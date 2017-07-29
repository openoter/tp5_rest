<?php

namespace app\api\model;

use think\Model;

class Category extends BaseModel {

    protected $hidden = ["delete_time", "update_time", "topic_img_id"];

    /**
     * 一对一，关联image表
     * @return \think\model\relation\BelongsTo
     */
    public function img(){
        return $this->belongsTo("Image", "topic_img_id", "id");
    }

    /**
     * 获取所有的分类
     * @return false|static[]
     */
    public static function getAllCategories() {
//        self::with("img")->select();
        $res = self::all([],"img");
        return $res;
    }
}
