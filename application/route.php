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
Route::get("api/:version/banner/:id", "api/:version.Banner/getBanner");
/*Route::group("sample", function(){
    Route::get(":id", "sample/TestValidate/index");
});*/

//Theme
Route::get("api/:version/theme", "api/:version.Theme/getSimpleList");
Route::get("api/:version/theme/:id", "api/:version.Theme/getComplexOne");

//Product
Route::get("api/:version/product/recent", "api/:version.Product/getRecent");
Route::get("api/:version/product/by_category", "api/:version.Product/getAllByCategoryID");

//分类
Route::get("api/:version/category/all", "api/:version.Category/getAllCategories");