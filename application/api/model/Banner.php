<?php

namespace app\api\model;

use think\Db;
use think\Exception;
use think\Model;

/**
 * Class Banner
 *
 * @package app\api\model
 */
class Banner extends Model{

    protected $hidden = ["delete_time", "update_time"];
    /**
     * 根据banner的id获取Banner信息
     * @param $id
     * @return array|false|\PDOStatement|string|Model
     */
    public static function getBannerById($id) {
        //TODO：根据banner的id获取Banner信息
//        return Db::query("select * from banner where id= ?", [$id]);
        /*$res = Db::table("banner_item")
            ->where("banner_id","=", $id)
            ->select();*/

        $res = self::with(['items', 'items.img'])->find($id);
        return $res;
    }

    /**
     * 查询banner_id对应的BannerItem
     * @return \think\model\relation\HasMany
     */
    public function items(){
//        关联模型，通过 Banner关联BannerItem 关联模型名，关联模型的外键，主键
        return $this->hasMany('BannerItem', 'banner_id', 'id');
    }
}