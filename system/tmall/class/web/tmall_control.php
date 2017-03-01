<?php
defined('SYSTEM_IN') or exit('Access Denied');
$operation = !empty($_GP['op']) ? $_GP['op'] : 'index';
$idt_ary = array('1' => '店长', '2' => '员工');
$result = array();
if ($operation == 'index') {
	// 首页列表
	$pindex = max(1, intval($_GP['page']));
  	$psize = 30;
  	$where = "b.id<>0";
  	$staff = $_GP['staff'];
  	$department = $_GP['department'];
  	$identity = $_GP['identity'];
  	if (!empty($staff)) {
		$where.=" AND b.name='".$staff."'";
	}
	if (!empty($department)) {
		$where.=" AND a.department='".$department."'";
	}
	if (!empty($identity)) {
		$where.=" AND b.identity=".$identity;
	}


	$al_staff = mysqld_selectall("SELECT SQL_CALC_FOUND_ROWS a.department as dpm_name, b.* FROM ".table("tmall")." as a left join ".table("tmall_staff")." as b on a.id=b.department WHERE ".$where." ORDER BY b.id DESC "." LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
	// 总记录数
  	$data_total = mysqld_select("SELECT FOUND_ROWS() as total;");
 //  	if (empty($al_staff)) {
	// 	message('查询失败，请检查查询条件!',refresh(),'error');
	// }
	$total = $data_total['total'];
	$department_ary = array();
	$admin_ary = array();
	$staff_ary = array();
	$al_department = mysqld_selectall("SELECT * FROM ".table("tmall")." ORDER BY createtime DESC");
	$al_admin = mysqld_selectall("SELECT * FROM ".table("user"));
	$al_sta = mysqld_selectall("SELECT * FROM ".table("tmall_staff"));
	foreach ($al_department as $adv) {
		$department_ary[] = $adv['department'];
	}
	foreach ($al_admin as $admv) {
		$admin_ary[] = $admv['username'];
	}
	foreach ($al_sta as $astv) {
		$staff_ary[] = $astv['name'];
	}
	$department_ary = array_unique($department_ary);
	$identity_ary = array('1', '2');

	$pager = pagination($total, $pindex, $psize);
	include page('tmall_control');
}elseif ($operation == 'add_staff') {
	// 添加人员
	$id = $_GP['edit_id'];
	$name = $_GP['user_name'];
	$department = $_GP['check_department'];
	$admin = $_GP['back_account'];
	$idt = $_GP['idt'];

	$depart_id = mysqld_select("SELECT id FROM ".table("tmall")." WHERE department='".$department."'");
	if (empty($id)) {
		// 新增
		mysqld_insert("tmall_staff", array('name'=>$name, 'department'=>$depart_id['id'], 'admin'=>$admin, 'identity'=>$idt));
		$i_id = mysqld_insertid();
		if ($idt == '1') {
			mysqld_update('tmall', array('manager'=>$i_id),array('id'=>$depart_id['id']));
		}
	}else{
		$have_man = mysqld_select("SELECT * FROM ".table('tmall')." WHERE manager=".$id);
		if (!empty($have_man['id']) AND $idt == '2') {
			// 店铺经理降为员工
			$other_man = mysqld_select("SELECT id FROM ".table('tmall_staff')." WHERE department=".$have_man['id']." AND identity=1");
			if (!empty($other_man['id'])) {
				mysqld_update('tmall', array('manager'=>$other_man['id']),array('id'=>$have_man['id']));
			}else{
				mysqld_update('tmall', array('manager'=>NULL),array('id'=>$have_man['id']));
			}
		}
		// 更新
		mysqld_update("tmall_staff", array('name'=>$name, 'department'=>$depart_id['id'], 'admin'=>$admin, 'identity'=>$idt), array('id'=>$id));
	}
	$result['message'] = 1;
	echo json_encode($result);
}elseif ($operation == 'get_staff') {
	// 获取当前信息
	$id = $_GP['id'];
	if (!empty($id)) {
		$this_staff = mysqld_select("SELECT a.*, b.department as b_depart FROM ".table("tmall_staff")." as a left join ".table("tmall")." as b on a.department=b.id WHERE a.id=".$id);
	}
	$result['username'] = $this_staff['name'];
	$result['backaccount'] = $this_staff['admin'];
	$result['department'] = $this_staff['b_depart'];
	$result['idt'] = $this_staff['identity'];
	echo json_encode($result);
}elseif ($operation == 'add_department') {
	// 添加店铺
	$department_name = $_GP['department_name'];
	$department_code = $_GP['department_code'];

	if (!empty($department_name)) {
		$re = mysqld_insert("tmall", array('department'=>$department_name, 'code'=>$department_code, 'createtime'=>time()));
		$result['message'] = 1;
	}else{
		$result['message'] = 0;
	}
	echo json_encode($result);
}elseif ($operation == 'set_department') {
	// 设置店铺
	$depart_name = $_GP['depart_name'];
	$manager = $_GP['manager'];

	if (!empty($depart_name) AND !empty($manager)) {
		$use_id = mysqld_select("SELECT a.id as depart_id, b.id as manager_id FROM ".table("tmall")." as a left join ".table("tmall_staff")." as b on a.id=b.department WHERE a.department='".$depart_name."' AND b.name='".$manager."'");
		// $de_id = mysqld_select("SELECT id FROM ".table("tmall")." WHERE department='".$depart_name."'");
		// $man_id = mysqld_select("SELECT id FROM ".table("tmall_staff")." WHERE name='".$manager."'");
		if (!empty($use_id)) {
			$re1 = mysqld_update("tmall", array('manager'=>$use_id['manager_id']), array('id'=>$use_id['depart_id']));
			$re2 = mysqld_update("tmall_staff", array('identity'=>1), array('id'=>$use_id['manager_id']));
			$result['message'] = 1;
		}else{
			$result['message'] = 0;
		}
	}else{
		$result['message'] = 0;
	}
	echo json_encode($result);
}
