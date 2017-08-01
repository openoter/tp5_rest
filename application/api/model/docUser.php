<?php
/**
 * Created by PhpStorm.
 * User: marmo
 * Date: 2017/8/1
 * Time: 22:04
 */

namespace app\api\model;


use app\lib\exception\ParameterException;
use think\Request;
use think\Validate;

class DocUser extends BaseModel{

    public static function addUser(){
        $all = Request::instance()->param();
        $v = new Validate([
            "email"=>"require|email",
            "username"=>"require|max:15"
        ]);

        if(!$v->check($all)){
            throw new ParameterException([
                "msg"=>$v->getError()
            ]);
        }

        $res = self::findByEmail($all["email"]);

        if($res){
            throw new ParameterException([
                "msg"=> "该邮箱已经被注册"
            ]);
        } else{
            $d = [
                "username"=>$all["username"],
                "email"=>$all["email"],
                "password"=>md5($all["password"])
            ];

            $u = new DocUser();
            $user = $u->allowField(["username", "email", "password"])->save($d);


            if(!$user){
                throw new ParameterException([
                    "msg"=>"数据插入失败"
                ]);
            }else{
                $data  = [
                    "code"=>200,
                    "data"=>true,
                    "msg"=>"插入成功"
                ];
                return $data;
            }
        }


    }



    private static function findByEmail($email){
        $res =  self::where("email", "=", $email)->find();
        if($res){
            return $res;
        }else{
            return false;
        }
    }

    public static function loginUser(){
        $all = Request::instance()->param();
        $v = new Validate([
            "email"=>"require|email",
            "password"=>"require"
        ]);

        if(!$v->check($all)){
            throw new ParameterException([
                "msg"=>$v->getError()
            ]);
        }

        $res = self::findByEmail($all["email"]);

        if($res){
            $res = $res->toArray();
        }
        $pwd = $res["password"];

        if($pwd === md5($all["password"])){
            $data  = [
                "code"=>200,
                "data"=>true,
                "msg"=>"身份验证成功"
            ];
            return $data;
        }

    }
}