<?php
/**
 * 通用通知接口demo
 * ====================================================
 * 支付完成后，微信会把相关支付和用户信息发送到商户设定的通知URL，
 * 商户接收回调信息后，根据需要设定相应的处理流程。
 * 
 * 这里举例使用log文件形式记录回调信息。
*/
	$payment = mysqld_select("SELECT * FROM " . table('payment') . " WHERE  enabled=1 and code='weixin' limit 1");
   $configs=unserialize($payment['configs']);
          
	$settings=globaSetting();
          
  $_CMS['weixin_pay_appid'] = $settings['weixin_appId'];
	//受理商ID，身份标识
	$_CMS['weixin_pay_mchId']  = $configs['weixin_pay_mchId'];
	//商户支付密钥Key。审核通过后，在微信发送的邮件中查看
	$_CMS['weixin_pay_paySignKey'] = $configs['weixin_pay_paySignKey'];
	//JSAPI接口中获取openid，审核后在公众平台开启开发模式后可查看
		$_CMS['weixin_pay_appSecret']= $settings['weixin_appSecret'];
             


	////存储微信的回调
	 $xml = $GLOBALS['HTTP_RAW_POST_DATA'];	
	 mysqld_insert('paylog', array('typename'=>'微信支付记录','pdate'=>$xml,'ptype'=>'success','paytype'=>'weixin','createtime'=>date('Y-m-d H:i:s')));

	 $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);		
	if (false && empty($array_data)) {
		exit('fail');
	}
	
	ksort($array_data, SORT_STRING);
		$string1 = '';
		foreach($array_data as $k => $v) {
			if($v != '' && $k != 'sign') {
				$string1 .= "{$k}={$v}&";
			}
		}
		
	//如果是APP发起的支付时
	if($array_data['trade_type']=='APP')
	{
		$weixin_mobile= mysqld_select ( "SELECT value FROM " . table ( 'config' ) . " where name='weixin_mobile' " );
		$arrWeixinMobile = unserialize($weixin_mobile['value']);
		
		$signkey = $arrWeixinMobile['weixin_mobile_signKey'];
	}
	else{
		$signkey = $_CMS['weixin_pay_paySignKey'];
	}

	$member  = get_member_account();
	$memInfo = member_get($member['openid'],'mobile');

	$sign = strtoupper(md5($string1 . "key={$signkey}"));
	 if($sign == $array_data['sign']) {
			if ($array_data["return_code"] == "FAIL") {
				//此处应该更新一下订单状态，商户自行增删操作
			  	mysqld_insert('paylog', array('typename'=>'通信出错','pdate'=>$xml,'ptype'=>'error','paytype'=>'weixin','createtime'=>date('Y-m-d H:i:s')));
				logRecord("{$memInfo['mobile']}用户支付异步错误---{$array_data['return_msg']}",'payError');
				exit;
			}
			elseif($array_data["result_code"] == "FAIL"){
				//此处应该更新一下订单状态，商户自行增删操作
				mysqld_insert('paylog', array('typename'=>'业务出错','pdate'=>$xml,'ptype'=>'error','paytype'=>'weixin','createtime'=>date('Y-m-d H:i:s')));
				logRecord("{$memInfo['mobile']}用户支付异步错误---{$array_data['return_msg']}",'payError');
				exit;
			}
			else{
				mysqld_insert('paylog', array('typename'=>'微支付成功返回','pdate'=>$xml,'ptype'=>'success','paytype'=>'weixin','createtime'=>date('Y-m-d H:i:s')));
				//$out_trade_no=explode('-',$array_data['out_trade_no']);
				$ordersn = $array_data['out_trade_no'];
				$ordersn_arr = explode('_',$ordersn);   //多商家导致，可能有多个订单号
				//$orderid = $out_trade_no[1];
				$index=strpos($ordersn_arr[0],"g");
				if(empty($index))
				{
					/**
					 * 支付完毕 处理账单 佣金提成，卖家所得，平台费率
					 */
					foreach($ordersn_arr as $ordersn){
						paySuccessProcess($ordersn,$settings);
					}
					message('支付成功！',mobile_url('myorder',array('name'=>'shopwap','status'=>99)),'success');
					exit;
				}else{//余额充值
					$order = mysqld_select("SELECT * FROM " . table('gold_order') . " WHERE ordersn=:ordersn", array(':ordersn'=>$ordersn));
						if(!empty($order['id']))
						{
							if($order['status']==0)
							{
								mysqld_update('gold_order', array('status'=>1), array('id' =>  $order['id']));

								mysqld_insert('paylog', array('typename'=>'余额充值成功','pdate'=>$xml,'ptype'=>'success','paytype'=>'weixin'));

								member_gold($order['openid'],$order['price'],'addgold','余额在线充值-微信二维码支付');
							}
							exit;
						}else
						{
							mysqld_insert('paylog', array('typename'=>'余额充值未找到订单','pdate'=>$xml,'ptype'=>'error','paytype'=>'weixin'));
		      				exit;
						}
					}
				}
		
				mysqld_insert('paylog', array('typename'=>'微支付出现错误','pdate'=>$xml,'ptype'=>'error','paytype'=>'weixin','createtime'=>date('Y-m-d H:i:s')));
		}else
		{
			mysqld_insert('paylog', array('typename'=>'签名验证失败','pdate'=>$xml,'ptype'=>'error','paytype'=>'weixin','createtime'=>date('Y-m-d H:i:s')));
			logRecord("{$memInfo['mobile']}用户支付异步签名验证失败",'payError');
		}
	

      
?>