<?php
$member = get_member_account();
$openid = $member['openid'];
$orderid = intval($_GP['orderid']);
$order = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE id = :id and openid =:openid", array(
    ':id' => $orderid,
    ':openid' => $openid
));
$goodsstr = "";
if (empty($order['id'])) {
    message("未找到相关订单");
}
if ($_GP['isok'] == '1' && $order['paytypecode'] == 'weixin') {
    message('支付成功！', WEBSITE_ROOT . mobile_url('myorder'), 'success');
}
if (($order['paytype'] != 3 && $order['status'] > 0) && (! ($order['paytype'] == 3 && $order['status'] == 1))) {
    message('抱歉，您的订单已经付款或是被关闭，请重新进入付款！', mobile_url('myorder'), 'error');
}

// 余额付款直接完成作业
if ($order['price'] <= 0){
    if($order['status']==0){
         mysqld_update('shop_order', array('status'=>1), array('id' => $orderid, 'openid' => $openid));
		 paySuccessProcess($order);	//支付成功后的处理
		 require_once WEB_ROOT.'/system/shopwap/class/mobile/order_notice_mail.php';  
		 mailnotice($orderid);
        if($order['ifcustoms'] != 2){
            //上传清关材料
            $order_cookie =  new LtCookie();
            $order_cookie->setCookie('success', serialize($order));
            message('支付成功！',WEBSITE_ROOT.mobile_url('success'),'success');
        }else{
            message('支付成功！',WEBSITE_ROOT.mobile_url('myorder',array('status'=>99)),'success');
        }

        exit;
    }
}
$ordergoods = mysqld_selectall("SELECT goodsid,shopgoodsid,optionid,total FROM " . table('shop_order_goods') . " WHERE orderid = '{$orderid}'");
if (! empty($ordergoods)) {
    $goodsids = array();
    foreach ($ordergoods as $gooditem) {
        $goodsids[] = $gooditem['shopgoodsid'];
    }
    $goods = mysqld_selectall("SELECT id, title, thumb, marketprice, total,credit FROM " . table('shop_goods') . " WHERE id IN ('" . implode("','", $goodsids) . "')");
}
$goodtitle = '';
if (! empty($goods)) {
    foreach ($goods as $row) {
        if (empty($goodtitle)) {
            $goodtitle = $row['title'];
        }
        $_optionid = $ordergoods[$row['id']]['optionid'];
        $optionidtitle = '';
        if (! empty($_optionid)) {
            $optionidtitle = mysqld_select("select title from " . table("shop_goods_option") . " where id=:id", array(
                'id' => $_optionid
            ));
            $optionidtitle = $optionidtitle['title'];
        }
        $goodsstr .= "{$row['title']} {$optionidtitle} X{$ordergoods[$row['id']]['total']}\n";
    }
}
$paytypecode = $order['paytypecode'];
if (! empty($_GP['paymentcode'])) {
    $paytypecode = $_GP['paymentcode'];
}
$payment = mysqld_select("select * from " . table("payment") . " where enabled=1 and `code`=:code ", array(
    'code' => $paytypecode
));
if (empty($payment['id'])) {
    message("未找到付款方式，付款失败");
}

if ($order['paytypecode'] != $paytypecode && $order['price'] > 0 ) {
    $paytype = $this->getPaytypebycode($paytypecode);
    mysqld_update('shop_order', array(
        'paytypecode' => $payment['code'],
        'paytypename' => $payment['name'],
        'paytype' => $paytype
    ), array(
        'id' => $orderid
    ));
}
require (WEB_ROOT . '/system/modules/plugin/payment/' . $paytypecode . '/payaction.php');
exit();