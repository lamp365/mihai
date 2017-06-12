<?php
namespace shopwap\controller;

class success extends \common\controller\basecontroller
{
   public function index()
   {
      $_GP = $this->request;
      $success = new \LtCookie();
      $order =  $success->getCookie('success');
      if ( !empty($order) ){
         $order = unserialize($order);
      }
      include themePage('noidentity');
   }

   public function afterPay()
   {
      $mem = get_member_account();
      $_GP = $this->request;
      $orderId = $_GP['orderid'];
      if(empty($orderId)){
         logRecord("支付成功后，后续操作失败(openid:{$mem['openid']})",'afterPay');
         die();
      }
      $order = mysqld_select("select * from ".table('shop_order')." where id={$orderId}");
      paySuccessProcess($order);
   }
}
