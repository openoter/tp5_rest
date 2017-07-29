<?php

namespace app\api\controller\v2;

use app\lib\exception\BaseException;
use think\Exception;
use think\Loader;
use app\api\model\Banner as BannerModel;

class Banner {

    public function getBanner($id){
        $banner = ["user"=>"janc", "id"=>$id];
        return json($banner);
    }
}