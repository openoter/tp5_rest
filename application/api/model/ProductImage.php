<?php
/**
 * Created by PhpStorm.
 * User: marmo
 * Date: 2017/8/2
 * Time: 1:37
 */

namespace app\api\model;


class ProductImage extends BaseModel{
    protected $hidden = ["img_id", "delete_time", "product_id"];

    public function imgUrl(){
        return $this->belongsTo("Image", 'img_id', "id");
    }


}