<?php

function get_weixin_fans_byopenid($openid, $weixin_openid)
{
	$weixin_wxfans = mysqld_select("SELECT * FROM " . table('weixin_wxfans') . " where openid=:openid or weixin_openid=:weixin_openid", array(
		':openid' => $openid,
		':weixin_openid' => $weixin_openid
	));
	return $weixin_wxfans;
}

function get_js_ticket()
{
	$configs = globaSetting();

	$jsapi_ticket = $configs['jsapi_ticket'];
	$jsapi_ticket_exptime = intval($configs['jsapi_ticket_exptime']);

	$weixin_access_token = unserialize($configs['weixin_access_token']);

	//加入 对token的过期验证，，可能是token的过期，导致 分享js 报错 config invalid signature
	if (empty($jsapi_ticket) || $jsapi_ticket_exptime < time() || empty($weixin_access_token['expire']) || $weixin_access_token['expire'] < TIMESTAMP) {

		$accessToken = get_weixin_token();
		$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
		$content = http_get($url);
		$res = @json_decode($content, true);
		$ticket = $res['ticket'];

		if (! empty($ticket)) {
			$cfg = array(
				'jsapi_ticket' => $ticket,
				'jsapi_ticket_exptime' => time() + intval($res['expires_in'])
			);
			refreshSetting($cfg);
			return $ticket;
		}
		return '';
	} else {
		return $jsapi_ticket;
	}
}

function get_weixin_token($refresh = false)
{
	if ($refresh) {
		save_weixin_access_token('');
	}
	$configs = globaSetting(array(
		"weixin_access_token",
		"weixin_appId",
		"weixin_appSecret"
	));
	$weixin_access_token = unserialize($configs['weixin_access_token']);
	if (is_array($weixin_access_token) && ! empty($weixin_access_token['token']) && $weixin_access_token['expire'] > TIMESTAMP) {
		return $weixin_access_token['token'];
	} else {

		$appid = $configs['weixin_appId'];
		$secret = $configs['weixin_appSecret'];

		if (empty($appid) || empty($secret)) {
			message('请填写公众号的appid及appsecret！');
		}
		$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";
		$content = http_get($url);

		if (empty($content)) {
			logRecord("微信授权失败！当前appid：{$appid}",'weixin_server');
			message('获取微信公众号授权失败, 请稍后重试！');
		}
		$token = @json_decode($content, true);
		if (empty($token) || ! is_array($token)) {
			logRecord("微信获取token失败！",'weixin_server');
			message('获取微信公众号授权失败, 请稍后重试！ 公众平台返回原始数据为:' . $token);
		}
		if (empty($token['access_token']) || empty($token['expires_in'])) {
			message('解析微信公众号授权失败, 请稍后重试！');
		}
		/**
		 * {
		"access_token": "NU7Kr6v9L9TQaqm5NE3OTPctTZx797Wxw4Snd2WL2HHBqLCiXlDVOw2l-Se0I-WmOLLniAYLAwzhbYhXNjbLc_KAA092cxkmpj5FpuqNO0IL7bB0Exz5s5qC9Umypy-rz2y441W9qgfnmNtIZWSjSQ",
		"expires_in": 7200
		}
		 */
		$record = array();
		$record['token'] = $token['access_token'];
		$record['expire'] = TIMESTAMP + $token['expires_in'];
		$seriaze_access_token = serialize($record);
		save_weixin_access_token($seriaze_access_token);
		return $record['token'];
	}
}

function weixin_send_custom_message($from_user, $msg)
{
	$access_token = get_weixin_token();
	$url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$access_token}";
	$msg = str_replace('"', '\\"', $msg);
	$post = '{"touser":"' . $from_user . '","msgtype":"text","text":{"content":"' . $msg . '"}}';

	http_post($url, $post);
}

function make_nonceStr()
{
	$codeSet = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	for ($i = 0; $i<16; $i++) {
		$codes[$i] = $codeSet[mt_rand(0, strlen($codeSet)-1)];
	}
	$nonceStr = implode($codes);
	return $nonceStr;
}

function make_signature($nonceStr,$timestamp,$jsapi_ticket,$url)
{
	$tmpArr = array(
	'noncestr' => $nonceStr,
	'timestamp' => $timestamp,
	'jsapi_ticket' => $jsapi_ticket,
	'url' => $url
	);
	ksort($tmpArr, SORT_STRING);
	$string1 = http_build_query( $tmpArr );
	$string1 = urldecode( $string1 );
	$signature = sha1( $string1 );
	return $signature;
}

//用于模板需要加载调用微信的分享的时候
//调用该方法会返回对应的一些参数 用于模板上使用
function get_share_js_parame(){
	if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {
		//微信端操作
		$configs             = globaSetting();
		$weixin_appid        = $configs['weixin_appId'];
		$timestamp    = time();
		$jsapi_ticket = get_js_ticket();
		$nonceStr     = make_nonceStr();
		$curt_url     = WEB_HTTP.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$signature    = make_signature($nonceStr,$timestamp,$jsapi_ticket,$curt_url);
		return array(
			'weixin_appid'    => $weixin_appid,
			'timestamp'       => $timestamp,
			'nonceStr'        => $nonceStr,
			'signature'       => $signature
		);
	}else{
		return array();
	}
}

/**
 * 是否显示 关注公众号的提示，用于wap端，需要有这个提示
 * @return bool|string
 */
function is_show_follow(){
	if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {
		//微信端进入的 会缓存  MOBILE_SESSION_ACCOUNT这个key
		$follow  = $_SESSION[MOBILE_SESSION_ACCOUNT]['follow'];
		if($follow){
			//已经订阅
			return '';
		}
		//缓存没有，则查看数据库
		$unionid = $_SESSION[MOBILE_SESSION_ACCOUNT]['unionid'];
		if(empty($unionid)){
			return '';
		}
		//查看是否已经订阅
		$res = mysqld_select("select follow from ".table('weixin_wxfans')." where unionid='{$unionid}'");
		if(empty($res) || $res['follow']==0){
			//未订阅
			return true;
		}else{
			//已经订阅
			$_SESSION[MOBILE_SESSION_ACCOUNT]['follow'] = 1;
			return '';
		}

	}else{
		return '';
	}
}

/**
 * 根据消息模板，组装对应的信息格式 用于微信推送消息模板
 * @param $toUser
 * @param $template_id
 * @param $data_arr  需要的数据
 * @return array|string
 */
function getWeixinPopMsg($toUser,$template_id,$data_arr){
	$data = '';
	switch($template_id){
		//许愿词满了
		case "MKqE5TWNvq1WWV3bry4jHgsGzGILblr59a3mcuHHTaU":
			$price = round($data_arr['price'],2);
			$data = array(
				'touser'      => $toUser,
				"template_id" => $template_id,
				"url"         => WEBSITE_ROOT.mobile_url("shareactive"),  //跳转到活动地址
				'data'     => array(
					'first'=>array(
						'value'=>'现在许愿可以赶上这次抽奖呦！',
						'color'=>'#4169e1',
					),
					'shop' => array(
						'value'=>$data_arr['title'],
						'color'=>'#080808',
					),
					'price' => array(
						'value'=>"{$price}￥",
						'color'=>'#fb4b0e',
					),
					'num' => array(
						'value'=>"{$data_arr['amount']}人",
						'color'=>'#fb4b0e',
					),
					'tips'=>array(
						'value'=> "祝您心愿之旅愉快!",
						'color'=>'#080808',
					),
				),
			);
			break;

		//许愿块过期了
		case "691Sa2pdIhfX45uEQYSbp_Q-ksN5ad3hfr8Vfh135Bk":
			$data = array(
				'touser'      => $toUser,
				"template_id" => $template_id,
				"url"         => WEBSITE_ROOT.mobile_url("shareactive"),  //跳转到活动地址
				'data'     => array(
					'first'=>array(
						'value'=>'总有人会中的为什么不会是你呢',
						'color'=>'#4169e1',
					),
					'num1' => array(
						'value'=>$data_arr['today_num'].'个',
						'color'=>'#fb4b0e',
					),
					'num2' => array(
						'value'=>$data_arr['tommor_num'].'个',
						'color'=>'#fb4b0e',
					),
					'tips'=>array(
						'value'=> "邀好友共同许愿每天可获更多心愿",
						'color'=>'#080808',
					),
				),
			);
			break;
	}

	return $data;

}

/**
 * 将从微信端获取到的二维码地址 进行存到 阿里云
 * @param $temp   false 是永久二维码  true 临时二维码
 * @return bool
 */
function upload_erweima_imgali($openid,$temp = false){
	$weixin_tool  = new WeixinTool();
	$erweima_info = $weixin_tool->get_weixin_erweima($openid,$temp);

	if(in_array($_SERVER['HTTP_HOST'],array('www.hinrc.com','hinrc.com'))){
		//以免测试环境跟线上环境 二维码发生覆盖
		$dir      = 'erweima';
		$picname  = "{$openid}.png";
	}else{
		$dir      = 'erweima';
		$picname  = "dev_{$openid}.png";
	}
	if($temp){
		$picname = "tmp_".$picname;
	}
	$filename = WEB_ROOT."/images/".$picname;

	//临时保存在本地
	$data = file_get_contents($erweima_info);
	$res  = file_put_contents($filename,$data);
	if($res){
		//传到阿里服务器
		$info = aliyunOSS::putObject($filename,$picname,$dir);
		//上传到阿里后 可以删除了
		@unlink($filename);
		return $info['oss-request-url'];
	}else{
		return false;
	}
}

/**
 * 获取渠道商的 二维码图片
 * @param $openid
 * @param $temp   false 是永久二维码  true 临时二维码
 * @return bool|string
 */
function get_weixin_erweima($openid,$temp = false){
	if(in_array($_SERVER['HTTP_HOST'],array('www.hinrc.com','hinrc.com'))){
		//以免测试环境跟线上环境 二维码发生覆盖
		$picname = "{$openid}.png";
	}else{
		$picname = "dev_{$openid}.png";
	}
	if($temp){
		$file = "erweima/tmp_{$picname}";
	}else{
		$file = "erweima/{$picname}";
	}

	$res  = aliyunOSS::doesObjectExist($file);
	if($res){
		//如果存在  直接拼接二维码地址
		$erweima_url = aliyunOSS::aliurl.'/'.$file;
	}else{
		$erweima_url  = upload_erweima_imgali($openid,$temp);
	}
	return $erweima_url;
}

/**
 * 多加这个方法 是为了 后台列表页，调用，而不调用上面方法
 * 调用上面方法，会导致，列表页每个用户都要向阿里请求，判断是否存在二维码
 * 影响网速
 * @param $openid
 * @param $temp   false 是永久二维码  true 临时二维码
 * @return string
 */
function get_erweima_img($openid,$temp = false){
	if(in_array($_SERVER['HTTP_HOST'],array('www.hinrc.com','hinrc.com'))){
		//以免测试环境跟线上环境 二维码发生覆盖
		$picname = "{$openid}.png";
	}else{
		$picname = "dev_{$openid}.png";
	}
	if($temp){
		$file = "erweima/tmp_{$picname}";
	}else{
		$file = "erweima/{$picname}";
	}
	$erweima_url = aliyunOSS::aliurl.'/'.$file;
	return $erweima_url;
}

/**
 * 设置扫码进来获取到渠道商 商家的openid
 * 在扫码的时候，进行缓存，一旦进入商城，就会创建该 微信用户 则会一起存入weixin_wxfans表中
 * 之后无论哪天完成注册可以直接从weixin_wxfans表中获取 就形成绑定关系
 */
function set_scan_cache($data){
		//扫码进来的  session无效 不是基于浏览器的
	$weixin_openid  = $data['fromusername'];
	$scan_openid    = $data['openid'];
	if(class_exists('Memcached')) {
		$key = "{$weixin_openid}@scan";
		$memcache = new Mcache();
		$memcache->set($key,$scan_openid,time()+3600*7);
	}


}

/**
 * 获取通过扫码进来的用户 取得渠道商 商家的openid
 */
function get_scan_cache($weixin_openid){
	$key  = "{$weixin_openid}@scan";
	$scan_openid = '';
	if(class_exists('Memcached')) {
		$memcache    = new Mcache();
		$scan_openid = $memcache->get($key);
	}
	return $scan_openid;
}
