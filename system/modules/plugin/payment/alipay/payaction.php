<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>支付宝支付</title>
</head>
<body>
<?php 
      defined('SYSTEM_IN') or exit('Access Denied');
      require_once("common.php");
	  if ( !isset( $tags ) ){
          $tags = '';
      }
	  $payment = mysqld_select("SELECT * FROM " . table('payment') . " WHERE  enabled=1 and code='alipay' limit 1");
      $configs=unserialize($payment['configs']);
	  if ( !is_mobile_request()){
           $service = "create_direct_pay_by_user";
	  }else{
           $service = "alipay.wap.create.direct.pay.by.user";
	  }
	  $return_url = WEBSITE_ROOT.'notify/'.$tags.'alipay_return_url.php';
      $parameter = array(
		"service" => $service,
		"partner" =>  trim($configs['alipay_safepid']),
		"seller_id" => trim($configs['alipay_safepid']),
		"payment_type"	=> 1,
		"notify_url"	=> WEBSITE_ROOT.'notify/alipay_notify.php',
		"return_url"	=> $return_url,
		"out_trade_no"	=> $order['ordersn'].'-'.$order['id'],
		"subject"	=> $order['ordersn'],
		"total_fee"	=> $order['price'],
		"app_pay"	=> "Y",
		"show_url"	=> WEBSITE_ROOT.mobile_url($tags.'myorder',array('name'=>'shopwap','op'=>'detail','orderid'=>$order['id'])),
		"body"	    => $goodtitle,
		//"it_b_pay"	=> $it_b_pay,
	    //"extern_token"	=> $extern_token,
		"_input_charset"	=> 'utf-8'
);


$para_filter = paraFilter($parameter);
	

$para_filter=argSort($para_filter);

		
		$mysign_t = buildRequestMysign($para_filter,$configs['alipay_safekey']);
		$para_filter['sign'] = $mysign_t;
		$para_filter['sign_type'] = 'MD5';

	$sHtml = "<form id='alipaysubmit' name='alipaysubmit' action='https://mapi.alipay.com/gateway.do' method='get'>";
		while (list ($key, $val) = each ($para_filter)) {
            $sHtml.= "<input type='hidden' name='".$key."' value='".$val."'/>";
        }

        $sHtml = $sHtml."<input type='submit' style='display:none' value='确认'></form>";
		
		$sHtml = $sHtml."<script>document.forms['alipaysubmit'].submit();</script>";
		echo $sHtml;
	//	echo "<textarea rows=\"3\" cols=\"20\">1212".$sHtml."</textarea>";
		exit;
?>
</body>
</html>