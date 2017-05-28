<?php
	/**
	 * app 某个订单的详情
	 * @author WZW
	 * 
	 */

	$result = array();
	
	// 订单ID
	$id = $_GP['id'];
	
	if (empty($id)) {
		$result['message'] 	= "订单ID为空!";
		$result['code'] 	= 0;
		echo apiReturn($result);
		exit;
	}

	$where = "id = '" . $id . "'";
    
    $list = get_order(
    	array('where' => $where)
    	);
    
    if (empty($list)) {
		$result['message'] 	= "查询订单信息失败!";
		$result['code'] 	= 0;
		echo apiReturn($result);
		exit;
	}

    $result['data']['order'] = $list;
    $result['code'] = 1;

	echo apiReturn($result);
	exit;