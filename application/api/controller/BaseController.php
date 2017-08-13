<?php
namespace app\api\controller;


use app\api\service\Token as TokenService;

use think\Controller;

/**
 * Class BaseController
 * 控制器基类
 */
class BaseController extends Controller{
    /**
     *  用户专用的权限（管理员不能使用权限，如订单）
     * @throws \app\lib\exception\ForbiddenException
     * @throws \app\lib\exception\TokenException
     */
    protected function checkExclusiveScope(){
        /*$scope = Token::getCurrentTokenVar('scope');
        //判断token是否存在
        if($scope){
            //判断是有权限（用户专用权限）
            if($scope == ScopeEnum::USER) {
                return true;
            }else{
                throw new ForbiddenException();
            }
        }else{
            throw new TokenException();
        }*/
        TokenService::needExclusiveScope() ;
    }

    /**
     * 检查用户的权限(普通用户或者管理员)
     * @throws \app\lib\exception\ForbiddenException
     * @throws \app\lib\exception\TokenException
     */
    protected function checkPrimaryScope(){
        TokenService::needPrimaryScope();
    }

    /**
     * 超级管理员
     */
    protected function checkSuperScope(){

    }
}