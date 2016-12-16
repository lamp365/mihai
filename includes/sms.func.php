<?php
/*
短信验证码，使用同一个签名，对同一个手机号码发送短信验证码，允许每分钟1条，累计每小时7条。 短信通知，使用同一签名、同一模板，对同一手机号发送短信通知，允许每天50条（自然日）。
$app = 0 默认启用PC端短信发送，如果传入值为1，则为APP的签名短信发送
$type 0 默认:注册用验证  1 可选：修改密码用认证  
*/
function set_sms_code($telphone='',$app=0,$type=''){
    if ( !empty($telphone) and preg_match("/^1[34578]{1}\d{9}$/",$telphone) ){
        $code = get_code();
		switch ( $type ){
            case 1:
				$template = 'SMS_13756412';
				break;
			default:
				$template = 'SMS_13756414';
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
	    $req->setSmsFreeSignName("觅海掌门人");
		$req->setSmsParam("{\"code\":\"{$code}\",\"product\":\"觅海掌门人\"}");
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
function send_warring($id){
	   if ( empty($id) ){
           return false;
	   }
	   if( !strstr($_SERVER['HTTP_HOST'] , 'hinrc')){
           return false;
	   }
	   $telphone = '18649713342';
       if (file_exists(WEB_ROOT . '/includes/TopSdk.php')) {
              require WEB_ROOT . '/includes/TopSdk.php';
			  $respObject = send_warring_sms($telphone,$id);
			  //如果发送失败
			  if (isset($respObject->code))
			  {
			  	return false;
			  }
			  else{
			  	return $id;
			  }
        }	
}
function send_warring_sms($telphone,$code){
        $c = new TopClient;					
        $c->appkey = '23444674';
		$c->secretKey = 'ccca8b6f6ffb7de45f73972254f4c1cb';
		$req = new AlibabaAliqinFcSmsNumSendRequest;
		$req->setSmsType("normal");
	    $req->setSmsFreeSignName("觅海环球购");
		$req->setSmsParam("{\"product\":\"{$code}\"}");
		$req->setRecNum("$telphone");
		$req->setSmsTemplateCode("SMS_25215215");
		$resp = $c->execute($req);
		return $resp;
}