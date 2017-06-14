<?php
/**
 * Created by PhpStorm.
 * User: HP
 * Date: 2017/5/17
 * Time: 15:16
 */
namespace seller\controller;
use  seller\controller;

class mobilecode extends base{

    /**
     * 只针对与卖家的 卖家的不用传手机号  用法人的手机号接收信息
     * 发送短信验证码
     * action 为 bank表示添加添加提款账户 银行卡的操作
     * action 为 ali 表示添加添加提款账户 支付宝的操作
     */
    public function index()
    {
        $_GP     = $this->request;
        $service = new \service\seller\StoreShopService();
        $res     = $service->send_mobile_code($_GP);
        if($res){
            ajaxReturnData(1,LANG('COMMON_SMS_SEND_SUCCESS'));
        }else{
            ajaxReturnData(0,$service->getError());
        }
    }
}