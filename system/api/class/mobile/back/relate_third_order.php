<?php
/**
 * 关联第三方订单接口
 */


$result = array();

$member = get_member_account ( true, true );

if(!empty($member) AND $member != 3)
{
	$openid 	= $member ['openid'];
	$telephone 	= trim($_GP['telephone']);
	$VerifyCode	= trim($_GP['VerifyCode']);
	
	
	$objValidator	= new Validator();
	
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
	//短信验证码为空时
	elseif(empty($VerifyCode))
	{
		$result['message'] 	= '手机验证码不能为空！';
		$result['code'] 	= 0;
	}
	elseif (! checkSmsCode ( trim($_GP ['VerifyCode']),$telephone)) {
	
		$result['message'] 	= '手机验证码输入错误！';
		$result['code'] 	= 0;
	}
	else{
		if(updateThirdOrderOwner($telephone,$openid)){
			
			$result['message'] 	= '订单关联成功';
			$result['code'] 	= 1;
		}
		else{
			$result['message'] 	= '亲，您输入的手机号下没有关联的订单';
			$result['code'] 	= 0;
		}
	}
	
}
elseif ($member == 3) {
	$result['message'] 	= "该账号已在别的设备上登录！";
	$result['code'] 	= 3;
}else{
	$result['message'] 	= "用户还未登陆。";
	$result['code'] 	= 2;
}


echo json_encode($result);
exit;


