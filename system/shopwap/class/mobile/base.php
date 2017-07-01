<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/4/19
 * Time: 11:52
 * 基类，用于操作一些父类的东西 如登录授权  权限控制
 */
namespace shopwap\controller;


class base extends \common\controller\basecontroller{

    public function __construct()
    {
        parent::__construct();
        $weixinAuth = new \service\shopwap\weixinAuthService();
        $user_wx_openid = $weixinAuth->GetOpenid(); //授权获取openid以及微信用户信息
    }

}
