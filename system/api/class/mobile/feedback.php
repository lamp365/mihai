<?php
	/**
	 * app 意见反馈
	 * @author WZW
	 * 
	 */

	$result = array();
	
	// 联系方式
	$connection = $_GP['connection'];
	// 反馈内容
	$content = $_GP['content'];
	
	if (empty($content)) {
		$result['message'] 	= "反馈内容为空!";
		$result['code'] 	= 0;
		echo apiReturn($result);
		exit;
	}

	$data = array('content' => $content, 'createtime' => time());
	if (!empty($connection)) {
		$data['connection'] = $connection;
	}
	$in = mysqld_insert('feedback', $data);

	if ($in) {
		$result['message'] = "反馈成功!";
    	$result['code'] = 1;
	}else{
		$result['message'] = "反馈失败!";
    	$result['code'] = 0;
	}

	echo apiReturn($result);
	exit;