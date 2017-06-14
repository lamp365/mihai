<?php
/**
 * Created by PhpStorm.
 * User: HP
 * Date: 2017/5/17
 * Time: 15:16
 */
namespace api\controller;
use  api\controller;

class mobilecode extends homebase{

    /**
     * 只针对与卖家的 卖家的不用传手机号  用法人的手机号接收信息
     * 发送短信验证码
     * action 为 bank表示添加提款账户或修改提款账户
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