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

       //根据购物车中的商品 获取店铺 查看是否店铺属于全球购，是的话，需要身份证
       $sts_id_arr = array();
       foreach($cartlist['goodslist'] as $catone){
           $sts_id_arr[] = $catone['sts_id'];
       }
       $need_identy = $service->checkCarStoreIsNeedIdenty($sts_id_arr);

       //获取默认地址
       $defaultAddress   =   mysqld_select("SELECT * FROM " . table('shop_address') . " WHERE openid ='{$memInfo['openid']}'  and isdefault =1 and  deleted = 0 ");
       if($need_identy == 1 && empty($defaultAddress['idnumber'])){
           //如果本次默认收货地址  没有身份证 但是有全球购的产品，就不给默认收货地址
           $defaultAddress = array();
       }
       $cartlist['default_address'] = $defaultAddress;
       $cartlist['need_identy']     = $need_identy;
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
        $weixinpay    = new \service\shopwap\weixinpayService(1);
        $orderservice = new \service\shopwap\payorderService();
        //插入订单的信息
        $res_data     = $orderservice->insertOrder($_GP);
        if(!$res_data){
              ajaxReturnData(0,$orderservice->getError());
        }

        if($res_data['pay_total_money'] == 0){
            //不用发起微信支付 用户支付金额为0  直接程序完成支付
            $seting = globaSetting();
            foreach($res_data['pay_orderid'] as $orderid){
                paySuccessProcess($orderid,$seting);
            }
            //告诉前端不用向微信发起支付
            ajaxReturnData(1,'操作成功!',array('nopay'=>1));
        }

        $pay_data = array(
            'out_trade_no'  => $res_data['pay_orderid'], //订单号
            'total_fee'     => $res_data['pay_total_money'], //订单金额，单位为分
            'body'          => $res_data['pay_title'],
        );
        $result = $weixinpay->weixinpay($pay_data);
        if (!$result) {
            ajaxReturnData(0,$weixinpay->getError());
        }else{
            ajaxReturnData(1,'操作成功!',$result);
        }
    }


    /**
     * 从个人中心中继续完成支付   参数orderid
     */
    public function payorder()
    {
        $_GP =  $this->request;
        $weixinpay    = new \service\shopwap\weixinpayService(1);
        $orderservice = new \service\shopwap\payorderService();
        //获取要支付的订单
        $res_data  = $orderservice->getPayOrder($_GP['orderid']);
        $pay_data = array(
            'out_trade_no'  => $res_data['pay_orderid'], //订单号
            'total_fee'     => $res_data['pay_total_money'], //订单金额，单位为分
            'body'          => $res_data['pay_title'],
        );
        $return    = $weixinpay->weixinpay($pay_data);
        if(!$return){
            ajaxReturnData(0,$weixinpay->getError());
        }else{
            ajaxReturnData(1,'操作成功!',$return);
        }
    }


    /**
     * 临时使用 完成订单支付，上线后，去掉该接口，只是为了开发
     * 金额可以不用分来支付，测试通道支付来完成支付
     */
    public function surepay()
    {
        $_GP =  $this->request;
        $orderid = $_GP['orderid'];
        if(empty($orderid)){
            ajaxReturnData(0,'参数有误！');
        }
        $set     = globaSetting();
        paySuccessProcess($orderid,$set);
        ajaxReturnData(1,'已确认支付！');
    }
}