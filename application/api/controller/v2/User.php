<?php

namespace app\api\controller\v2;


use app\api\model\DocUser;

class User {

    public function create(){
        $res = DocUser::addUser();
        return json($res);
    }

    public function login(){
        $res = DocUser::loginUser();
        return json($res);
    }
}