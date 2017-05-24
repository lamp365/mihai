<?php
			$operation = !empty($_GP['op']) ? $_GP['op'] : 'display';
			if($operation=='delete')
			{
					mysqld_delete('shop_diymenu',array("id"=>intval($_GP['id'])));
						message("删除成功！","refresh","success");
			}
			if($operation=='post')
			{
					$fansindex_menu = mysqld_select("SELECT * FROM " . table('shop_diymenu')." where id='".intval($_GP['id'])."' and menu_type='fansindex'" );
  	   if (checksubmit('submit')) {
  	   			if (!empty($_GP['wap']) AND $_GP['wap']!='false') {
  	   				$wap = 1;
  	   			}else{
  	   				$wap = 0;
  	   			}
  	   			if (!empty($_GP['web']) AND $_GP['web']!='false') {
  	   				$web = 1;
  	   			}else{
  	   				$web = 0;
  	   			}
  	   			if (!empty($_GP['app']) AND $_GP['app']!='false') {
  	   				$app = 1;
  	   			}else{
  	   				$app = 0;
  	   			}
  	   			if ($wap == 0 AND $web == 0 AND $app == 0) {
  	   				message("至少要勾选一个平台！","refresh","error");
  	   			}
  	   			if (!empty($_FILES['icon']['tmp_name'])) {
		            $upload = file_upload($_FILES['icon']);
		            // dump($_FILES['icon']);
		            if (is_error($upload)) {
		                message($upload['message'], '', 'error');
		            }
		            // dump($upload);
		            $icon = $upload['path'];
		        }
					  if(	empty($_GP['id']))
					   {
				$data=array('tname'=>
				$_GP['tname'],'url'=>
				$_GP['url'],'menu_type'=>
				'fansindex','torder'=>
				intval($_GP['torder']),'web_use'=>$web,'wap_use'=>$wap,'app_use'=>$app,'remark'=>$_GP['remark']);
				if (!empty($icon)) {
					$data['icon'] = $icon;
				}
				mysqld_insert('shop_diymenu',$data);
				message("添加成功",create_url('site', array('name' => 'shopwap','do' => 'fansindex_menu','op'=>'display')),"success");
					}else
					{
							$data=array('tname'=>
				$_GP['tname'],'url'=>
				$_GP['url'],'menu_type'=>
				'fansindex','torder'=>
				intval($_GP['torder']),'web_use'=>$web,'wap_use'=>$wap,'app_use'=>$app,'remark'=>$_GP['remark']);
				if (!empty($icon)) {
					$data['icon'] = $icon;
				}
				mysqld_update('shop_diymenu',$data,array('id'=>
				$_GP['id']));
				
				message("修改成功","refresh","success");
					}
				}
				   
				    	include page('fansindex_menu');
				    	exit;
			}
		$fansindex_menu_list = mysqld_selectall("SELECT * FROM " . table('shop_diymenu')." where menu_type='fansindex' order by torder desc" );
       			
        	include page('fansindex_menu_list');