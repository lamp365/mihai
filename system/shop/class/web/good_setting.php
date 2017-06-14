<?php
set_time_limit(1800000) ;
setlocale(LC_ALL, 'zh_CN');

$operation = ! empty($_GP['op']) ? $_GP['op'] : 'display';

if ($operation == 'display') {
	$head = mysqld_select("SELECT * FROM ".table('config')." where name=:uname", array('uname' => 'detail_head'));
	$foot = mysqld_select("SELECT * FROM ".table('config')." where name=:uname", array('uname' => 'detail_foot'));
	$pc_head = mysqld_select("SELECT * FROM ".table('config')." where name=:uname", array('uname' => 'detail_pc_head'));

	if (checksubmit('submit')) {
		// if (empty($_GP['content']) or empty($_GP['content2'])) {
		// 	message('页头或页脚为空!',refresh(),'error');
		// }
		if (empty($_GP['content'])) {
			$u_head = array('name' => 'detail_head', 'value' => '');
		}else{
			$u_head = array('name' => 'detail_head', 'value' => htmlspecialchars_decode($_GP['content']));
		}
		if (empty($_GP['content2'])) {
			$u_foot = array('name' => 'detail_foot', 'value' => '');
		}else{
			$u_foot = array('name' => 'detail_foot', 'value' => htmlspecialchars_decode($_GP['content2']));
		}
		if (empty($_GP['content3'])) {
			$pcu_head = array('name' => 'detail_pc_head', 'value' => '');
		}else{
			$pcu_head = array('name' => 'detail_pc_head', 'value' => htmlspecialchars_decode($_GP['content3']));
		}

		if (empty($head)) {
			mysqld_insert('config', $u_head);
		}else{
			mysqld_update('config', $u_head, array('name'=> 'detail_head'));
		}
		if (empty($foot)) {
			mysqld_insert('config', $u_foot);
		}else{
			mysqld_update('config', $u_foot, array('name'=> 'detail_foot'));
		}
		if (empty($pc_head)) {
			mysqld_insert('config', $pcu_head);
		}else{
			mysqld_update('config', $pcu_head, array('name'=> 'detail_pc_head'));
		}
		message('设置成功！',web_url('good_setting'),'succes');
		return;
	}
	include page('good_setting');
}

