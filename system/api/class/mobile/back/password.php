<?php
	/**
	 * app 修改密码
	 * @var unknown
	 * 
	 */
	
	$result = array();
	
	$member=get_member_account(true,true);
	
	if(!empty($member) AND $member != 3)
	{
		$objValidator = new Validator();
		
		$memberInfo = mysqld_select("SELECT * FROM " . table('member') . " where openid=:openid ", array(':openid' => $member['openid']));
		
		if(empty($_GP ['old_password'])){
			$result['message'] 	= "请输入原密码！";
			$result['code'] 	= 0;
		}
		elseif(empty($_GP ['new_password'])){
			$result['message'] 	= "请输入新密码！";
			$result['code'] 	= 0;
		}
		elseif(empty($_GP ['re_new_password'])){
			$result['message'] 	= "请输入确认新密码！";
			$result['code'] 	= 0;
		}
		elseif($_GP ['new_password'] != $_GP ['re_new_password']){
			
			$result['message'] 	= "新密码与确认新密码不一致！";
			$result['code'] 	= 0;
		}
		elseif($memberInfo['pwd']!=md5($_GP ['old_password']))
		{
			$result['message'] 	= "原密码不正确！";
			$result['code'] 	= 0;
		}
		elseif(!$objValidator->is($_GP ['new_password'],'alphaNum') || $objValidator->passwordStrongValidator($_GP ['new_password'],6)<2){
			$result['message'] 	= "密码格式不对！";
			$result['code'] 	= 0;
		}
		elseif(!$objValidator->lengthValidator($_GP ['new_password'],'6,20')){
			$result['message'] 	= "密码格式不对！";
			$result['code'] 	= 0;
		}
		else{
			$data = array('pwd' => md5($_GP ['new_password']));
		
			mysqld_update('member', $data, array('openid' => $member['openid']));
		
			$result['message'] 	= '修改密码成功！';
			$result['code'] 	= 1;
		}
	}elseif ($member == 3) {
		$result['message'] 	= "该账号已在别的设备上登录！";
		$result['code'] 	= 3;
	}else{
		$result['message'] 	= "用户还未登陆。";
		$result['code'] 	= 2;
	}
	
	echo apiReturn($result);
	exit;
