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
    public function __construct()
    {
        parent::__construct();
        if(!checkIsLogin()){
            ajaxReturnData(0,'请授权登录！');
        }
    }

    /**
     * 清单列表
     */
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

    /**
     * 从结算页进行提交订单结算
     参数 array(
                 address_id  => 2
                 bonus  => '2_68,3_89'  //表示店铺2 优惠卷 68  店铺3优惠卷89
     * )
     */
    public function topay()
    {
        $_GP =  $this->request;
        $meminfo = get_member_account();
        $setting = globaSetting();
        $payment = mysqld_select("SELECT * FROM " . table('payment') . " WHERE  enabled=1 and code='weixin' limit 1");
        $configs = unserialize($payment['configs']);

        $appid  = $setting['xcx_appid'];//小程序appid
        $openid = $meminfo['weixin_openid'];  //个人要支付的openid
        $mch_id = $configs['weixin_pay_mchId'];
        $key    = $configs['weixin_pay_paySignKey'];

        $weixinpay = new \service\wapi\wxpayService($appid,$openid,$mch_id,$key);
        //插入订单的信息
        $res_data = $weixinpay->insertOrder($_GP);
        if(!$res_data){
            ajaxReturnData(0,$weixinpay->getError());
        }

        $return    = $weixinpay->pay($res_data['pay_ordersn'],$res_data['pay_total_money'],$res_data['pay_title']);
        if(!$return){
            ajaxReturnData(0,$weixinpay->getError());
        }else{
            ajaxReturnData(1,'操作成功!',$return);
        }
    }

    /**
     * 从个人中心中继续完成支付   参数orderid
     */
    public function payorder()
    {
        $_GP =  $this->request;
        $meminfo = get_member_account();
        $setting = globaSetting();
        $payment = mysqld_select("SELECT * FROM " . table('payment') . " WHERE  enabled=1 and code='weixin' limit 1");
        $configs = unserialize($payment['configs']);

        $appid  = $setting['xcx_appid'];//小程序appid
        $openid = $meminfo['weixin_openid'];  //个人要支付的openid
        $mch_id = $configs['weixin_pay_mchId'];
        $key    = $configs['weixin_pay_paySignKey'];

        $weixinpay = new \service\wapi\wxpayService($appid,$openid,$mch_id,$key);
        $res_data  = $weixinpay->getPayOrder($_GP['orderid']);
        $return    = $weixinpay->pay($res_data['pay_ordersn'],$res_data['pay_total_money'],$res_data['pay_title']);
        if(!$return){
            ajaxReturnData(0,$weixinpay->getError());
        }else{
            ajaxReturnData(1,'操作成功!',$return);
        }
    }

}