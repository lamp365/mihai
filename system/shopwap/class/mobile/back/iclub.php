<?php
$openid   = checkIsLogin();
$op = empty($_GP['op']) ? 'display' : $_GP['op'];
if($op == 'display'){
	// 获取用户签到信息
	$merArr  = array();
	$merInfo = array();
	if($openid){
		//获取用户的签到数据
		$merArr  = mysqld_select("select * from ".table('member_sign')." where openid='{$openid}'");
		$merInfo = member_get($openid);
		$merInfo['name'] = getNameByMemberInfo($merInfo);
	}
	//获取用户特权以及会员信息
	$priviel = get_member_priviel($openid);

	//签到配置项
	$sign_seting = globaSetting();

	//查找出有兑换礼品的商品
	$config     = mysqld_select("SELECT * FROM " . table('addon7_config') );
	$goods_list = array();
	if($config['open_gift_change'] == 1){
		$now_time    = time();
		$goods_list  = mysqld_selectall("select * from ".table('addon7_award')." where add_jifen_change=1  order by sort desc,id desc limit 8");
	}

	//获取广告图
	$master_adv = mysqld_selectall("select * from ".table('shop_adv')." where page=6");


	//获取用户地址
	$userAddress   = array();
	if($openid)
		$userAddress   =   mysqld_selectall("SELECT * FROM " . table('shop_address') . " WHERE  deleted = 0 and  openid = {$openid} order by isdefault desc ");


	//记住当前地址
	tosaveloginfrom();
	include themePage('iclub');
}else if($op == 'game'){
	include themePage('vip_game');
}else if($op == 'privilege'){
	//成长特权
	// 获取用户签到信息
	$merInfo = array();
	if($openid){
		$merInfo = member_get($openid);
		$merInfo['name'] = getNameByMemberInfo($merInfo);
	}
	//获取用户特权以及会员信息
	$priviel = get_member_priviel($openid);

	//获取所有的特权和等级
	$rank_list    = mysqld_selectall('SELECT * FROM '.table('rank_model')." order by sort asc");
	$priviel_list = mysqld_selectall("select * from ".table('rank_privile')." order by sort desc");

	//记住当前地址
	tosaveloginfrom();
	include themePage('vip_privilege');

}else if($op == 'list'){

	$psize =  8;
	$pindex = max(1, intval($_GP["page"]));
	$limit  = ' limit '.($pindex-1)*$psize.','.$psize;

	if(is_mobile_request()){
		//获取广告图
		$master_adv = mysqld_selectall("select * from ".table('shop_adv')." where page=6");

		//获取用户默认地址
		$merInfo = array();
		if($openid){
			$merInfo = member_get($openid);
		}
	}

	//获取用户地址
	$userAddress   = array();
	if($openid)
		$userAddress   =   mysqld_selectall("SELECT * FROM " . table('shop_address') . " WHERE  deleted = 0 and  openid = {$openid} order by isdefault desc ");


	//查找出有兑换礼品的商品
	$config     = mysqld_select("SELECT * FROM " . table('addon7_config') );
	$goods_list = array();
	if($config['open_gift_change'] == 1){
		$now_time    = time();
		$goods_list  = mysqld_selectall("select * from ".table('addon7_award')." where add_jifen_change=1  order by sort desc,id desc {$limit}");
	}
	$total = $total = '';
	if(!empty($goods_list)){
		$sqlnum = "SELECT count(id) FROM".table('addon7_award')." where add_jifen_change=1";
		$total  = mysqld_selectcolumn($sqlnum);
		$pager  = pagination($total, $pindex, $psize);
	}

	//当手机端滑动的时候加载下一页
	if ($_GP['nextpage'] == 'ajax' && $_GP['page'] > 1 ){
		if ( empty($goods_list) ){
			die(showAjaxMess(1002,'查无数据！'));
		}else{
			die(showAjaxMess(200,$goods_list));
		}
	}

	include themePage('privilege_list');

}else if($op == 'recorder'){
	//兑换记录
	$changeRecorder      = array();
	if($openid)
		$changeRecorder  = mysqld_selectall("select c.*,a.title,a.logo,a.jifen_change from ".table('addon7_change')." as c left join ". table('addon7_award')." as a on a.id=c.award_id where c.openid={$openid} order by c.id desc");
	include themePage('iclub_recorder');

}else if($op == 'level'){
	//成长体系
	$list  = mysqld_selectall('SELECT * FROM '.table('rank_model')." order by sort asc");
	include themePage('vip_level');

}else if($op == 'sign'){
	if(empty($openid)){
		die(showAjaxMess(1002,"对不起请您先登录！"));
	}
	//用户签到
	$datainfo = member_sign($openid);
	die(json_encode($datainfo));

}else if($op == 'change'){  //积分兑换礼品
	$config     = mysqld_select("SELECT * FROM " . table('addon7_config') );
	if($config['open_gift_change'] == 0){
		die(showAjaxMess(1002,'积分兑换活动已结束！'));
	}
	//兑换商品
	if(empty($_GP['id'])){
		die(showAjaxMess(1002,'参数有误！'));
	}
	if(empty($openid)){
		die(showAjaxMess(1002,'您还没登录！'));
	}
	$award = mysqld_select("select * from ".table('addon7_award')." where id={$_GP['id']}");
	if(empty($award) || $award['add_jifen_change'] == 0){
		die(showAjaxMess(1002,'商品不存在！'));
	}
	$member = member_get($openid);
	if($member['credit'] < $award['jifen_change']){
		die(showAjaxMess(1002,'您的积分不足以兑换！'));
	}

	if($award['endtime'] > time()){
		//如果开始时间 大于当前时间 说明还不能兑换
		die(showAjaxMess(1002,'请进行期待！'));
	}

	if($award['award_type'] != 2){
		//不是优惠卷类型，需验证身份证
		if(empty($_GP['address_id'])){
			die(showAjaxMess(1002,'参数有误！'));
		}
		$defaultAddress   =   mysqld_select("SELECT * FROM " . table('shop_address') . " WHERE  deleted = 0 and id = {$_GP['address_id']} and openid = {$openid} ");
		if(empty($defaultAddress)){
			die(showAjaxMess(1004,'请您先设置收货地址'));
		}
	}else{
		//查找优惠卷是否已经过期了
		$bonus = mysqld_select("select send_end_date from ".table('bonus_type')." where type_id={$award['gid']}");
		if(empty($bonus)){
			die(showAjaxMess("1002",'对不起，参数有误！'));
		}
		if($bonus['send_end_date'] < time()){
			die(showAjaxMess("1002",'哦哦，优惠卷都被抢光了！'));
		}
	}

	//插入一个记录
	if($award['award_type'] == 2){
		$des    = '您已经获得优惠卷';
	}else{
		$des    = '系统正在审核中';
	}
	$data = array(
		'openid'         => $openid,
		'award_id'       => $_GP['id'],
		'createtime'     => time(),
		'address_id'     => intval($_GP['address_id']),
		'status'		 => 1
	);
	$res  = mysqld_insert('addon7_change',$data);
	if($res){
		if($award['award_type'] == 2){
			//发放优惠卷
			change_user_bonus($award['gid'],$openid);
		}
		//扣除积分
		member_credit($openid,$award['jifen_change'],'usecredit',PayLogEnum::getLogTip('LOG_JIFEN_CHANGE_TIP'));
		die(showAjaxMess(200,array('tit'=>'兑换成功','des'=>$des)));
	}else{
		die(showAjaxMess(1002,'兑换失败了！'));
	}

}
