<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/5/2
 * Time: 18:24
 */
namespace api\controller;



class base extends \common\controller\basecontroller
{
    public function __construct()
    {
        parent::__construct();
        //检验数据签名校验
        parent::checkAppSign();
        //验证卖家身份状态
        $this->checkSellerLoginStatus();
    }

    /**
     * 检查是否登录
     */
    public function checkSellerLoginStatus()
    {
        checkSellerLoginStatus();
    }


}