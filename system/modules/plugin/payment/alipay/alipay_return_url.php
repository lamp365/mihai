<?php
defined('SYSTEM_IN') or exit('Access Denied');
require_once("common.php");
$payment = mysqld_select("SELECT * FROM " . table('payment') . " WHERE  enabled=1 and code='alipay' limit 1");
$configs=unserialize($payment['configs']);
$verify_result = verifyReturn($configs['alipay_safepid'],$configs['alipay_safekey']);

$post_data = $GLOBALS['HTTP_RAW_POST_DATA']."|".$_SERVER["QUERY_STRING"];

mysqld_insert('paylog', array('typename'=>'支付宝返回数据记录','pdate'=>$post_data,'ptype'=>'success','paytype'=>'alipay'));
$response_msg="";
if($verify_result) {
	$out_trade_no = $_GET['out_trade_no'];
	$trade_no = $_GET['trade_no'];
	$trade_status = $_GET['trade_status'];
    if($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {	
    	$out_trade_no=explode('-',$out_trade_no);
				        $ordersn = $out_trade_no[0];
					    $orderid = $out_trade_no[1];
						$index=strpos($ordersn,"g");
				if(empty($index))
				{
						$order = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE id = :id and ordersn=:ordersn", array(':id' => $orderid,':ordersn'=>$ordersn));
						if(!empty($order['id']))
						{
							require_once WEB_ROOT.'/system/shopwap/class/mobile/order_notice_mail.php';  
		                    mailnotice($orderid);
							$order_cookie =  new LtCookie();
							$order_cookie->setCookie('success', serialize($order));
							if($order['status']==1){
							      mysqld_insert('paylog', array('typename'=>'支付成功','pdate'=>$post_data,'ptype'=>'success','paytype'=>'alipay'));
								  Header("Location:".WEBSITE_ROOT.'index.php?mod=mobile&name=shopwap&do=success'); 
		                     	  //message('支付成功！',WEBSITE_ROOT.'index.php?mod=mobile&name=shopwap&do=success','success');
							}else{
							      mysqld_update('shop_order', array('status'=>1), array('id' =>  $order['id']));
								  //paylog  少用 updateOrderStock 已没有价值了
								  $mark = PayLogEnum::getLogTip('LOG_SHOPBUY_TIP');
								  member_gold($order['openid'],$order['price'],'usegold',$mark,false,$order['ordersn']);
							      mysqld_insert('paylog', array('typename'=>'支付成功','pdate'=>$post_data,'ptype'=>'success','paytype'=>'alipay'));
								  Header("Location:".WEBSITE_ROOT.'index.php?mod=mobile&name=shopwap&do=success'); 
		                          // message('支付成功！',WEBSITE_ROOT.'index.php?mod=mobile&name=shopwap&do=success','success');
							}
							exit;
						}else
						{
							mysqld_insert('paylog', array('typename'=>'未找到相关订单','pdate'=>$post_data,'ptype'=>'error','paytype'=>'alipay'));
		                    exit;
						}
			}else
			{//余额充值
					$order = mysqld_select("SELECT * FROM " . table('gold_order') . " WHERE id = :id and ordersn=:ordersn", array(':id' => $orderid,':ordersn'=>$ordersn));
					if(!empty($order['id'])){
						if($order['status']==0){
							mysqld_update('gold_order', array('status'=>1), array('id' =>  $order['id']));
							mysqld_insert('paylog', array('typename'=>'余额充值成功','pdate'=>$post_data,'ptype'=>'success','paytype'=>'alipay'));	
							member_gold($order['openid'],$order['price'],'addgold','余额在线充值-支付宝支付');
						}
				            message('余额充值成功！',WEBSITE_ROOT.'index.php?mod=mobile&name=shopwap&do=success','success');
					}else{
						 mysqld_insert('paylog', array('typename'=>'余额充值未找到订单','pdate'=>$post_data,'ptype'=>'error','paytype'=>'alipay'));
	                	 message('余额充值未找到订单！',WEBSITE_ROOT.'index.php?mod=mobile&name=shopwap&do=fansindex','error');
	                     exit;
					}
			}	
      } 
      $response_msg= "trade_status=".$_GET['trade_status'];
}
else {
	  mysqld_insert('paylog', array('typename'=>'验证失败','pdate'=>$post_data,'ptype'=>'error','paytype'=>'alipay'));
      $response_msg= $response_msg. "验证失败";
}
?>
<!DOCTYPE HTML>
<html>
 <head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
 <title>支付宝手机网站支付接口</title>
	</head>
    <body>
    	<?php echo  $response_msg;?>
    </body>
</html>