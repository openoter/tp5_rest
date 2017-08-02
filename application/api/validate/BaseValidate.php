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

    /**
     * 判断值是否为正整数
     * @param $value
     * @param string $rule
     * @param string $data
     * @param string $field
     * @return bool|string
     */
    protected function isPositiveInteger($value, $rule='', $data='', $field='') {

        if (is_numeric($value) && is_int($value + 0) && ($value + 0) > 0) {
            return true;
        }else{
            return false;
        }
    }

    /**
     * 判断值是否为空
     * @param $value
     * @param string $rule
     * @return bool
     */
    protected function isNotEmpty($value, $rule=""){
        if(empty($value)){
            return false;
        }else{
            return true;
        }
    }

    /**
     * 判断是否是手机号
     * @param $value
     * @return bool
     */
    public function isMobile($value){
        $rule = "^1(3|4|5|6|7|8)[0-9]\d{8}$^";
        $res = preg_match($rule, $value);
        if($res){
            return true;
        }else{
            return false;
        }
    }
    /**
     * @param $arr 通常传入request.post变量数组
     * @return array 按照规则key过滤后的变量数组
     * @throws ParameterException
     */
    public function getDataByRules($arr){
        if(array_key_exists("user_id", $arr) | array_key_exists("uid", $arr)){
            //不允许包含user_id或者uid，防止恶意覆盖user_id外键
            throw new ParameterException([
                "msg"=>"参数中包含非法的参数名user_id或uid"
            ]);
        }

        $newArr = [];
        /**
         * 根据当前的rule的取值来得到需要的数据，防止用户传递多的无用数据
         *
         * $this->rule：指定是代用验证器是子类中的 $rule
         */
        foreach($this->rule as $key=>$value){
            $newArr[$key] = $arr[$key];
        }

        return $newArr;
    }
}