<?php
$member = get_member_account(true,true);
$openid = $member['openid'];
$orderid = intval($_GP['orderid']);
$order = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE id = :id and openid =:openid", array(
    ':id' => $orderid,
    ':openid' => $openid
));

if (empty($order['id'])) {
    message("未找到相关订单");
}
if ($_GP['isok'] == '1' && $order['paytypecode'] == 'weixin') {
    message('支付成功！', WEBSITE_ROOT . mobile_url('myorder'), 'success');
}
if ($order['status'] > 0) {
    message('抱歉，您的订单已经付款或是被关闭，请重新进入付款！', mobile_url('myorder'), 'error');
}

// 余额付款直接完成作业
if ($order['price'] <= 0){
    if($order['status']==0){
         mysqld_update('shop_order', array('status'=>1), array('id' => $orderid, 'openid' => $openid));
		 paySuccessProcess($order);	//支付成功后的处理
         message('支付成功！',WEBSITE_ROOT.mobile_url('myorder'),'success');
        exit;
    }
}

$ordergoods = mysqld_select("SELECT goodsid FROM " . table('shop_order_goods') . " WHERE orderid = '{$orderid}'");
$dish       = mysqld_select("select title from ".table('shop_dish')." where id={$ordergoods['goodsid']}");
$goodtitle  = str_replace("&",'',$dish['title']);

$paytypecode = $order['paytypecode'];
require (WEB_ROOT . '/system/modules/plugin/payment/' . $paytypecode . '/payaction.php');
exit();