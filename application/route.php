<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

/*return [
    '__pattern__' => [
        'name' => '\w+',
    ],
    '[hello]'     => [
        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/hello', ['method' => 'post']],
    ],

];*/

use think\Route;

//Route::get("home/:id", "index/index/index");

//Route::group("banner", function(){
//    Route::get(":id", 'api/v1.Banner/getBanner');
//});
Route::get("api/v1/banner/:id", "api/v1.Banner/getBanner");
/*Route::group("sample", function(){
    Route::get(":id", "sample/TestValidate/index");
});*/
