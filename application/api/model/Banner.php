<?php

namespace app\api\model;

use think\Db;
use think\Exception;

/**
 * Class Banner
 *
 * @package app\api\model
 */
class Banner {

    /**
     * 根据banner的id获取Banner信息
     * @param $id
     */
    public static function getBannerById($id) {
        //TODO：根据banner的id获取Banner信息


        return Db::query("select * from banner where id= ?", [$id]);
    }
}