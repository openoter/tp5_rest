<?php
/**
 * Created by PhpStorm.
 * User: marmo
 * Date: 2017/7/23
 * Time: 20:57
 */

namespace app\api\validate;


use think\Exception;
use think\Request;
use think\Validate;

class BaseValidate extends Validate{

    /**
     * 检查
     * @return bool
     * @throws Exception
     */
    public function goCheck(){
        //获取传递的参数
        $all = Request::instance()->param();
        //校验
        $result = $this->check($all);
        if(!$result){
            //如果出错，抛出异常
            $error = $this->getError();
            throw new Exception($error);
        }else{
            return $result;
        }
    }
}