<?php
namespace app\sample\controller;

use app\sample\validate\UserV;
use think\Loader;
use \think\Validate;
class TestValidate {
    public function index($id) {
        $data = [
            'name'=>"vender",
            "email"=>"124@qq.com"
        ];

//        $v = new Validate([
//            'name'=>"require|max:20",
//            'email'=>"email"
//        ]);
//        $v = Loader::validate("UserV");
            $v = new UserV();
//        $v = validate("UserV");
        $res = $v->check($data);
        return json(["res"=>$res]);
    }
}