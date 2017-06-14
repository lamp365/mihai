<?php
/**
 *团购相关函数
 * 
 */


/**
 * 参团
 * 
 * @param $group_id:团ID
 * @param $openid:用户ID
 * @param $team_buy_count: 商品的成团人数
 * 
 */
function AddToTeamBuyGroup($group_id,$openid,$team_buy_count)
{
	$time 		= date('Y-m-d H:i:s');
	$groupInfo 	= mysqld_select("SELECT * FROM " . table('team_buy_group') . " WHERE group_id={$group_id} and status=2");
	
	//团不存在时
	if(empty($groupInfo))
	{
		return false;
	}
	//超时的时候
	elseif((strtotime($groupInfo['createtime'])+TEAM_BUY_EXPIRY)<=time()){
		
		//更新团购表记录
		mysqld_update('team_buy_group',array('status'=>0,'modifiedtime'=>$time),array('group_id'=>$group_id));
		
		return false;
	}
	else{
		
		$memberCount = mysqld_select("SELECT count(*) as cnt FROM " . table('team_buy_member') . " WHERE group_id={$group_id} ");
		
		if(($memberCount['cnt']+1)==$team_buy_count )
		{
			//更新团购表记录
			mysqld_update('team_buy_group',array('status'=>1,'modifiedtime'=>$time),array('group_id'=>$group_id));
		}
		
		
		$member_data = array ('group_id' 		=> $group_id,
								'openid' 		=> $openid,
								'createtime' 	=> $time,
								'modifiedtime' 	=> $time);
		
		mysqld_insert ( 'team_buy_member', $member_data );
		
		return true;
	}
	
}

/**
 * 独立建团
 * 
 * @param $dish_id 商品ID
 * @param $openid  用户ID
 * 
 * @return array :建团信息
 * 			group_id：团ID
 *  		team_buy_member_id：团成员ID
 */
function createTeamBuyGroup($dish_id,$openid)
{
	$result = array();
	
	$time = date('Y-m-d H:i:s');
	
	$data = array ('dish_id' 		=> $dish_id,
					'status' 		=> 2,						//组团中
					'createtime' 	=> $time, 			
					'modifiedtime' 	=> $time,
					'creator' 		=> $openid);
	
	mysqld_insert ( 'team_buy_group', $data );
	
	$result['group_id'] = mysqld_insertid ();
	
	
	$member_data = array ('group_id' 		=> $result['group_id'],
							'openid' 		=> $openid,
							'createtime' 	=> $time,
							'modifiedtime' 	=> $time);
	
	mysqld_insert ( 'team_buy_member', $member_data );
	
	$result['team_buy_member_id'] = mysqld_insertid ();
	
	return $result;
}


/**
 * 是否已经加入同商品的其他团
 * 
 */
function isAddedTeamBuyGroup($dish_id,$openid)
{
	$sql = "SELECT g.group_id FROM " . table('team_buy_group') . "  AS g LEFT JOIN " . table('team_buy_member')." as m ON m.group_id = g.group_id ";
	$sql.= " WHERE g.dish_id={$dish_id} ";
	$sql.= " and m.openid={$openid} ";
	$sql.= " and g.finish=0 ";				//活动还未结束
	$sql.= " and ((g.status=2 ";			//组团中的
	$sql.= " and g.createtime>'".date('Y-m-d H:i:s',(time()-TEAM_BUY_EXPIRY))."') ";
	$sql.= " or g.status=1 )";				//组团成功的

	return mysqld_select($sql);
}

/**
 * 更新商品团购状态，是否人为凑单，是否成团过期
 * 更新 过期团购商品为一般商品
 * 活动结束 未支付的为订单关闭
 */
function update_group_status($dish_id) {
	$dishinfo = mysqld_select("select * from ".table('shop_dish')." where id={$dish_id}");
	if(empty($dishinfo))
		return;
	$group    = mysqld_selectall("SELECT createtime, group_id FROM ".table('team_buy_group')." WHERE dish_id=".$dish_id." AND status=2");
	if($dishinfo['open_groupbuy'] == 1){
		//说明平台开启了 自动凑单功能
		if (!empty($group)) {
			//最后5分钟开始凑单 以及时间过期了都可以
			$last_time = 60*5;
			// 或者活动时间结束了
			$finishtime = time()-$dishinfo['timeend'];
			foreach ($group as &$gv) {
				$total_time = strtotime($gv['createtime'])+TEAM_BUY_EXPIRY;
				if(time()-$total_time>=0 || $total_time-time()<=$last_time || $finishtime>=0){
					//如果活动时间结束，则最后时间定为活动的结束时间
					if($finishtime >=0 )
						$total_time = $dishinfo['timeend'];
					//开始凑单
					add_group_mmber($gv['group_id'],$dishinfo['team_buy_count'],$total_time);
					//修改该团购的状态为成功
					mysqld_update('team_buy_group',array('status'=>1,'modifiedtime'=>date('Y-m-d H:i:s')),array('group_id'=>$gv['group_id']));
				}
			}
		}
	}else{
		if (!empty($group)) {
			foreach ($group as &$gv) {
				//半个小时已经过去了 过期了
				$expretime  = strtotime($gv['createtime'])+TEAM_BUY_EXPIRY ;
				// 或者活动时间结束了
				$finishtime = time()-$dishinfo['timeend'];
				if ($expretime<=time()|| $finishtime>=0 ) {
					$group_member = mysqld_selectall("SELECT order_id FROM ".table('team_buy_member')." WHERE group_id=".$gv['group_id']);
					foreach ($group_member as $gmv) {
						update_order_status($gmv['order_id'], -2,$dishinfo);
					}
					//修改该团购的状态为失败
					mysqld_update('team_buy_group',array('status'=>0,'modifiedtime'=>date('Y-m-d H:i:s')),array('group_id'=>$gv['group_id']));
				}
			}
		}
	}

	// 进行抽奖判断处理
	draw_team_buy($dishinfo);
	//成功状态的团购，修改一些未支付订单的状态 在活动结束时 为关闭
	//更新该商品类型为一般商品
	update_timeover_to_normalshop($dishinfo);

}

/**
 * @param $group_id
 * @param $team_buy_count   商品团购的限制人数
 * @param $endtime          该团购最后过期的时间  时间戳
 * @content  人数不够尽行凑单
 */
function add_group_mmber($group_id,$team_buy_count,$endtime){
	//开启自动凑单,自动补充人数
	$group_member_num = mysqld_selectcolumn("select count(group_id) from ".table('team_buy_member')." where group_id={$group_id}");
	//还差多少人够单
	$diff_member_num  = $team_buy_count - $group_member_num;
	if($diff_member_num != 0){
		//取出虚拟用户
		$member = mysqld_selectall('select openid from '.table('member')." where dummy=1");
		if(!empty($member)){
			for($j = 1 ; $j<=$diff_member_num; $j++){
				//凑单人的时间范围将是从结束时间 到前推 7分钟
				$starttime  = $endtime - 300 - 120;
				$step       = mt_rand(8,20);
				$time_range = range($starttime,$endtime,$step);
				$time_key   = array_rand($time_range);
				$createtime = $time_range[$time_key];

				$num = array_rand($member);
				mysqld_insert('team_buy_member',array(
					'group_id'    => $group_id,
					'openid'      => $member[$num]['openid'],
					'order_id'    => 0,  //虚拟用户没有订单 orderid为0
					'createtime'  => date('Y-m-d H:i:s',$createtime),
					'modifiedtime'=> date('Y-m-d H:i:s',$createtime)
				));
				unset($member[$num]);  //要尽行删除掉，避免这个用户再次参加这个团
			}
		}
	}
}

/**
 * @param $dishinfo
 * @content 及时把一些商品过期的更新为一般商品
 */
function update_timeover_to_normalshop($dishinfo){
	if(time()-$dishinfo['timeend']>=0) {
		//更新该商品类型为一般商品
		mysqld_update('shop_dish', array(
			'type' => '0',
			'istime' => '0',
			'timestart' => '0',
			'timeend' => '0'
		), array('id' => $dishinfo['id']));
	}

}

/**
 * 用于后台宝贝列表处  以及 团购订单处调用
 * 前台个人中心或者团购详情页要调用 可以调用 update_group_status($dishid)该函数
 * 更新全部团购状态，是否人为凑单，是否成团过期
 * 更新 过期团购商品为一般商品
 * **/
function update_all_shop_status(){
	//获取所有非一般商品
	$dishinfo = mysqld_selectall("select type,timeend,id from ".table('shop_dish')." where type<>0");
	if(!empty($dishinfo)){
		foreach($dishinfo as $dish){
			//时间过期的则更新状态
			update_timeover_to_normalshop($dish);
		}
	}
}

// 抽奖团处理
function draw_team_buy($d_info) {
	if ($d_info['draw'] == '0') {
		// 未开启抽奖
		return false;
	}
	if ($d_info['draw'] == '1') {
		// 已开启抽奖把成团并且已付款的订单设为待开奖
		$group = mysqld_selectall("SELECT * FROM ".table('team_buy_group')." WHERE dish_id=".$d_info['id']." AND status=1 AND finish=0");
		if (!empty($group)) {
			foreach ($group as $gk1 => $gv1) {
				if (strtotime($gv1['createtime']) > $d_info['timestart']) {
					$group_member = mysqld_selectall("SELECT order_id FROM ".table('team_buy_member')." WHERE group_id=".$gv1['group_id']." and order_id<>0");
					foreach ($group_member as $gmv1) {
						mysqld_query("UPDATE ".table('shop_order')." SET isprize=3 WHERE id=".$gmv1['order_id']." AND status=1 AND isprize=0");
					}
				}
			}
		}
		// 所有订单设为抽奖团
		$al_group = mysqld_selectall("SELECT * FROM ".table('team_buy_group')." WHERE dish_id=".$d_info['id']." AND finish=0");
		if (!empty($al_group)) {
			foreach ($al_group as $agk => $agv) {
				if (strtotime($agv['createtime']) > $d_info['timestart']) {
					$group_member = mysqld_selectall("SELECT order_id FROM ".table('team_buy_member')." WHERE group_id=".$agv['group_id']." and order_id<>0");
					foreach ($group_member as $agmv) {
						mysqld_query("UPDATE ".table('shop_order')." SET isdraw=1 WHERE id=".$agmv['order_id']." AND isdraw=0");
					}
				}
			}
		}
	}
	if ($d_info['timeend'] > time()) {
		// 活动未结束
		return false;
	}

	$group = mysqld_selectall("SELECT * FROM ".table('team_buy_group')." WHERE dish_id=".$d_info['id']." AND status=1 AND finish=0");
	if (empty($group)) {
		// 活动结束也没有任何成团，关闭商品抽奖状态
		mysqld_update('shop_dish',array('draw'=>0, 'draw_num'=>0),array('id'=>$d_info['id']));
		return false;
	}
	$all_member = array();
	foreach ($group as $gk => $gv) {
		if (strtotime($gv['createtime']) > $d_info['timestart']) {
			$group_member = mysqld_selectall("SELECT order_id FROM ".table('team_buy_member')." WHERE group_id=".$gv['group_id']." and order_id<>0");
			foreach ($group_member as $gmv) {
				$od_sta = mysqld_select("SELECT status FROM ".table('shop_order')." WHERE id=".$gmv['order_id']);
				if ($od_sta['status'] == '1') {
					$all_member[] = $gmv['order_id'];
				}
			}
		}
	}
	if (!empty($all_member)) {
		// 随机抽取
		if ($d_info['draw_num'] > count($all_member)) {
			$d_info['draw_num'] = count($all_member);
		}
		$isprize = array_rand($all_member, intval($d_info['draw_num']));
		// 更新订单中奖状态
		if (is_array($isprize)) {
			foreach ($isprize as $vp) {
				mysqld_update('shop_order',array('isprize'=>1),array('id'=>$all_member[$vp]));
				unset($all_member[$vp]);
			}
		}elseif (!empty($isprize)) {
			mysqld_update('shop_order',array('isprize'=>1),array('id'=>$all_member[$isprize]));
			unset($all_member[$isprize]);
		}
		if (!empty($all_member)) {
			// 重新排列数组下标
			$all_member = array_merge($all_member);
			// 未中奖订单退款
			foreach ($all_member as $am_v) {
				update_order_status($am_v, -2, $d_info);
				// 更新未中奖订单状态
				mysqld_update('shop_order',array('isprize'=>2),array('id'=>$am_v));
			}
		}
	}
	
	// 关闭商品抽奖状态
	mysqld_update('shop_dish',array('draw'=>0, 'draw_num'=>0),array('id'=>$d_info['id']));
}

/**
 * @content  该方法用于购物车mycart，判断团购商品时 时间没开始不能开团
 * 因为返回数据格式为了与之前mycart之前的数据格式一致 只好这么定义
 * @param $type  type类型在mycart已经被定义了    type -1,正常类型  0，新增团  >0 参与的团购队伍ID
 * @param $id   宝贝id
 */
function isCanGoupBuy($type,$id){
	if($type >= 0){
		$dish = mysqld_select("select timestart from ".table('shop_dish')." where id={$id}");
		if(empty($dish)){
			$result = array('result' => 1002,'message'=>'对不起，该商品不存在！');
			die(json_encode($result));
		}else{
			if(time() < $dish['timestart']){
				//如果活动还没开始 不能进行开团
				$result = array('result' => 1002,'message'=>'商品活动时间还没开始！');
				die(json_encode($result));
			}
		}
	}
}