<?php
	/**
	 * app 用户注册接口
	 * @var unknown
	 */

	$telephone = trim($_GP['telephone']);

	$member = mysqld_select ( "SELECT * FROM " . table ( 'member' ) . " where mobile=:mobile ", array (':mobile' => $telephone ) );
	
	$result = array();

	$objValidator	= new Validator();
	$objOpenIm 		= new OpenIm();
	
	if(empty($telephone))
	{
		$result['message'] 	= '请输入手机号码！';
		$result['code'] 	= 0;
	}
	elseif(!$objValidator->is($telephone,'mobile'))
	{
		$result['message'] 	= '请输入正确的手机号码！';
		$result['code'] 	= 0;
	}
	elseif (! empty ( $member)) {
		$result['message'] 	= $telephone . "已被注册。";
		$result['code'] 	= 0;
	}
	elseif(empty($_GP ['pwd'])){
		$result['message'] 	= "请输入密码！";
		$result['code'] 	= 0;
	}
	elseif(!$objValidator->is($_GP ['pwd'],'alphaNum') || $objValidator->passwordStrongValidator($_GP ['pwd'],6)<2){
		$result['message'] 	= "密码格式不对！";
		$result['code'] 	= 0;
	}
	elseif(!$objValidator->lengthValidator($_GP ['pwd'],'6,20')){
		$result['message'] 	= "密码格式不对！";
		$result['code'] 	= 0;
	}
	elseif (! check_verify ( trim($_GP ['VerifyCode']),$telephone)) {
	
		$result['message'] 	= '手机验证码输入错误！';
		$result['code'] 	= 0;
	}
	else{
		$openid = date ( "YmdH", time () ) . rand ( 100, 999 );
		$hasmember = mysqld_select ( "SELECT * FROM " . table ( 'member' ) . " WHERE openid = :openid ", array (
				':openid' => $openid
		) );
		if (! empty ( $hasmember ['openid'] )) {
			$openid = date ( "YmdH", time () ) . rand ( 100, 999 );
		}
		$data = array ('mobile' 	=> $telephone,
						'pwd' 		=> md5(trim($_GP['pwd'])),
						'createtime'=> time (),
						'status' 	=> 1,
						'istemplate'=> 0,
						'experience'=> 0,
						'mess_id' 	=> 0,
						'openid' 	=> $openid,
						'nickname'	=> '掌门人'+randString(5));
		
		$userinfos['userid'] 	= $openid;
		$userinfos['password'] 	= randString(10);
		$userinfos['nick'] 		= $data['nickname'];
		$userinfos['name'] 		= $data['nickname'];
		$userinfos['icon_url'] 	= IM_ICON_URL;
			
		if($objOpenIm->createUser($userinfos))
		{
			if(mysqld_insert ( 'member', $data )){
			
				$member = get_session_account ();
				$oldsessionid = $member ['openid'];
			
				$loginid = save_member_login ( '', $openid );
			
				integration_session_account ( $loginid, $oldsessionid );

				if (extension_loaded('Memcached')) {
					$mcache = new Mcache();
					// 登陆初始化
					$mcache->init_msession($_REQUEST['device_code']);
				}
				
				//释放验证码信息
				unset ( $_SESSION['api'][$telephone] );
				unset ( $_SESSION['api']['sms_code_expired'] );
					
				$result['message'] 			= '注册成功！';
				$result['data']['openid'] 	= $openid;
				$result['code'] 			= 1;
			}
			else{
				$result['message'] 	= '注册失败！';
				$result['code'] 	= 0;
			}
		}
		else{
		
			$result['message'] 	= '注册失败！';
			$result['code'] 	= 0;
		}
	}
	
	echo apiReturn($result);
	exit;
	
	/**
	 * 验证码验证
	 * 
	 * @param $verify 验证码
	 * @param $telephone 手机号码
	 * 
	 * @return boolean
	 */
	function check_verify($verify,$telephone) {
		
		logRecord('telephone:'.$telephone,'register');
		logRecord('verify:'.$verify,'register');

		//验证码未过期
		if(isset($_SESSION['api']['sms_code_expired']) && $_SESSION['api']['sms_code_expired']>time())
		{
			//验证码是否正确
			if (isset($_SESSION['api'][$telephone]) && strtolower ( $_SESSION['api'][$telephone] ) == strtolower ( $verify )) {
				
				return true;
			}
		}
		else{
			unset ( $_SESSION['api'][$telephone] );
			unset ( $_SESSION['api']['sms_code_expired'] );
		}
		
		return false;
	}
