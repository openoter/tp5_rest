<?php

namespace app\api\model;

use think\Model;

class Image extends Model
{
    //
    protected $hidden = ["delete_time", "id", "from", "update_time"];

    /**
     * 修改图片路径（相对路径变成绝对路径）
     * get字段Attr： 固定写法，获取字段的值
     * @param $value 字段的值
     * @return string
     */
    public function getUrlAttr($value){
        return config("setting.img_prefix").$value;
    }
}
