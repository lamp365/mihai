<?php
// 天猫对接短信发送方法
function tmall_sms_code($telphone='', $shopname='', $code='', $order=''){
    if ( !empty($telphone) and preg_match("/^1[34578]{1}\d{9}$/",$telphone) ){
         $shop = array('faith信念营养海外专营店', 'nrc营养优选海外专营店', 'nrfs湖畔海外专营店', 'nyc美林健康海外专营店');
		 // 判断传进来的店铺是否在预选项里
		 if ( in_array($shopname, $shop) ){
               switch ($shopname){
                   case 'faith信念营养海外专营店':
					   $codetemp  = 'SMS_56160094';
				       $sharetemp = 'SMS_56070116';
					   $sign       = 'faith信念';
					   break;
				   case 'nrc营养优选海外专营店':
					   $codetemp  = 'SMS_56020080';
				       $sharetemp = 'SMS_56085090';
					   $sign       = 'NRC营养';
					   break;
				   case 'nrfs湖畔海外专营店':
					   $codetemp  = 'SMS_56080122';
				       $sharetemp = 'SMS_56210065';
					   $sign       = 'nrfs湖畔';
					   break;
				   default:
					   $codetemp  = 'SMS_56100108';
				       $sharetemp = 'SMS_56215117';
					   $sign       = 'NYC美林';
					   break;
			   }
			  if (file_exists(WEB_ROOT . '/includes/TopSdk.php')) {
                  require WEB_ROOT . '/includes/TopSdk.php';
				  // 发送验证码
                  send_tmall_sms($telphone, $sign, $codetemp, $code, $order);
			      // 发送推广
			      send_tmall_sms($telphone, $sign, $sharetemp);
			  }
             
		 }else{
              return false;
		 }
	}else{
         return false;
	}
}
// 电话，签名，模板是必填的，其它非必填
function send_tmall_sms($telphone,$sign,$template,$code='',$order=''){
	  if ( !empty($telphone) && !empty($template) && !empty($sign)){
            $c = new TopClient;					
			$c->appkey = '23444674';
			$c->secretKey = 'ccca8b6f6ffb7de45f73972254f4c1cb';
			$req = new AlibabaAliqinFcSmsNumSendRequest;
			$req->setSmsType("normal");
			$req->setSmsFreeSignName("$sign");
			if ( !empty($code) && !empty($order)){
			    $req->setSmsParam("{\"product\":\"{$order}\",\"code\":\"{$code}\"}");
			}
			$req->setRecNum("$telphone");
			$req->setSmsTemplateCode("$template");
			$resp = $c->execute($req);
			return $resp;
	  }else{
            return false;
	  }
}



/*
短信验证码，使用同一个签名，对同一个手机号码发送短信验证码，允许每分钟1条，累计每小时7条。 短信通知，使用同一签名、同一模板，对同一手机号发送短信通知，允许每天50条（自然日）。
$app = 0 默认启用PC端短信发送，如果传入值为1，则为APP的签名短信发送
$type 0 默认:注册用验证  1 可选：修改密码用认证  
*/
function set_sms_code($telphone='',$app=0,$type=''){
    if ( !empty($telphone) and preg_match("/^1[34578]{1}\d{9}$/",$telphone) ){
        $code = get_code();
		switch ( $type ){
			case 4:			//空包感谢回馈
				$template = 'SMS_75880042';
				break;
			case 2:			//更换手机号码用的短信模板
				$template = 'SMS_35035487';
				break;
            case 1:
				$template = 'SMS_13756412';
				break;
			default:      //用户注册短信
				$template = 'SMS_75880042';
				break;
		}
		if (file_exists(WEB_ROOT . '/includes/TopSdk.php')) {
              require WEB_ROOT . '/includes/TopSdk.php';
			  if ( $app == 1 ){
                   $respObject = send_app_sms($telphone,$code,$template);
			  }else{
			       $respObject = send_sms($telphone,$code,$template);
			  }
			  //如果发送失败
			  if (isset($respObject->code))
			  {
			  	return false;
			  }
			  else{
			  	return $code;
			  }
        }	
	}else{
        return false;
	}
}
function get_code(){
       mt_srand((double) microtime() * 1000000);
       $_code = str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);  
	   return $_code;
}
// 设置$template的默认值是为了兼容早期服务端的验证码写法
function send_app_sms($telphone,$code,$template='SMS_13756414'){
        $c = new TopClient;					
        $c->appkey = '23444674';
		$c->secretKey = 'ccca8b6f6ffb7de45f73972254f4c1cb';
		$req = new AlibabaAliqinFcSmsNumSendRequest;
		$req->setSmsType("normal");
	    $req->setSmsFreeSignName("觅海环球购");
		$req->setSmsParam("{\"code\":\"{$code}\",\"product\":\"觅海环球购\"}");
		$req->setRecNum("$telphone");
		$req->setSmsTemplateCode("$template");
		$resp = $c->execute($req);
		return $resp;
}
function send_sms($telphone,$code,$template='SMS_13756414'){
        $c = new TopClient;					
        $c->appkey = '23444674';
		$c->secretKey = 'ccca8b6f6ffb7de45f73972254f4c1cb';
		$req = new AlibabaAliqinFcSmsNumSendRequest;
		$req->setSmsType("normal");
	    $req->setSmsFreeSignName("觅海环球购");
		$req->setSmsParam("{\"code\":\"{$code}\",\"product\":\"觅海环球购\"}");
		$req->setRecNum("$telphone");
		$req->setSmsTemplateCode("$template");
		$resp = $c->execute($req);
		return $resp;
}

/**
 * 空包我帮你，感谢您是平台的活跃用户，系统已经赠送您${money}元，作为回馈，已存入您的平台账户。感谢有您！
 * @param $telphone
 * @param $money
 * @return mixed|ResultSet|SimpleXMLElement
 */
function send_kongbao_sms($telphone,$money){
	require WEB_ROOT . '/includes/TopSdk.php';
	$c = new TopClient;
	$c->appkey = '23499623';
	$c->secretKey = 'e2a5c71e4eca9cc7e4d6141ce5c5f0b4';
	$req = new AlibabaAliqinFcSmsNumSendRequest;
	$req->setSmsType("normal");
	$req->setSmsFreeSignName("空包网");
	$req->setSmsParam("{\"money\":\"{$money}\"}");
	$req->setRecNum("$telphone");
	$req->setSmsTemplateCode("SMS_75880042");
	$resp = $c->execute($req);
	ppd($resp);
	return $resp;
}

/**
 * 您已很久没登录了，您的平台账户还有${money}元体验金呢，空包我帮你(kongbao580.cn)诚邀您来体验！
 * @param $telphone
 * @param $money
 * @return mixed|ResultSet|SimpleXMLElement
 */
function send_tips_sms($telphone,$money){
	require WEB_ROOT . '/includes/TopSdk.php';
	$c = new TopClient;
	$c->appkey = '23499623';
	$c->secretKey = 'e2a5c71e4eca9cc7e4d6141ce5c5f0b4';
	$req = new AlibabaAliqinFcSmsNumSendRequest;
	$req->setSmsType("normal");
	$req->setSmsFreeSignName("空包网");
	$req->setSmsParam("{\"money\":\"{$money}\"}");
	$req->setRecNum("$telphone");
	$req->setSmsTemplateCode("SMS_75790126");
	$resp = $c->execute($req);
	ppd($resp);
	return $resp;
}


