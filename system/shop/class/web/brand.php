<?php
set_time_limit(1800000) ;
setlocale(LC_ALL, 'zh_CN');
$cfg = globaSetting();
$operation = ! empty($_GP['op']) ? $_GP['op'] : 'display';

if ($operation == 'display') {
	$brand = mysqld_selectall("SELECT * FROM " . table('shop_brand') . "  where deleted=0");
    foreach ( $brand as &$brand_value ){
         $country = get_country($brand_value['country_id']);
		 $brand_value['country_img'] = $country['icon'];
	}
	if ($_GP['ajax'] == 'daifa' && !empty($_GP['id'])){
		$result = mysqld_select("SELECT * FROM ".table('shop_brand')." WHERE id = ".$_GP['id']);
        $daifa = ABS($result['daifa'] - 1 );
		$data = array('daifa'=>$daifa);
		mysqld_update('shop_brand',$data,array('id'=>$_GP['id']));
		die(showAjaxMess('200',array('id'=>$_GP['id'], 'daifa'=>$daifa )));
	}
	if ($_GP['ajax'] == 'pifa' && !empty($_GP['id'])){
         $result = mysqld_select("SELECT * FROM ".table('shop_brand')." WHERE id = ".$_GP['id']);
        $pifa = ABS($result['pifa'] - 1 );
		$data = array('pifa'=>$pifa);
		mysqld_update('shop_brand',$data,array('id'=>$_GP['id']));
		die(showAjaxMess('200',array('id'=>$_GP['id'], 'pifa'=>$pifa )));
	}
	include page('brand_list');
}elseif ($operation == 'add') {
	$country = mysqld_selectall("SELECT * FROM " . table('shop_country') . "  where deleted=0");
	$idEdit = false;
	if (checksubmit('submit')) {
		if ($_GP['country'] == 'nil' or empty($_GP['u_brand'])) {
			message('品牌或国家为空!',refresh(),'error');
		}
		$_GP['recommend'] = !empty($_GP['recommend']) ? 1 : 0 ;
		$_GP['isindex'] = !empty($_GP['isindex']) ? 1 : 0 ;
		$data = array('brand' => $_GP['u_brand'], 'country_id' => $_GP['country'], 'recommend' => $_GP['recommend'], 'description' => $_GP['description'], 'content' => $_GP['content'], 'isindex' => $_GP['isindex']);
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
		if (!empty($_FILES['brand_public']['tmp_name'])) {
                    file_delete($_GP['brand_public_old']);
                    $upload = file_upload($_FILES['brand_public']);
                    if (is_error($upload)) {
                        message($upload['message'], '', 'error');
                    }
                    $data['brand_public'] = $upload['path'];
                }
		if (!empty($_FILES['brand_ad']['tmp_name'])) {
                    file_delete($_GP['brand_ad_old']);
                    $upload = file_upload($_FILES['brand_ad']);
                    if (is_error($upload)) {
                        message($upload['message'], '', 'error');
                    }
                    $data['brand_ad'] = $upload['path'];
          }
		mysqld_insert('shop_brand', $data);
		message('增加成功！',web_url('brand'),'succes');
		return;
	}
	include page('brand_add');
}elseif ($operation == 'edit') {
	$id = $_GP['id'];
	$isEdit = true;
	$country = mysqld_selectall("SELECT * FROM " . table('shop_country') . "  where deleted=0");
	$this_brand = mysqld_select('SELECT * FROM '.table('shop_brand')." WHERE  id=:uid AND deleted=0" , array(':uid'=> $id));
	// dump($this_hot);

	if (checksubmit('submit')) {
		if ($_GP['country'] == 'nil' or empty($_GP['u_brand'])) {
			message('品牌或国家为空!',refresh(),'error');
		}
		$_GP['recommend'] = !empty($_GP['recommend']) ? 1 : 0 ;
        	$_GP['isindex'] = !empty($_GP['isindex']) ? 1 : 0 ;
		$data = array('brand' => $_GP['u_brand'], 'country_id' => $_GP['country'], 'recommend' => $_GP['recommend'],'description' => $_GP['description'], 'content' => $_GP['content'],  'isindex' => $_GP['isindex']);
		if (!empty($_FILES['thumb']['tmp_name'])) {
            $upload = file_upload($_FILES['thumb']);
            // dump($_FILES['thumb']);
            if (is_error($upload)) {
                message($upload['message'], '', 'error');
            }
            // dump($upload);
            $data['icon'] = $upload['path'];
        }
			if (!empty($_FILES['brand_public']['tmp_name'])) {
                    file_delete($_GP['brand_public_old']);
                    $upload = file_upload($_FILES['brand_public']);
                    if (is_error($upload)) {
                        message($upload['message'], '', 'error');
                    }
                    $data['brand_public'] = $upload['path'];
                }
		if (!empty($_FILES['brand_ad']['tmp_name'])) {
                    file_delete($_GP['brand_ad_old']);
                    $upload = file_upload($_FILES['brand_ad']);
                    if (is_error($upload)) {
                        message($upload['message'], '', 'error');
                    }
                    $data['brand_ad'] = $upload['path'];
          }
		mysqld_update('shop_brand', $data, array('id'=> $_GP['id']));
		message('修改成功！',web_url('brand'),'succes');
		return;
	}
	include page('brand_add');
}elseif ($operation == 'delete') {
	mysqld_update('shop_brand', array('deleted' => 1), array('id'=> $_GP['id']));
	message('删除成功',refresh(),'success');	
}

// 获取指定ID国家信息
function get_country($id) {
	$country_data = mysqld_select('SELECT * FROM '.table('shop_country')." WHERE  id=:uid AND deleted=0" , array(':uid'=> $id));
	return $country_data;
}