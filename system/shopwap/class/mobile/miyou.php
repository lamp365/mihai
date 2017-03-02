<?php
$op = empty($_GP['op']) ? 'display': $_GP['op'];
$openid = checkIsLogin();

if($op == 'display'){
	if(!$openid){
		message("对不起，您还没登录！",mobile_url('login'),'error');
	}
	$psize =  16;
	$pindex = max(1, intval($_GP["page"]));
	$limit = ' limit '.($pindex-1)*$psize.','.$psize;

	$memberInfo = mysqld_select("SELECT recommend_openid FROM " . table('member') . " where openid=:openid ", array(':openid' => $openid));

	//推荐人信息
	$recommendInfo = mysqld_select("SELECT openid,gold,nickname,realname,credit,friend_count,avatar FROM " . table('member') . " where openid=:openid ", array(':openid' => $memberInfo['recommend_openid']));
	$recommendInfo = getMiyouInfo($openid,$recommendInfo['openid'],$recommendInfo);

	//获取我的觅友
	$friend_member = mysqld_selectall("select openid,gold,nickname,realname,credit,friend_count,avatar FROM " . table('member') ." where recommend_openid={$openid} {$limit}");
	if(!empty($friend_member)){
		foreach($friend_member as &$friend){
			$friend = getMiyouInfo($openid,$friend['openid'],$friend);
		}
	}

	include themePage('miyou');
}else if($op == 'invite'){
	include themePage('invite_miyou');
}else if($op == 'share'){
	include themePage('share_page');
}
