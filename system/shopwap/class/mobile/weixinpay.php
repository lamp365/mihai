<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/6/22
 * Time: 16:28
 */
namespace shopwap\controller;

class weixinpay extends \common\controller\basecontroller
{
    /**
     * 微信（即时到帐）
     * @param string $ordersn 订单号
     */
    public function pay() {
        $pay_ordersn = 'sn54654864'.uniqid();
        $pay_money   = '1';
        $pay_title   = str_replace("'", '‘', '测试商品');

        $pay_data = array(
            'out_trade_no'  => $pay_ordersn, //订单号
            'total_fee'     => $pay_money, //订单金额，单位为分
            'body'          => $pay_title,
        );
        $pay = new \service\shopwap\weixinpayService();
        $result = $pay->weixinpay($pay_data);
        if (!$result) {
            message($pay->getError());
        }

        $cfg = globaSetting();
        //如果是PC端那么返回的是一段 扫码地址  如果是小程序或者微信端返回一个数组参数
        include themePage('weixinpay');
    }

    /**
     * 服务器异步通知页面方法
     */
    function notifyurl()
    {
        $pay  = new \service\shopwap\weixinpayService();
        $data = $pay->checkCallParame();
        if(!$data){
            ajaxReturnData(0,$pay->getError());
        }
        $result = $pay->notify_weixinpay($data);
        if($result){
            ajaxReturnData(1,'支付成功','success');
        }else{
            ajaxReturnData(0,'支付失败','fail');
        }
    }

    /**
     * 同步通知页面跳转处理方法
     */
    function native_notify()
    {
        $pay  = new \service\shopwap\weixinpayService();
        $data = $pay->checkCallParame();
        if(!$data){
            ajaxReturnData(0,$pay->getError());
        }
        $result = $pay->native_notify($data);
        if($result) {
            message('支付成功！',mobile_url('myorder',array('name'=>'shopwap')),'success');
        } else {
            message($pay->getError());
        }
    }

}