<?php

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\service\Order as OrderService;
use app\api\service\Token as TokenService;
use app\api\model\Order as OrderModel;
use app\api\service\Token;
use app\api\service\WxNotify;
use app\api\validate\IDMustBePositiveInt;
use app\api\validate\OrderPlace;
use app\api\validate\PageParameter;
use app\lib\exception\OrderException;
use think\Controller;
/**
 * Class Order
 * 订单
 * @package app\api\controller\v1
 */
class Order extends BaseController{
    //前置方法
    protected $beforeActionList = [
        //只要用户才能访问
        "checkExclusiveScope" => ["only" => "placeOrder"],
        "checkPrimaryScope" => ["only" => "getSummaryByUser,getDetail"]
    ];
    /**
     * 下单
     *  库存量
     *  支付
     *
     * 流程
     * 1. 用户选择商品，向 API 提交选择商品的相关信息
     * 2. API 接收到商品信息后，检查订单相关商品的*库存量*
     *  为啥要检查库存？
     *  客户端的信息与服务端的信息不不一定是同步的
     * 3. 有库存，把订单信息存入数据库==下单成功了，返回客户端信息
     * 4. 告诉客户端可以支付了，调用支付接口，进行支付接口（运行在一段时间内支付，仍然需要检测*库存量*）
     * 5. 服务器端调用微信支付接口进行支付（微信会返回一个结果，异步调用<不是实时的>）
     * //结果不能由我们的服务端返回，而是由微信自己返回
     * 成功：还需要检测*库存量*（可选）
     * 6. 小程序根据服务器返回的结果，开启微信支付
     * 7. 如果支付成功，减去库存量，否则不能减（返回支付失败的结果）
     */

    /**
     * 下单接口
     * 管理员不能访问该接口
     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     */
    public function placeOrder(){
        //验证
        (new OrderPlace())->goCheck();
//        (new OrderPlace())->goCheck();
        //获取Product数据，要获取数组数据，需要加上`/a`
        $products = input("post.products/a");
//        $products = input('post.products/a');
        $uid = TokenService::getCurrentUid();

        $order = new OrderService();
        $status = $order->place($uid, $products);
        return json($status);
    }

    /**
     * 获取用户订单历史列表
     * @param int $page
     * @param int $size
     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     */
    public function getSummaryByUser($page = 1, $size = 15){
        //验证器
        (new PageParameter())->goCheck();

        $uid = Token::getCurrentUid(); //获取当前的用户id

        $pageOrders = OrderModel::getSummaryByUser($uid, $page, $size);
        $data = [
            "data"=>[],
            'current_page'=>0
        ];
        if($pageOrders->isEmpty()){
            $data['current_page'] = $pageOrders->getCurrentPage();
            return json($data);
        }else{
            //字段掩藏、转换成数组
            $d = $pageOrders
                ->hidden(['snap_items', 'snap_address', 'prepay_id'])
                ->toArray();
            $data['data'] = $d;
            return json($data);
        }
    }

    /**
     * 获取订单的信息信息
     * @param $id 订单的id
     * @return $this
     * @throws OrderException
     * @throws \app\lib\exception\ParameterException
     */
    public function getDetail($id){
        (new IDMustBePositiveInt())->goCheck();

        $orderDetail = OrderModel::get($id);
        if(!$orderDetail){
            throw new OrderException();
        }
        return $orderDetail->hidden(['prepay_id']);
    }
    /**
     * 回调结果
     * 调用频率：5/15/30/180/1800/1800/1800/1800/3600，单位：秒
     *
     * 前一次调用服务器没有正确的接受到回调通知
     */
    public function receiveNotify(){
        /**
         * 1. 检测库存量，超卖
         * 2. 更新订单的状态Order->status字段
         * 3. 减库存
         * 4. 返回消息，如果成功返回微信成功处理的消息，否则需要单独返回没有成功处理
         *
         * 特点：post，xml格式，参数不能传递？形式的
         */
        $notify = new WxNotify();
        $notify->Handle();

        /**
         * //测试
         * $xmlData = file_get_contents("php://input");
         * 在common.php中定义
         * $result = curl_post_raw("http://zreg.com/api/v1/pay/notify?XDEBUG_...", $xmlData)
         */

    }
    public function redirectNotify(){
         //测试
         $xmlData = file_get_contents("php://input");
//         在common.php中定义
         $result = curl_post_raw("http://zreg.com/api/v1/pay/notify?XDEBUG_...", $xmlData);
    }


}