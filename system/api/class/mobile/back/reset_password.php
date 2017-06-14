<?php
	/**
	 * app 忘记密码
	 * @var unknown
	 * 
	 */

	$member = mysqld_select ( "SELECT * FROM " . table ( 'member' ) . " where mobile=:mobile ", array (':mobile' => $_GP ['telephone'] ) );
	
	$result = array();
	
	$objValidator = new Validator();
	
	if (empty ( $member)) {
		$result['message'] 	= $_GP ['telephone'] . "还未注册。";
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
	elseif (! check_verify ( trim($_GP ['VerifyCode']),trim($_GP['telephone']))) {
	
		$result['message'] 	= '手机验证码输入错误！';
		$result['code'] 	= 0;
	}
	else{
		$data = array('pwd' => md5($_GP ['pwd']));

		mysqld_update('member', $data, array('mobile' => $_GP['telephone']));
		
		//清除短信验证码信息
		unset ( $_SESSION['api'][$telephone] );
		unset ( $_SESSION['api']['sms_code_expired'] );
		
		$result['message'] 	= '密码重置成功！';
		$result['code'] 	= 1;
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
		
		logRecord('telephone:'.$telephone,'resetpwdlog');
		logRecord('verify:'.$verify,'resetpwdlog');

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