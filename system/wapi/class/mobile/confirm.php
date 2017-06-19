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
        $meminfo = get_member_account();
        $setting = globaSetting();
        $payment = mysqld_select("SELECT * FROM " . table('payment') . " WHERE  enabled=1 and code='weixin' limit 1");
        $configs = unserialize($payment['configs']);

        $appid  = $setting['xcx_appid'];//小程序appid
        $openid = $meminfo['weixin_openid'];  //个人要支付的openid
        $mch_id = $configs['weixin_pay_mchId'];
        $key    = $configs['weixin_pay_paySignKey'];

        $weixinpay = new \service\wapi\wxpayService($appid,$openid,$mch_id,$key);
        $return    = $weixinpay->pay();
        ajaxReturnData(1,'操作成功!',$return);
    }
}