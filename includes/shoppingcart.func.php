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

?>