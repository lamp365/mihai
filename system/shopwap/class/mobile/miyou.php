<?php
if(!is_mobile_request()){
	message("请下载APP或者移动端打开",refresh(),'error');
}
$op = empty($_GP['op']) ? 'display': $_GP['op'];
$openid = checkIsLogin();

if($op == 'display'){
	if(!$openid){
		message("对不起，您还没登录！",mobile_url('login'),'error');
	}
	$psize =  16;
	$pindex = max(1, intval($_GP["page"]));
	$limit = ' limit '.($pindex-1)*$psize.','.$psize;

	//获取我的觅友
	$friend_member = mysqld_selectall("select openid,gold,nickname,realname,mobile,credit,friend_count,avatar FROM " . table('member') ." where recommend_openid={$openid} {$limit}");
	if(!empty($friend_member)){
		foreach($friend_member as &$friend){
			$friend = getMiyouInfo($openid,$friend['openid'],$friend);
		}
	}
	//当手机端滑动的时候加载下一页
	if ($_GP['nextpage'] == 'ajax' && $_GP['page'] > 1 ){
		if ( empty($friend_member) ){
			die(showAjaxMess(1002,'查无数据！'));
		}else{
			die(showAjaxMess(200,$friend_member));
		}
	}


	$memberInfo = mysqld_select("SELECT recommend_openid FROM " . table('member') . " where openid=:openid ", array(':openid' => $openid));
	//推荐人信息
	$recommendInfo = mysqld_select("SELECT openid,gold,nickname,realname,mobile,credit,friend_count,avatar FROM " . table('member') . " where openid=:openid ", array(':openid' => $memberInfo['recommend_openid']));
	$recommendInfo = getMiyouInfo($openid,$recommendInfo['openid'],$recommendInfo,2);
	include themePage('miyou');

}else if($op == 'invite'){
	$self_info = $share_order = $zhuli_info = $all_zhuli_info = array();
	if($openid){  //如果已经登录
		//获取一个分享中的 订单
		$share_order = mysqld_select("SELECT ordersn,price from ".table('shop_order')." where openid={$openid} and status=3 and share_status=1");
		if(empty($share_order))
			$share_order = mysqld_select("SELECT ordersn,price from ".table('third_order')." where openid={$openid} and status=3 and share_status=1");

		//是否跳转到分分享注册页面
		isGoToShareRegeditPage($openid,$share_order['ordersn']);

		if(!empty($share_order)){
			//获取 本笔订单的助力人数和钱
			$zhuli_info = zhuliOrderTotalMoneyAndNum($share_order['ordersn']);
		}

		//获取总的助力人数和钱
		$all_zhuli_info = zhuliOrderTotalMoneyAndNum($openid,'all');
		//个人信息
		$self_info      = member_get($openid);
	}else{   //还没登录
		//是否跳转到分分享注册页面
		isGoToShareRegeditPage($openid);
	}

	//微信分享所需的参数
	$weixin_share = get_share_js_parame();
	//获取本周分享达人 邀请好友金额收入的 排序
//	$showSharers  = showSharersList(2);
	//邀请收益
	$invit_money   = array('direct_share_price'=>0,'order_share_price'=>'0','direct_share_jifen'=>0);
	$inviteSetting = mysqld_select ( "SELECT value FROM " . table ( 'config' ) . " where name='invite_setting' " );
	if(!empty($inviteSetting)){
		$invit_money   = unserialize($inviteSetting['value']);
	}

	//优惠礼包 新手礼包的总额
	$new_member_money = newmember_bonuse_money();
	//下单返佣数据比例
	$bankSetting = bankSetting();

	//获取qq客服
	$cfg   = globaSetting();
	$qqarr = getQQ_onWork($cfg);

	//记住当前地址
	tosaveloginfrom();
	include themePage('invite_miyou');

}else if($op == 'share'){
	if(empty($_GP['accesskey'])){
		message("对不起，参数有误！",refresh(),'error');
	}
	//获取分类图片 和对应分类的推荐商品
	$cat_result = getOrderBelongCatByAccesskey($_GP['accesskey']);
    $cat_pic    = $cat_result['cat_pic'];
    $cat_goods  = $cat_result['goods'];
	include themePage('share_page');

}else if($op == 'choose_order'){
	if(empty($openid)){
		message("请您先登录！",mobile_url('login'),'error');
	}
	//第三方平台订单
	$thirdSql = "SELECT order_id as oid,ordersn,share_status,price,from_platform,createtime FROM " . table('third_order');
	$thirdSql.= " where openid='".$openid."' ";
	$thirdSql.= " and status=3 ";//交易成功订单
	$thirdSql.= " and (share_status =0 or share_status =1) ";
	$thirdOrderList = mysqld_selectall($thirdSql);

	//自有平台订单
	$sql = "SELECT id as oid,ordersn,price,share_status,createtime,'self' as from_platform FROM " . table('shop_order');
	$sql.= " where openid='".$openid."' ";
	$sql.= " and status=3 ";
	$sql.= " and (share_status =0 or share_status =1) ";
	$orderList = mysqld_selectall($sql);

	$list = array_merge($orderList,$thirdOrderList);
    //按照订单的更新时间排序
	$list = arraySequence($list,'createtime');

	//为了减少一次查询，所以把0和1的拿出来，从数组中找是否有1的，有一个1的状态了，不能再次添加订单分享
	//基本这种情况不会发生，一个订单分享未结束不会，有添加订单的入口，以免恶意。故还是要取0和1的 判断是否有1 的
	if(!empty($list)){
		foreach($list as $order){
			if($order['share_status'] == 1)
				message("您已有一个订单还在分享中",refresh(),'error');
		}
	}
	//平台来源
	$platform = array('self'=>'觅海','tmall'=>'天猫','jd'=>'京东');
	//记住当前地址
	tosaveloginfrom();
	include themePage('miyou_choose_order');

}else if($op == 'add_order'){
	if(empty($openid)){
		message("请您先登录！",mobile_url('login'),'error');
	}
	if(empty($_GP['platform']) || empty($_GP['orderid'])){
		message("对不起，参数有误！",refresh(),'error');
	}

	//邀请收益
	$invit_money   = array('direct_share_price'=>0,'order_share_price'=>'0');
	$inviteSetting = mysqld_select ( "SELECT value FROM " . table ( 'config' ) . " where name='invite_setting' " );
	if(!empty($inviteSetting)){
		$invit_money   = unserialize($inviteSetting['value']);
	}

	if($_GP['platform'] == 'self'){
		//自己平台的订单
		$the_order = mysqld_select("select price,id as oid from ".table('shop_order')." where id={$_GP['orderid']} and openid='{$openid}'");
	}else{
		$the_order = mysqld_select("select price,order_id as oid from ".table('third_order')." where order_id={$_GP['orderid']} and openid='{$openid}'");
	}
	if(empty($the_order)){
		die(showAjaxMess(1002,'订单不存在！'));
	}

	if($the_order['price'] < $invit_money['order_share_price']){
		die(showAjaxMess(1002,'订单金额需大于助力的金额'));
	}

	if($_GP['platform'] == 'self'){
		//自己平台的订单
		$res = mysqld_update('shop_order',array('share_status'=>1),array('id'=>$the_order['oid']));
	}else{
		$res = mysqld_update('third_order',array('share_status'=>1),array('order_id'=>$the_order['oid']));
	}
	if($res){
		die(showAjaxMess(200,'订单减免添加成功!'));
	}else{
		die(showAjaxMess(1002,'添加失败!'));
	}

}else if($op == 'add_tm_order'){
	if(empty($openid)){
		message("请您先登录！",mobile_url('login'),'error');
	}

	//得到验证码进行提交关联订单
	if(!empty($_GP['add_order'])){
		if(empty($_GP['phone']) || strlen($_GP['phone']) != 11){
			die(showAjaxMess(1002,'对不起，手机格式不对'));
		}
		if(empty($_GP['code'])){
			die(showAjaxMess(1002,'对不起，请输入验证码'));
		}
		$regedits = new LtCookie();
		$reg_code = $regedits->getCookie($_GP['phone']);
		if($reg_code != $_GP['code']){
			die(showAjaxMess(1002,'对不起，验证码有误'));
		}
		//更新该用户天猫订单  与 该openid关联
		$res = mysqld_query("update ".table('third_order')." set openid='{$openid}' where address_mobile='{$_GP['phone']}' and openid='' ");
		if($res){
			$regedits->delCookie($_GP['phone']);
			die(showAjaxMess(200,'关联成功！'));
		}else{
			die(showAjaxMess(1002,'查无订单！'));
		}
	}

	//验证手机号 发送验证码
	if(!empty($_GP['phone'])){
		if(strlen($_GP['phone']) != 11){
			die(showAjaxMess(1002,'手机格式不对！'));
		}else{
			$code = set_sms_code($_GP['phone'],0,3);
			if($code){
				$regedits = new LtCookie();
				$regedits->setCookie($_GP['phone'], $code,time()+300);
				die(showAjaxMess("200",'发送成功'));
			}else{
				die(showAjaxMess("1002",'发送失败了！'));
			}
		}
	}
	include themePage('add_tm_order');

}else if($op == 'history'){  //历史记录
	if(empty($openid)){
		message("请您先登录！",mobile_url('login'),'error');
	}
	//第三方平台订单
	$thirdSql = "SELECT t.ordersn,t.price,t.from_platform,t.share_status,t.createtime,t.updatetime,count(p.friend_openid) as friend_cnt,sum(p.fee) as order_fee FROM " . table('third_order')." t ";
	$thirdSql.= " left join ".table('member_paylog_detail')." p on p.ordersn=t.ordersn and p.type='addgold_byinvite' ";
	$thirdSql.= " where t.openid='".$openid."' ";
//	$thirdSql.= " and t.status=3 ";				//交易成功订单 不用给 只有statsu是3的才有  share_status会出现 0 1 2 3
	$thirdSql.= " and t.share_status!=0 and t.share_status!=1 ";
	$thirdSql.= " group by t.ordersn ";
	$thirdOrderList = mysqld_selectall($thirdSql);

	//自有平台订单
	$sql = "SELECT o.ordersn,o.price,o.createtime,o.share_status,'self' as from_platform,o.updatetime,count(p.friend_openid) as friend_cnt,sum(p.fee) as order_fee FROM " . table('shop_order')." o ";
	$sql.= " left join ".table('member_paylog_detail')." p on p.ordersn=o.ordersn and p.type='addgold_byinvite' ";
	$sql.= " where o.openid='".$openid."' ";
//	$sql.= " and o.status=3 ";					//交易成功订单 不用给 只有statsu是3的才有  share_status会出现 0 1 2 3
	$sql.= " and o.share_status!=0 and o.share_status!=1 ";
	$sql.= " group by o.ordersn ";
	$orderList = mysqld_selectall($sql);

	$list = array_merge($orderList,$thirdOrderList);
	//按照订单的更新时间
	$list = arraySequence($list,'updatetime');
	//平台来源
	$platform = array('self'=>'觅海','tmall'=>'天猫','jd'=>'京东');
	include themePage('miyou_history');

}else if($op == 'apply_fee'){  //申请减免
	if(empty($openid)){
		die(showAjaxMess(1002,'请您先登录！'));
	}
	//获取分享的订单
	$share_order = mysqld_select("SELECT id as oid,ordersn,'self' as from_platform  from ".table('shop_order')." where openid={$openid} and status=3 and share_status=1");
	if(empty($share_order))
		$share_order = mysqld_select("SELECT order_id as oid, ordersn,from_platform from ".table('third_order')." where openid={$openid} and status=3 and share_status=1");

	if(empty($share_order)){
		die(showAjaxMess(1002,'对不起，查无订单！'));
	}

	$updateData = array('share_status'=>2,'updatetime'=>time());
	if($share_order['from_platform'] == 'self'){
		$res = mysqld_update('shop_order',$updateData,array('id'=>$share_order['oid']));
	}else{
		$res = mysqld_update('third_order',$updateData,array('order_id'=>$share_order['oid']));
	}
	if($res){
		die(showAjaxMess(200,'申请成功，请耐心等待'));
	}else{
		die(showAjaxMess(200,'申请订单减免失败'));
	}

}else if($op == 'register'){
	//邀请好友注册
	if($_GP['type'] == 'check'){
		//检查手机是否注册过
		if(empty($_GP['phone']) || strlen($_GP['phone']) != 11){
			die(showAjaxMess(1002,'对不起，手机号有误'));
		}
		$res = mysqld_select("select openid from ".table('member')." where mobile={$_GP['phone']}");
		if($res){
			die(showAjaxMess(1002,'该手机号已经注册了觅海账号'));
		}else{
			die(showAjaxMess(200,'可以注册'));
		}
	}

	if($_GP['type'] == 'getcode'){
		//获取验证码
		if(empty($_GP['phone']) || strlen($_GP['phone']) != 11){
			die(showAjaxMess(1002,'对不起，手机号有误'));
		}
		$code = set_sms_code($_GP['phone']);
		if($code){
			$regedits = new LtCookie();
			$regedits->setCookie($_GP['phone'], $code,time()+360);
			die(showAjaxMess("200",'发送成功！'));
		}else{
			die(showAjaxMess("1002",'网络异常，稍后再试'));
		}
	}

	if($_GP['type'] == 'regedit'){
		//开始注册 并给用户对应的 红包 和邀请者对应的奖励
		if(empty($_GP['phone']) || strlen($_GP['phone']) != 11){
			die(showAjaxMess(1002,'手机格式不对'));
		}
		if(empty($_GP['password']) || strlen($_GP['password']) < 6){
			die(showAjaxMess(1002,'密码至少6个字符'));
		}
		if(empty($_GP['code'])){
			die(showAjaxMess(1002,'请输入验证码'));
		}
		$regedits = new LtCookie();
		$reg_code = $regedits->getCookie($_GP['phone']);
		if($reg_code != $_GP['code']){
			die(showAjaxMess(1002,'对不起，验证码有误'));
		}

		$res = mysqld_select("select openid from ".table('member')." where mobile={$_GP['phone']}");
		if($res){
			die(showAjaxMess(1002,'该手机号已经注册了觅海账号'));
		}
		//注册该用户
		$res  = miyou_register($_GP['phone'],$_GP['password']);
		if($res){
			$regedits->delCookie($_GP['phone']);
			die(showAjaxMess(200,'已经注册并成功领取'));
		}else{
			die(showAjaxMess(1002,'注册失败，请稍后再试'));
		}

	}

}else if($op == 'exclusive'){ //专属码页面

	if($_GP['type'] == 'regiet'){
		if(empty($_GP['code'])){
			die(showAjaxMess(1002,"请输入您的专属码！"));
		}
		//以防止强制刷这个入口，设置一个短期时间只能请求次数不超过一个阈值，否则静止该请求
		if(!check_request_times('exclusive')){
			die(showAjaxMess(1002,'您的请求过于频繁！'));
		}

		//进行提交注册 该用户
		//先获取第三方订单中的手机号 去验证是否已经在member中存在过，存在的话，则不让注册
		$third_order = mysqld_select("select * from ".table('third_order')." where regcode={$_GP['code']}");
		if(empty($third_order)){
			die(showAjaxMess(1002,"该专属码不存在！"));
		}
		if($third_order['ifreg'] == 1){
			die(showAjaxMess("1002",'该专属码已经验证过了'));
		}
		$phone  = $third_order['address_mobile'];
		$passwd = $_GP['code'];
		$member = mysqld_select("select openid from ".table('member')." where mobile='{$phone}'");
		if(!empty($member)){
			//不为空  用户已经存在
			if(!empty($third_order['openid'])){
				//并且已经绑定过 订单openid
				die(showAjaxMess("1002",'该专属码已经验证过了'));
			}else{
				//新的订单 未绑定 但是用户存在  直接关联openid 并奖励积分
				$openid = $member['openid'];
				//如果没有过参与 免单助力，此次订单自动加入免单助力
				//获取一个分享中的 订单
				$share_order = mysqld_select("SELECT ordersn,price from ".table('shop_order')." where openid={$openid} and status=3 and share_status=1");
				if(empty($share_order))
					$share_order = mysqld_select("SELECT ordersn,price from ".table('third_order')." where openid={$openid} and status=3 and share_status=1");

				$up_data = array(
					'openid'	  => $openid,
					'ifmoney'	  => 1,
					'ifreg'		  => 1,
					'updatetime'  =>time()
				);
				if(empty($share_order)){
					//如果没有参与过 助力免单 则将该订单设置为助力免单
					$up_data['share_status'] = 1;
				}
				mysqld_update('third_order',$up_data,array('order_id'=>$third_order['order_id']));

				//自动登录
				save_member_login('',$openid);

				//赠送订单对应的奖励积分
				$credit_ratio = bankSetting('credit_ratio');
				$credit = ceil($credit_ratio*$third_order['price']);
				member_credit($openid,$credit,"addcredit",PayLogEnum::getLogTip('LOG_TORDER_JIFEN_TIP'));
				//reg_credit 0表示此次没有再次注册 是老客户了
				die(showAjaxMess(200,array('credit'=>$credit,'reg_credit'=>0)));
			}
		}else{
			//拿到专属码  注册该用户 并得到对应的奖励 并登录
			$openid = exclusive_register($phone,$passwd,$third_order);
			if($openid){
				//关联订单 修改订单信息  并加入免单助力  首次注册坑定还没参与过免单助力
				mysqld_update("third_order",array(
					'openid'	  => $openid,
					'ifmoney'	  => 1,
					'ifreg'		  => 1,
					'share_status'=> 1,   //加入免单申请
					'updatetime'  =>time()
				),array('order_id'=>$third_order['order_id']));

				//赠送订单对应的奖励积分
				$credit_ratio = bankSetting('credit_ratio');
				$credit = ceil($credit_ratio*$third_order['price']);
				member_credit($openid,$credit,"addcredit",PayLogEnum::getLogTip('LOG_TORDER_JIFEN_TIP'));

				$cfg = globaSetting();
				$regcredit = intval($cfg['shop_regcredit']);

				die(showAjaxMess(200,array('credit'=>$credit,'reg_credit'=>$regcredit)));
			}else{
				die(showAjaxMess(1002,'验证失败，请稍后再试'));
			}
		}
	}

	//活动优惠卷总额
	$total_price = exclusive_bonuse_money();
	//查找出有兑换礼品的商品
	$config     = mysqld_select("SELECT * FROM " . table('addon7_config') );
	$goods_list = array();
	if($config['open_gift_change'] == 1){
		$goods_list  = mysqld_selectall("select * from ".table('addon7_award')." where add_jifen_change=1 and isrecommand=1 limit 20");
	}
	include themePage('exclusive_code');
}
