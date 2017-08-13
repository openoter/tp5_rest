# 报错“Call to a member function eagerlyResult() on null”

原因：关联模型时没有返回，如：

```php
public function properties(){
    $this->hasMany("ProductProperty", "product_id", "id");
}
```

修改：

```php
public function properties(){
    return $this->hasMany("ProductProperty", "product_id", "id");
}
```

# 使用ORM怎么实现对子表的数据进行排序？

问题：
```php
public static function getProductDetail($id) {
    $res = self::with("imgs.imgUrl, properties")
        ->find($id);

    return $res;
}
```
上面的代码是没办法对`ProductImage`中的数据排序的，即不能对子表中的字段进行排序。那么什么解决办法呢？

办法：

```php
public static function getProductDetail($id) {
    $res = self::with(["imgs"=>function($query){
        $query->with("imgUrl")
            ->order("order", "asc");
    }])
        ->with(["properties"])
        ->find($id);

    return $res;
}
```

# 怎么让接口拥有访问权限（token的具体用法）

根据用户的uid来判断他是否有权限：

实例：

```php
public function createOrUpdateAddress(){
    $v = new AddressNew();
    $v->goCheck();
    /**
     * 1. 根据Token获取uid
     * 2. 根据uid查询数据，判断用户是否存在，如果不存在则抛出异常
     * 3. 获取从客户端提交的地址信息
     * 4. 根据用户地址信息是否存在，判断是添加还是更新地址
     */
    //1. 根据Token获取uid
    $uid = TokenService::getCurrentUid();

    //2. 根据uid查询数据，判断用户是否存在，如果不存在则抛出异常
    $user = UserModel::get($uid);
    if(!$user){
        throw new UserException();
    }

    //3. 获取从客户端提交的地址信息

    /**
     * 为什么这里不能直接使用$all = Request::instance()->param()？
     * 直接保存有可能被用户传递过来的数据直接覆盖掉（不需要的字段会直接覆盖掉）
     */
    $dataArr = $v->getDataByRules(input("post."));
    //4. 根据用户地址信息是否存在，判断是添加还是更新地址
    $userAddress = $user->address;
    if(!$userAddress){ //新增
        $user->address()->save($dataArr);
    }else{ //更新
        $user->address->save($dataArr);
    }
    return new SuccessMessage();
}
```

## 怎么根据id直接查询到数据

```php
//2. 根据uid查询数据，判断用户是否存在，如果不存在则抛出异常
$user = UserModel::get($uid);
if(!$user){
    throw new UserException();
}
```

# 更新、删除、创建的接口应该返回什么值？

在表中的`REST`中直接把模型返回。

```php
public function createOrUpdateAddress(){
    //...
    $user = UserModel::get($uid);
    if(!$user){
        throw new UserException();
    }
    //...
    $userAddress = $user->address;
    if(!$userAddress){ //新增
        $user->address()->save($dataArr);
    }else{ //更新
        $user->address->save($dataArr);
    }
    return json($user);
}
```

另外一种思路：

返回整个模型客户端很多时候是用不到的，我们只需要返回成功的消息就行了。

```php
return new SuccessMessage();
```

SuccessMessage.php

```php
/**
 * Class SuccessMessage
 * 删除、更新、增加成功后消息
 * @package app\lib\exception
 */
class SuccessMessage extends BaseException{
    /**
     * 201: 创建、更新成功
     * 202: 需要一个异步的处理才能完成请求
     */
    public $code = 201;
    public $msg = "ok";
    public $errorCode = 0;
}
```

# 返回json对象时怎么返回指定的状态码？

```php
return json(new SuccessMessage(), 201);
```

只需要在后面加上指定的状态码就行了。

# 检查权限应该使用什么方法？

在`thinkphp`中有一个前置方法，我们可以在前置方法中区检测权限，不过控制器需要继承`controller`。

```php
class Address extends Controller{

    protected $beforeActionList = [
        "checkPrimaryScope"=> ["only"=>"createOrUpdateAddress"]
    ];

    /**
     * 检测初级权限
     */
    protected function checkPrimaryScope(){
        $scope = TokenService::getCurrentTokenVar('scope');
        //判断token是否存在
        if($scope){
            //判断是有权限
            if($scope>= ScopeEnum::USER) {
                return true;
            }else{
                throw new ForbiddenException();
            }
        }else{
            throw new TokenException();
        }

    }
    public function createOrUpdateAddress(){
    //...
    }
}
```

# 在`thinkphp`中，复杂类型是数据怎么验证？

在服务器端校验如下格式的数据：

```php
protected $data = [
    [
        "productId" => 1,
        "count" => 3,
    ],
    [
        "productId" => 2,
        "count" => 3,
    ]
];
```

思路：

+ 首先验证数据是否为数组；
+ 再验证单个商品的数据是否符合规范。

代码：

```php
class OrderPlace extends BaseValidate{
    protected $rule = [
        "products" => "checkProducts"
    ];

    /**
     * 单个商品的验证规则
     * @type array
     */
    protected $singRule = [
        "product_id" => "require|isPositiveInteger",
        "count" => "require|isPositiveInteger"
    ];

    /**
     * 检测订单列表的数据是否符合规范
     * @param $value
     * @throws ParameterException
     */
    protected function checkProducts($value){
        //判断是否为数组
        if(!is_array($value)){
            throw new ParameterException([
                "msg" => "商品参数不正确"
            ]);
        }
        //判断是否为空
        if(empty($value)){
            throw new ParameterException([
                "msg" => "商品列表不能为空"
            ]);
        }
        foreach($value as $val){
            //验证单个商品
            $this->checkProduct($val);
        }

    }

    /**
     * 检查单个商品的合法性
     * @param $val
     * @throws ParameterException
     */
    private function checkProduct($val){
        //使用验证器
        $validate = new Validate($this->singRule);
        $result = $validate->check($val);
        if(!$result){
            throw new ParameterException([
                "msg" => "商品列表参数错误"
            ]);
        }
    }
}
```

# 如何生成订单号

```php
public static function makeOrderNo(){
    $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
    $orderSn =
        $yCode[intval(date('Y')) - 2017] . strtoupper(dechex(date('m'))) . date(
            'd') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf(
            '%02d', rand(0, 99));
    return $orderSn;
}
```

# `Thinkphp5`怎么自动写入时间？

我们只需要在在模型中加入以下代码：

tp5会自动检测是否是插入操作，如果是那么就会自动插入create_time，如果数据库中的字段并不是create_time，那么需要修改`$createTime`的值。

```php
protected $autoWriteTimestamp = true;
```

# `Thinkphp5`中怎么使用事务？

事务：

数据完整性，一致性
```php
private function createOrder($snap){
        Db::startTrans(); //开启事务
        try{

            //...
            Db::commit();
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
```

# TP5中接口怎么做分页查询

tp5中提供了一个`paginate()`方法，我们可以通过它来获取分页数据。