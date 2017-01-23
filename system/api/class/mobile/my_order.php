<?php
	/**
	 * app 我的订单接口
	 * @author WZW
	 * 
	 */

	$result = array();
	
	// 订单状态值
	$status = intval($_GP['status']);
	// 预留APP账户验证接口
	$member = get_member_account(true, true);
	// $member['openid'] = '2015111911924';
	if (empty($member)) {
		$result['message'] 	= "用户验证失败!";
		$result['code'] 	= 2;
		echo apiReturn($result);
		exit;
	}elseif ($member == 3) {
		$result['message'] 	= "该账号已在别的设备上登录！";
		$result['code'] 	= 3;
		echo apiReturn($result);
		exit;
	}

	$openid = $member['openid'];
	$where = "a.openid='".$openid."' AND a.deleted=0";
	$pindex = max(1, intval($_GP['page']));
    $psize = 20;
    if ($status != 0) {
    	if ($status == 1) {
    		// 待付款
    		$u_status = 0;
    		$where.= " AND a.status=$u_status";
    	}elseif ($status == 3) {
    		// 待发货
    		$u_status = 1;
    		$where.= " AND a.status=$u_status";
    	}elseif ($status == 4) {
    		// 待收货
    		$u_status = 2;
    		$where.= " AND a.status=$u_status";
    	}elseif ($status == 5) {
    		// 待评价
    		$u_status = 3;
    		$where.= " AND a.status=$u_status AND b.iscomment=0";
    	}elseif ($status == 2) {
    		// 团购中
    		$where.= " AND e.status<>0 AND e.finish=0";
    	}elseif ($status == 6) {
    		// 售后单
    		$where.= " AND b.status IN (1, 2, 3, 4)";
    	}else{
    		$result['message'] 	= "订单状态错误!";
			$result['code'] 	= 0;
			echo apiReturn($result);
			exit;
    	}
    }
    
    // $list = get_orders(
    // 	array('where' => $where, 'limit' => ($pindex - 1) * $psize . ',' . $psize)
    // 	);

    $list = mysqld_selectall("SELECT SQL_CALC_FOUND_ROWS a.id, a.ordersn, a.status, a.price as total_price, a.taxprice, a.dispatchprice, a.isprize, a.isdraw, a.has_balance, a.balance_sprice, b.id as bid, b.goodsid, b.total, b.seller_openid, b.price as goodprice, b.iscomment, b.shop_type, b.status as b_status, e.status as group_status, c.group_id, d.shopname, a.createtime FROM ".table('shop_order')." as a left join ".table('shop_order_goods')." as b on a.id=b.orderid left join ".table('team_buy_member')." as c on a.id=c.order_id left join ".table('openshop')." as d on b.seller_openid=d.openid left join ".table('team_buy_group')." as e on c.group_id=e.group_id WHERE ".$where." ORDER BY a.createtime DESC LIMIT ".($pindex - 1) * $psize . ',' . $psize);
    // 总记录数
	$total = mysqld_select("SELECT FOUND_ROWS() as total;");

    // dump($list);

    if (empty($list)) {
    	$list = array();
    }else{
    	$ary = array();
	    foreach ($list as $l_k => &$l_v) {
	    	update_group_status($l_v['goodsid']);
	    	// // 处理有余额抵扣订单的价格展示
	    	// if ($l_v['has_balance'] == '1') {
	    	// 	$l_v['total_price'] += $l_v['balance_sprice'];
	    	// }
	    	$l_v['goods'] = array();
	    	$usa = array();
	    	$good = get_good(array(
	                "table"=>"shop_dish",
					"where"=>"a.id = ".$l_v['goodsid'],
				));
	    	if ($status == 2) {
	    		// 团购商品需判断订单是否抽奖订单
	    		if (intval($l_v['isdraw']) == 0) {
	    			if ($l_v['group_status'] == '1') {
	    				unset($list[$l_k]);
	    				$total['total'] -= 1;
	    				continue;
	    			}
	    		}
	    	}
	    	if ($status == 3) {
	    		// 未成团不在待发货
	    		if ($l_v['group_status'] != '1' AND $l_v['group_status'] != NULL) {
	    			unset($list[$l_k]);
    				$total['total'] -= 1;
    				continue;
	    		}
	    		// 抽奖团只有中奖之后才到待发货
	    		if ($l_v['isdraw'] == '1') {
	    			if ($l_v['isprize'] != '1') {
	    				unset($list[$l_k]);
	    				$total['total'] -= 1;
	    				continue;
	    			}
	    		}
	    	}
	    	$usa['id'] = $l_v['bid'];
	    	unset($l_v['bid']);
	    	$usa['title'] = $good['title'];
	    	$usa['thumb'] = $good['thumb'];
	    	$usa['p1'] = $good['p1'];
	    	$usa['p2'] = $good['p2'];
	    	$usa['p3'] = $good['p3'];
	    	$usa['productprice'] = $good['productprice'];
	    	$usa['marketprice'] = $good['marketprice'];
	    	$usa['timeprice'] = $good['timeprice'];
	    	$usa['goodprice'] = $l_v['goodprice'];
	    	$usa['draw'] = $good['draw'];
	    	$usa['goodsid'] = $l_v['goodsid'];
	    	unset($l_v['goodsid']);
	    	$usa['total'] = $l_v['total'];
	    	unset($l_v['total']);
	    	$usa['status'] = $l_v['b_status'];
	    	unset($l_v['b_status']);
	    	$usa['seller_openid'] = $l_v['seller_openid'];
	    	unset($l_v['seller_openid']);
	    	$usa['shopname'] = $l_v['shopname'];
	    	unset($l_v['shopname']);
	    	$usa['iscomment'] = $l_v['iscomment'];
	    	unset($l_v['iscomment']);
	    	$usa['group_status'] = $l_v['group_status'];
	    	unset($l_v['group_status']);
	    	$usa['group_id'] = $l_v['group_id'];
	    	unset($l_v['group_id']);
	    	$usa['shop_type'] = $l_v['shop_type'];
	    	unset($l_v['shop_type']);
	    	if ($usa['status'] == 4) {
	    		$refund_price = mysqld_select("SELECT refund_price FROM ".table('aftersales')." WHERE order_goods_id=".$usa['id']);
	    		$usa['refund_price'] = $refund_price['refund_price'];
	    	}

	    	$l_v['goods'][] = $usa;
	    }
	    unset($l_v);
	    // 重新排列数组下标
		$list = array_merge($list);
	   	
	   	// 处理单订单多商品
		$orderid_ary = array();
		foreach ($list as $orrk => $orrv) {
			foreach ($orderid_ary as $ody) {
				if ($orrv['id'] == $ody['orderid']) {
					$list[$ody['key']]['goods'][] = $orrv['goods'][0];
					unset($list[$orrk]);
					continue 2;
				}
			}
			$oa = array();
			$oa['orderid'] = $orrv['id'];
			$oa['key'] = $orrk;
			$orderid_ary[] = $oa;
		}
		// 重新排列数组下标
		$list = array_merge($list);
		// 将订单商品数组按卖家排序
		foreach ($list as &$l) {
			$l['goods'] = array_order($l['goods'], 'seller_openid', 'SORT_DESC');
			// 订单总件数
			$l['goods_total'] = 0;
			foreach ($l['goods'] as $lgsv) {
				$l['goods_total'] += intval($lgsv['total']);
			}
		}
		unset($l);
    }
    
    $result['data']['order'] = $list;
    $result['data']['total'] = $total['total'];
    $result['code'] = 1;
    // dump($result);

	echo apiReturn($result);
	exit;