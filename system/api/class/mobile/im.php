<?php
$member	=get_member_account(true,true);
$openid =$member['openid'];

if(!empty($member) AND $member != 3)
{
	$operation = $_GP ['op'];
	
	$objOpenIm = new OpenIm();
	
	switch ($operation)
	{
		/*
		case 'insert':			//新增IM用户
			
			$userinfos['userid'] 	= $openid;
			$userinfos['password'] 	= randString(10);
			$userinfos['nick'] 		= $member['nickname'];
			$userinfos['name'] 		= $member['nickname'];
			
			if($objOpenIm->createUser($userinfos))
			{
				$result['message'] 	= "用户新增成功。";
				$result['code'] 	= 1;
			}
			else{
				
				$result['message'] 	= "用户新增失败。";
				$result['code'] 	= 0;
			}
			
			break;
		*/
			
		case 'update':			//编辑IM用户
			
			$userinfos['userid']	= $openid;			//必填项
			$userinfos['password'] 	= randString(10);
			
			if($objOpenIm->updateUser($userinfos))
			{
				$result['message'] 	= "用户更新成功。";
				$result['code'] 	= 1;
			}
			else{
			
				$result['message'] 	= "用户更新失败。";
				$result['code'] 	= 0;
			}
			
			break;
			
		case 'remove':			//删除IM用户
			
			if($objOpenIm->deleteUser($openid))
			{
				$result['message'] 	= "用户删除成功。";
				$result['code'] 	= 1;
			}
			else{
				$result['message'] 	= "用户删除失败。";
				$result['code'] 	= 0;
			}
			
			break;
			
		case 'custmsg':			//推送自定义openim消息
			
			/*$custmsg['to_users']	= 'mytest2016092903';
			$custmsg['summary']		= 'test2客户端最近消息里面显示的消息摘要';
			$custmsg['data']		= 'push payload';
			$custmsg['from_taobao'] = '0';*/
			
			$custmsg['to_users']	= $_GP['to_users'];
			$custmsg['summary']		= $_GP['summary'];
			$custmsg['data']		= 'push payload';
			
			if(isset($_GP['from_taobao']))
			{
				$custmsg['from_taobao'] = (int)$_GP['from_taobao'];
			}
			
			$resp=$objOpenIm->custMessagePush($custmsg);
			
			if($resp)
			{
				$result['message'] 	= "消息发送成功。";
				$result['code'] 	= 1;
			}
			else{
				$result['message'] 	= "消息发送失败。";
				$result['code'] 	= 0;
			}
			
			break;
		
			
		case 'immsg':			//openim标准消息发送
			
			$immsg['to_users']	= $_GP['to_users'];
			$immsg['context']	= $_GP['context'];
			
			$resp = $objOpenIm->imMessagePush($immsg);
			
			if($resp)
			{
				$result['message'] 	= "消息发送成功。";
				$result['code'] 	= 1;
			}
			else{
				$result['message'] 	= "消息发送失败。";
				$result['code'] 	= 0;
			}
			
			break;
			
			
		case 'chatlogs':		//聊天记录查询
			/*
			$uid1 		= 'nrctongyong';
			$uid2 		= 'mytest2016092902';
			$starttime 	= '1475143132';
			$endtime 	= '1476143132';
			$count 		= strval(100);*/
			
			$uid1 		= $_GP['uid1'];
			$uid2 		= $_GP['uid2'];
			$starttime 	= $_GP['starttime'];
			$endtime 	= $_GP['endtime'];
			$count 		= $_GP['count'];
			
			$resp = $objOpenIm->getChatlogs($uid1,$uid2,$starttime,$endtime,$count);
			
			if($resp)
			{
				$result['data']['message_list']	= $resp;
				$result['code']					= 1;
			}
			else{
				$result['message'] 	= "聊天记录不存在。";
				$result['code'] 	= 0;
			}
			
			break;
		/*	
		default:				//用户详情
			
			$resp = $objOpenIm->getUserInfo($openid);
			
			$result['data']['userinfos']= $resp->userinfos;
			$result['code'] 			= 1;
			
			break;
			*/
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