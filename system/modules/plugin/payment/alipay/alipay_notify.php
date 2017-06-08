<?php
defined('SYSTEM_IN') or exit('Access Denied');
require_once("common.php");
$payment = mysqld_select("SELECT * FROM " . table('payment') . " WHERE  enabled=1 and code='alipay' limit 1");
$configs=unserialize($payment['configs']);
$verify_result = verifyNotify($configs['alipay_safepid'],$configs['alipay_safekey']);

$post_data = $GLOBALS['HTTP_RAW_POST_DATA']."|".$_SERVER["QUERY_STRING"];
mysqld_insert('paylog', array('typename'=>'支付宝返回数据记录','pdate'=>$post_data,'ptype'=>'success','paytype'=>'alipay','createtime'=>date('Y-m-d H:i:s')));
     
if($verify_result) {//验证成功
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	mysqld_insert('paylog', array('typename'=>'验签成功','pdate'=>'verify_result:'.$verify_result,'ptype'=>'verify success','paytype'=>'alipay','createtime'=>date('Y-m-d H:i:s')));

	$out_trade_no = $_POST['out_trade_no'];

	//支付宝交易号

	$trade_no = $_POST['trade_no'];

	//交易状态
	$trade_status = $_POST['trade_status'];


    if ($_POST['trade_status'] == 'TRADE_SUCCESS'||$_POST['trade_status'] == 'TRADE_FINISHED') {
				$out_trade_no=explode('-',$out_trade_no);
				$ordersn = $out_trade_no[0];
				$orderid = $out_trade_no[1];
				$index=strpos($ordersn,"g");
				if(empty($index))
				{
					 $order = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE id = :id and ordersn=:ordersn", array(':id' => $orderid,':ordersn'=>$ordersn));
					if(!empty($order['id']))
					{
						if($order['status']==0)
						{
                            mysqld_update('shop_order', array('status'=>1,'paytime'=>time()), array('id' =>  $order['id']));

                            mysqld_insert('paylog', array('typename'=>'支付成功','pdate'=>$post_data,'ptype'=>'success','paytype'=>'alipay','createtime'=>date('Y-m-d H:i:s')));

                            paySuccessProcess($order);	//支付成功后的处理  库存的处理

	      	                message('支付成功！',WEBSITE_ROOT.'index.php?mod=mobile&name=shopwap&do=myorder','success');
						}else
						{
                            message('该订单不是支付状态无法支付',WEBSITE_ROOT.'index.php?mod=mobile&name=shopwap&do=myorder','error');
		
						}
						exit;
					}else
					{
						mysqld_insert('paylog', array('typename'=>'未找到相关订单','pdate'=>$post_data,'ptype'=>'error','paytype'=>'alipay'));
                        message('支付失败未找到订单！',WEBSITE_ROOT.'index.php?mod=mobile&name=shopwap&do=myorder','error');
					}
			}else
			{//余额支付
					 $order = mysqld_select("SELECT * FROM " . table('gold_order') . " WHERE id = :id and ordersn=:ordersn", array(':id' => $orderid,':ordersn'=>$ordersn));
					if(!empty($order['id']))
					{
						if($order['status']==0)
						{
						mysqld_update('gold_order', array('status'=>1), array('id' =>  $order['id']));
	      
						mysqld_insert('paylog', array('typename'=>'余额充值成功','pdate'=>$post_data,'ptype'=>'success','paytype'=>'alipay'));
						
     				member_gold($order['openid'],$order['price'],'addgold','余额在线充值-支付宝支付');
						}
						exit;
					}else
					{
						mysqld_insert('paylog', array('typename'=>'余额充值未找到订单','pdate'=>$post_data,'ptype'=>'error','paytype'=>'alipay'));
	      exit;
					}
				
			}
    }

        
	echo "success";		
	
}
else {
	
	mysqld_insert('paylog', array('typename'=>'通信出错','pdate'=>$post_data,'ptype'=>'error','paytype'=>'alipay','createtime'=>date('Y-m-d H:i:s')));
    echo "fail";

}