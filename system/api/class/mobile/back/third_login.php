<?php
/**
 * app 第三方用户登陆接口
 * @var unknown
 *
 */

$result = array();

//第三方登录类型
$login_type = trim($_GP['login_type']);
$device_code= trim($_REQUEST['device_code']);

if(empty($device_code))
{
	$result['message'] 	= "设备码不能空。";
	$result['code'] 	= 0;
	
	echo apiReturn($result);
	exit;
}

switch ($login_type) {
		
	case 'weixin' : 	//微信登录

			$code = trim($_GP['code']);
		
			//授权码为空时
			if(empty($code))
			{
				$result['message'] 	= "微信授权码不能空。";
				$result['code'] 	= 0;
			}
			elseif(empty($device_code))
			{
				$result['message'] 	= "设备码不能空。";
				$result['code'] 	= 0;
			}
			else{
				$result = requestWeixinInfo($code);
							
				if($result['code']==1)
				{
					$userinfo = $result['data']['userinfo'];

					$wxfans = mysqld_select("SELECT unionid,openid,deleted FROM " . table('weixin_wxfans') . " WHERE unionid = :unionid ", array(':unionid' => $userinfo['unionid']));
					
					unset($result['data']);
					
					//无微信账号信息时
					if(empty($wxfans))
					{
						$memberData = array('createtime'=> time (),
											'status' 	=> 1,
											'istemplate'=> 0,
											'experience'=> 0,
											'nickname'	=> !empty($userinfo['nickname']) ? $userinfo['nickname'] : '掌门人'.random(5),
											'avatar'	=> !empty($userinfo['headimgurl']) ? $userinfo['headimgurl'] : IM_ICON_URL,
											'mess_id' 	=> 0);
										
						$openid = createMember($memberData,$device_code);
										
						//用户注册成功时
						if($openid)
						{
							$weixin_data =array('createtime'	=> time (),
												'modifiedtime'	=> time (),
												'openid' 		=> $openid,
												'weixin_openid'	=> $userinfo['openid'],
												'follow'		=> 0,
												'nickname'		=> $userinfo['nickname'],
												'avatar'		=> $userinfo['headimgurl'],
												'gender'		=> $userinfo['sex'],
												'unionid' 		=> $userinfo['unionid']);
											
											
							mysqld_insert('weixin_wxfans', $weixin_data);
							
							$memberInfo = member_get($openid);	//用户信息
											
							$result['message'] 			= "用户登录成功。";
							$result['data']['openid'] 	= $openid;
							$result['data']['mobile'] 	= $memberInfo['mobile'];
							$result['code'] 			= 1;
						}
						else{
							$result['message'] 	= "用户登录失败。";
							$result['code'] 	= 0;
						}
					}
					//用户ID为空时
					elseif(empty($wxfans['openid']))
					{
						$memberData = array('createtime'=> time (),
											'status' 	=> 1,
											'istemplate'=> 0,
											'experience'=> 0,
											'nickname'	=> !empty($userinfo['nickname']) ? $userinfo['nickname'] : '掌门人'.random(5),
											'avatar'	=> !empty($userinfo['headimgurl']) ? $userinfo['headimgurl'] : IM_ICON_URL,
											'mess_id' 	=> 0);
										
						$openid = createMember($memberData,$device_code);
										
						//用户注册成功时
						if($openid)
						{
							//更新weixin_wxfans表记录
							mysqld_update('weixin_wxfans', array('openid'=>$openid,'deleted'=>0,'modifiedtime'=> time()),array('unionid'=>$wxfans['unionid']));

							$memberInfo = member_get($openid);	//用户信息
							
							$result['message'] 			= "用户登录成功。";
							$result['data']['openid'] 	= $openid;
							$result['data']['mobile'] 	= $memberInfo['mobile'];
							$result['code'] 			= 1;
						}
						else{
							$result['message'] 	= "用户登录失败。";
							$result['code'] 	= 0;
						}
					}
					//第三方登录成功时
					elseif(thirdLoginProcess($wxfans['openid'],$device_code)){
								
						//原先是解绑的，重新绑定回去
						if($wxfans['deleted']==1)
						{
							//更新weixin_wxfans表记录
							mysqld_update('weixin_wxfans', array('deleted'=>0),array('unionid'=>$wxfans['unionid']));
						}
						
						$memberInfo = member_get($wxfans['openid']);	//用户信息

						$result['message'] 			= "用户登录成功。";
						$result['data']['openid'] 	= $wxfans['openid'];
						$result['data']['mobile'] 	= $memberInfo['mobile'];
						$result['code'] 			= 1;
					}
					else{
						$result['message'] 	= "用户登录失败。";
						$result['code'] 	= 0;
					}
				}
			}
		
		break;
		
	case 'qq':		//QQ登录
		
		$access_token 	= trim($_GP['access_token']);
		
		$qqConfig = mysqld_select("SELECT configs FROM " . table('thirdlogin') . " WHERE code = :code and enabled=1 ", array(':code' => 'qq'));
		
		if(empty($device_code))
		{
			$result['message'] 	= "设备码不能空。";
			$result['code'] 	= 0;
		}
		else{
			$result = requestQQInfo($access_token);
				
			if($result['code']==1)
			{
				$qqInfo = $result['data']['qqInfo'];
				unset($result['data']);
				
				$qqfans = mysqld_select("SELECT openid,deleted,qq_openid,unionid FROM " . table('qq_qqfans') . " WHERE unionid = :unionid ", array(':unionid' => $qqInfo['unionid']));
				
				//无qq账号信息时
				if(empty($qqfans))
				{
					$memberData = array('createtime'=> time (),
										'status' 	=> 1,
										'istemplate'=> 0,
										'experience'=> 0,
										'nickname'	=> !empty($qqInfo['nickname']) ? $qqInfo['nickname'] : '掌门人'.random(5),
										'avatar'	=> !empty($qqInfo['figureurl_qq_2']) ? $qqInfo['figureurl_qq_2'] : IM_ICON_URL,
										'mess_id' 	=> 0);
					
					$openid = createMember($memberData,$device_code);
					
					//用户注册成功时
					if($openid)
					{
						$qq_data =array('createtime'	=> time (),
										'modifiedtime'	=> time(),
										'openid' 		=> $openid,
										'qq_openid'		=> $qqInfo['qq_openid'],
										'unionid'		=> $qqInfo['unionid'],
										'nickname'		=> $qqInfo['nickname'],
										'avatar'		=> $qqInfo['figureurl_qq_2'],
										'gender'		=> ($qqInfo['gender']=='男') ? 1: 2);
					
					
						mysqld_insert('qq_qqfans', $qq_data);
						
						$memberInfo = member_get($openid);	//用户信息
					
						$result['message'] 			= "用户登录成功。";
						$result['data']['openid'] 	= $openid;
						$result['data']['mobile'] 	= $memberInfo['mobile'];
						$result['code'] 			= 1;
					}
					else{
						$result['message'] 	= "用户登录失败。";
						$result['code'] 	= 0;
					}
				}
				//第三方登录成功时
				elseif(thirdLoginProcess($qqfans['openid'],$device_code)){
							
						//原先是解绑的，重新绑定回去
						if($qqfans['deleted']==1)
						{
							//更新记录
							mysqld_update('qq_qqfans', array('deleted'=>0),array('unionid'=>$qqfans['unionid']));
						}
						
						$memberInfo = member_get($qqfans['openid']);	//用户信息
					
						$result['message'] 			= "用户登录成功。";
						$result['data']['openid'] 	= $qqfans['openid'];
						$result['data']['mobile'] 	= $memberInfo['mobile'];
						$result['code'] 			= 1;
				}
				else{
						$result['message'] 	= "用户登录失败。";
						$result['code'] 	= 0;
				}
			}
		}
		
		break;
		
	case 'weibo':			//微博
		
		$uid 			= trim($_GP['uid']);
		$access_token 	= trim($_GP['access_token']);
		
		$result = requestWeiboInfo($access_token,$uid);
					
		if($result['code']==1)
		{
			$weiboFans= mysqld_select("SELECT uid,openid,deleted FROM " . table('weibo_fans') . " WHERE uid = :uid ", array(':uid' => $uid));
			$userinfo = $result['data']['userinfo'];
			
			unset($result['data']);
						
			//无微博账号信息时
			if(empty($weiboFans))
			{
				$memberData = array('createtime'=> time (),
									'status' 	=> 1,
									'istemplate'=> 0,
									'experience'=> 0,
									'nickname'	=> !empty($userinfo['screen_name']) ? $userinfo['screen_name'] : '掌门人'.random(5),
									'avatar'	=> !empty($userinfo['avatar_large']) ? $userinfo['avatar_large'] : IM_ICON_URL,
									'mess_id' 	=> 0);
						
				$openid = createMember($memberData,$device_code);
						
				//用户注册成功时
				if($openid)
				{
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
									
					$weibo_data =array('uid' 			=> $userinfo['id'],
										'openid' 		=> $openid,
										'nickname'		=> $userinfo['screen_name'],
										'avatar'		=> $userinfo['avatar_large'],
										'gender'		=> $gender,
										'createtime'	=> time(),
										'modifiedtime'	=> time()
								);
									
					//新增微博用户账号
					mysqld_insert('weibo_fans', $weibo_data);
					
					$memberInfo = member_get($openid);	//用户信息
						
					$result['message'] 			= "用户登录成功。";
					$result['data']['openid'] 	= $openid;
					$result['data']['mobile'] 	= $memberInfo['mobile'];
					$result['code'] 			= 1;
				}
				else{
					$result['message'] 	= "用户登录失败。";
					$result['code'] 	= 0;
				}
			}
			//第三方登录成功时
			elseif(thirdLoginProcess($weiboFans['openid'],$device_code)){
						
				//原先是解绑的，重新绑定回去
				if($weiboFans['deleted']==1)
				{
					//更新记录
					mysqld_update('weibo_fans', array('deleted'=>0),array('uid'=>$weiboFans['uid']));
				}
				
				$memberInfo = member_get($weiboFans['openid']);	//用户信息
						
				$result['message'] 			= "用户登录成功。";
				$result['data']['openid'] 	= $weiboFans['openid'];
				$result['data']['mobile'] 	= $memberInfo['mobile'];
				$result['code'] 			= 1;
			}
			else{
				$result['message'] 	= "用户登录失败。";
				$result['code'] 	= 0;
			}
						
		}
		
		break;
		
	default:
		
		$result['message'] 	= "操作不合法。";
		$result['code'] 	= 0;
		
		break;
}
ifApp($openid);
echo apiReturn($result);
exit;

/**
 * 创建账户
 * 
 * $data:账号数组
 * $device_code:设备码
 * 
 * @return $openid|false
 */
function createMember($data,$device_code)
{
	$openid = date ( "YmdH", time () ) . rand ( 100, 999 );
	$hasmember = mysqld_select ( "SELECT * FROM " . table ( 'member' ) . " WHERE openid = :openid ", array (
			':openid' => $openid
	) );
	if (! empty ( $hasmember ['openid'] )) {
		$openid = date ( "YmdH", time () ) . rand ( 100, 999 );
	}
	
	$data['openid'] = $openid;
	
	//IM账号信息数组
	$userinfos['userid'] 	= $openid;
	$userinfos['password'] 	= randString(10);
	$userinfos['nick'] 		= $data['nickname'];
	$userinfos['name'] 		= $userinfos['nick'];
	$userinfos['icon_url'] 	= $data['avatar'];
	
	$objOpenIm = new OpenIm();

	//创建
	if($objOpenIm->createUser($userinfos))
	{
		if(mysqld_insert ( 'member', $data )){
				
			$member = get_session_account ();
			$oldsessionid = $member ['openid'];

			$res_data     = save_member_login('',$openid);  //当前openid
			$loginid      = $res_data['openid'];
				
			integration_session_account ( $loginid, $oldsessionid );
	
			if (extension_loaded('Memcached')) {
				$mcache = new Mcache();
				// 登陆初始化
				$mcache->init_msession($device_code);
			}
			
			return $openid;
		}
		else{
			return false;
		}
	}
	else{
		return false;
	}
}

/**
 * 第三方登录处理
 * 
 * @param $openid:用户ID
 * @param $device_code:设备码
 * 
 * @return boolean
 * 
 */
function thirdLoginProcess($openid,$device_code)
{
	if (! empty($openid)) {
		
		$member = mysqld_select("SELECT * FROM " . table('member') . " where openid=:openid and status=1", array(':openid' => $openid));
			
		if (! empty($member['openid'])) {
				
				$_SESSION[MOBILE_ACCOUNT] = $member;
				
				$objOpenIm = new OpenIm();
					
				if (extension_loaded('Memcached')) {
					$mcache = new Mcache();
					// 登陆初始化
					$re = $mcache->init_msession($device_code);
				}
				
				//IM账号不存在时
				if(!$objOpenIm->isImUser($member['openid']))
				{
					$userinfos['userid'] 	= $member['openid'];
					$userinfos['password'] 	= randString(10);
					$userinfos['nick'] 		= '掌门人'.random(5);
					$userinfos['name'] 		= $userinfos['nick'];
					$userinfos['icon_url'] 	= IM_ICON_URL;
				
					//创建IM账号
					$objOpenIm->createUser($userinfos);
				}
				
				return true;
		}
		else{
				return false;
		}
	}
	else{
		return false;
	}
}