<?php
namespace app\index\controller;

use think\Request;

class Index {
    public function index(Request $request) {
        $all = $request->param();
//        $all = input("param.");
//        $all = Request::instance()->param();
//        $name = Request::instance()->param("name");
//        $age = Request::instance()->param("age");

//        return json(["id"=>$id, "name"=>$name, "age"=>$age]);
        return json($all);
    }
}
