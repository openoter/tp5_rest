<?php
/**
 * Created by PhpStorm.
 * User: marmo
 * Date: 2017/8/9
 * Time: 23:58
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\validate\IDMustBePositiveInt;
use app\api\service\Pay as PayService;


/**
 * Class Pay
 * 支付接口
 * @package app\api\controller\v1
 */
class Pay extends BaseController{
    protected $beforeActionList = [
        "checkExclusiveScope" => ['only' => "getPreOrder"]
    ];
    /**
     * 请求预订单的信息
     */
    public function getPreOrder($id){
        (new IDMustBePositiveInt())->goCheck();

        $pay = new PayService($id);
        //返回的数据
        return json($pay->pay());
    }
}