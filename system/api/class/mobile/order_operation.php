<?php
	/**
	 * app 订单操作
	 * @author WZW
	 * 
	 */

	$result = array();
	
	// 订单ID
	$id = $_GP['id'];
	// 操作类型
	$op = $_GP['op'];
	
	// if (empty($id) or empty($op)) {
	// 	$result['message'] 	= "订单ID和操作类型不能为空!";
	// 	$result['code'] 	= 0;
	// 	echo apiReturn($result);
	// 	exit;
	// }

	$where = "id = '" . $id . "'";
	$order = get_order(
    	array('where' => $where)
    	);
	if (empty($order)) {
		$result['message'] 	= "查询订单信息失败!";
		$result['code'] 	= 0;
		echo apiReturn($result);
		exit;
	}
	// dump($order);
	if ($op == 'cancel') {
		// 取消订单
		if ($order['status'] == 0) {
			update_order_status($id, -1);
			$result['message'] 	= "订单取消成功。";
			$result['code'] 	= 1;
		}else{
			$result['message'] 	= "当前订单状态无法取消订单!";
			$result['code'] 	= 0;
		}
	}elseif ($op == 'notarize') {
		// 确认收货
		if ($order['status'] == 2) {
			update_order_status($id, 3);
			$result['message'] 	= "确认收货成功。";
			$result['code'] 	= 1;
		}else{
			$result['message'] 	= "当前订单状态无法确认收货!";
			$result['code'] 	= 0;
		}
	}elseif ($op == 'delete') {
		// 删除订单
		if ($order['status'] == -6 or $order['status'] == 3) {
			// update_order_status($id, 3);
    		mysqld_update('shop_order', array('deleted' => 1), array('id'=> $id));
			$result['message'] 	= "删除订单成功。";
			$result['code'] 	= 1;
		}else{
			$result['message'] 	= "当前订单状态无法删除订单!";
			$result['code'] 	= 0;
		}
	}else{
		$result['message'] 	= "操作类型错误!";
		$result['code'] 	= 0;
	}

	// dump($result);
	echo apiReturn($result);
	exit;