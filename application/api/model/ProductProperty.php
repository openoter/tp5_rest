<?php
/**
 * Created by PhpStorm.
 * User: marmo
 * Date: 2017/8/2
 * Time: 1:37
 */

namespace app\api\model;


class ProductProperty extends BaseModel{

    protected $hidden = ["product_id", "delete_time", "id"];
}