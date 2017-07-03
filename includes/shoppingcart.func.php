<?php
//购物车的公共函数文件

/**
 * 获取购物车的商品数目
 * @type  为1 的时候表示只获取 购物车商品种类的个数
 *        为2 的时候 获取购物车中总商品个数
 * @return bool|int|string
 */
function getCartTotal($type = 1){
	$member   = get_member_account(false);
	$openid   = $member['openid'] ?: get_sessionid();
	$cartotal = 0;

	$list   = mysqld_selectall("SELECT * FROM " . table('shop_cart') . " WHERE  session_id='{$openid}'");
	foreach($list as $key => $item){
		$field = 'id,title,marketprice,thumb,sts_id,store_count,status';
		$dish  = mysqld_select("select {$field} from ".table('shop_dish')." where id={$item['goodsid']}");

		if(empty($dish) || $dish['store_count'] ==0 || $dish['status'] == 0){
			unset($list[$key]);
			continue;
		}

		//判断商品是否属于活动中的商品
		$active = checkDishIsActive($dish['id'],$dish['store_count']);
		if(!empty($active) && ($active['ac_dish_total'] == 0 || $active['ac_dish_status'] == 0)){
			unset($list[$key]);
			continue;
		}
	}

	if($type == 1){
		//只获取种类数目
		is_array($list) && $cartotal = count($list);
	}else if($type == 2){
		//只获取 购物车中的商品数量
		if(!empty($list)){
			foreach($list as $one_list){
				$cartotal += $one_list['total'];
			}
		}
	}else{
		is_array($list) && $cartotal = count($list);
	}


	return intval($cartotal);
}

/**
 * 加入购物车的时候， 校验活动商品和普通商品库存
 * 如果是活动商品，返回活动商品，不是活动商品，返回空数组
 * @param $dishid
 * @param $store_count
 * @return array|bool|mixed
 */
function checkDishIsActive($dishid,$store_count){
	//找出本次活动的场次
	$active = getCurrentAct();
	$find   = array();
	if(!empty($active)){
		//那么看看本次活动时间开始了么
		if($active['ac_time_str'] < time()){
			//有活动，那么判断该商品是不是属于限时购商品
			$sql = "select ac_dish_id,ac_shop_dish,ac_dish_status,ac_dish_total,ac_dish_price,ac_action_id,ac_area_id from ".table('activity_dish');
			$sql .= " where ac_action_id={$active['ac_id']} and ac_shop_dish={$dishid}";
			$find = mysqld_select($sql);
			if (!empty($find)) {
				//校验一下活动表的库存跟dish表的库存
				if($store_count < $find['ac_dish_total']){
					mysqld_update('activity_dish',array('ac_dish_total'=>$store_count),array('ac_dish_id'=>$find['ac_dish_id']));
					$find['ac_dish_total'] = $store_count;
				}
				//根据 ac_action_id 获取 在该场活动中的 哪个区间时间段
				if($find['ac_area_id'] != 0){
					$findArea = mysqld_select("select ac_area_time_str,ac_area_time_end from ".table('activity_area')." where ac_area_id={$find['ac_area_id']}");
					if(empty($findArea)){
						$find = array();
					}else{
						$start_time = getTodayTimeByActtime($findArea['ac_area_time_str']);
						$end_time   = getTodayTimeByActtime($findArea['ac_area_time_end']);
						if(time()>$end_time || time()<$start_time){
							//已经过时间了，或者还没开始
							$find = array();
						}
					}
				}
			}
		}
	}
	return $find;
}

/**
 * 获取活动商品所属的 活动时间 对应到今天的时间
 * @param $time
 * @return int
 */
function getTodayTimeByActtime($time){
	//那天的凌晨时间
	$that_zero  = strtotime(date("Y-m-d",$time));
	//今天的凌晨时间
	$today_zero = strtotime(date("Y-m-d",time()));
	//今天的活动时刻
	$todaytime = $today_zero+($time-$that_zero);
	return $todaytime;
}

/**
 * 更新购物车的最新加入时间
 * @return string
 */
function update_cart_record_time(){
	$member = get_member_account();
	if(empty($member)){
		return '';
	}
	$find = mysqld_select("select last_time from ".table('shop_cart_record')." where session_id='{$member['openid']}'");
	if($find){
		mysqld_update('shop_cart_record',array('last_time'=>time()),array('session_id'=>$member['openid']));
	}else{
		$in_data['session_id'] = $member['openid'];
		$in_data['last_time']  = time();
		$in_data['createtime'] =time();
		mysqld_insert('shop_cart_record',$in_data);
	}
}

/**
 * 检测购物车是否已经过了20分钟
 * 过了时间 释放库存，并删除购物车
 */
function check_shop_cart_time(){
	$time = time()-20*60;
	$cart_record = mysqld_selectall("select id,session_id from ".table('shop_cart_record')." where last_time<{$time}");
	foreach($cart_record as $item){
		//超时了 进行清除购物车 释放库存
		$cart_list = mysqld_selectall("select id,goodsid,total,ac_dish_id from ".table('shop_cart')." where session_id='{$item['session_id']}'");
		foreach($cart_list as $list){
			$ac_action_id = 0;
			if(!empty($list['ac_dish_id'])){
				$action_info  = mysqld_select("select ac_action_id from ".table('activity_dish')." where ac_dish_id={$list['ac_dish_id']}");
				$ac_action_id = intval($action_info['ac_action_id']);
			}
			//库存放回去
			operateStoreCount($list['goodsid'],$list['total'],$ac_action_id,2);
			//删掉该购物车
			mysqld_delete('shop_cart',array('id'=>$list['id']));
		}
		//删除掉 购物车时间的记录
		mysqld_delete('shop_cart_record',array('id'=>$item['id']));
	}
}
?>