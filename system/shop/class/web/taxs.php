<?php
set_time_limit(1800000) ;
setlocale(LC_ALL, 'zh_CN');
$cfg = globaSetting();
$operation = ! empty($_GP['op']) ? $_GP['op'] : 'display';
// print_r($operation);

if ($operation == 'display') {
	$list = mysqld_selectall("SELECT * FROM " . table('shop_tax'));
	$list = json_encode($list);
	include page('taxs_list');
}elseif ($operation == 'post') {
	if (checksubmit('submit')) {
		if (empty($_GP['use_type']) or empty($_GP['use_tax'])) {
			message('商品类型或税率为空!',refresh(),'error');
		}

		$data = array('type' => $_GP['use_type'], 'tax' => $_GP['use_tax']);
		mysqld_insert('shop_tax', $data);
		message('新增税率成功！',web_url('taxs'),'succes');
		return;
	}
	include page('taxs_add');
}elseif ($operation == 'edit') {
	$id = $_GP['id'];
	$this_tax = mysqld_select('SELECT * FROM '.table('shop_tax')." WHERE  id=:uid" , array(':uid'=> $id));
	
	if (checksubmit('submit')) {
		if (empty($_GP['use_type']) or empty($_GP['use_tax'])) {
			message('商品类型或税率为空!',refresh(),'error');
		}

		$data = array('type' => $_GP['use_type'], 'tax' => $_GP['use_tax']);
		mysqld_update('shop_tax', $data, array('id'=> $_GP['id']));
		message('修改税率成功！',web_url('taxs'),'succes');
		return;
	}
	include page('taxs_add');
}elseif ($operation == 'delete') {
	mysqld_delete('shop_tax', array('id'=>$_GP['id']));
	message('删除成功',refresh(),'success');	
}

