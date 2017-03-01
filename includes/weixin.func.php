<?php
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
						'value'=>'总有人会中的，为什么不会是你呢？',
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