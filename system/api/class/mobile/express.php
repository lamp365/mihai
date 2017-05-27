<?php
	/**
	 * app 物流信息
	 * @author WZW
	 * 
	 */

	$result = array();
	
	// 订单ID
	$id = $_GP['id'];

	// dump(fetch_beihai("DB743249534US"));
	// 1478620800
	// 1478628300
 	// dump(date("Y-m-d H:i:s", 1478628300));
	
	if (empty($id)) {
		$result['message'] 	= "订单ID不能为空!";
		$result['code'] 	= 0;
		echo apiReturn($result);
		exit;
	}

	$where = "id = '" . $id . "'";
	$order = get_order(
    	array('where' => $where)
    	);

	if (empty($order)) {
		$result['message'] 	= "查询订单信息失败!";
		$result['code'] 	= 0;
		echo apiReturn($result);
		exit;
	}elseif ($order['status'] != 2 and $order['status'] != 3 and $order['status'] != -1) {
		$result['message'] 	= "当前订单状态无法查询物流信息!";
		$result['code'] 	= 0;
		echo apiReturn($result);
		exit;
	}
	
	// $expressage = get_expressage($order['express'], $order['expresssn']);
	// 'tiantian', '666837890736'
	// 'yunda', '1901514887605'
	$expressage = fetch_expressage($order['express'], $order['expresssn']);
	if (empty($expressage)) {
		$result['message'] 	= "物流查询失败,请重试!";
		$result['code'] 	= 0;
		echo apiReturn($result);
		exit;
	}
	$expressage['company'] = $order['expresscom'];
	// dump($expressage);

	$result['data'] = $expressage;
	$result['code'] = 1;

	echo apiReturn($result);
	exit;