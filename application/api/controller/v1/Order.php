<?php

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\service\Order as OrderService;
use app\api\service\Token as TokenService;
use app\api\validate\OrderPlace;
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
        "checkExclusiveScope" => ["only" => "placeOrder"]
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
     * 6. 如果支付成功，减去库存量，否则不能减（返回支付失败的结果）
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



}