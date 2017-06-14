<?php
set_time_limit(1800000) ;
setlocale(LC_ALL, 'zh_CN');
$cfg = globaSetting();
$operation = ! empty($_GP['op']) ? $_GP['op'] : 'display';

if ($operation == 'display') {
	//优先展示第一级分类的品牌
	$all_category  = getCategoryAllparent();
	$first_son     = array();
	$where = "deleted=0";
	if(!empty($_GP['p1'])){
		$first_son   = getCategoryByParentid($_GP['p1']);
		$where .= " and p1={$_GP['p1']}";
	}
	if(!empty($_GP['p2'])){
		$where .= " and p2={$_GP['p2']}";
	}
	if(!empty($_GP['p3'])){
		$where .= " and p3={$_GP['p3']}";
	}

	$psize =  30;
	$pindex = max(1, intval($_GP["page"]));
	$limit = ' limit '.($pindex-1)*$psize.','.$psize;

	$brand  = mysqld_selectall("SELECT * FROM " . table('shop_brand') . "  where {$where} {$limit}");
	$total  = mysqld_selectcolumn("select count('id') from ".table('shop_brand')." where {$where}");
	$pager  = pagination($total, $pindex, $psize);
    foreach ( $brand as &$brand_value ){
         $country = get_country($brand_value['country_id']);
		 $brand_value['country_img'] = $country['icon'];
	}

	include page('brand_list');
}elseif ($operation == 'add') {

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
          
          //获取分类所属行业ID
          $industryArr =  mysqld_select("SELECT industry_p1_id,industry_p2_id FROM " . table('shop_category') . " where id={$_GP['p1']}");
          $data['industry_p1_id'] = $industryArr['industry_p1_id'];
          $data['industry_p2_id'] = $industryArr['industry_p2_id'];
          
		mysqld_insert('shop_brand', $data);
		message('增加成功！',web_url('brand'),'succes');
		return;
	}

	if(!empty($_GP['ajaxCat']) && !empty($_GP['id'])){
		$all_category  = mysqld_selectall("SELECT * FROM " . table('shop_category') . "  where parentid={$_GP['id']} and  deleted=0  ORDER BY parentid ASC, displayorder ASC");
		if(empty($all_category)){
			die(showAjaxMess(1002,'无数据！'));
		}else{
			die(showAjaxMess(200,$all_category));
		}
	}

	$all_category  = mysqld_selectall("SELECT * FROM " . table('shop_category') . "  where parentid=0 and  deleted=0  ORDER BY parentid ASC, displayorder ASC");
	$country       = mysqld_selectall("SELECT * FROM " . table('shop_country') . "  where deleted=0");
	include page('brand_add');
}elseif ($operation == 'edit') {
	$id = $_GP['id'];
	$isEdit = true;
	if (checksubmit('submit')) {
		if ($_GP['country'] == 'nil' or empty($_GP['u_brand'])) {
			message('品牌或国家为空!',refresh(),'error');
		}
		$_GP['recommend'] = !empty($_GP['recommend']) ? 1 : 0 ;
        	$_GP['isindex'] = !empty($_GP['isindex']) ? 1 : 0 ;
		$data = array(
			'brand' => $_GP['u_brand'],
			'country_id' => $_GP['country'],
			'recommend' => $_GP['recommend'],
			'description' => $_GP['description'],
			'content' => $_GP['content'],
			'isindex' => $_GP['isindex'],
			'p1' => $_GP['p1'],
			'p2' => $_GP['p2'],
			'p3' => $_GP['p3'],
		);
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
          
          
          //获取分类所属行业ID
          $industryArr =  mysqld_select("SELECT industry_p1_id,industry_p2_id FROM " . table('shop_category') . "  where id={$_GP['p1']}");
          $data['industry_p1_id'] = $industryArr['industry_p1_id'];
          $data['industry_p2_id'] = $industryArr['industry_p2_id'];
          
		mysqld_update('shop_brand', $data, array('id'=> $_GP['id']));
		message('修改成功！',web_url('brand'),'succes');
		return;
	}

	$country    = mysqld_selectall("SELECT * FROM " . table('shop_country') . "  where deleted=0");
	$this_brand = mysqld_select('SELECT * FROM '.table('shop_brand')." WHERE  id=:uid AND deleted=0" , array(':uid'=> $id));

	$all_category  = mysqld_selectall("SELECT * FROM " . table('shop_category') . "  where parentid=0 and  deleted=0  ORDER BY parentid ASC, displayorder ASC");
	$first_son     = $second_son = array();
	if(!empty($this_brand['p1'])){
		$first_son   = mysqld_selectall("SELECT * FROM " . table('shop_category') . "  where parentid={$this_brand['p1']} and  deleted=0  ORDER BY parentid ASC, displayorder ASC");
	}
	if(!empty($this_brand['p2'])){
		$second_son   = mysqld_selectall("SELECT * FROM " . table('shop_category') . "  where parentid={$this_brand['p2']} and  deleted=0  ORDER BY parentid ASC, displayorder ASC");
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