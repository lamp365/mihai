<?php
/**
 * app 更改绑定账号接口
 * @var unknown
 *
 */
$result = array();

$member = get_member_account ( true, true );
$op 	= $_GP ['op'];

if (!empty($member) AND $member != 3) {
	
	$openid = $member['openid'];
	
	switch ($op) {
	
		case 'weixin' : 	//微信

			$code 		= trim($_GP['code']);
			$old_unionid= trim($_GP['old_unionid']);		//旧微信号的unionid
			
			//授权码为空时
			if(empty($code))
			{
				$result['message'] 	= "微信授权码不能空。";
				$result['code'] 	= 0;
			}
			else{
				$result = requestWeixinInfo($code);
				
				if($result['code']==1)
				{
					$userinfo = $result['data']['userinfo'];
					
					unset($result['data']);
					
					$wxfansInfo = mysqld_select ("SELECT unionid,deleted FROM " . table ( 'weixin_wxfans' ) . " where unionid='".$userinfo['unionid']."'" );
					
					if($old_unionid!=$userinfo['unionid'])
					{
						//旧微信号删除
						mysqld_delete ( 'weixin_wxfans' ,array('unionid'=>$old_unionid));
					}
					
					//无微信号记录时
					if(empty($wxfansInfo))
					{
						$weixin_data =array('weixin_openid'	=> $userinfo['openid'],
											'openid'		=> $openid,
											'nickname'		=> $userinfo['nickname'],
											'avatar'		=> $userinfo['headimgurl'],
											'gender'		=> $userinfo['sex'],
											'modifiedtime'	=> time(),
											'createtime'	=> time(),
											'deleted'		=> 0,
											'unionid' 		=> $userinfo['unionid']);
							
							
						if(mysqld_insert('weixin_wxfans', $weixin_data))
						{
							$result['message'] 			= "更改微信账号绑定成功。";
							$result['data']['nickname'] = $userinfo['nickname'];
							$result['data']['unionid'] 	= $userinfo['unionid'];
							$result['code'] 			= 1;
						}
						else{
							$result['message'] 	= "更改微信账号绑定失败。";
							$result['code'] 	= 0;
						}
					}
					//被解绑的微信号时
					elseif($wxfansInfo['deleted']==1)
					{
						$weixin_data =array('weixin_openid'	=> $userinfo['openid'],
											'openid'		=> $openid,
											'nickname'		=> $userinfo['nickname'],
											'avatar'		=> $userinfo['headimgurl'],
											'gender'		=> $userinfo['sex'],
											'modifiedtime'	=> time(),
											'deleted'		=> 0,
											'unionid' 		=> $userinfo['unionid']);
							
							
						if(mysqld_update('weixin_wxfans', $weixin_data,array('unionid'=>$userinfo['unionid'])))
						{
							$result['message'] 			= "更改微信账号绑定成功。";
							$result['data']['nickname'] = $userinfo['nickname'];
							$result['data']['unionid'] 	= $userinfo['unionid'];
							$result['code'] 			= 1;
						}
						else{
							$result['message'] 	= "更改微信账号绑定失败。";
							$result['code'] 	= 0;
						}
					}
					else{
						$result['message'] 	= "该微信账号已被占用,请使用其他微信账号绑定!";
						$result['code'] 	= 0;
					}
				}
			}
			
			break;
			
			
		case 'qq':		//QQ
			
			$access_token 	= trim($_GP['access_token']);
			$qq_openid		= trim($_GP['qq_openid']);
			$old_qq_openid	= trim($_GP['old_qq_openid']);		//旧QQ的ID
			
			$result = requestQQInfo($access_token,$qq_openid);
				
			if($result['code']==1)
			{
				$requestQQInfo = $result['data']['qqInfo'];
				
				unset($result['data']);
				
				$qqInfo = mysqld_select ("SELECT qq_openid,deleted FROM " . table ( 'qq_qqfans' ) . " where qq_openid='".$qq_openid."'" );
				
				if($old_qq_openid!=$qq_openid)
				{
					//旧QQ账号删除
					mysqld_delete ('qq_qqfans',array('qq_openid'=>$old_qq_openid));
				}
				
				//无QQ记录时
				if(empty($qqInfo))
				{
					$qq_data =array('createtime'	=> time (),
									'modifiedtime'	=> time(),
									'openid' 		=> $openid,
									'qq_openid'		=> $qq_openid,
									'nickname'		=> $requestQQInfo['nickname'],
									'avatar'		=> $requestQQInfo['figureurl_qq_2'],
									'gender'		=> ($requestQQInfo['gender']=='男') ? 1: 2);
						
						
					if(mysqld_insert('qq_qqfans', $qq_data))
					{
						$result['message'] 			= "更改QQ账号绑定成功。";
						$result['data']['nickname'] = $requestQQInfo['nickname'];
						$result['data']['qq_openid']= $qq_openid;
						$result['code'] 			= 1;
					}
					else{
						$result['message'] 			= "更改QQ账号绑定失败。";
						$result['code'] 			= 0;
					}
				}
				//被解绑的QQ账号时
				elseif($qqInfo['deleted']==1)
				{
					$qq_data =array('modifiedtime'	=> time(),
									'openid' 		=> $openid,
									'qq_openid'		=> $qq_openid,
									'nickname'		=> $requestQQInfo['nickname'],
									'avatar'		=> $requestQQInfo['figureurl_qq_2'],
									'gender'		=> ($requestQQInfo['gender']=='男') ? 1: 2,
									'deleted'		=> 0);
						
					if(mysqld_update('qq_qqfans', $qq_data,array('qq_openid'=>$qq_openid)))
					{
						$result['message'] 			= "更改QQ账号绑定成功。";
						$result['data']['nickname'] = $requestQQInfo['nickname'];
						$result['data']['qq_openid']= $qq_openid;
						$result['code'] 			= 1;
					}
					else{
						$result['message'] 			= "更改QQ账号绑定失败。";
						$result['code'] 			= 0;
					}
				}
				else{
					$result['message'] 	= "该QQ账号已被占用,请使用其他QQ账号绑定!";
					$result['code'] 	= 0;
				}
			}
			
			break;
			
		case 'weibo':	//微博
			
			$uid 			= trim($_GP['uid']);
			$access_token 	= trim($_GP['access_token']);
			$old_uid 		= trim($_GP['old_uid']);			//旧微博账号
			
					
			$result = requestWeiboInfo($access_token,$uid);
						
			if($result['code']==1)
			{
				$userinfo = $result['data']['userinfo'];

				unset($result['data']);
				
				$weiboInfo = mysqld_select ("SELECT uid,deleted FROM " . table ( 'weibo_fans' ) . " where uid='".$uid."'" );
				
				if($old_uid!=$uid)
				{
					//旧微博账号删除
					mysqld_delete ('weibo_fans',array('uid'=>$old_uid));
				}
				
				if($userinfo['gender']=='m')
				{
					$gender = 1;
				}
				elseif($userinfo['gender']=='f')
				{
					$gender = 2;
				}
				else{
					$gender = 0;
				}
				
				
				//无微博记录时
				if(empty($weiboInfo))
				{
					$weibo_data =array('uid' 			=> $userinfo['id'],
										'openid' 		=> $openid,
										'nickname'		=> $userinfo['screen_name'],
										'avatar'		=> $userinfo['avatar_large'],
										'gender'		=> $gender,
										'createtime'	=> time(),
										'modifiedtime'	=> time(),
										'deleted'		=> 0
					);
						
					//新增微博用户账号
					if(mysqld_insert('weibo_fans', $weibo_data))
					{
						$result['message'] 			= "更改微博账号绑定成功。";
						$result['data']['nickname'] = $userinfo['screen_name'];
						$result['data']['uid'] 		= $userinfo['id'];
						$result['code'] 			= 1;
					}
					else{
						$result['message'] 			= "更改微博账号绑定失败。";
						$result['code'] 			= 0;
					}
				}
				//被解绑的微博账号时
				elseif($weiboInfo['deleted']==1)
				{
					$weibo_data = array('uid' 			=> $userinfo['id'],
										'openid' 		=> $openid,
										'nickname'		=> $userinfo['screen_name'],
										'avatar'		=> $userinfo['avatar_large'],
										'gender'		=> $gender,
										'modifiedtime'	=> time(),
										'deleted'		=> 0
					);
					
					if(mysqld_update('weibo_fans', $weibo_data,array('uid'=>$userinfo['id'])))
					{
						$result['message'] 			= "更改微博账号绑定成功。";
						$result['data']['nickname'] = $userinfo['screen_name'];
						$result['data']['uid'] 		= $userinfo['id'];
						$result['code'] 			= 1;
					}
					else{
						$result['message'] 			= "更改微博账号绑定失败。";
						$result['code'] 			= 0;
					}
				}
				else{
					$result['message'] 	= "该微博账号已被占用,请使用其他微博账号绑定!";
					$result['code'] 	= 0;
				}
			}
					
			break;
	}
	
}elseif ($member == 3) {
	$result['message'] 	= "该账号已在别的设备上登录！";
	$result['code'] 	= 3;
}else {
	$result ['message'] = "用户还未登陆。";
	$result ['code'] = 2;
}

echo apiReturn($result);
exit;
