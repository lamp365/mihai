<?php
/**
 * app待付款订单支付接口
 *
 */

require_once WEB_ROOT.'/system/modules/plugin/payment/alipay/common.php';

$result = array ();

$member = get_member_account ( true, true );

if (!empty($member) AND $member != 3) {
	
	$orderid 	= intval ( $_GP ['orderid'] ); 		// 订单ID
	$openid 	= $member ['openid']; 				// 用户ID
	
	$orderInfo 	= mysqld_select ( "SELECT ordersn,price,paytypecode,ifcustoms FROM " . table ( 'shop_order' ) . " WHERE openid = :openid and deleted=0 and status=0 and id=:id", array (':openid' => $openid,':id'=>$orderid) );
	
	if($orderInfo)
	{
		$order_goods= mysqld_selectall ( "SELECT d.title FROM " . table ( 'shop_order_goods' ) . " as og left join ".table ( 'shop_dish' )." as d on d.id=og.goodsid WHERE orderid = :orderid ", array (':orderid' => $orderid) );
		
		$payBody = '';		//支付时的商品详情
		
		// 插入订单商品
		foreach ( $order_goods as $row ) {
		
			$payBody = $row['title'];
		}
		
		$aliParam = array('out_trade_no'=> $orderInfo['ordersn'].'-'.$orderid,				//商户网站唯一订单号
							'subject' 	=> $orderInfo['ordersn'],							//商品名称
							'total_fee' => $orderInfo['price'],								//总金额
							'body' 		=> preg_replace("/[\&\+]+/", '', $payBody)			//商品详情
		);
		
		$result ['data']['aliPayParam']	= buildRequestRsaParaToString($aliParam);			//支付宝的参数
		$result ['data']['paytypecode']	= $orderInfo['paytypecode'];						//支付方式code
		$result ['data']['ifcustoms']	= $orderInfo['ifcustoms'];							//是否需要清关材料
		$result ['code'] 				= 1;
	}
	else{
		$result ['message'] = "待支付订单不存在。";
		$result ['code'] 	= 0;
	}
}elseif ($member == 3) {
	$result['message'] 	= "该账号已在别的设备上登录！";
	$result['code'] 	= 3;
}else {
	$result ['message'] = "用户还未登陆。";
	$result ['code'] 	= 2;
}

echo apiReturn ( $result );
exit ();