<?php
			$operation = !empty($_GP['op']) ? $_GP['op'] : 'display';
			if($operation=='delete')
			{
					mysqld_delete('shop_menu',array("id"=>intval($_GP['id'])));
						message("删除成功！","refresh","success");
			}
			if($operation=='post')
			{
					$shop_menu = mysqld_select("SELECT * FROM " . table('shop_menu')." where id='".intval($_GP['id'])."' and menu_type='fansindex'" );
  	   if (checksubmit('submit')) {
					  if(	empty($_GP['id']))
					   {
				$data=array('tname'=>
				$_GP['tname'],'url'=>
				$_GP['url'],'icon'=>
				$_GP['icon'],'menu_type'=>
				'fansindex','torder'=>
				intval($_GP['torder']),'type'=>$_GP['type']);
                 
				  if (!empty($_GP['img_del'])) {
                	$data['img'] = '';
                }
		   if (!empty($_FILES['img']['tmp_name'])) {
                    file_delete($_GP['img_old']);
                    $upload = file_upload($_FILES['img']);
                    if (is_error($upload)) {
                        message($upload['message'], '', 'error');
                    }
                    $data['img'] = $upload['path'];
                }




				mysqld_insert('shop_menu',$data);
				message("添加成功",create_url('site', array('name' => 'shopwap','do' => 'shop_menu','op'=>'display')),"success");
					}else
					{
							$data=array('tname'=>
				$_GP['tname'],'url'=>
				$_GP['url'],'icon'=>
				$_GP['icon'],'menu_type'=>
				'fansindex','torder'=>
				intval($_GP['torder']),'type'=>$_GP['type']);

                  if (!empty($_GP['img_del'])) {
                	$data['img'] = '';
                }
		   if (!empty($_FILES['img']['tmp_name'])) {
                    file_delete($_GP['img_old']);
                    $upload = file_upload($_FILES['img']);
                    if (is_error($upload)) {
                        message($upload['message'], '', 'error');
                    }
                    $data['img'] = $upload['path'];
                }

				mysqld_update('shop_menu',$data,array('id'=>
				$_GP['id']));
				
				message("修改成功","refresh","success");
					}
				}
				   
				    	include page('shop_menu');
				    	exit;
			}
		$shop_menu_list = mysqld_selectall("SELECT * FROM " . table('shop_menu')." where menu_type='fansindex' order by torder desc" );
       			
        	include page('shop_menu_list');