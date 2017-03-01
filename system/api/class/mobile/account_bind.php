<?php
/**
 * app 账号绑定接口
 * @var unknown
 *
 */
$result = array();

$member = get_member_account ( true, true );
$op 	= $_GP ['op'];

if (!empty($member) AND $member != 3) {
	
	$openid = $member['openid'];
	
	switch ($op) {
		
		case 'mobile':		//手机
			
			$telephone = trim($_GP['telephone']);
			
			$memberByMobile = mysqld_select ( "SELECT openid FROM " . table ( 'member' ) . " where mobile=:mobile ", array (':mobile' => $telephone ) );
			
			$result = array();
			
			$objValidator	= new Validator();
			
			if(empty($telephone))
			{
				$result['message'] 	= '请输入手机号码';
				$result['code'] 	= 0;
			}
			elseif(!$objValidator->is($telephone,'mobile'))
			{
				$result['message'] 	= '请输入正确的手机号码';
				$result['code'] 	= 0;
			}
			elseif (! empty ( $memberByMobile)) {
				$result['message'] 	= $telephone . "已被注册";
				$result['code'] 	= 0;
			}
			elseif(empty($_GP ['pwd'])){
				$result['message'] 	= "请输入密码";
				$result['code'] 	= 0;
			}
			elseif(!$objValidator->is($_GP ['pwd'],'alphaNum') || $objValidator->passwordStrongValidator($_GP ['pwd'],6)<2){
				$result['message'] 	= "密码格式不对";
				$result['code'] 	= 0;
			}
			elseif(!$objValidator->lengthValidator($_GP ['pwd'],'6,20')){
				$result['message'] 	= "密码格式不对";
				$result['code'] 	= 0;
			}
			elseif (! checkSmsCode( trim($_GP ['VerifyCode']),$telephone)) {
			
				$result['message'] 	= '手机验证码输入错误';
				$result['code'] 	= 0;
			}
			else{
				$data = array('mobile' => $telephone,'pwd' => md5(trim($_GP ['pwd'])));
					
				if(mysqld_update('member', $data, array('openid' => $openid))){
				
					//释放验证码信息
					unset ( $_SESSION['api'][$telephone] );
					unset ( $_SESSION['api']['sms_code_expired'] );
				
					$result['message'] 	= '绑定手机号码成功';
					$result['code'] 	= 1;
				}
				else{
					$result['message'] 	= '绑定手机号码失败';
					$result['code'] 	= 0;
				}
			}
			
			break;
	
		case 'weixin' : 	//微信

			$code = trim($_GP['code']);
			
			//授权码为空时
			if(empty($code))
			{
				$result['message'] 	= "微信授权码不能空";
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
							
						$result['message'] 			= "微信账号绑定成功";
						$result['data']['nickname'] = $userinfo['nickname'];
						$result['data']['unionid'] 	= $userinfo['unionid'];
						$result['code'] 			= 1;
					}
					//被删除时
					elseif($wxfans['deleted']==1)
					{
						//重新开启绑定
						mysqld_update('weixin_wxfans', array('deleted'=>0,'modifiedtime'=>time(),'openid'=> $openid),array('unionid'=>$userinfo['unionid']));
							
						$result['message'] 			= "微信账号绑定成功";
						$result['data']['nickname'] = $userinfo['nickname'];
						$result['data']['unionid'] 	= $userinfo['unionid'];
						$result['code'] 			= 1;
					}
					else{
						$result['message'] 	= "该微信账号已经被绑定了";
						$result['code'] 	= 0;
					}
				}
			}
			
			break;
			
		case 'qq':		//QQ
			
			$access_token 	= trim($_GP['access_token']);
			
			$result = requestQQInfo($access_token);
				
			if($result['code']==1)
			{
				$qqInfo = $result['data']['qqInfo'];
				
				unset($result['data']);

				$qqFans = mysqld_select("SELECT qq_openid,openid,unionid,deleted FROM " . table('qq_qqfans') . " WHERE unionid = :unionid ", array(':unionid' => $qqInfo['unionid']));
				
				//无QQ账号信息时
				if(empty($qqFans))
				{
					$qq_data =array('createtime'	=> time (),
									'modifiedtime'	=> time(),
									'openid' 		=> $openid,
									'qq_openid'		=> $qqInfo['qq_openid'],
									'unionid'		=> $qqInfo['unionid'],
									'nickname'		=> $qqInfo['nickname'],
									'avatar'		=> $qqInfo['figureurl_qq_2'],
									'gender'		=> ($qqInfo['gender']=='男') ? 1: 2);
							
					//新增QQ用户账号
					if(mysqld_insert('qq_qqfans', $qq_data))
					{
						$result['message'] 			= "QQ账号绑定成功";
						$result['data']['nickname'] = $qqInfo['nickname'];
						$result['data']['unionid']	= $qqInfo['unionid'];
						$result['code'] 			= 1;
					}
					else{
						$result['message'] 	= "QQ账号绑定失败";
						$result['code'] 	= 0;
					}
				}
				elseif($qqFans['deleted']==1)
				{
					//重新开启
					mysqld_update('qq_qqfans', array('deleted'=>0,'modifiedtime'=>time(),'openid'=> $openid),array('unionid'=>$qqInfo['unionid']));
						
					$result['message'] 			= "QQ账号绑定成功";
					$result['data']['nickname'] = $qqInfo['nickname'];
					$result['data']['unionid']	= $qqInfo['unionid'];
					$result['code'] 			= 1;
				}
				else{
					$result['message'] 	= "该QQ账号已经被绑定了";
					$result['code'] 	= 0;
				}
			}
			
			break;
			
		case 'weibo':	//微博
			
			$uid 			= trim($_GP['uid']);
			$access_token 	= trim($_GP['access_token']);
			
			$result = requestWeiboInfo($access_token,$uid);
						
			if($result['code']==1)
			{
				$userinfo = $result['data']['userinfo'];
				
				unset($result['data']);
							
				$weiboFans = mysqld_select("SELECT uid,openid,deleted FROM " . table('weibo_fans') . " WHERE uid = :uid ", array(':uid' => $userinfo['id']));
								
				//无微博账号信息时
				if(empty($weiboFans))
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
					
					$weibo_data = array('uid' 			=> $userinfo['id'],
										'openid' 		=> $openid,
										'nickname'		=> $userinfo['screen_name'],
										'avatar'		=> $userinfo['avatar_large'],
										'gender'		=> $gender,
										'createtime'	=> time(),
										'modifiedtime'	=> time()
								);
									
					//新增微博用户账号
					if(mysqld_insert('weibo_fans', $weibo_data)){
						
						$result['message'] 			= "微博账号绑定成功";
						$result['data']['nickname'] = $userinfo['screen_name'];
						$result['data']['uid'] 		= $userinfo['id'];
						$result['code'] 			= 1;
					}
					else{
						$result['message'] 	= "微博账号绑定失败";
						$result['code'] 	= 0;
					}
				}
				elseif($weiboFans['deleted']==1)
				{
					//重新开启
					mysqld_update('weibo_fans', array('deleted'=>0,'modifiedtime'=>time(),'openid'=> $openid),array('uid'=>$userinfo['id']));
							
					$result['message'] 			= "微博账号绑定成功";
					$result['data']['nickname'] = $userinfo['screen_name'];
					$result['data']['uid'] 		= $userinfo['id'];
					$result['code'] 			= 1;
				}
				else{
					$result['message'] 	= "该微博账号已经被绑定了";
					$result['code'] 	= 0;
				}
			}
			
			break;
			
		default:			//账号绑定详情
			
			$wxfans 	= mysqld_select("SELECT nickname,unionid FROM " . table('weixin_wxfans') . " WHERE openid = :openid and deleted=0 order by modifiedtime desc", array(':openid' => $openid));
			$weiboFans 	= mysqld_select("SELECT nickname,uid FROM " . table('weibo_fans') . " WHERE openid = :openid and deleted=0 order by modifiedtime desc", array(':openid' => $openid));
			$qqFans 	= mysqld_select("SELECT nickname,unionid FROM " . table('qq_qqfans') . " WHERE openid = :openid and deleted=0 order by modifiedtime desc", array(':openid' => $openid));
			$memberInfo = mysqld_select("SELECT mobile FROM " . table('member') . " where openid=:openid ", array(':openid' => $openid));
			
			
			
			$result['data']['mobile']	= $memberInfo['mobile'];
			$result['data']['weixin']	= $wxfans ? $wxfans : '';
			$result['data']['weibo']	= $weiboFans ? $weiboFans : '';
			$result['data']['qq']		= $qqFans ? $qqFans : '';
			$result['code'] 			= 1;
			
			break;
	}
	
}elseif ($member == 3) {
	$result['message'] 	= "该账号已在别的设备上登录";
	$result['code'] 	= 3;
}else {
	$result ['message'] = "用户还未登陆";
	$result ['code'] = 2;
}

echo apiReturn($result);
exit;
