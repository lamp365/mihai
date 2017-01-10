<?php
	/**
	 * app 更换手机号码接口
	 * @var unknown
	 * 
	 */
	$result = array();

	$member = get_member_account ( true, true );
	$op = $_GP ['op'];

	if (!empty($member) AND $member != 3) {
		
		switch ($op) {
			
			case 'change_mobile':			//更换新号码
				
				$telephone = trim($_GP['telephone']);
				
				//短信验证码为空时
				if(empty($_GP ['VerifyCode']))
				{
					$result['message'] 	= '手机验证码不能为空！';
					$result['code'] 	= 0;
				}
				if (! checkSmsCode ( trim($_GP ['VerifyCode']),$telephone)) {
				
					$result['message'] 	= '手机验证码输入错误！';
					$result['code'] 	= 0;
				}
				elseif(mysqld_select ( "SELECT openid FROM " . table ( 'member' ) . " where mobile=:mobile ", array (':mobile' => $telephone ) ))
				{
					$result['message'] 	= '新手机号码已经被注册！';
					$result['code'] 	= 0;
				}
				else{
					$data = array('mobile' => $telephone);
					
					if(mysqld_update('member', $data, array('openid' => $member['openid']))){
						
						//释放验证码信息
						unset ( $_SESSION['api'][$telephone] );
						unset ( $_SESSION['api']['sms_code_expired'] );
						
						$result['message'] 	= '更换手机号码成功！';
						$result['code'] 	= 1;
					}
					else{
						$result['message'] 	= '更换手机号码失败！';
						$result['code'] 	= 0;
					}
				}
				
				break;
				
			default:						//旧手机号码短信验证
				
				$memberInfo = mysqld_select ( "SELECT mobile FROM " . table ( 'member' ) . " where openid=:openid ", array (':openid' => $member['openid']));
				
				//短信验证码为空时
				if(empty($_GP ['VerifyCode']))
				{
					$result['message'] 	= '手机验证码不能为空！';
					$result['code'] 	= 0;
				}
				elseif (! checkSmsCode ( trim($_GP ['VerifyCode']),$memberInfo['mobile'])) {
				
					$result['message'] 	= '手机验证码输入错误！';
					$result['code'] 	= 0;
				}
				else{
					
					//释放验证码信息
					unset ( $_SESSION['api'][$memberInfo['mobile']] );
					unset ( $_SESSION['api']['sms_code_expired'] );
					
					$result['message'] 	= '手机验证码验证成功！';
					$result['code'] 	= 1;
				}
				
				break;
		}
	}
	elseif ($member == 3) {
		$result['message'] 	= "该账号已在别的设备上登录！";
		$result['code'] 	= 3;
	}else {
		$result ['message'] = "用户还未登陆。";
		$result ['code'] = 2;
	}

	echo apiReturn($result);
	exit;