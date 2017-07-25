<?php

namespace app\api\controller\v1;

use app\lib\exception\BaseException;
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
//        (new IDMustBePositiveInt())->goCheck();
        Loader::validate('IDMustBePositiveInt')->goCheck();
        $banner = BannerModel::getBannerById($id);

        if(!$banner){
            throw new BaseException();
        }
        return json($banner);
    }
}