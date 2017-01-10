<?php
/**
 * app 账号解绑接口
 * @var unknown
 *
 */
$result = array();

$member = get_member_account ( true, true );
$op 	= $_GP ['op'];

if (!empty($member) AND $member != 3) {
	
	$openid = $member['openid'];
	
	//获取实时的member信息
	$memberInfo = mysqld_select("SELECT openid,mobile FROM " . table('member') . " where openid=:openid ", array(':openid' => $openid));

	switch ($op) {
	
		case 'weixin' : 	//微信
			
			if(isUnbind($memberInfo)){
				
				if(mysqld_update('weixin_wxfans', array('deleted'=>1),array('openid'=>$openid,'unionid'=>trim($_GP['unionid']))))
				{
					$result['message'] 	= "微信账号解绑成功。";
					$result['code'] 	= 1;
				}
				else{
					$result['message'] 	= "微信账号解绑失败。";
					$result['code'] 	= 0;
				}
			}
			else{
				$result['message'] 	= "您未绑定其他掌门人账号，若解除绑定，该掌门人账号将无法登陆。";
				$result['code'] 	= 0;
			}
			
			break;
			
		case 'qq':			//QQ
			
			if(isUnbind($memberInfo)){
			
				if(mysqld_update("qq_qqfans",array('deleted'=>1), array('openid' => $openid,'qq_openid'=>trim($_GP['qq_openid']))))
				{
					$result['message'] 	= "QQ账号解绑成功。";
					$result['code'] 	= 1;
				}
				else{
					$result['message'] 	= "QQ账号解绑失败。";
					$result['code'] 	= 0;
				}
			}
			else{
				$result['message'] 	= "您未绑定其他掌门人账号，若解除绑定，该掌门人账号将无法登陆。";
				$result['code'] 	= 0;
			}
			
			break;

		case 'weibo':		//微博
			
			if(isUnbind($memberInfo)){
					
				if(mysqld_update("weibo_fans",array('deleted'=>1), array('openid' => $openid,'uid'=>trim($_GP['uid']))))
				{
					$result['message'] 	= "微博账号解绑成功。";
					$result['code'] 	= 1;
				}
				else{
					$result['message'] 	= "微博账号解绑失败。";
					$result['code'] 	= 0;
				}
			}
			else{
				$result['message'] 	= "您未绑定其他掌门人账号，若解除绑定，该掌门人账号将无法登陆。";
				$result['code'] 	= 0;
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

/**
 * 是否可以解绑
 * 
 * @param $member:array 当前用户信息
 * @return boolean
 */
function isUnbind($member)
{
	if(!empty($member['mobile']))
	{
		return true;
	}
	else{
		$bindNum = 0;
		
		$wxfans 	= mysqld_select("SELECT count(nickname) as cnt FROM " . table('weixin_wxfans') . " WHERE openid = :openid and deleted=0 ", array(':openid' => $member['openid']));
		$weiboFans 	= mysqld_select("SELECT count(nickname) as cnt FROM " . table('weibo_fans') . " WHERE openid = :openid and deleted=0 ", array(':openid' => $member['openid']));
		$qqFans 	= mysqld_select("SELECT count(nickname) as cnt FROM " . table('qq_qqfans') . " WHERE openid = :openid and deleted=0 ", array(':openid' => $member['openid']));
		
		$bindNum = $wxfans['cnt']+$weiboFans['cnt']+$qqFans['cnt'];
		
		if($bindNum>=2)
		{
			return true;
		}
		else{
			return false;
		}
	}
}