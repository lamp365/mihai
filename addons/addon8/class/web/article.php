<?php
			$stats_arr  = array(1=>'底部显示',2=>'使用条款',3=>'隐私声明',4=>'自定义','5'=>'wap关于我们',6=>'健康文化');
			$author_arr = array(
				'2016120616590' => '梦琪',
				'2016120616964' => '如萱',
				'2016120616275' => '沈丹珍'
			);
			//后台发布文章，也是一个用户，这里会优先让运营给出几个用户，用来专门后台发布文章
			$operation = !empty($_GP['op']) ? $_GP['op'] : 'display';
			if($operation=='delete')
			{
					mysqld_delete('addon8_article',array("id"=>intval($_GP['id'])));
					message("删除成功！","refresh","success");
			}
			if($operation=='post')
			{
				$category = mysqld_selectall("SELECT * FROM " . table('addon8_article_category') . "  where deleted=0  ORDER BY parentid ASC, displayorder DESC",array(), 'id');
               if (!empty($category)) {
					$children = '';
					foreach ($category as $cid => $cate) {
						if (!empty($cate['parentid'])) {
							$children[$cate['parentid']][$cate['id']] = array($cate['id'], $cate['name']);
						}
					}
				}
        
				$article = mysqld_select("SELECT * FROM " . table('addon8_article')." where id='".intval($_GP['id'])."' " );
  	   			if (checksubmit('submit')) {
					  if(empty($_GP['pcate']))
						  message('请选择分类!');
					  if($_GP['mobileTheme']){
						  $openid_keys = array_keys($author_arr);
						  if(empty($_GP['openid']))
							  $_GP['openid'] = $openid_keys[0];

						  //因为该用户是线上固定的几个用户，避免开发环境没有，自动创建
						  createTheUserForNotExist($_GP['openid']);
					  }
					  if(	empty($article['id']))
					   {
					   	
							$data=array(
								'createtime'=>time(),
								'pcate'		=>intval($_GP['pcate']),
								'ccate'		=>intval($_GP['ccate']),
								'iscommend' =>intval($_GP['iscommend']),
								'ishot'		=>intval($_GP['ishot']),
								'mobileTheme'=>intval($_GP['mobileTheme']),
								'title'		=>$_GP['title'],
								'readcount' =>intval($_GP['readcount']),
								'description'=>$_GP['description'],
								'content'   => htmlspecialchars_decode($_GP['content'],ENT_NOQUOTES),
								'displayorder'=>intval($_GP['displayorder']),
								'openid'    => $_GP['openid']
							);
								if (!empty($_GP['state'])){
									$data['state'] = $_GP['state'];
								}else{
									$data['state'] = 0;
								}
								if (!empty($_GP['thumb_del'])) {
									$data['thumb'] = '';
								}
								if (!empty($_FILES['thumb']['tmp_name'])) {
									file_delete($_GP['thumb_old']);
									$upload = file_upload($_FILES['thumb']);
									if (is_error($upload)) {
										message($upload['message'], '', 'error');
									}
									$data['thumb'] = $upload['path'];
								}
            
				
							mysqld_insert('addon8_article',$data);
							message("添加成功",create_url('site', array('name' => 'addon8','do' => 'article','op'=>'post','id'=>mysqld_insertid())),"success");
					}else
					{
						$data=array(
							'createtime'=>time(),
							'pcate'		=>intval($_GP['pcate']),
							'ccate'		=>intval($_GP['ccate']),
							'iscommend' =>intval($_GP['iscommend']),
							'ishot'		=>intval($_GP['ishot']),
							'mobileTheme'=>intval($_GP['mobileTheme']),
							'title'		=>$_GP['title'],
							'readcount' =>intval($_GP['readcount']),
							'description'=>$_GP['description'],
							'content'	 =>htmlspecialchars_decode($_GP['content'],ENT_NOQUOTES),
							'displayorder'=>intval($_GP['displayorder']),
							'openid'     => $_GP['openid']
						);
						if (!empty($_GP['state'])){
							$data['state'] = $_GP['state'];
						}else{
							$data['state'] = 0;
						}
					    if (!empty($_GP['thumb_del'])) {
						   $data['thumb'] = '';
					    }
						if (!empty($_FILES['thumb']['tmp_name'])) {
							file_delete($_GP['thumb_old']);
							$upload = file_upload($_FILES['thumb']);
							if (is_error($upload)) {
								message($upload['message'], '', 'error');
							}
							$data['thumb'] = $upload['path'];
						}
              
                
						mysqld_update('addon8_article',$data,array('id'=>$_GP['id']));
				
						message("修改成功","refresh","success");
					}
				}
				   
				include addons_page('article');
				exit;
			}

 			if($operation == 'display'){
				$psize =  20;
				$pindex = max(1, intval($_GP["page"]));
				$limit = ' limit '.($pindex-1)*$psize.','.$psize;

				if(empty($_GP['pcate'])){
					$article_list = mysqld_selectall("SELECT * FROM " . table('addon8_article')." order by id desc, displayorder desc {$limit}" );
					$total        = mysqld_selectcolumn("SELECT count(id) FROM " . table('addon8_article'));
				}else{
					$article_list = mysqld_selectall("SELECT * FROM " . table('addon8_article')." where pcate={$_GP['pcate']}  order by  id desc , displayorder desc {$limit}" );
					$total        = mysqld_selectcolumn("SELECT count(id) FROM " . table('addon8_article')." where pcate={$_GP['pcate']}");
				}

				$pager  = pagination($total, $pindex, $psize);
				$category_pcate = mysqld_selectall("SELECT * FROM " . table('addon8_article_category') . "  where parentid=0 order by displayorder desc",array(), 'id');
				$category_ccate = mysqld_selectall("SELECT * FROM " . table('addon8_article_category') . "  where parentid!=0 ",array(), 'id');

				include addons_page('article_list');
			}

			if($operation == 'sethot'){
				mysqld_update('addon8_article',array('ishot'=>1),array('id'=>$_GP['id']));
				message('操作成功！','','success');
			}
			if($operation == 'canclehot'){
				mysqld_update('addon8_article',array('ishot'=>0),array('id'=>$_GP['id']));
				message('操作成功！','','success');
			}
			if($operation == 'setcommend'){
				mysqld_update('addon8_article',array('iscommend'=>1),array('id'=>$_GP['id']));
				message('操作成功！','','success');
			}
			if($operation == 'canclecommend'){
				mysqld_update('addon8_article',array('iscommend'=>0),array('id'=>$_GP['id']));
				message('操作成功！','','success');
			}