<?php

namespace app\api\controller\v1;

use app\api\model\Category as CategoryModel;
use app\lib\exception\CategoryException;

class Category {
    /**
     * 获取全部分类数据
     * @url     /category/all
     * @return \think\response\Json
     * @throws CategoryException
     */
    public function getAllCategories(){
        $res = CategoryModel::getAllCategories();

        if(!$res){
            throw new CategoryException();
        }
        return json($res);
    }
}