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

