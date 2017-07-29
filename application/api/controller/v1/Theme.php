<?php

namespace app\api\controller\v1;

use app\api\validate\IDCollection;
use app\api\validate\IDMustBePositiveInt;
use app\lib\exception\ThemeException;
use think\Controller;

use app\api\model\Theme as ThemeModel;

class Theme{

    /**
     * url /theme?ids=id1,id2...
     * @param string $ids
     * @return \think\response\Json
     * @throws ThemeException
     * @throws \app\lib\exception\ParameterException
     */
    public function getSimpleList($ids=''){
//        Loader::validate('IDCollection')->goCheck();

        /*$v = new IDCollection();
        $v->goCheck();*/
        ( new IDCollection())->goCheck();
        $model = new ThemeModel();
            $res = $model->getSimpleList($ids);
//        $res = ThemeModel::with(['topicImg',"headImg"])->select($ids);
        if(!$res){
            throw new ThemeException();
        }
        return json($res);
    }

    /**
     * @url /theme/:id
     * @param $id
     * @return string
     */
    public function getComplexOne($id){
//        验证必须为正整数
        (new IDMustBePositiveInt())->goCheck();

        $res = ThemeModel::getThemeWithProducts($id);
        if(!$res){
            throw new ThemeException();
        }
        return json($res);
    }
}
