<?php
	/**
	 * app 通用长时登陆操作接口
	 * @author WZW
	 * 
	 */
	$result = array();
	
	// 设备标识码
	$d_code = $_REQUEST['device_code'];
	$op = $_GP['op'];
	
	if (empty($d_code)) {
		$result['message'] 	= "设备标识码为空!";
		$result['code'] 	= 0;
		echo apiReturn($result);
		exit;
	}

	if (!extension_loaded('Memcached')) {
		$result['message'] 	= "Memcached扩展未安装!";
		$result['code'] 	= 0;
		echo apiReturn($result);
		exit;
	}

	$mcache = new Mcache();
	// $m = new Memcached();
	// $m->addServer('localhost',11211);
	// dump($mcache->set('ar777', 999999));
	// dump($mcache->get('ar777'));
	// dump($mcache->delete('ar777'));
	// dump($mcache->delete('ar777'));
	// dump($mcache->delete('ar777'));
	// return;

	if ($op == 'login') {
		// 登陆初始化
		// $mcache->init_msession($d_code);
		$result['message'] = "此参数已废弃!";
    	$result['code'] = 0;
	}elseif ($op == 'logout') {
		// 退出登陆
		$_SESSION[MOBILE_ACCOUNT] = null;
		$mcache->del_msession($d_code);

		$result['message'] = "登陆信息删除成功!";
		$result['code'] = 1;
	}

	echo apiReturn($result);
	exit;