<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/4/20
 * Time: 18:39
 */

namespace wapi\controller;

class confirm extends base
{
   public function index()
   {

   }

    public function topay()
    {
        $setting = globaSetting();
        $payment = mysqld_select("SELECT * FROM " . table('payment') . " WHERE  enabled=1 and code='weixin' limit 1");
        $configs = unserialize($payment['configs']);
//        $appid  = $setting['weixin_appId'];
        $appid  = 'wxee3d6d279578322b';//小程序appid
        $openid = 'oxDr-0ObKhg0Ly52XMpR07WxouLE';  //个人要支付的openid
        $mch_id = $configs['weixin_pay_mchId'];
        $key    = $configs['weixin_pay_paySignKey'];

        $weixinpay = new \service\wapi\wxpayService($appid,$openid,$mch_id,$key);
        $return    = $weixinpay->pay();
        ajaxReturnData(1,'操作成功!',$return);
    }
}