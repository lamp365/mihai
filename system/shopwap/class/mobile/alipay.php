<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/6/22
 * Time: 16:28
 */
namespace shopwap\controller;

class alipay extends \common\controller\basecontroller
{
    /**
     * 支付宝支付（即时到帐）
     * @param string $ordersn 订单号
     */
    public function pay() {
        $pay_data = array(
//            'notify_url'  => WEBSITE_ROOT.'notify/alipay_notify.php',      //服务器异步通知页面路径
            'notify_url'    => mobile_url('alipay',array('name'=>'shopwap','op'=>'notifyurl')),        //服务器异步通知页面路径
//            'return_url'  => WEBSITE_ROOT.'notify/alipay_return_url.php', //页面跳转同步通知页面路径
            'return_url'    => mobile_url('alipay',array('name'=>'shopwap','op'=>'returnurl')), //页面跳转同步通知页面路径
            'out_trade_no'  => 'sn099239283879'.uniqid(), //订单号
            'subject'       => 'sn099239283879'.uniqid(),  //标题
            'total_fee'     => '0.01', //订单金额，单位为元
            'body'          => str_replace("'", '‘', '测试商品'),
            'show_url'      => WEBSITE_ROOT,  //商品展示地址 通过支付页面的表单进行传递
        );
        $pay = new \service\shopwap\alipayService();
        $result = $pay->alipay($pay_data);
        if (!$result) {
            message($pay->getError());
        }
        die($result);
    }

    /**
     * 服务器异步通知页面方法
     */
    function notifyurl()
    {
        $pay = new \service\shopwap\alipayService();
        $result = $pay->notify_alipay();
        if($result){
            ajaxReturnData(1,'支付成功','success');
        }else{
            ajaxReturnData(0,'支付失败','fail');
        }
    }

    /**
     * 同步通知页面跳转处理方法
     */
    function returnurl()
    {
        $pay = new \service\shopwap\alipayService();
        $result = $pay->return_alipay();
        if($result) {
            message('支付成功！',mobile_url('myorder',array('name'=>'shopwap')),'success');
        } else {
            message($pay->getError());
        }
    }

}