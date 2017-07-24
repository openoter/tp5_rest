# tp5+小程序+微信+RESTful api

+ 服务器端程序：Zerg
+ 客户端小程序：Protoss
+ CMS：Terran

# ThinkPHP 5.x

## 路由

+ PATH_INFO（默认）
+ 混合模式（既可以使用PATH_INFO、也可以使用路由模式）
+ 强制使用路由模式

**PATH_INFO的缺点**

+ 太长
+ URL暴露了服务器的文件结构
+ 不够灵活
+ 不能很好的支持URL的语义化（最大的缺陷）

> 一旦定义了路由之后，原有的`PATH_INFO`模式就会失效

**动态注册**

```php
Route::rule(‘路由表达式’,‘路由地址’,‘请求类型’,‘路由参数（数组）’,‘变量规则（数组）’);
```

### 传递参数

**route.php**
```php
Route::rule("home/:id", "index/index/index");
```

获取传递的参数：

**controller/index.php**

+ 第一种方式

```php
 public function index($id, $name) {
    return json(["id"=>$id, "name"=>$name]);
}
```

+ 第二种方式:Request

文档详见：https://www.kancloud.cn/manual/thinkphp5/118044

分别获取：
```php
public function index() {
    $id = Request::instance()->param("id");
    $name = Request::instance()->param("name");
    $age = Request::instance()->param("age");

    return json(["id"=>$id, "name"=>$name, "age"=>$age]);
}
```

一起获取:

```php
public function index() {
    $all = Request::instance()->param();
    return json($all);
}
```

依赖注入方式：

```php
public function index(Request $request) {
    $all = $request->param();
    return json($all);
}
```

+ 第三种方式： 助手函数`input()`

```php
public function index() {
    $all = input("param.");
    return json($all);
}
```

**访问地址**

```url
http://zerg.com/home/1?name=34
```

## 验证（Validate）

### 独立验证

```php
public function index($id) {
    //验证数据
    $data = [
        'name'=>"vender",
        "email"=>"124@qq.com"
    ];
    // 验证规则
    $v = new Validate([
        'name'=>"require|max:20",
        'email'=>"email"
    ]);
    //验证
    $res = $v->check($data);

    //获取验证错误
    $erro = $v->getError();
}
```

**批量验证**

```php
...
//验证
$res = $v->batch()->check($data);
...
```
### 验证器

**定义验证器**

定义一个`application\sample\validate\UserV.php` ，用于验证`User`的数据：

```php
namespace app\sample\validate;

use think\Validate;
class UserV extends Validate{
    protected $rule = [
        'name'=>"require|max:20",
        'email'=>"email"
    ];
}
```

**使用验证器**

在控制器中使用该验证器：

```php
public function index($id) {
    $data = [
        'name'=>"vender",
        "email"=>"124@qq.com"
    ];
    //第一种方式
//        $v = Loader::validate("UserV");
    //第二种方式：实例化
        $v = new UserV();
        //第三种方式：助手函数
//        $v = validate("UserV");
    $res = $v->check($data);
    return json(["res"=>$res]);
}
```

### 自定义验证规则

新建文件`application\api\validate\IDMustBePositiveInt.php`，在文件中输入内容：

```php
class IDMustBePositiveInt extends Validate{
    protected $rule = [
        "id"=>"require|isPositiveInt"
    ];
    protected function isPositiveInt($value, $rule="", $data="", $field=""){
        if(is_numeric($value) && is_int($value+0) && ($value+0)>0){
            return true;
        }else{
            return $field."必须是正整数";
        }
    }
}
```

# 异常处理

## 常规异常处理

在模型`Banner.php`中，有以下代码：

```php
public static function getBannerById($id) {
    //TODO：根据banner的id获取Banner信息

    try{
        1/0;
    }catch(Exception $e){
        throw $e;
    }
}
```

在控制器中`Banner.php`中有以下代码：

```php
public function getBanner($id){
    $res = Loader::validate('IDMustBePositiveInt')->goCheck();
    try{
        $banner = BannerModel::getBannerById($id);
    } catch (Exception $e){
        $error = [
            "code"=> 10001,
            'msg'=>$e->getMessage()
        ];
        return json($error, 400);
    }
    return json($banner);
}
```
## 封装异常

**常见的异常分类**

+ 用户行为导致的异常（如：用户的输入没有通过验证器，没有查询到结果）
    + 不需要要记录日志记录（不是绝对的，如一个用户频繁的请求）
    + 需要向用户放回具体的错误信息
+ 服务器自身的异常（代码错误，调用外部接口错误）
     + 需要要记录日志记录
     + 不需要向用户放回具体的错误信息


**实现自定义全局异常**

在`application\lib\exception`中新建文件`ExceptionHandler.php`，从写tp5中的`Handler`类：

```php
class ExceptionHandler extends Handle{
    public function render(Exception $e) {
        return json(["error"=>"90```----"]);
    }
}
```
同级目录下，新建`BaseException.php`，并继承`Exception`类，包含以下内容：

```php
/**
 * Class BaseException
 * 自定义异常的基类，用于描述错误码、错误信息
 * @package app\lib\exception
 */
class BaseException extends Exception{
    public $code = 400; //HTTP状态码，如404、202
    public $msg = "参数错误"; //错误的具体信息
    public $errorCode = 10000; //自动错误码
}
```

定义一个类`BannerMissException.php`，继承`BaseException`：

```php
/**
 * Class BannerMissException
 * Banner没有找到的异常
 * @package app\lib\exception
 */
class BannerMissException extends BaseException{
    public $code = 404;
    public $msg = "请求的Banner不存在";
    public $errorCode = 40000;
}
```

然后我们在`config.php`中找到`exception_handle`，并将`ExceptionHandler`所在的命令空间写上：

```php
'exception_handle'       => 'app\lib\exception\ExceptionHandler',
```