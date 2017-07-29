<?php

namespace app\api\model;

use think\Model;

class BaseModel extends Model {

    /**
     * 修改图片的地址
     * @param $value
     * @param $data
     * @return string
     */
    public function prefixImgUrl($value, $data){
        $url = $value;
        //判断是网络地址还是本地地址
        if($data["from"] == 1){
            $url = config("setting.img_prefix").$value;
        }
        return $url;
    }
}
