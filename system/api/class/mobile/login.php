<?php
	/**
	 * app 用户登陆接口
	 * @var unknown
	 * 
	 */
	
	$result = array();
	
	if (empty($_GP['telephone'])) {
		
		$result['message'] 	= "请输入手机号。";
		$result['code'] 	= 0;
	}
	elseif (empty($_GP['pwd'])) {
		
		$result['message'] 	= "请输入密码。";
		$result['code'] 	= 0;
	}
	else{
		// $member = get_member_account(true, true);
		// $oldsessionid = $member['openid'];
		$loginid = member_login($_GP['telephone'], $_GP['pwd']);
		
		if (empty($loginid)) {
				
			$result['message'] 	= "用户名或密码错误。";
			$result['code'] 	= 0;
		}
		elseif ($loginid == - 1) {
		
			$result['message'] 	= "账户已被禁用。";
			$result['code'] 	= 0;
		}
		else {
			//integration_session_account($loginid, $oldsessionid);
			$objOpenIm = new OpenIm();
			
			if (extension_loaded('Memcached')) {
				$mcache = new Mcache();
				// 登陆初始化
				$re = $mcache->init_msession($_REQUEST['device_code']);
				// if (!$re) {
				// 	$result['message'] 	= "登陆失败，账号信息初始化失败！";
				// 	$result['code'] 	= 0;
				// 	echo apiReturn($result);
				// 	exit;
				// }
			}
			//IM账号不存在时
			if(!$objOpenIm->isImUser($loginid))
			{
				$userinfos['userid'] 	= $loginid;
				$userinfos['password'] 	= randString(10);
				$userinfos['nick'] 		= $_GP['telephone'];
				$userinfos['name'] 		= $_GP['telephone'];
				$userinfos['icon_url'] 	= IM_ICON_URL;
				
				//创建IM账号
				$objOpenIm->createUser($userinfos);
			}

			$result['message'] 	= "账户登陆成功。";
			$result['code'] 	= 1;
		}
	}
	
	echo apiReturn($result);
	exit;