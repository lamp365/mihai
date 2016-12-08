<?php defined('SYSTEM_IN') or exit('Access Denied');

function verifyReturn($alipay_safepid,$md5){
		if(empty($_GET)) {//判断POST来的数组是否为空
			return false;
		}
		else {
			//生成签名结果
			$isSign = getSignVeryfy($_GET, $_GET["sign"],$md5);
			//获取支付宝远程服务器ATN结果（验证是否是支付宝发来的消息）
			$responseTxt = 'true';
			if (! empty($_GET["notify_id"])) {$responseTxt = getResponse($_GET["notify_id"],$alipay_safepid);}
					
			//写日志记录
			//if ($isSign) {
			//	$isSignStr = 'true';
			//}
			//else {
			//	$isSignStr = 'false';
			//}
			//$log_text = "responseTxt=".$responseTxt."\n return_url_log:isSign=".$isSignStr.",";
			//$log_text = $log_text.createLinkString($_GET);
			//logResult($log_text);
			
			//验证
			//$responsetTxt的结果不是true，与服务器设置问题、合作身份者ID、notify_id一分钟失效有关
			//isSign的结果不是true，与安全校验码、请求时的参数格式（如：带自定义参数等）、编码格式有关
			if (preg_match("/true$/i",$responseTxt) && $isSign) {
				return true;
			} else {
				return false;
			}
		}
	}

function verifyNotify($alipay_safepid,$md5){
		if(empty($_POST)) {//判断POST来的数组是否为空
			return false;
		}
		else {
			//生成签名结果
			$isSign = getSignVeryfy($_POST, $_POST["sign"],$md5);
			//获取支付宝远程服务器ATN结果（验证是否是支付宝发来的消息）
			$responseTxt = 'true';
			if (! empty($_POST["notify_id"])) {$responseTxt = getResponse($_POST["notify_id"],$alipay_safepid);}

			//写日志记录
			//if ($isSign) {
			//	$isSignStr = 'true';
			//}
			//else {
			//	$isSignStr = 'false';
			//}
			//$log_text = "responseTxt=".$responseTxt."\n notify_url_log:isSign=".$isSignStr.",";
			//$log_text = $log_text.createLinkString($_POST);
			//logResult($log_text);
			
			//验证
			//$responsetTxt的结果不是true，与服务器设置问题、合作身份者ID、notify_id一分钟失效有关
			//isSign的结果不是true，与安全校验码、请求时的参数格式（如：带自定义参数等）、编码格式有关
			if (preg_match("/true$/i",$responseTxt) && $isSign) {
				return true;
			} else {
				return false;
			}
		}
	}

		function getResponse($notify_id,$alipay_safepid) {
		$transport = strtolower('http');
		$partner = trim($alipay_safepid);
		$veryfy_url = '';
		if($transport == 'https') {
		//	$veryfy_url = 'https://mapi.alipay.com/gateway.do?service=notify_verify&';
		}
		else {
			
		}
		$veryfy_url = 'http://notify.alipay.com/trade/notify_query.do?';
		$veryfy_url = $veryfy_url."partner=" . $partner . "&notify_id=" . $notify_id;
		$responseTxt = http_get($veryfy_url);
		
	
		return $responseTxt;
	}
	
	function createLinkstring($para) {
	$arg  = "";
	while (list ($key, $val) = each ($para)) {
		$arg.=$key."=".$val."&";
	}
	//去掉最后一个&字符
	$arg = substr($arg,0,count($arg)-2);
	
	//如果存在转义字符，那么去掉转义
	if(get_magic_quotes_gpc()){$arg = stripslashes($arg);}
	
	return $arg;
}

function paraFilter($para) {
	
	$para_filter = array();
	while (list ($key, $val) = each ($para)) {
		if($key == "sign" || $key == "sign_type" || $val == "")continue;
		else	$para_filter[$key] = $para[$key];
	}
	return $para_filter;
}
function argSort($para) {
	ksort($para);
	reset($para);
	return $para;
}
	

function getSignVeryfy($para_temp, $sign,$md5) {
		//除去待签名参数数组中的空值和签名参数
		$para_filter = paraFilter($para_temp);
		
		//对待签名参数数组排序
		$para_sort = argSort($para_filter);
		
		//把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
		$prestr = createLinkstring($para_sort);
		$isSgin = false;

		switch (strtoupper(trim($para_temp['sign_type']))) {
			case "RSA" :
				
				$publicKey = file_get_contents(WEB_ROOT . "/config/alipay_key/alipay_public_key_file.pem");//公钥key

				$isSgin = rsaVerify($prestr, trim($publicKey), $sign);
				break;
			default :
				$isSgin = md5Verify($prestr,  $sign,$md5);
		}
		
		return $isSgin;
}
	
	
/**
 * 签名字符串
 * @param $prestr 需要签名的字符串
 * @param $key 私钥
 * return 签名结果
 */
function md5Sign($prestr, $key) {
	$prestr = $prestr . $key;
	return md5($prestr);
}

/**
 * 验证签名
 * @param $prestr 需要签名的字符串
 * @param $sign 签名结果
 * @param $key 私钥
 * return 签名结果
 */
function md5Verify($prestr, $sign, $key) {
	$prestr = $prestr . $key;
	$mysgin = md5($prestr);

	if($mysgin == $sign) {
		return true;
	}
	else {
		return false;
	}
}
		
	function buildRequestMysign($para_sort,$md5) {
		//把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
		$prestr = createLinkstring($para_sort);
		
				$mysign = md5Sign($prestr, $md5);
	
		
		return $mysign;
	}
	
	/**
	 * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串，并对字符串做urlencode编码
	 * @param $para 需要拼接的数组
	 * return 拼接完成以后的字符串
	 */
	function createLinkstringUrlencode($para) {
		$arg  = "";
		while (list ($key, $val) = each ($para)) {
			$arg.=$key."=".urlencode($val)."&";
		}
		//去掉最后一个&字符
		$arg = substr($arg,0,count($arg)-2);
	
		//如果存在转义字符，那么去掉转义
		if(get_magic_quotes_gpc()){$arg = stripslashes($arg);}
	
		return $arg;
	}
	
########################## 以下是RSA #######################################

	/**
	 * 生成要请求给支付宝的参数字符串
	 * @param $para_temp 请求前的参数数组
	 * @return 要请求的参数字符串
	 */
	function buildRequestRsaParaToString($para_temp) {
		
		$payment = mysqld_select("SELECT configs FROM " . table('payment') . " WHERE  enabled=1 and code='alipay'");
		
		$paymentConfigs = unserialize($payment['configs']);

		$para_temp['service'] 		= 'mobile.securitypay.pay';
		$para_temp['partner'] 		= trim($paymentConfigs['alipay_safepid']);
		$para_temp['seller_id'] 	= trim($paymentConfigs['alipay_safepid']);
		$para_temp['_input_charset']= 'utf-8';
		$para_temp['sign_type'] 	= 'RSA';
		$para_temp['notify_url'] 	= WEBSITE_ROOT.'notify/alipay_notify.php';			//回调地址
		$para_temp['payment_type'] 	= 1;												//支付类型

		//待请求参数数组
		$para = buildRequestRsaPara($para_temp);
	
		//把参数组中所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串，并对字符串做urlencode编码
		//$request_data = createLinkstringUrlencode($para);
		$request_data = createLinkstring($para);
	
		return $request_data;
	}
	
	/**
	 * 生成要请求给支付宝的参数数组
	 * @param $para_temp 请求前的参数数组
	 * @return 要请求的参数数组
	 */
	function buildRequestRsaPara($para_temp) {
		//除去待签名参数数组中的空值和签名参数
		$para_filter = paraFilter($para_temp);
	
		//对待签名参数数组排序
		$para_sort = argSort($para_filter);
	
		//生成签名结果
		$mysign = buildRequestRsaSign($para_sort);
	
		//签名结果与签名方式加入请求提交参数组中
		$para_sort['sign'] = urlencode($mysign);
		$para_sort['sign_type'] = strtoupper('RSA');
	
		return $para_sort;
	}
	
	/**
	 * 生成签名结果
	 * @param $para_sort 已排序要签名的数组
	 * return 签名结果字符串
	 */
	function buildRequestRsaSign($para_sort) {
		//把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
		$prestr = createLinkstring($para_sort);
	
		$mysign = "";
	
		$priKey = file_get_contents(WEB_ROOT . "/config/alipay_key/rsa_private_key.pem");//私钥文件路径
	
		$mysign = rsaSign($prestr, $priKey);
	
		return $mysign;
	}