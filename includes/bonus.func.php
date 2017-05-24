<?php
/*
    根据产品数据信息来获取适用的优惠卷信息
    in: array('price'=>'产品总金额，不包含运费和税费', 'openid'=>'用户标识符', 'goods'=>array(array('id'=>'产品ID','num'=>'产品数量'),array('id'=>'产品ID','num'=>'产品数量')));
    out: array(array(优惠卷),array(优惠卷),array(优惠卷));
    优惠卷数据结构：
    Array ( [bonus_id] => 5 [bonus_type_id] => 3 [openid] => 2015111911924 [type_id] => 3 [type_name] => 这是一个优惠卷 [type_money] => 100.00 [send_type] => 2 [deleted] => 0 [min_amount] => 0.00 [max_amount] => 0.00 [send_start_date] => 1477044060 [send_end_date] => 1480545000 [use_start_date] => 1477044060 [use_end_date] => 1480545000 [min_goods_amount] => 200.00 ) 
	bonus_id : 个人优惠卷的ID
	bonus_type_id: 个人优惠卷对应的主题ID
	openid: 优惠卷所属用户的ID
	type_id: 主题ID
	type_name:主题名称
	type_money:优惠卷减免金额
	send_type: 优惠卷类型
	deleted : 优惠卷状态 0: 正常 1:不可用
	min_goods_amount : 优惠卷订单金额需求
	本函数已经通过订单及用户的ID，把可以使用的优惠卷按金额大小排序，不可用的优惠卷已经做了过滤, 后期会继续完善本功能，目前只针对满足商品，及满足订单金额2种情况的优惠卷
*/
function get_bonus_list($order_date=array()){
	if ( empty ( $order_date['openid'] ) ||  empty ( $order_date['price'] ) ){
         return false;
	}
	// 获取优惠卷信息
	$bonus = mysqld_selectall("SELECT a.bonus_id,a.bonus_type_id,a.openid,a.bonus_sn,b.* FROM " . table('bonus_user')." a left join ".table('bonus_type')." b on a.bonus_type_id = b.type_id where b.deleted = 0 and a.deleted = 0 and a.isuse = 0 and b.use_start_date <= " .time(). " and b.use_end_date > ".time() ." and min_goods_amount <= ".$order_date['price']." and a.openid = '".$order_date['openid']."' order by type_money desc, min_goods_amount asc, use_end_date asc ");
	$bonus_list = array();
	// 开始过滤不适用的优惠卷
	foreach ( $bonus as $bonus_value ) {

		 $goods = array();
		 // 默认为不可以使用
		 $iscan = 0;
		// 排查按商品发放的优惠卷
         if ( ($bonus_value['send_type'] == 1) && !empty($order_date['goods'])){
               $goods = mysqld_selectall("SELECT good_id FROM".table('bonus_good')." WHERE bonus_type_id = ".$bonus_value['type_id']);
	           foreach ( $order_date['goods'] as $goods_value ) {
				  foreach ( $goods as $good_value ){
                        if ( $good_value['good_id'] == $goods_value['dishid'] ){
                            $iscan = 1;
					        continue;
				        }
				  }  
			   }
		 }
		 // 排查满减的优惠卷   0 新手优惠卷  2 按订单金额 4 活动优惠卷 5 积分兑换
		$send_type_arr = array(0,2,4,5);
		if ( in_array($bonus_value['send_type'],$send_type_arr)){
               if ($bonus_value['min_goods_amount'] <= $order_date['price'] ){
                  $iscan =1;
			   }
		 }
		 if ( $iscan == 1 ){
               $bonus_list[] = $bonus_value;
		 }
	}
    return $bonus_list;
}

//新手礼券 领取
function new_member_bonus($openid){
	if(empty($openid)){
		$data['errno']   = 1002;
		$data['message'] = '您还没登录！';
		return $data;
	}
	/** @var 去除验证订单，只判断是否领取过
	$order = mysqld_selectall("SELECT id FROM " . table('shop_order')." where openid='".$openid."' ");
	if($order)
	{
		$data['errno']   = 1002;
		$data['message'] = '非新会员，无法领取新手券';
		return $data;
	}
	 ***/
	//是否已经领过券
	$bonusUser = mysqld_selectall("SELECT u.bonus_id FROM " . table('bonus_user')." u left join ". table('bonus_type')." t on t.type_id=u.bonus_type_id where u.openid='".$openid."' and t.send_type=0 ");
	if($bonusUser)
	{
		$data['errno']   = 1002;
		$data['message'] = '你已经领取过了';
		return $data;
	}
	// 找到优惠卷的信息 send_start_date 	send_end_date
	$time = time();
	$bonus = mysqld_selectall("SELECT type_id FROM " . table('bonus_type')." where send_type=0 and deleted = 0 and send_end_date>{$time}");
	if (empty($bonus)){
		$data['errno']   = 1002;
		$data['message'] = '当前优惠卷已失效';
		return $data;
	}
	else{

		foreach($bonus as $bv)
		{
			$bonus_sn = date("Ymd",time()).$bv['type_id'].rand(1000000,9999999);
			$bonus_user = mysqld_select("SELECT * FROM " . table('bonus_user')." where bonus_sn='".$bonus_sn."'" );
			while(!empty($bonus_user['bonus_id']))
			{
				$bonus_sn=date("Ymd",time()).$bv['type_id'].rand(1000000,9999999);
				$bonus_user = mysqld_select("SELECT * FROM " . table('bonus_user')." where bonus_sn='".$bonus_sn."'" );
			}
			$data=array(
				'createtime'	=> time(),
				'openid'		=> $openid,
				'bonus_sn'		=> $bonus_sn,
				'deleted'		=> 0,
				'isuse'			=> 0,
				'bonus_type_id'	=> $bv['type_id']
			);
			mysqld_insert('bonus_user',$data);
		}
		$data['errno']   = 200;
		$data['message'] = '恭喜，领取成功';
		return $data;
	}
}

/**
 * 活动专属优惠券 领取
 * 根据send_max  为0 则只能领取一次，不为0 表示该用户一次性 能领取这么多张。
 * 同时领取过的不用在领取
 * @param $openid
 * @return array
 */
function exclusive_bonus($openid){
	if(empty($openid)){
		$data['errno']   = 1002;
		$data['message'] = '您还没登录！';
		return $data;
	}
	//找到优惠券
	$time = time();
	$bonus = mysqld_selectall("SELECT * FROM " . table('bonus_type')." where send_type=4 and deleted = 0 and send_end_date>{$time}");
	if (empty($bonus)){
		$data['errno']   = 1002;
		$data['message'] = '当前优惠卷已失效';
		return $data;
	}
	$get_bonus_num = 0;
	foreach($bonus as $one_bonus){
		$sql = "SELECT bonus_id FROM " . table('bonus_user')." where openid='".$openid."' and bonus_type_id={$one_bonus['type_id']} ";
		$has_get = mysqld_select($sql);
		if($has_get){
			//已经领取的跳过
			continue;
		}
		if($one_bonus['send_max'] == 0){
			$can_get = 1;  //只能领取一张
		}else{
			$can_get = $one_bonus['send_max'];
		}
		//领取计数
		$get_bonus_num ++;
		for($i=0; $i<$can_get; $i++){
			$bonus_sn = date("Ymd",time()).$one_bonus['type_id'].rand(1000000,9999999);
			$bonus_user = mysqld_select("SELECT * FROM " . table('bonus_user')." where bonus_sn='".$bonus_sn."'" );
			while(!empty($bonus_user['bonus_id']))
			{
				$bonus_sn=date("Ymd",time()).$one_bonus['type_id'].rand(1000000,9999999);
				$bonus_user = mysqld_select("SELECT * FROM " . table('bonus_user')." where bonus_sn='".$bonus_sn."'" );
			}
			$data=array(
				'createtime'	=> time(),
				'openid'		=> $openid,
				'bonus_sn'		=> $bonus_sn,
				'deleted'		=> 0,
				'isuse'			=> 0,
				'bonus_type_id'	=> $one_bonus['type_id']
			);

			mysqld_insert('bonus_user',$data);
		}
	}

	if($get_bonus_num == 0){
		//都没有领取一张，说明已经领取过了
		$r_data['errno']   = 1002;
		$r_data['message'] = '您已领取过了！';
		return $r_data;
	}else{
		$r_data['errno']   = 200;
		$r_data['message'] = '恭喜，领取成功';
		return $r_data;
	}

}

/**
 * 活动专享优惠券 领取
 * 版本2  能不能继续领取，判断send_max 为0,用户可以继续取，
 * 不为0 则用户每次领取，优先统计用户领取该优惠券的张数，是否超过 send_max..超过不能领取，反之可领取
 * 目前逻辑定位版本1  版本2先留着，待用
 */
function exclusive_bonus2($openid){
	if(empty($openid)){
		$data['errno']   = 1002;
		$data['message'] = '您还没登录！';
		return $data;
	}
	//找到优惠券
	$time = time();
	$bonus = mysqld_selectall("SELECT type_id FROM " . table('bonus_type')." where send_type=4 and deleted = 0 and send_end_date>{$time}");
	if (empty($bonus)){
		$data['errno']   = 1002;
		$data['message'] = '当前优惠卷已失效';
		return $data;
	}
	$can_get = 0;
	foreach($bonus as $one_bonus){
		if($one_bonus['send_max'] == 0){
			//不限制张数
			$can_get = 1;  //可以取
		}else{
			//该用户已经领取此优惠卷张数
			$sql = "SELECT count(bonus_id) FROM " . table('bonus_user')." where openid='".$openid."' and bonus_type_id={$one_bonus['type_id']} ";
			$has_num = mysqld_selectcolumn($sql);
			if($has_num >= $one_bonus['send_max']){
				//不能在领取
				$can_get = 0;
			}else{
				$can_get = 1;
			}
		}

		if($can_get == 1){
			$bonus_sn = date("Ymd",time()).$one_bonus['type_id'].rand(1000000,9999999);
			$bonus_user = mysqld_select("SELECT * FROM " . table('bonus_user')." where bonus_sn='".$bonus_sn."'" );
			while(!empty($bonus_user['bonus_id']))
			{
				$bonus_sn=date("Ymd",time()).$one_bonus['type_id'].rand(1000000,9999999);
				$bonus_user = mysqld_select("SELECT * FROM " . table('bonus_user')." where bonus_sn='".$bonus_sn."'" );
			}
			$data=array(
				'createtime'	=> time(),
				'openid'		=> $openid,
				'bonus_sn'		=> $bonus_sn,
				'deleted'		=> 0,
				'isuse'			=> 0,
				'bonus_type_id'	=> $one_bonus['type_id']
			);

			mysqld_insert('bonus_user',$data);
		}
	}

	if($can_get == 1){
		$data['errno']   = 200;
		$data['message'] = '恭喜，领取成功';
		return $data;
	}else{
		//都没有领取一张，说明已经领取过了
		$data['errno']   = 1002;
		$data['message'] = '您已领取过了！';
		return $data;
	}

}


/**
 * 获取活动专属领取到的总额
 * 有多少张 就可以领取多少张
 * @return int
 */
function exclusive_bonuse_money(){
	$total = 0;
	$time  = time();
	$bonus = mysqld_selectall("SELECT type_money,send_max FROM " . table('bonus_type')." where send_type=4 and deleted = 0 and send_end_date>{$time}");
	foreach($bonus as $one){
		$send_max = empty($one['send_max']) ? 1 : $one['send_max'];
		$total += $one['type_money'] * $send_max;
	}
	return $total;
}

/**
 * 获取新人礼包的总额
 * @return int
 */
function newmember_bonuse_money(){
	$total = 0;
	$time  = time();
	$bonus = mysqld_select("SELECT sum(type_money) as t_money FROM " . table('bonus_type')." where send_type=0 and deleted = 0 and send_end_date>{$time}");
	$total = empty($bonus) ? $total : $bonus['t_money'];
	return $total;
}

/**
 * 优惠券类型，其他地方 有需要的直接从这里获取，统一地方获取，以免后期加入很多类型后，不同地方调用都要修改
 * 有扩展新的类型在这里添加， 需要对应 bonus_type表中的字段send_type
 * @return array
 */
function get_bonus_enum_arr(){
	$bonus_enum_arr = array(
		0 => '新用户注册',
		1 => '按商品发放',
		2 => '按订单金额发放',
		3 => '按线下发放',
		4 => '活动优惠卷 ',
		5 => '积分兑换',
	);
	return $bonus_enum_arr;
}