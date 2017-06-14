<?php
        set_time_limit(1800000) ;
	    setlocale(LC_ALL, 'zh_CN');
		$op= $operation = $_GP['op']?$_GP['op']:'display';
		$operation = !empty($_GP['op']) ? $_GP['op'] : 'display';
        if ($operation == 'display') {

            if (!empty($_GP['displayorder'])) {
                foreach ($_GP['displayorder'] as $id => $displayorder) {
                    mysqld_update('shop_category', array('displayorder' => $displayorder), array('id' => $id));
                }
                message('分类排序更新成功！', web_url('category', array('op' => 'display')), 'success');
            }

            if(!empty($_GP['id'])){
                $all_category     = mysqld_selectall("SELECT * FROM " . table('shop_category') . "  where parentid ={$_GP['id']} and  deleted=0  ORDER BY parentid ASC, displayorder ASC");
                if(empty($all_category)){
                    die(showAjaxMess('1002','无数据！'));
                }else{
                    die(showAjaxMess('200',$all_category));
                }
            }
            $first_instu      = mysqld_selectall("select * from ".table('industry')." where gc_pid=0");
            $second_instu     = array();
            if(!empty($_GP['industry_p1_id'])){
                $second_instu = mysqld_selectall('select * from '.table('industry')." where gc_pid={$_GP['industry_p1_id']}");
            }
            if(!empty($_GP['industry_p2_id'])){
                $where = " industry_p2_id={$_GP['industry_p2_id']} and parentid =0 and  deleted=0 ";
            }else{
                $where = " parentid =0 and  deleted=0 ";
            }
            $all_category     = mysqld_selectall("SELECT * FROM " . table('shop_category') . "  where {$where} ORDER BY parentid ASC, displayorder ASC");
            include page('category_list');
        } elseif ($operation == 'post') {
            //*************2017-5-3增加行业归属id，因此输出行业列表************//
            $Service = new \service\shop\IndustryService();
            $industry_data =   $Service->getAllDataStruct();
            
            $parentid = intval($_GP['parentid']);
            $id = intval($_GP['id']);
			$brands = array();
            if (!empty($id)) {
                $category = mysqld_select("SELECT * FROM " . table('shop_category') . " WHERE id = '$id'");
                $category['industry_p1_name'] =$industry_data[$category['industry_p1_id']]['gc_name'] ;
                $category['industry_p2_name'] =$industry_data[$category['industry_p1_id']]['sub']['gc_name'] ;
				$brands = unserialize($category['brands']);
            } else {
                $category = array(
                    'displayorder' => 0,
                );
            }
			
            if (!empty($parentid)) {
                $parent = mysqld_select("SELECT id, name,industry_p1_id,industry_p2_id FROM " . table('shop_category') . " WHERE id = '$parentid'");
                $category['industry_p1_name'] =$industry_data[$parent['industry_p1_id']]['gc_name'] ;
                $category['industry_p2_name'] =$industry_data[$parent['industry_p1_id']]['sub'][$parent['industry_p2_id']]['gc_name'] ;
                $category['industry_p1_id'] =$parent['industry_p1_id'] ;
                $category['industry_p2_id'] =$parent['industry_p2_id'] ;
//                ppd($industry_data);
                if (empty($parent)) {
                    message('抱歉，上级分类不存在或是已经被删除！', web_url('post'), 'error');
                }
            }else{
               // 获取推荐品牌
			   $best_brand = mysqld_selectall("SELECT * FROM " . table('shop_brand') . " WHERE recommend = 1 ");
			   $best_id = array();
			   $best_b = array();
			   if ( is_array($best_brand) ){
				   foreach ( $best_brand as $brand_id ){
						$best_id[] = $brand_id['id'];
						$best_b[$brand_id['id']] = $brand_id;
				   }
			   }
			   if ( is_array($brands) ){
                   $best_id = array_diff($best_id, $brands);
			   }
			}
            
            if (checksubmit('submit')) {
                if (empty($_GP['catename'])) {
                    message('抱歉，请输入分类名称！');
                }
                if (empty($_GP['industry_p2_id'])) {
                    message('抱歉，请选择二级行业类别！');
                }
                $data = array(
                    'name' => $_GP['catename'],
                    'enabled' => intval($_GP['enabled']),
                    'industry_p2_id' => intval($_GP['industry_p2_id']),
                    'industry_p1_id' => intval($_GP['industry_p1_id']),
					'brands' => serialize($_GP['sql_query']),
                    'displayorder' => intval($_GP['displayorder']),
                    'isrecommand' => intval($_GP['isrecommand']),
                    'app_isrecommand' => intval($_GP['app_isrecommand']),
                    'description' => $_GP['description'],
                    'parentid' => intval($parentid),
                );
                if (!empty($_GP['thumb_del'])) {
                	$data['thumb'] = '';
                }
				if (!empty($_GP['adv_del'])) {
                	$data['adv'] = '';
                }
				if (!empty($_GP['adv_wap_del'])) {
                	$data['adv_wap'] = '';
                }
                if (!empty($_FILES['thumb']['tmp_name'])) {
                    file_delete($_GP['thumb_old']);
                    $upload = file_upload($_FILES['thumb']);
                    if (is_error($upload)) {
                        message($upload['message'], '', 'error');
                    }
                    $data['thumb'] = $upload['path'];
                }
                if (!empty($_FILES['app_ico']['tmp_name'])) {
                    file_delete($_GP['app_ico_old']);
                    $upload = file_upload($_FILES['app_ico']);
                    if (is_error($upload)) {
                        message($upload['message'], '', 'error');
                    }
                    $data['app_ico'] = $upload['path'];
                }

                if (!empty($_FILES['adv']['tmp_name'])) {
                    file_delete($_GP['adv_old']);
                    $upload = file_upload($_FILES['adv']);
                    if (is_error($upload)) {
                        message($upload['message'], '', 'error');
                    }
                    $data['adv'] = $upload['path'];
                }
				 if (!empty($_FILES['adv_wap']['tmp_name'])) {
                    file_delete($_GP['adv_wap_old']);
                    $upload = file_upload($_FILES['adv_wap']);
                    if (is_error($upload)) {
                        message($upload['message'], '', 'error');
                    }
                    $data['adv_wap'] = $upload['path'];
                }
                if (!empty($id)) {
                    unset($data['parentid']);
                    mysqld_update('shop_category', $data, array('id' => $id));
                } else {
                    mysqld_insert('shop_category', $data);
                    $id = mysqld_insertid();
                }
                message('更新分类成功！', web_url('category', array('op' => 'display')), 'success');
            }
            include page('category');
        } elseif ($operation == 'delete') {
            $id = intval($_GP['id']);
            $category = mysqld_select("SELECT id, parentid FROM " . table('shop_category') . " WHERE id = '$id' and deleted=0 ");
            if (empty($category)) {
                message('抱歉，分类不存在或是已经被删除！', web_url('category', array('op' => 'display')), 'error');
            }
            mysqld_update('shop_category', array('deleted' => 1), array('id' => $id, 'parentid' => $id), 'OR');
            message('分类删除成功！', web_url('category', array('op' => 'display')), 'success');
        }else if($operation == 'getNextInstry'){
            if(empty($_GP['industry_p1_id'])){
                ajaxReturnData(0,'参数有误！');
            }
            $result = mysqld_selectall("select gc_id,gc_name from ".table('industry')." where gc_pid={$_GP['industry_p1_id']}");
            ajaxReturnData(1,'',$result);
        }
		elseif ($operation == 'csv_post') {
		    if( !empty($_FILES['csv']["name"]) ){
				if ( $_FILES["csv"]["size"] < 10240000 ) {
					  $csvreader = new CsvReader($_FILES["csv"]["tmp_name"]);
                      $line_number = $csvreader->get_lines();
	                  $arrobj = new arrayiconv();
                      $rows = ceil($line_number / 20);
					  $num = 0;
					  for ( $i = 0; $i < $rows; $i++ ){
                           $arr = $csvreader->get_data(20,$i*20+1);
					       $arr = $arrobj->Conversion($arr,"GBK","utf-8");
						   if ($i == 0){
					            array_shift($arr);
						   }
						   c_category($arr);
					  }
				}else{
					 message('文件过大,请控制在1MB', '', 'error');
				}
		    }
            include page('csv_category');
        }
function c_category($array=array()){
   if ( !empty($array) ){
       foreach ( $array as $key=>$value ){
            // 判断类目名称是否存在 [0] => 101 [1] => 电脑办公 [2] => 0 [3] => 0 [4] => 0 
            $category_name = trim($value[1]);
			$category_id    = trim($value[0]);
			$order          = trim($value[3]);
			if ( empty($category_name) or empty($category_id) ){
                 continue;
			}
			$check = mysqld_select("SELECT * FROM " . table('shop_category') . " WHERE name = '$category_name' or id = '$category_id'");
			if ( !$check ){
                 $data = array(
					'id'    => $category_id,
                    'name' => $category_name,
                    'enabled' => 1,
                    'displayorder' => $order,
                    'isrecommand' => '',
                    'description' => $category_name,
                    'parentid' => trim($value[2])
                );
				mysqld_insert('shop_category', $data);
			}
			//如果不存在则插入数据
	   }
   }
}