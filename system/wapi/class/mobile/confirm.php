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
       $_GP =  $this->request;
       $memInfo  = get_member_account();
       $service  = new \service\wapi\mycartService();
       $cart_where = "to_pay=1";
       $cartlist   = $service->cartlist($cart_where,1);
       //获取优惠卷和默认地址
       $defaultAddress   =   mysqld_select("SELECT * FROM " . table('shop_address') . " WHERE openid ='{$memInfo['openid']}'  and isdefault =1 and  deleted = 0 ");
       $cartlist['default_address'] = $defaultAddress;
       //去除过期商品对象，在清单结算页 不需要
       unset($cartlist['out_gooslist']);
       ajaxReturnData(1,'请求成功',$cartlist);
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