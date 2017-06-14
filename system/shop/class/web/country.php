<?php
set_time_limit(1800000) ;
setlocale(LC_ALL, 'zh_CN');
$cfg = globaSetting();
$operation = ! empty($_GP['op']) ? $_GP['op'] : 'display';

if ($operation == 'display') {
	$country = mysqld_selectall("SELECT * FROM " . table('shop_country') . "  where deleted=0");
	// dump($country);
	
	include page('country_list');
}elseif ($operation == 'add') {
	$country = mysqld_selectall("SELECT * FROM " . table('shop_country') . "  where deleted=0");

	if (checksubmit('submit')) {
		if (empty($_GP['country_name'])) {
			message('国家为空!',refresh(),'error');
		}
		$data = array('name' => $_GP['country_name']);
		if (!empty($_FILES['thumb']['tmp_name'])) {
            $upload = file_upload($_FILES['thumb']);
            // dump($_FILES['thumb']);
            if (is_error($upload)) {
                message($upload['message'], '', 'error');
            }
            // dump($upload);
            $data['icon'] = $upload['path'];
        }else{
        	message('图标不存在!',refresh(),'error');
        }
		mysqld_insert('shop_country', $data);
		message('增加成功！',web_url('country'),'succes');
		return;
	}
	include page('country_add');
}elseif ($operation == 'edit') {
	$id = $_GP['id'];
	$this_country = mysqld_select('SELECT * FROM '.table('shop_country')." WHERE  id=:uid AND deleted=0" , array(':uid'=> $id));
	if (checksubmit('submit')) {
		if (empty($_GP['country_name'])) {
			message('国家为空!',refresh(),'error');
		}
		$data = array('name' => $_GP['country_name']);
		if (!empty($_FILES['thumb']['tmp_name'])) {
            $upload = file_upload($_FILES['thumb']);
            // dump($_FILES['thumb']);
            if (is_error($upload)) {
                message($upload['message'], '', 'error');
            }
            // dump($upload);
            $data['icon'] = $upload['path'];
        }
		mysqld_update('shop_country', $data, array('id'=> $_GP['id']));
		message('修改成功！',web_url('country'),'succes');
		return;
	}
	include page('country_add');
}elseif ($operation == 'delete') {
	mysqld_update('shop_country', array('deleted' => 1), array('id'=> $_GP['id']));
	message('删除成功',refresh(),'success');	
}