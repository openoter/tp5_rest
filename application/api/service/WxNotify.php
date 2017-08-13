<?php

namespace app\api\service;

use app\api\model\Order as OrderModel;
use app\api\model\Product;
use app\api\service\Order as OrderService;
use app\lib\enum\OrderStatusEnum;
use think\Db;
use think\Exception;
use think\Loader;
use think\Log;

//导入微信SDK类
Loader::import('WxPay.WxPay', EXTEND_PATH, '.Api.php');

/**
 * Class WxNotify
 * 微信Notify
 * @package app\api\service
 */
class WxNotify extends \WxPayNotify{

    /**
     * notify回调方法
     * @param array $data
     * @param string $msg
     * @return bool
     */
    public function NotifyProcess($data, &$msg) {
        if($data['result_code'] == "SUCCESS"){
            $orderNo = $data['out_trade_no'];
            /**
             * 为了防止微信支付时超时，所以使用事务
             */
            Db::startTrans(); //开启事务
            try{ //支付成功
                $order = OrderModel::where('id', '=', $orderNo)
                    ->lock(true) //锁
                    ->find();

                //只有当status为1时，才进行支付
                if($order->status == 1){
                    $service = new OrderService();
                    $stockStatus = $service->checkOrderStock($order->id);
                    if($stockStatus["pass"]){
                        //更新状态
                        $this->updateOrderStatus($order->id, true);
                        //减少库存
                        $this->reduceStock($stockStatus);
                    }else{ //没有通过库存量检测
                        $this->updateOrderStatus($order->id, false);
                    }
                }
                Db::commit(); //提交事务
                return true;
            }catch(Exception $e){ //支付失败
                Log::record($e);
                Db::rollback(); //事务回滚
                return false;
            }
        }else{
            return true;
        }
    }

    /**
     * 更新订单状态
     * @param $orderId
     * @param $success
     */
    private function updateOrderStatus($orderId, $success){
        $status = $success ? OrderStatusEnum::PAID : OrderStatusEnum::PAID_BUT_OUT_OF;

        OrderModel::where('id', "=", $orderId)
            ->update(['status'=>$status]);
    }

    /**
     * 消减库存量
     * @param $stockStatus
     */
    private function reduceStock($stockStatus){
        foreach ($stockStatus['pStatusArray'] as $singleStatus){
            Product::where('id', '=', $singleStatus['id'])
                ->setDec('stock', $singleStatus['count']); //直接对数据库的字段进行减法
        }
    }
}