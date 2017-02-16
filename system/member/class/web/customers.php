<?php
defined('SYSTEM_IN') or exit('Access Denied');
$operation = !empty($_GP['op']) ? $_GP['op'] : 'display';
$client_status = array('0' => '未入驻', '1' => '已入驻');
$contact_status = array('0' => '否', '1' => '已联系');
// 根据当前后台账号进行展示
$admin = $_CMS['account']['username'];
// $admin = 'yanfa';
$n_user = mysqld_select("SELECT * FROM ".table('shop_department_staff')." WHERE admin='".$admin."'");

if ($operation == 'display') {
	$pindex = max(1, intval($_GP['page']));
  	$psize = 30;
  	if ($admin != 'root') {
  		if ($n_user['identity'] == '1') {
	  		$where = "b.department=".$n_user['department'];
	  	}else{
	  		$where = "b.department=".$n_user['department']." AND a.salesman=".$n_user['id'];
	  	}
	  	$uw1 = "WHERE b.department=".$n_user['department'];
	  	$uw2 = "WHERE department=".$n_user['department'];
  	}else{
  		$where = 'a.id<>0';
  		$n_user['identity'] = 1;
  		$uw1 = '';
  		$uw2 = '';
  	}
  	
	$city = $_GP['city'];
	$level = $_GP['member'];
	$shop = $_GP['shop'];
	$staff = $_GP['department'];
	$review = $_GP['bad'];
	$refund = $_GP['refund'];
	$blacklist = $_GP['blacklist'];
	$d_money = $_GP['d_money'];
  	$h_money = $_GP['h_money'];
  	$allot = $_GP['allot'];
  	$ienter = $_GP['ienter'];

	if (!empty($city)) {
		$where.=" AND a.city='".$city."'";
	}
	if (!empty($level)) {
		$where.=" AND a.level='".$level."'";
	}
	if (!empty($shop)) {
		$where.=" AND a.shop='".$shop."'";
	}
	if (!empty($review) AND $review!='false') {
		$where.=" AND a.review='是'";
	}
	if (!empty($refund) AND $refund!='false') {
		$where.=" AND a.refund='是'";
	}
	if (!empty($blacklist) AND $blacklist!='false') {
		$where.=" AND a.blacklist='是'";
	}
	if (!empty($staff)) {
		$man = mysqld_select("SELECT id FROM ".table('shop_department_staff')." WHERE name='".$staff."'");
		$where.=" AND a.salesman=".$man['id'];
	}
	if (!empty($d_money)) {
	    $where.=" AND price>".$d_money;
	}
  	if (!empty($h_money)) {
	    $where.=" AND price<".$h_money;
	}
	if (!empty($allot) AND $allot!='false') {
		$where.=" AND salesman=''";
	}
	if (!empty($ienter) AND $ienter!='false') {
		$where.=" AND status=0";
	}

	$al_client = mysqld_selectall("SELECT SQL_CALC_FOUND_ROWS a.*, b.name FROM ".table('shop_customers')." as a left join ".table('shop_department_staff')." as b on a.salesman=b.id WHERE ".$where." ORDER BY a.lasttime DESC,a.contact ASC"." LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
	// 总记录数
  	$data_total = mysqld_select("SELECT FOUND_ROWS() as total;");
	if ( is_array($al_client) ){
  	foreach ($al_client as &$aclv) {
  		if (mysqld_select("SELECT * FROM ".table('member')." WHERE mobile=".$aclv['mobile'])) {
			$aclv['status'] = '1';
			mysqld_update('shop_customers', array('status'=>1), array('id'=>$aclv['id']));
	    }
  	}
	unset($aclv);
	}
  	
  	$total = $data_total['total'];
	$city_a = array();
	$level_a = array();
	$shop_a = array();
	$staff_a = array();
	// $all_c = mysqld_selectall("SELECT a.*, b.name FROM ".table('shop_customers')." as a left join ".table('shop_department_staff')." as b on a.salesman=b.id ".$uw1." ORDER BY a.updatetime DESC");
	$all_sta = mysqld_selectall("SELECT * FROM ".table('shop_department_staff')." ".$uw2);
	// if (!empty($all_c)) {
	// 	foreach ($all_c as $acv) {
	// 		$city_a[] = $acv['city'];
	// 		$level_a[] = $acv['level'];
	//     	$shop_a[] = $acv['shop'];
	// 	}
	// }
	if (!empty($all_sta)) {
		foreach ($all_sta as $aslv) {
			$staff_a[] = $aslv['name'];
		}
	}
	$city_a = mysqld_selectall("SELECT a.city FROM ".table('shop_customers')." as a left join ".table('shop_department_staff')." as b on a.salesman=b.id ".$uw1." GROUP BY a.city");
	$level_a = mysqld_selectall("SELECT a.level FROM ".table('shop_customers')." as a left join ".table('shop_department_staff')." as b on a.salesman=b.id ".$uw1." GROUP BY a.level");
	$shop_a = mysqld_selectall("SELECT a.shop FROM ".table('shop_customers')." as a left join ".table('shop_department_staff')." as b on a.salesman=b.id ".$uw1." GROUP BY a.shop");
	$staff_a = array_unique($staff_a);

	if ($n_user['identity'] == '2') {
		// 员工权限
		$is_boos = false;
	}elseif ($n_user['identity'] == '1' or $n_user['identity'] == '0') {
		// 经理权限
		$is_boos = true;
	}else{
		$is_boos = false;
	}
	$pager = pagination($total, $pindex, $psize);
  	include page('customers');
}elseif ($operation == 'check_allot') {
  // 检查分配
  $where = "a.salesman=".$n_user['id']." AND b.department=".$n_user['department'];
  $city = $_GP['city'];
  $level = $_GP['member'];
  $shop = $_GP['shop'];
  $manager = $_GP['department'];
  $review = $_GP['bad'];
  $refund = $_GP['refund'];
  $blacklist = $_GP['blacklist'];
  $d_money = $_GP['d_money'];
  $h_money = $_GP['h_money'];
  $allot = $_GP['allot'];
  $ienter = $_GP['ienter'];

  if (!empty($city)) {
    $where.=" AND a.city='".$city."'";
  }
  if (!empty($level)) {
    $where.=" AND a.level='".$level."'";
  }
  if (!empty($shop)) {
    $where.=" AND a.shop='".$shop."'";
  }
  if (!empty($review) AND $review!='false') {
    $where.=" AND a.review='是'";
  }
  if (!empty($refund) AND $refund!='false') {
    $where.=" AND a.refund='是'";
  }
  if (!empty($blacklist) AND $blacklist!='false') {
    $where.=" AND a.blacklist='是'";
  }
  if (!empty($d_money)) {
    $where.=" AND price>".$d_money;
  }
  if (!empty($h_money)) {
    $where.=" AND price<".$h_money;
  }
  if (!empty($allot) AND $allot!='false') {
	$where.=" AND salesman=''";
  }
  if (!empty($ienter) AND $ienter!='false') {
	$where.=" AND status=0";
  }

  $al_client = mysqld_selectall("SELECT SQL_CALC_FOUND_ROWS a.*, b.name FROM ".table('shop_customers')." as a left join ".table('shop_department_staff')." as b on a.salesman=b.id WHERE ".$where." ORDER BY a.updatetime DESC");
  // 总记录数
  $data_total = mysqld_select("SELECT FOUND_ROWS() as total;");
  if (empty($al_client)) {
    $data_total['total'] = 0;
  }
  echo json_encode($data_total);
}elseif ($operation == 'allot_ones') {
	// 单个分配
	$client_id = $_GP['data_id'];
	$staff = $_GP['department'];

	if (empty($client_id) or empty($staff)) {
	$result['message'] = '员工或客户不能为空!';
	echo json_encode($result);
	exit;
	}
	$man = mysqld_select("SELECT id, name FROM ".table('shop_department_staff')." WHERE name='".$staff."'");
	mysqld_update('shop_customers', array('salesman' => $man['id'], 'updatetime' => time()), array('id'=> $client_id));

	$result['message'] = '分配完成!';
	$result['staff_name'] = $man['name'];
	echo json_encode($result);
}elseif ($operation == 'allot_all') {
	// 批量分配
	$where = "a.salesman=".$n_user['id']." AND b.department=".$n_user['department'];
	$city = $_GP['city'];
	$level = $_GP['member'];
	$shop = $_GP['shop'];
	$staff = $_GP['department'];
	$review = $_GP['bad'];
	$refund = $_GP['refund'];
	$blacklist = $_GP['blacklist'];
	$d_money = $_GP['d_money'];
  	$h_money = $_GP['h_money'];
  	$allot = $_GP['allot'];
    $ienter = $_GP['ienter'];

	if (!empty($city)) {
		$where.=" AND a.city='".$city."'";
	}
	if (!empty($level)) {
		$where.=" AND a.level='".$level."'";
	}
	if (!empty($shop)) {
		$where.=" AND a.shop='".$shop."'";
	}
	if (!empty($review) AND $review!='false') {
		$where.=" AND a.review='是'";
	}
	if (!empty($refund) AND $refund!='false') {
		$where.=" AND a.refund='是'";
	}
	if (!empty($blacklist) AND $blacklist!='false') {
		$where.=" AND a.blacklist='是'";
	}
	if (!empty($d_money)) {
		$where.=" AND price>".$d_money;
	}
	if (!empty($h_money)) {
		$where.=" AND price<".$h_money;
	}
	if (!empty($allot) AND $allot!='false') {
		$where.=" AND salesman=''";
	}
	if (!empty($ienter) AND $ienter!='false') {
		$where.=" AND status=0";
	}

	$al_client = mysqld_selectall("SELECT a.*, b.name FROM ".table('shop_customers')." as a left join ".table('shop_department_staff')." as b on a.salesman=b.id WHERE ".$where." ORDER BY a.updatetime DESC");
	if (empty($al_client)) {
		$result['message'] = '客户查询失败!';
	}else{
		foreach ($al_client as $almv) {
			$man = mysqld_select("SELECT id, name FROM ".table('shop_department_staff')." WHERE name='".$staff."'");
			mysqld_update('shop_customers', array('salesman' => $man['id'], 'updatetime' => time()), array('id'=> $almv['id']));
		}
		$result['message'] = '批量分配完成!';
	}
	echo json_encode($result);
}elseif ($operation == 'contact') {
	// 标记联系
	$con_id = $_GP['data_id'];

	if (!empty($con_id)) {
		mysqld_update('shop_customers', array('contact' => 1, 'contact_time' => time()), array('id'=> $con_id));
		$result['message'] = 1;
		$result['ctime'] = time();
	}else{
		$result['message'] = 0;
	}
	echo json_encode($result);
}elseif ($operation == 'sendsms') {
	// 发送短信
	$con_id = $_GP['data_id'];
	if (!empty($con_id)) {
		$cus = mysqld_select("SELECT * FROM ".table('shop_customers')." WHERE id=".$con_id);
		$telphone = $cus['mobile'];
		if (date('Y-m-d',$cus['sms_time']) == date('Y-m-d')) {
			$result['message'] = '今日已发过短信!';
			echo json_encode($result);
			return;
		}
		if ( !empty($telphone) and preg_match("/^1[34578]{1}\d{9}$/",$telphone) ){
			 $code = get_code();
			 if (file_exists(WEB_ROOT . '/includes/TopSdk.php')) {
			 	require WEB_ROOT . '/includes/TopSdk.php';
			 	$respObject = send_sms($telphone,$code,'SMS_47070001');
			 	//如果发送失败
				if (isset($respObject->code))
				{
					$result['message'] = '发送失败!';
				}
				else{
					mysqld_update('shop_customers', array('sms_time' => time()), array('id'=> $con_id));
					$result['message'] = '发送成功!';
				}
			}
		}
	}else{
		$result['message'] = '发送失败!';
	}
	echo json_encode($result);
}elseif ($operation == 'get_remark') {
	// 获取备注
	$data_id = $_GP['data_id'];

	if (!empty($data_id)) {
		$re = mysqld_select("SELECT remark FROM ".table('shop_customers')."WHERE id=".$data_id);
		$result['text'] = $re['remark'];
	}
	echo json_encode($result);
}
