<?php

namespace app\api\controller\v1;

use think\Exception;
use think\Loader;
use app\api\model\Banner as BannerModel;

class Banner {

    /**
     * 获取指定id的banner信息
     * @url /banner/:id
     * @http GET
     * @param $id banner的id号
     */
    public function getBanner($id){
//        $validate = new IDMustBePositiveInt();
        $res = Loader::validate('IDMustBePositiveInt')->goCheck();
//        $res = $validate->goC;
        $banner = BannerModel::getBannerById($id);


        return json($banner);
    }
}