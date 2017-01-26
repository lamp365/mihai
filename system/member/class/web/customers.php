<?php
defined('SYSTEM_IN') or exit('Access Denied');
$operation = !empty($_GP['op']) ? $_GP['op'] : 'display';
$client_status = array('0' => '未入驻', '1' => '已入驻');
$contact_status = array('0' => '未联系', '1' => '已联系');
// 根据当前后台账号进行展示
$admin = $_CMS['account']['username'];
// $admin = 'meigong';
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

	$al_client = mysqld_selectall("SELECT SQL_CALC_FOUND_ROWS a.*, b.name FROM ".table('shop_customers')." as a left join ".table('shop_department_staff')." as b on a.salesman=b.id WHERE ".$where." ORDER BY a.updatetime DESC"." LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
	// 总记录数
  	$data_total = mysqld_select("SELECT FOUND_ROWS() as total;");
  	foreach ($al_client as &$aclv) {
  		if (mysqld_select("SELECT * FROM ".table('member')." WHERE mobile=".$aclv['mobile'])) {
			$aclv['status'] = '1';
			mysqld_update('shop_customers', array('status'=>1), array('id'=>$aclv['id']));
	    }
  	}
  	unset($aclv);
  	$total = $data_total['total'];
	$city_a = array();
	$level_a = array();
	$shop_a = array();
	$staff_a = array();
	$all_c = mysqld_selectall("SELECT a.*, b.name FROM ".table('shop_customers')." as a left join ".table('shop_department_staff')." as b on a.salesman=b.id ".$uw1." ORDER BY a.updatetime DESC");
	$all_sta = mysqld_selectall("SELECT * FROM ".table('shop_department_staff')." ".$uw2);
	if (!empty($all_c)) {
		foreach ($all_c as $acv) {
			$city_a[] = $acv['city'];
			$level_a[] = $acv['level'];
	    	$shop_a[] = $acv['shop'];
		}
	}
	if (!empty($all_sta)) {
		foreach ($all_sta as $aslv) {
			$staff_a[] = $aslv['name'];
		}
	}
	$city_a = array_unique($city_a);
	$level_a = array_unique($level_a);
	$shop_a = array_unique($shop_a);
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
}
