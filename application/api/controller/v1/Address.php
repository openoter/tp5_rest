<?php

namespace app\api\controller\v1;


use app\api\model\User as UserModel;
use app\api\service\Token as TokenService;
use app\api\validate\AddressNew;
use app\lib\exception\SuccessMessage;
use app\lib\exception\UserException;

class Address {
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
        return json(new SuccessMessage(), 201);
    }
}