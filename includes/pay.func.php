<?php
/**
 * 支付相关公共函数
 * 
 * 
 */


/**
 * 获得支付方式信息
 * 
 */
function getPayment() {
	return mysqld_selectall ( "select code,name,id from " . table ( "payment" ) . " where enabled=1 order by `order` desc" );
}


/**
 * 获得微信支付数据
 * @param $out_trade_no:商户订单号
 * @param $body:商品描述
 * @param $total_fee:总金额
 *
 * @return array :$appParameters 
 */
function weixinPayData($out_trade_no,$body,$total_fee)
{
	$appParameters = array();
	
	$settings= mysqld_select ( "SELECT value FROM " . table ( 'config' ) . " where name='weixin_mobile' " );
	$configs = unserialize($settings['value']);

	$package = array();
	$package['appid'] 			= $configs['weixin_mobile_appId'];
	$package['mch_id'] 			= $configs['weixin_mobile_mchId'];
	$package['nonce_str'] 		= random(8);
	$package['body'] 			= !empty($body) ? $body : $out_trade_no;
	$package['out_trade_no']	= $out_trade_no;
	$package['total_fee'] 		= $total_fee*100;
	$package['spbill_create_ip']= $_SERVER['REMOTE_ADDR'];
	$package['notify_url'] 		= WEBSITE_ROOT . 'notify/weixin_native_notify.php'; // todo: 这里调用$_W['siteroot']是在子目录下. 获取的是当前二级目录
	$package['trade_type'] 		= 'APP';

	ksort($package, SORT_STRING);
	$string1 = '';
	foreach ($package as $key => $v) {
		$string1 .= "{$key}={$v}&";
	}
	$string1 .= "key=".$configs['weixin_mobile_signKey'];
	$package['sign'] = strtoupper(md5($string1));

	$xml = "<xml>";
	foreach ($package as $key => $val) {

		if (is_numeric($val)) {
			$xml .= "<" . $key . ">" . $val . "</" . $key . ">";
		} else
			$xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
	}
	$xml .= "</xml>";

	//统一下单请求
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, "https://api.mch.weixin.qq.com/pay/unifiedorder");
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	// post提交方式
	curl_setopt($ch, CURLOPT_POST, TRUE);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
	$data = curl_exec($ch);
	curl_close($ch);

	if (! empty($data)) {
		//将xml转为array
		$arrData = json_decode(json_encode(simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA)),true);
                
        if( array_key_exists("return_code", $arrData) && $arrData["return_code"] == "SUCCESS" && array_key_exists("result_code", $arrData) && $arrData["result_code"] == "SUCCESS")
        {
        	$prepayid = $arrData['prepay_id'];
        	
        	$appParameters['appid'] 	= $configs['weixin_mobile_appId'];
        	$appParameters['partnerid'] = $configs['weixin_mobile_mchId'];
        	$appParameters['prepayid'] 	= $prepayid;
        	$appParameters['package'] 	= 'Sign=WXPay';
        	$appParameters['noncestr'] 	= random(8);
        	$appParameters['timestamp'] = time();
        	
        	ksort($appParameters, SORT_STRING);
        	foreach ($appParameters as $key => $v) {
        		$string .= "{$key}={$v}&";
        	}
        	$string .= "key=".$configs['weixin_mobile_signKey'];
      
        	$appParameters['sign'] 	= strtoupper(md5($string));
        }
	}
	
	return $appParameters;
}

/**
 * 获取商品对应的提成的收入价格 商家所得佣金和商家约定给推广员的提成   返回分为单位
 * @param $dishid
 * @param $sts_id
 * @param $price  传进来的price单位是元  商品的价格
 * @param $sts_shop_type
 * @param $earn_rate  商家给子用户的 提成比例
 * @return int|string
 */
function getStoreAndMemberEarnPrice($dishid,$sts_id,$price,$sts_shop_type,$earn_rate){
	$promot_price = 0;   //提成收入价
	//获取商品 得到佣金比例或者推广价
	$this_dish = mysqld_select("select commision,promot_price from ".table('shop_dish')." where id={$dishid}");
	if($sts_shop_type == 5){
		//自营的商家
		if(empty($this_dish['promot_price'])){
			//用店铺的默认 佣金来处理
			$store_info   = mysqld_select("select commision from ".table('store_extend_info')." where store_id={$sts_id}");
			$promot_price = number_format(($store_info['commision']/100)*$price,2);  //单位是元  得到的佣金提成
			$promot_price = FormatMoney($promot_price,1);     //转为分
		}else{
			$promot_price = FormatMoney($price,0) - $this_dish['promot_price'];      //从库里面取出来的是分
		}
	}else{
		if(empty($this_dish['commision'])){
			//用店铺的默认 佣金来处理
			$store_info   = mysqld_select("select commision from ".table('store_extend_info')." where store_id={$sts_id}");
			$promot_price = number_format(($store_info['commision']/100)*$price,2);  //单位是元  得到的佣金提成
			$promot_price = FormatMoney($promot_price,1);     //转为分
		}else{
			$promot_price = number_format(($this_dish['commision']/100)*$price,2);  //单位是元  得到的佣金提成
			$promot_price = FormatMoney($promot_price,1);     //转为分
		}
	}

	$store_earn_price  = $promot_price; //单位分，店铺所得的佣金
	$member_earn_price = 0;
	if(!empty($store_earn_price) && !empty($earn_rate)){
		//推广用户所得的 提成
		$member_earn_price = intval(($earn_rate/100)*$store_earn_price);  //如0.15*1000 == 150 分 即1.5元
	}
	//返回提成收入  单位分
	return array(
		'store_earn_price'  => $store_earn_price,
		'member_earn_price' => $member_earn_price,
	);
}
