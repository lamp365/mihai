<?php
/**
 * 社交关注相关公共函数
 * 
 * @author setsu
 */


/**
 * 是否已经被关注
 * 
 * @param $followed_openid: string 被关注的用户ID
 * @param $member:用户登录信息
 * 
 * @return boolean
 */
function isFollowed($followed_openid,$member)
{
	//已登录
	if(!empty($member) AND $member != 3)
	{
		$follow = mysqld_select("SELECT follow_id FROM " . table('follow') . " where followed_openid=:followed_openid and follower_openid=:follower_openid ", array(':followed_openid' => $followed_openid,':follower_openid' => $member['openid']));
		
		//未被关注时
		if(empty($follow))
		{
			return 0;
		}
		else{ 
			return 1;
		}
	}
	//未登录
	else{
		return 0;
	}
}