<?php
//购物车的公共函数文件

/**
 * 获取购物车的种类数目 只获取有效中的产品数量
 * @return bool|int|string
 */
function getCartTotal(){
	$member   = get_member_account(false);
	$openid   = $member['openid'] ?: get_sessionid();

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

	$cartotal = count($list);

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
		//那么看看时间开始了么
		if($active['ac_time_str'] < time()){
			//有活动，那么判断该商品是不是属于限时购商品
			$sql = "select ac_dish_id,ac_shop_dish,ac_dish_status,ac_dish_total,ac_dish_price,ac_action_id from ".table('activity_dish');
			$sql .= " where ac_action_id={$active['ac_id']} and ac_shop_dish={$dishid}";
			$find = mysqld_select($sql);
			if (!empty($find)) {
				//校验一下活动表的库存跟dish表的库存
				if($store_count < $find['ac_dish_total']){
					mysqld_update('activity_dish',array('ac_dish_total'=>$store_count),array('ac_dish_id'=>$find['ac_dish_id']));
					$find['ac_dish_total'] = $store_count;
				}
			}
		}
	}
	return $find;
}
?>