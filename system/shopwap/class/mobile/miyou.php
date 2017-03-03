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
	//获取本周分享达人 邀请好友金额收入的 排序
	showSharersEarn

	if($openid){
		//第三方平台订单
		$thirdSql = "SELECT ordersn,price,from_platform,createtime FROM " . table('third_order');
		$thirdSql.= " where openid='".$openid."' ";
		$thirdSql.= " and status=3 ";
		$thirdSql.= " and share_status !=2 ";
		$thirdSql.= " order by createtime desc ";
		$thirdOrderList = mysqld_selectall($thirdSql);

		//自有平台订单
		$sql = "SELECT ordersn,price,createtime,'self' as from_platform FROM " . table('shop_order');
		$sql.= " where openid='".$openid."' ";
		$sql.= " and status=3 ";
		$sql.= " and share_status !=2 ";
		$sql.= " order by createtime desc ";
		$orderList = mysqld_selectall($sql);

		$list = array_merge($orderList,$thirdOrderList);
		//获取一个分享中的   订单状态是 1
		$share_order = getShareOrder($list);
	}else{
		//没登录
	}

	include themePage('share_page');
}
