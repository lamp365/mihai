<?php
          $operation = !empty($_GP['op']) ? $_GP['op'] : 'display';
		  if($operation=='delete'){
					mysqld_delete('addon7_point',array("id"=>intval($_GP['id'])));
				    message("删除成功！","refresh","success");
		   }
		   if($operation=='open'){
              $id = $_GP['id'];
			  // 获取标准数字
			  $object = mysqld_select("SELECT * FROM ".table("addon7_point")." WHERE vn=3 and id = ".$id);
			  if ( $object ) {
				   // 除数
                   $stext      =  intval($object['nums']);
				   $lock_time  = trim($object['lock_time']);
                   // 找出开奖信息
				   $points = mysqld_selectall("SELECT * FROM " .table('addon7_award')." WHERE state = 2 and lock_time = '".$lock_time."'");
				   // 开始处理开奖信息
				   $num = 0;
				   foreach ( $points as $value ){
						$date = array();
                        if ( $value['dicount'] > 0){
							 $result =  fmod($stext,$value['dicount']); //用point表中的 nums % amount   数据信息比去总的参与人数
							 $r_s = $result + 1;
							 // 中奖号码 p24000003
							 $open = "p".$value['id'].'00000'.$r_s;
							 $date = array(
                                 'stext'  => $stext,
								 'state'  => 3,  //已经开奖
								 'sn'     => $open
							 );
                             mysqld_update('addon7_award',$date,array('id'=>$value['id']));
							 // 根据中奖号码，对云购号码进行设置
//							 $ob = mysqld_select("SELECT id,(star_num + count -1), star_num FROM ".table('addon7_request'). " WHERE star_num <= ".$r_s." and (star_num + count -1) >= ".$r_s." and award_id = ".$value['id']);
							$ob  = mysqld_select("select id from ".table('addon7_request')." where award_id={$value['id']} and star_num_order={$r_s}");
							if ( $ob ){
								 mysqld_update('addon7_request',array("status"=>1),array('id'=>$ob['id']));
							 }
							 $num +=1;
						}
				   }
				   $or = array(
                      'states' => 1
					);
				   mysqld_update('addon7_point',$or,array('id'=>$id));
				   message("开奖完成,共开出".$num."项云购活动号码",create_url('site', array('name' => 'addon7','do' => 'point')),"success");
			  }else{
                   message("无法开奖",create_url('site', array('name' => 'addon7','do' => 'point')),"error");
			  }
		   }
		  if ($operation=='sign'){
               $c = mysqld_select("SELECT * FROM " . table('addon7_point')." where id= ".intval($_GP['id']));
			   if ( ($c['v1'] == $_CMS['account']['username']) || ($c['v2'] == $_CMS['account']['username']) || ($c['v3'] == $_CMS['account']['username']) ){
                     message("您不能重复签名",create_url('site', array('name' => 'addon7','do' => 'point')),"error");
			   }else{
				   if ( $c['vn'] < 3){
				    $key = 'v'.($c['vn']+1);
                    $data=array(
					    $key=>$_CMS['account']['username'],
						'vn' => $c['vn']+1
					);	
					mysqld_update('addon7_point',$data,array('id'=>$_GP['id']));
					message("签名成功","refresh","success");
				   }else{
                          message("不需要更多的签名了",create_url('site', array('name' => 'addon7','do' => 'point')),"error");
				   }
			   }
		  }
		  if ($operation=='update'){
			   $article = mysqld_select("SELECT * FROM " . table('addon7_point')." where id='".intval($_GP['id'])."' " );
			    if ( $article['vn'] == 1 ){
					   if ( $article['v1'] != $_CMS['account']['username']){
							message("只有".$article['v1']."可以修改数据,你无权修改数据",create_url('site', array('name' => 'addon7','do' => 'point')),"error");
					   }
				}
			    if ( $article['vn'] == 2 ){
					   if ( $article['v2'] != $_CMS['account']['username']){
							message("只有".$article['v2']."可以修改数据,你无权修改数据",create_url('site', array('name' => 'addon7','do' => 'point')),"error");
					   }
				}
				if ( $article['vn'] == 3 ){
					if ( $article['v3'] != $_CMS['account']['username'] ){
                     message("只有".$article['v3']."可以修改数据,您无权修改数据",create_url('site', array('name' => 'addon7','do' => 'point')),"error");
					}
				}
  	            if (checksubmit('submit')) {
					  if(	!empty($article['id'])) {  
						    $key = 'v'.($article['vn']+1);
						    if(!empty($_GP['nums']) && !is_numeric($_GP['nums'])){
								message("请输入一个数字",refresh(),'error');
							}

						    if(($key == 'v3' || $key == 'v4') && empty($_GP['nums'])){
								//最后一次如果还是空的话，不允许
								message("请输入数据信息",refresh(),'error');
							}
							$data=array(
		                       'nums'=>$_GP['nums'],
							   'states'=>0
							);	
						   if (  $article['vn'] == 0  ){
                                $data[$key] = $_CMS['account']['username'];
								$data['vn'] = $article['vn']+1;
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
							mysqld_update('addon7_point',$data,array('id'=>$_GP['id']));
							message("修改成功",create_url('site', array('name' => 'addon7','do' => 'point')),"success");
					}
				}
				include addons_page('point');
			}else{
		         $article_list = mysqld_selectall("SELECT * FROM " . table('addon7_point')." order by lock_time desc" );
        	     include addons_page('pointlist');
			}