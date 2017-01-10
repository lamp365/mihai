<?php
/**
 * 被关注者(他人)资料接口
 */

$openid = trim($_GP ['openid']);		//被关注者用户ID

if(empty($openid))
{
	$result['message'] 	= '用户ID不能为空';
	$result['code'] 	= 0;
}
else{
	$followed_member = mysqld_select("SELECT openid,realname,nickname,member_description,avatar,mobile FROM " . table('member') . " where openid=:openid and status=1", array(':openid' => $openid));
	
	//他人用户不存在
	if(empty($followed_member))
	{
		$result['message'] 	= '用户不存在或已被禁用！';
		$result['code'] 	= 0;
	}
	else{
		$member=get_member_account(true,true);
		
		//被人关注的数量（粉丝）
		$followedCount = mysqld_select("SELECT count(follow_id) as cnt FROM " . table('follow') . " where followed_openid=:openid", array(':openid' => $openid));
		
		//关注别人的数量（关注）
		$followerCount = mysqld_select("SELECT count(follow_id) as cnt FROM " . table('follow') . " where follower_openid=:openid", array(':openid' => $openid));
		
		$followed_member['fansCnt'] 	= $followedCount['cnt'];			//粉丝数
		$followed_member['followCnt'] 	= $followerCount['cnt'];			//关注别人的数量
		$followed_member['isFollow'] 	= isFollowed($openid,$member);		//是否已关注
		
		
		$result['data']['member_info'] 	= $followed_member;					//用户信息
		$result ['code'] 				= 1;
	}
	
}

echo apiReturn ( $result );
exit ();
