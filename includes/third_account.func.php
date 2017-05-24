<?php
/**
 * 第三方登录相关公共函数
 */



/**
 * 请求微信的用户信息
 * @param $code:string 微信授权码
 * @return array
 * 
 */
function requestWeixinInfo($code)
{
	$result = array();
	
	$weixinMobile	= mysqld_select ( "SELECT value FROM " . table ( 'config' ) . " where name='weixin_mobile' " );
	
	if(empty($weixinMobile))
	{
		$result['message'] 	= "请在后台配置移动端微信信息。";
		$result['code'] 	= 0;
	}
	else{
		$arrWeixinMobile= unserialize($weixinMobile['value']);
		
		$appid 	= $arrWeixinMobile['weixin_mobile_appId'];
		$secret = $arrWeixinMobile['weixin_mobile_appSecret'];
			
		//通过code获取access_token
		$token_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $appid . "&secret=" . $secret . "&code=" . $code . "&grant_type=authorization_code";
			
		$content= http_get($token_url);
		$token 	= json_decode($content, true);
			
		if (empty($token) || ! is_array($token) || empty($token['access_token']) || empty($token['openid'])) {
				
			$result['message'] 	= "获取微信授权失败。";
			$result['code'] 	= 0;
		}
		else{
			$userinfo_url = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $token['access_token'] . "&openid=" . $token['openid'] . "&lang=zh_CN";
				
			$userinfo_content = http_get($userinfo_url);
			$userinfo = json_decode($userinfo_content, true);
				
			if(empty($userinfo) || ! is_array($userinfo))
			{
				$result['message'] 	= "获取微信用户信息失败。";
				$result['code'] 	= 0;
			}
			elseif(empty($userinfo['unionid']))
			{
				$result['message'] 	= "该移动应用可能未获得该用户的userinfo授权，无法获取用户unionid。";
				$result['code'] 	= 0;
			}
			else{
				$result['data']['userinfo'] = $userinfo;
				$result['code'] 			= 1;
			}
		}
	}
	
	return $result;
}


/**
 * 请求QQ的用户信息
 * @param $access_token:string  access_token码
 * @param $qq_openid:string  QQ的openid
 * 
 * @return array
 *
 */
function requestQQInfo($access_token)
{
	$result = array();
	
	$qqConfig = mysqld_select("SELECT configs FROM " . table('thirdlogin') . " WHERE code = :code and enabled=1 ", array(':code' => 'qq'));
		
	if(empty($access_token))
	{
		$result['message'] 	= "access_token码不能空。";
		$result['code'] 	= 0;
	}
	elseif(empty($qqConfig))
	{
		$result['message'] 	= "请在后台配置QQ登录信息。";
		$result['code'] 	= 0;
	}
	else{
		$unioid_url="https://graph.qq.com/oauth2.0/me?access_token={$access_token}&unionid=1";
		
		$unioid_content = http_get($unioid_url);
		
		
		$subStr = substr($unioid_content, strpos($unioid_content, "{"));
		$subStr = strstr($subStr, "}", true) . "}";
		$unioidInfo = json_decode($subStr, true);
		
		if(!isset($unioidInfo['openid']) || !isset($unioidInfo['unionid'])) {
			
			$result['message'] 	= "获取QQ用户唯一标识失败";
			$result['code'] 	= 0;
		}
		else{
			$configInfo = unserialize($qqConfig['configs']);
			$appid = $configInfo['thirdlogin_qq_appid'];
			
			$request_url="https://graph.qq.com/user/get_user_info?oauth_consumer_key={$appid}&access_token={$access_token}&openid={$unioidInfo['openid']}";
			
			$request_content = http_get($request_url);
			$qqInfo = json_decode($request_content, true);
			
			if(empty($qqInfo) || $qqInfo['ret']!=0)
			{
				$result['message'] 	= "获取QQ用户信息失败";
				$result['code'] 	= 0;
			}
			else{
				$qqInfo['unionid'] 	= $unioidInfo['unionid'];
				$qqInfo['qq_openid']= $unioidInfo['openid'];
				
				$result['data']['qqInfo'] 	= $qqInfo;
				$result['code'] 			= 1;
			}
		}
	}
	
	return $result;
}

/**
 * 请求微博的用户信息
 * @param $access_token:string  access_token码
 * @param $uid:string  微博的uid
 *
 * @return array
 *
 */
function requestWeiboInfo($access_token,$uid)
{
	$result = array();
	
	//微博uid为空时
	if(empty($uid))
	{
		$result['message'] 	= "微博uid不能空。";
		$result['code'] 	= 0;
	}
	//微博access_token为空时
	elseif(empty($access_token))
	{
		$result['message'] 	= "微博access_token不能空。";
		$result['code'] 	= 0;
	}
	else{
		$userinfo_url = 'https://api.weibo.com/2/users/show.json?access_token=' . $access_token . '&uid=' . $uid;
			
		$userinfo_content = http_get($userinfo_url);
		$userinfo = json_decode($userinfo_content, true);
			
		if(empty($userinfo) || ! is_array($userinfo) || empty($userinfo['id']))
		{
			$result['message'] 	= "获取微博用户信息失败。";
			$result['code'] 	= 0;
		}
		else{
			$result['data']['userinfo'] = $userinfo;
			$result['code'] 			= 1;
		}
	}
	
	return $result;
}


