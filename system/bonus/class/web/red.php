<?php
$operation = !empty($_GP['op']) ? $_GP['op'] : 'display';
$type_ary = array('1' => '现金', '2' => '优惠券');
if ($operation == 'display') {
	$red_setting = mysqld_selectall("SELECT * FROM ".table('redenvelope'));
	include page('red_list');
}elseif ($operation == 'post') {
	$red = mysqld_select("SELECT * FROM ".table('redenvelope')." WHERE id=".intval($_GP['id']));
	if (checksubmit('submit')) {
		if (empty($_GP['amount'])) {
			message('红包总金额不能为空！',refresh(),'error');
		}elseif (empty($_GP['goldmax'])) {
			message('红包领取最大值不能为空！',refresh(),'error');
		}elseif (empty($_GP['winrate'])) {
			message('中奖率不能为空！',refresh(),'error');
		}elseif (empty($_GP['sendmax'])) {
			message('每日最大摇奖数不能为空！',refresh(),'error');
		}elseif (empty($_GP['begintime'])) {
			message('开始时间不能为空！',refresh(),'error');
		}elseif (empty($_GP['endtime'])) {
			message('结束时间不能为空！',refresh(),'error');
		}
		if (empty($_GP['id'])) {
			$data = array('amount'=>
			$_GP['amount'],'type'=>
			$_GP['type'],'goldmax'=>
			$_GP['goldmax'],'winrate'=>
			$_GP['winrate'],'sendmax'=>
			$_GP['sendmax'],'begintime'=>
			strtotime($_GP['begintime']),'endtime'=>
			strtotime($_GP['endtime']));
			
			mysqld_insert('redenvelope',$data);
			message("添加成功",create_url('site', array('name' => 'bonus','do' => 'red','op'=>'display')),"success");
		}else{
			$data = array('amount'=>
			$_GP['amount'],'type'=>
			$_GP['type'],'goldmax'=>
			$_GP['goldmax'],'winrate'=>
			$_GP['winrate'],'sendmax'=>
			$_GP['sendmax'],'begintime'=>
			strtotime($_GP['begintime']),'endtime'=>
			strtotime($_GP['endtime']));

			mysqld_update('redenvelope',$data,array('id'=>$_GP['id']));
			message("修改成功",create_url('site', array('name' => 'bonus','do' => 'red','op'=>'display')),"success");
		}
	}
	include page('red_add');
}elseif ($operation == 'delete') {
	mysqld_delete('redenvelope', array('id'=>$_GP['id']));
	message("删除成功！","refresh","success");
}elseif ($operation == 'detail') {
	$pindex = max(1, intval($_GP['page']));
	$psize = 20;
	$where = "";
	$order = "";
	if (!empty($_GP['mobile'])) {
		$where .= " AND mobile='".$_GP['mobile']."'";
		$mobile = $_GP['mobile'];
	}
	if (!empty($_GP['realname'])) {
		$where .= " AND realname='".$_GP['realname']."'";
		$realname = $_GP['realname'];
	}
	if (!empty($_GP['nickname'])) {
		$where .= " AND nickname='".$_GP['nickname']."'";
		$nickname = $_GP['nickname'];
	}
	if (!empty($_GP['order'])) {
		$order .= "sendgold DESC,";
	}
	$redid = intval($_GP['id']);
	$red_detail = mysqld_selectall("SELECT SQL_CALC_FOUND_ROWS * FROM ".table('redenvelope_user')." WHERE redid=".intval($_GP['id']).$where." ORDER BY ".$order." createtime DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
	// 总记录数
	$total = mysqld_select("SELECT FOUND_ROWS() as total;");
	$pager = pagination(intval($total['total']), $pindex, $psize);
	include page('red_detail');
}elseif ($operation == 'del_detail') {
	mysqld_delete('redenvelope_user', array('id'=>$_GP['id']));
	message("删除成功！","refresh","success");
}
