<?php

namespace app\api\service;

use app\api\model\Order as OrderModel;
use app\api\service\Order as OrderService;
use app\lib\enum\OrderStatusEnum;
use app\lib\exception\OrderException;
use app\lib\exception\TokenException;
use think\Config;
use think\Exception;
use think\Loader;
use think\Log;

/**
 * WxPay.WxPay：表示extend目录下的WxPay目录中的WxPay开始的文件
 * EXTEND_PATH表示extend目录
 *.Api.php的“后缀”
 */
Loader::import('WxPay.WxPay', EXTEND_PATH, '.Api.php');
/**
 * Class Pay
 *
 * 支付
 *
*@package app\api\service
 */
class Pay {

    private $orderId;
    private $orderNo;

    public function __construct($orderId) {
        if(!$orderId){
            throw new Exception("订单不能为空");
        }
        $this->orderId = $orderId;
    }

    /**
     * 支付逻辑
     * @return array
     * @throws OrderException
     * @throws TokenException
     */
    public function pay(){
        //检查订单的是否符合规范
        $this->checkOrderValid();

        $orderService = new OrderService();
        $status = $orderService->checkOrderStock($this->orderId);
        //检查库存没有通过
        if(!$status['pass']){
            return $status;
        }

        //构建微信预订单
        return $this->makeWXPreOrder($status['orderPrice']);
    }

    /**
     * 构建微信预订单
     */
    private function makeWXPreOrder($totalPrice){
        //openid(代表身份)
        $openid = Token::getCurrentTokenVar('openid');
        if(!$openid){
            throw new TokenException();
        }

        //SDK提供的
        $wxOrderData = new \WxPayUnifiedOrder();
        $wxOrderData->SetOut_trade_no($this->orderNo);
        $wxOrderData->SetTrade_type("JSAPI"); //开发文档规定
        $wxOrderData->SetBody('零贩商店');
        //设置总金额
        $wxOrderData->SetTotal_fee($totalPrice * 100);//单位：分
        $wxOrderData->SetOpenid($openid);
        $wxOrderData->SetNotify_url(config('secure.pay_back_url'));//回调通知

        //预支付订单
        return $this->getPaySignature($wxOrderData);
    }

    /**
     * 获取预支付订单
     * @param $wxOrderData
     * @return null
     * @throws \WxPayException
     */
    private function getPaySignature($wxOrderData){
        //统一下单
        $wxOrder = \WxPayApi::unifiedOrder($wxOrderData);
        if($wxOrder['return_code'] != "SUCCESS" ||
            $wxOrder['result_code'] != "SUCCESS"){
            Log::record($wxOrder, 'error');
            Log::record("获取预支付订单失败", 'error');
        }
//        prepay_id，将prepay_id存到数据库
        $this->recordPreOrder($wxOrder);
        $signature = $this->sign($wxOrder);

        return $signature;
    }

    /**
     * 签名，小程序支付
     * https://mp.weixin.qq.com/debug/wxadoc/dev/api/api-pay.html#wxrequestpaymentobject
     * @param $wxOrder
     * @return array
     */
    private function sign($wxOrder){
        $jsApiPayData = new \WxPayJsApiPay();
        $jsApiPayData->SetAppid(config('wx.app_id'));
        $jsApiPayData->SetTimeStamp((string)time());

        $rand = mdt(time().mt_rand(0, 1000)); //随机字符串
        $jsApiPayData->SetNonceStr($rand);

        $jsApiPayData->SetPackage("prepay_id=".$wxOrder['prepay_id']);

        $jsApiPayData->SetSignType('md5');

        $sign = $jsApiPayData->MakeSign();

        $rawValues = $jsApiPayData->GetValues();
        $rawValues['paySign'] = $sign;
        unset($rawValues['appId']); //删除程序的apppId
        return $rawValues;
    }
    private function recordPreOrder($wxOrder){
        OrderModel::where('id', '=', $this->orderId)
            ->update(['prepay_id'=>$wxOrder['prepay_id']]);
    }
    /**
     * 检查订单的是否符合规范
     * @return bool
     * @throws Exception
     * @throws OrderException
     * @throws TokenException
     */
    private function checkOrderValid(){
        /**
         * 订单号不存在
         * 订单号存在，但是订单号与当前用户不匹配
         * 订单有可能已经配支付
         */

        //1> 订单号不存在
        $order = OrderModel::where('id', '=', $this->orderId)
            ->find();

        if(!$order){
            throw new OrderException();
        }

        //2> 订单号与当前用户不匹配
        if(!Token::isValidOperate($order->user_id)){
            throw new TokenException([
                "msg" => "订单与用户不匹配",
                "errorCode" => 10003
            ]);
        }

        //3> 订单有可能已经配支付
        /**
         * 检查order表种status的值
         * 1--未支付
         * 2--已支付
         * 3--已发货
         * 4--已支付，但库存不足
         */

        if($order->status != OrderStatusEnum::UNPAID){
            throw new OrderException([
                "msg" => "该订单已经支付",
                "errorCode" => 80003,
                "code" => 400
            ]);
        }

        $this->orderNo = $order->order_no;
        return true;
    }
}