<?php
	/**
	 * app 发送短信验证码
	 * @var unknown
	 */
	
	$result = array();
	
	$telephone 	= trim($_GP ['telephone']);		//手机号码
	$type		= (int)$_GP ['type'];			//短信模板
	
	//允许发送短信时
	if(isSendSms($telephone,$type))
	{
		//发送短信
		$code = set_sms_code ( $telephone,1,$type);
		
		if (! $code ) {
		
			$result['message'] 	= '验证码发送失败！';
			$result['code'] 	= 0;
		}
		else{
			$_SESSION['api'][$telephone] 		= $code;
			$_SESSION['api']['sms_code_expired']= time()+120;		//短信的有效期,120s
		
			logRecord('telephone:'.$_SESSION['api'][$telephone],'send_sms_code');
			logRecord('sms_code_expired:'.$_SESSION['api']['sms_code_expired'],'send_sms_code');
		
		
			$result['message'] 	= '验证码发送成功！';
			$result['code'] 	= 1;
		}
	}
	//修改密码
	elseif($type==1)
	{
		$result['message'] 	= '该手机号码尚未注册，请先注册！';
		$result['code'] 	= 0;
	}
	//注册
	elseif($type==0){
		$result['message'] 	= '该手机号码已经注册，请用其他号码注册！';
		$result['code'] 	= 0;
	}
	
	echo apiReturn($result);
	exit;

	
	/**
	 * 判断是否允许发送短信
	 * @param $telephone:string 手机号码
	 * @param $type:int 短信模板类型
	 * 
	 * @return boolean
	 */
	function isSendSms($telephone,$type)
	{
		$member = mysqld_select("SELECT openid FROM " . table('member') . " where mobile=:mobile", array(':mobile' => $telephone));
		
		//修改密码
		if($type==1)
		{
			if($member)
			{
				return true;
			}
			else{
				return false;
			}
		}
		//注册时
		elseif($type==0){
			//已经注册时
			if($member)
			{
				return false;
			}
			else{
				return true;
			}
		}
		
		return true;
	}