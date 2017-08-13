<?php

namespace app\api\model;


class Order extends BaseModel{
    protected $hidden = ['user_id', 'delete_time', 'update_time'];
    protected $autoWriteTimestamp = true;

    //读取器，来格式化数据
    public function getSnapItemsAttr($value){
        if(empty($value)){
            return null;
        }
        return json_decode($value);
    }

    public function getSnapAddressAttr($value) {
        if(empty($value)){
            return null;
        }
        return json_decode($value);
    }
    /**
     * 根据用户查询历史订单列表
     * @param $uid
     * @param int $page
     * @param int $size
     * @return \think\Paginator
     */
    public static function getSummaryByUser($uid, $page=1, $size=15){
        $paginate = self::where('user_id', '=', $uid)
            ->order("create_time desc")
            ->paginate($size, true, ['page'=>$page]); //返回的是Paginate对象

        return $paginate;
    }
}