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