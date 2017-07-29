<?php

namespace app\api\controller\v1;

use app\lib\exception\BaseException;
use think\Exception;
use think\Loader;
use app\api\model\Banner as BannerModel;

class Banner {

    /**获取指定id的banner信息
     * @url /banner/:id
     * @http GET
     * @param $id banner的id号
     * @return \think\response\Json
     * @throws BaseException
     */
    public function getBanner($id){
//        (new IDMustBePositiveInt())->goCheck();
        Loader::validate('IDMustBePositiveInt')->goCheck();
//        使用ORM方式，调用的是Model中的get方法

        /*建议使用静态方法调用
         * */
//        $banner = BannerModel::with(['items', 'items.img'])->find($id);
        $banner = BannerModel::getBannerById($id);

//        $data = $banner->toArray(); //将$banner中的数据使用数组存起来
//        $banner->hidden(["delete_time", "items.delete_time", "items.img.delete_time"]);
        if(!$banner){
            throw new BaseException();
        }
        return json($banner);
    }
}