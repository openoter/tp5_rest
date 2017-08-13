<?php

namespace app\api\service;
use app\api\model\OrderProduct;
use app\api\model\Product;
use app\api\model\UserAddress;
use app\lib\exception\OrderException;
use app\lib\exception\UserException;
use app\api\model\Order as OrderModel;
use think\Db;
use think\Exception;

/**
 * Class Order
 * 订单服务，用于完成订单相关的逻辑
 * @package app\api\service
 */
class Order {
    //订单商品列表，即用户传递的 products 参数
    protected $oProducts;

    //数据库查询出来的真实商品
    protected $products;

    protected $uid;

    /**
     * Order constructor.
     *
     * @param $oProducts
     */
   /* public function __construct($oProducts) {
        $this->oProducts = $oProducts;
    }*/


    /**
     * 检查库存量、创建订单、生成订单号
     * @param $uid
     * @param $oProducts
     * @return array
     */
    public function place($uid, $oProducts){
        //将$oProducts和$products进行对比
        $this->oProducts = $oProducts;
        $this->uid = $uid;
        $this->products = $this->getProductsByOrder($this->oProducts);

        $status = $this->getOrderStatus();

        //检查库存量是否通过
        if(!$status['pass']){
            $status['order_id'] = -1; //表示创建失败
            return $status;
        }
        //创建订单
        $snapStatus = $this->snapOrder($status);
        $order = $this->createOrder($snapStatus);
        $order['pass'] = true;
        return $order;
    }

    /**
     * 创建订单，返回订单号
     * @param $snap
     * @return array
     * @throws Exception
     */
    private function createOrder($snap){
        Db::startTrans(); //开启事务
        try{

            //订单号
            $orderNo = $this->makeOrderNo();
            $order = new OrderModel();
            $order->user_id = $this->uid;
            $order->order_no = $orderNo;
            $order->total_price = $snap["orderPrice"];
            $order->snap_img = $snap["snapImg"];
            $order->snap_name = $snap["snapName"];
            $order->total_count = $snap["totalCount"];
            $order->snap_address = $snap["snapAddress"];
            $order->snap_items = json_encode($snap['pStatus']);

            $order->save();

            $orderId = $order->id;
            $create_time = $order->create_time;

            //&$p要对p数组进行修改，就必须加上引用符号
            foreach($this->oProducts as &$p){
                $p['order_id'] = $orderId;
            }

            $orderProduct = new OrderProduct();
            //保存数据到数据库
            $orderProduct->saveAll($this->oProducts);
            Db::commit(); //提交事务
            return [
                'order_no' => $orderNo,
                'order_id' => $orderId,
                'create_time' => $create_time
            ];
        }catch(Exception $e){
            Db::rollback(); //事务的回滚
           throw $e;
        }
    }

    /**
     * 生成订单号
     * @return string
     */
    public static function makeOrderNo(){
        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        $orderSn =
            $yCode[intval(date('Y')) - 2017] . strtoupper(dechex(date('m'))) . date(
                'd') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf(
                '%02d', rand(0, 99));
        return $orderSn;
    }

    /**
     * 生成订单快照信息
     * @param $status
     * @return array
     * @throws UserException
     */
    private function snapOrder($status){
        // 快照
        $snap = [
            'orderPrice' => 0, //订单的总价格
            'totalCount' => 0, //订单商品的总数量
            'pStatus' => [], //订单下所有商品的状态
            'snapAddress' => null, //订单的收货地址
            'snapName' => '', //订单的名字
            'snapImg' => '' //订单的封面图
        ];

        $snap['orderPrice'] = $status['orderPrice'];
        $snap['totalCount'] = $status['totalCount'];
        $snap['pStatus'] = $status['pStatusArray'];

        //存储一个对象，将对象转换成字符串，最好的方法使用NoSql——mongodb（市场作为关系型数据库的补充而存在）
        $snap['snapAddress'] = json_encode($this->getUserAddress());
        //默认取第一个商品的名字
        $snap['snapName'] = $this->products[0]['name'];
        $snap['snapImg'] = $this->products[0]['main_img_url'];

        /**
         * 如果订单中不止一个商品则加一个等
         */
        if(count($this->products) > 1){
            $snap['snapName'] .= "等";
        }

        return $snap;
    }

    /**
     * 根据uid获取用户的收货地址
     * @return array
     * @throws UserException
     */
    private function getUserAddress(){
        $userAddress = UserAddress::where('user_id', "=", $this->uid)
        ->find();
        if(!$userAddress){
            throw new UserException([
                'msg' => "用户地址不存在，下单失败",
                "errorCode" => 60001
            ]);
        }

        return $userAddress->toArray();
    }

    /**
     * 根据订单信息查询真实的商品信息
     * @param $oProducts
     * @return mixed
     */
    public function getProductsByOrder($oProducts){
        /**
         * 用循环或重复查询数据库，解决办法：
         *
         * 我们使用循环将id给获取出来，然后存放在一个数组中，再去查询数据库
         */
        /*foreach($oProducts as $oProduct){

        }*/

        //1. 获取所有的id
        $oPIDs = [];
        foreach($oProducts as $item){
            array_push($oPIDs, $item['product_id']);
        }


        //利用id查询数据（这里需要模型返回集合才能使用visible()、toArray()方法）
        $products = Product::all($oPIDs)
            ->visible(['id', 'price', 'stock', 'name', 'main_img_url'])
            ->toArray();
        return $products;
    }


    /**
     * 获取订单的状态
     * @return array
     * @throws OrderException
     */
    private function getOrderStatus(){
        $status = [
            "pass" => true, //检查库存量是否通过
            "orderPrice" => 0, //商品的总价格
            "totalCount" => 0, //总数
            "pStatusArray" => [] //保存所有订单的详细信息，如历史订单
        ];

        /**
         * 检查每个订单的库存量、计算价格
         */
        foreach($this->oProducts as $oProduct){
            $pStatus = $this->getProductStatus(
                $oProduct['product_id'], $oProduct['count'], $this->products);

            /**
             * 有一个订单不通过，则这个订单检查库存不通过
             */
            if(!$pStatus['haveStock']){
                $status['pass'] = false;
            }
            $status['orderPrice'] += $pStatus['totalPrice'];
            $status['totalCount'] += $pStatus['count'];
            array_push($status['pStatusArray'], $pStatus);
        }

        return $status;
    }

    /**
     * 查看商品的状态
     * @param $oPID 传过来的商品id
     * @param $oCount 购买的商品总数
     * @param $products 购买的商品数据详情
     * @return array
     * @throws OrderException
     */
    private function getProductStatus($oPID, $oCount, $products){
        $pIndex = -1; //序号，$oPID在$products（数据库插查询出来的）中的序号
        /**
         * 历史订单中的订单详情数据
         */
        $pStatus = [
            "id" => null, //商品id
            "haveStock" => false, //是否有库存量
            "count" => 0, //库存数量
            "name" => "", //商品的名字
            "totalPrice" => 0 //当前订单某一类商品的单价*数量
        ];

        //获取商品在数据库查询数据的序号
        for ($i=0; $i<count($products); $i++){
            //判断是否存在
            if($oPID == $products[$i]["id"]){
                $pIndex = $i;
            }
        }

        //判断订单是否找到（误传、下架），如果没找到需要抛出异常
        if($pIndex == -1){
            //用户传递的product_id不存在
            throw new OrderException([
                "msg" => "ID为".$oPID."的商品不存在，创建订单失败"
            ]);
        }else{
            $product = $products[$pIndex];
            $pStatus['id'] = $product['id'];
            $pStatus['count'] = $oCount;
            $pStatus['name'] = $product['name'];
            $pStatus['totalPrice'] = $product['price'] * $oCount;
            //有库存
            if($product['stock'] - $oCount > 0){
                $pStatus['haveStock'] = true;
            }
        }

        return $pStatus;
    }

    /**
     * 检查商品的库存量
     * @param $orderId 订单id
     * @return array
     */
    public function checkOrderStock($orderId){
        $oProduct = OrderProduct::where('order_id', '=', $orderId)
            ->select();
        $this->oProducts = $oProduct;

        $this->products = $this->getProductsByOrder($oProduct);

        $status = $this->getOrderStatus();
        return $status;
    }
}