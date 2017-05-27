<?php
	/**
	 * app 用户列表接口
	 * @var unknown
	 *
	 */

	$result = array();

	$page 	= $_GP['page'] ? (int)$_GP['page'] : 1;			//页码
	$limit 	= $_GP['limit'] ? (int)$_GP['limit'] : 10;		//每页记录数
	$member	= get_member_account(true,true);
	
	if(!empty($member) AND $member != 3)
	{
		$sql = "SELECT openid,realname,nickname,member_description,avatar,mobile,0 as isFollow FROM " . table('member') . " where status=1 and istemplate=0 ";
		$sql.= " and openid!='".$member['openid']."' ";
		$sql.= " and openid not in(select followed_openid from ".table('follow')." where follower_openid ='".$member['openid']."' ) ";
		$sql.= " order by RAND( ) ";
		$sql.= " limit ".(($page-1)*$limit).','.$limit;
		
		$arrMember = mysqld_selectall($sql);
	}
	//未登录
	else{
		$sql = "SELECT openid,realname,nickname,member_description,avatar,mobile,0 as isFollow FROM " . table('member') . " where status=1 and istemplate=0 ";
		$sql.= " order by RAND( ) ";
		$sql.= " limit ".(($page-1)*$limit).','.$limit;
		
		$arrMember = mysqld_selectall($sql);
	}
	
	$result['data']['member_list'] 	= $arrMember;
	$result['code'] 				= 1;
	
	echo json_encode($result);
	exit;
	
	