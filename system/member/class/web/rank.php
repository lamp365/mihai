<?php
	$operation = !empty($_GP['op']) ? $_GP['op'] : 'display';
    $goUrl  = web_url('rank');
	if($operation=='del')
	{
			$goUrl .= "#tab2primary";
			mysqld_delete('rank_model',array("rank_level"=>intval($_GP['rank_level'])));
			message("删除成功！",$goUrl,"success");
	}else if($operation=='detail') {
		//添加修改 等级
		$goUrl .= "#tab2primary";
		$rank   = array();
		if($_GP['rank_level']!='')
		{
			$condition=' rank_level='.intval($_GP['rank_level']);
			$rank = mysqld_select("SELECT * FROM " . table('rank_model')." where {$condition} " );
		}

		if (checksubmit('submit')) {
			if(empty($_GP['rank_name']))
			{
				message("等级名称不能空",refresh(),'error');
			}

			$data=array(
				'rank_name'  => $_GP['rank_name'],
				'experience' => intval($_GP['experience'])
			);
			if (!empty($_FILES['icon']['tmp_name'])) {
				$upload = file_upload($_FILES['icon']);
				if (is_error($upload)) {
					message($upload['message'], '', 'error');
				}
				$data['icon'] = $upload['path'];
			}
			if (!empty($_FILES['wap_icon']['tmp_name'])) {
				$upload = file_upload($_FILES['wap_icon']);
				if (is_error($upload)) {
					message($upload['message'], '', 'error');
				}
				$data['wap_icon'] = $upload['path'];
			}

			 if(empty($rank)) {
				   mysqld_insert('rank_model', $data);
			 }else {
				   mysqld_update('rank_model', $data,array('rank_level' => $rank['rank_level']));
			  }
			   message('操作成功！', $goUrl, 'success');
			}


		include page('rank');
	}else if($operation == 'display'){
		//默认列表页
		$setting = globaSetting();
		$list    = mysqld_selectall('SELECT * FROM '.table('rank_model')." order by sort asc");
		$priviel_list = mysqld_selectall("select * from ".table('rank_privile')." order by sort desc");
		include page('rank_list');

	}else if($operation == 'post_base'){
		//修改基础设置
		$data['continue_4day_jifen'] = $_GP['continue_4day_jifen'];
		$data['continue_7day_jifen'] = $_GP['continue_7day_jifen'];
		$data['every_day_jifen']     = $_GP['every_day_jifen'];
		refreshSetting($data);
		message('操作成功！', refresh(), 'success');
	}else if($operation == 'post_priviel'){
		//添加修改特权
		$goUrl .= "#tab3primary";
		$priviel = array();
		if(!empty($_GP['id'])){
			$priviel = mysqld_select("select * from ".table('rank_privile')." where id={$_GP['id']}");
		}

		if(!empty($_GP['insert_data'])){
			$data=array(
				'name'  => $_GP['priviel_name'],
			);
			if (!empty($_FILES['icon']['tmp_name'])) {
				$upload = file_upload($_FILES['icon']);
				if (is_error($upload)) {
					message($upload['message'], '', 'error');
				}
				$data['icon'] = $upload['path'];
			}

			if(empty($priviel)) {
				mysqld_insert('rank_privile', $data);
			}else {
				mysqld_update('rank_privile', $data,array('id' => $priviel['id']));
			}
			message("操作成功！",$goUrl,'success');
		}

		include page('priviel');
	}else if($operation == 'set_sort'){
		//排序
		if(empty($_GP['id'])){
			die(showAjaxMess(1002,'参数有误！'));
		}
		mysqld_update('rank_privile',array('sort'=>$_GP['sort']),array('id'=>$_GP['id']));
		die(showAjaxMess(200,'已设置排序成功！'));
	}else if($operation == 'rank_sort' ){
		//排序
		if(empty($_GP['id'])){
			die(showAjaxMess(1002,'参数有误！'));
		}
		mysqld_update('rank_model',array('sort'=>$_GP['sort']),array('rank_level'=>$_GP['id']));
		die(showAjaxMess(200,'已设置排序成功！'));

	}else if($operation == 'del_privile'){
		if(empty($_GP['id'])){
			die(showAjaxMess(1002,'参数有误！'));
		}
		mysqld_delete('rank_privile',array('id'=>$_GP['id']));
		die(showAjaxMess(200,'已成功删除！'));
	}else if($operation == 'set_priviel'){
		//分配对应的特权
		$goUrl .= "#tab3primary";
		if(empty($_GP['box_ids'])){
			mysqld_update("rank_model",array('privile'=>''));
		}else{
			foreach($_GP['box_ids'] as $key => $item){
				$priviel_str = implode(',',$item);
				mysqld_update("rank_model",array('privile'=>$priviel_str),array('rank_level'=>$key));
			}
		}
		message("操作成功！",$goUrl,'success');
	}
