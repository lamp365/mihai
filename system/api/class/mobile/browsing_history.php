<?php
	/**
	 * app 浏览历史
	 * @author WZW
	 * 
	 */

	$result = array();
	
	$goods_id = json_decode($_REQUEST['dish_id']);
	if (empty($goods_id) or !is_array($goods_id)) {
		$result['message'] = "商品ID为空!";
		$result['code']    = 0;
		echo apiReturn($result);
		exit;
	}

	// $goods_id = json_decode($goods_id, true);
	// $goods_id = [1, 2, 3, 4];
	$goods = array();
	foreach ($goods_id as $gk => $gv) {
		$good = get_good(array(
            "table"=>"shop_dish",
			"where"=>"a.id = ".$gv,
		));
		// $good = mysqld_select("SELECT id, title, thumb, marketprice FROM ".table('shop_goods')." WHERE id=".$gv);
		$ary = array();
		$ary['id'] = $good['id'];
		$ary['gid'] = $good['gid'];
		$ary['title'] = $good['title'];
		$ary['thumb'] = $good['thumb'];
		$ary['marketprice'] = $good['marketprice'];
		$goods[] = $ary;
	}
	// dump($goods);
	$result['data']['goods'] = $goods;
	$result['code'] = 1;
	echo apiReturn($result);
	exit;
