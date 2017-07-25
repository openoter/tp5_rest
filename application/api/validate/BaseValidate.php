<?php
/**
 * Created by PhpStorm.
 * User: marmo
 * Date: 2017/7/23
 * Time: 20:57
 */

namespace app\api\validate;


use app\lib\exception\ParameterException;
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
            $e = new ParameterException([
                //判断$this->error是否为数组
               "msg"=>is_array($this->error) ? implode(";", $this->error) : $this->error
            ]);
//            $e->msg = $this->error;
            throw $e;
//            $error = $this->getError();
//            throw new Exception($error);
        }else{
            return $result;
        }
    }
}