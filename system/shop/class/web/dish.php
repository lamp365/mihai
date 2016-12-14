<?php
        //每次有显示的地方就进行跟新团购状态  并且这里展示宝贝的时候进行更新所有商品时间过期了，自动更改宝贝类型为一般商品
        update_all_shop_status();
 		$cfg = globaSetting();
        // 获取批发商列表
		$vip_list = mysqld_selectall("SELECT * FROM ".table('rolers')." WHERE type = 2 or (type = 3 and pid != 0)  ");
        $area = mysqld_selectall("SELECT * FROM " . table('dish_list') . " where deleted=0 ORDER BY parentid ASC, displayorder DESC", array(), 'id');
        if (!empty($area)) {
            $children = '';
            foreach ($area as $cid => $cate) {
                if (!empty($cate['parentid'])) {
                    $children[$cate['parentid']][$cate['id']] = array($cate['id'], $cate['name']);
                }
            }
        }
        $operation = !empty($_GP['op']) ? $_GP['op'] : 'display';
        $category = mysqld_selectall("SELECT * FROM " . table('shop_category') . " where deleted=0 ORDER BY parentid ASC, displayorder DESC", array(), 'id');
		if (! empty($category)) {
			$childrens = '';
			foreach ($category as $cid => $cate) {
				if (! empty($cate['parentid'])) {
					$childrens[$cate['parentid']][$cate['id']] = array(
						$cate['id'],
						$cate['name']
					);
				}
			}
		}
		if ($operation == 'ajax') {
			 switch ( $_GP['todo'] ){
				// 批量上架
                case '1':
					if ( !empty($_GP['id']) && is_array($_GP['id'] )){
                         foreach ( $_GP['id'] as $goodsid ){
							  $data = array('status'=>1);
                              mysqld_update('shop_dish', $data, array('id' => $goodsid));      
						 }
				    }
					break;
				case '2':
					if ( !empty($_GP['id']) && is_array($_GP['id'] )){
                         foreach ( $_GP['id'] as $goodsid ){
							  $data = array('status'=>0);
                              mysqld_update('shop_dish', $data, array('id' => $goodsid));      
						 }
				    }
					break;
				default :
					break;
			 }
             exit;
		}
		if ( $operation == 'ajax_title' ){
            if ( !empty($_GP['ajax_id']) ){
				$data = array(
					'title'=>$_GP['ajax_title']
				);
                mysqld_update('shop_dish',$data,array('id'=>$_GP['ajax_id']));
				die(showAjaxMess('200',$_GP['ajax_title']));
			}else{
                die(showAjaxMess('1002','修改失败'));
			}
		}
		if ( $operation == 'ajax_total' ){
            if ( !empty($_GP['ajax_id']) ){
				$data = array(
					'total'=>$_GP['ajax_stock']
				);
                mysqld_update('shop_dish',$data,array('id'=>$_GP['ajax_id']));
				die(showAjaxMess('200',$_GP['ajax_stock']));
			}else{
                die(showAjaxMess('1002','修改失败'));
			}
		}
		if ( $operation == 'ajax_get_vip' ){
            if ( !empty($_GP['ajax_id']) ){
				$data = mysqld_selectall("SELECT * FROM ".table('shop_dish_vip')." WHERE dish_id = ".$_GP['ajax_id']);
				$vip_data = array(
					'vip_list' =>$vip_list,
					'vip_data'=>$data
				);
				die(showAjaxMess('200',$vip_data));
			}else{
                die(showAjaxMess('1002','获取失败'));
			}
		}
		if ( $operation == 'ajax_set_vip' ){
            if ( !empty($_GP['ajax_id']) ){
				$ajax_vip_data = $_GP['ajax_vip_data'];
				$vip_data = array();
				if ( is_array($ajax_vip_data) ){
					foreach ( $ajax_vip_data as $key=>$v2_value ){
						 if ( ($key != -1) && !empty($key) && !empty($v2_value)){
							  $vip_data[] = array(
									'dish_id' => $_GP['ajax_id'],
										'v2' => $key,
								  'vip_price' => $v2_value
							  );
						 }
					}
					if ( !empty($vip_data) ){
						setExtendPrice($vip_data);
					}else{
						mysqld_delete('shop_dish_vip', array('dish_id'=>$_GP['ajax_id']));
					}
				}
				 die(showAjaxMess('200', '设置成功'));
			}else{
                die(showAjaxMess('1002','获取失败'));
			}
		}
		if ( $operation == 'query' ){
            $condition = '';  
			if (!empty($_GP['ccate2'])) {
                $cid = intval($_GP['ccate2']);
                $condition .= " AND ccate2 = '{$cid}'";
            } elseif (!empty($_GP['ccate'])) {
                $cid = intval($_GP['ccate']);
                $condition .= " AND ccate = '{$cid}'";
            } elseif (!empty($_GP['pcate'])) {
                $cid = intval($_GP['pcate']);
                $condition .= " AND pcate = '{$cid}'";
            }
			$list = mysqld_selectall("SELECT id,title FROM " . table('shop_goods') . " WHERE  deleted=0 $condition ORDER BY status DESC, displayorder DESC, id DESC  ");
			$option = '<option value="0">请选择产品</option>';
			foreach ( $list as $key => $value ){
                  $option .= "<option value='".$value['id']."'>".$value['title']."</option>";
			}
			echo $option;
	    }
        if ($operation == 'post') {
			$taxlist = mysqld_selectall("SELECT * FROM ".table('shop_tax'));
            $id = intval($_GP['id']);
            if (!empty($id)) {
				$item = mysqld_select("SELECT a.*,b.title as gname,b.id as gid,b.thumb as gthumb FROM " . table('shop_dish') . " AS a LEFT JOIN " . table('shop_goods') . " as b on a.gid = b.id WHERE a.id = :id", array(':id' => $id));
                if (empty($item)) {
                    message('抱歉，商品不存在或是已经删除！', '', 'error');
                }
				// 获取渠道商设置列表
				$dish_vip_list = mysqld_selectall("SELECT * FROM ".table('shop_dish_vip')." WHERE  dish_id = ".$id);
                 $allspecs = mysqld_selectall("select * from " . table('shop_dish_spec')." where dishid=:id order by displayorder asc",array(":id"=>$id));
                foreach ($allspecs as &$s) {
                    $s['items'] = mysqld_selectall("select * from " . table('shop_dish_spec_item') . " where specid=:specid order by displayorder asc", array(":specid" => $s['id']));
                }
                unset($s);

                $piclist = mysqld_selectall("SELECT * FROM " . table('shop_dish_piclist') . " where goodid=$id ORDER BY id ASC");
       	
                //处理规格项
                $html = "";
                $options = mysqld_selectall("select * from " . table('shop_dish_option') . " where dishid=:id order by id asc", array(':id' => $id));

                //排序好的specs
                $specs = array();
                //找出数据库存储的排列顺序
                if (count($options) > 0) {
                    $specitemids = explode("_", $options[0]['specs'] );
                    foreach($specitemids as $itemid){
                        foreach($allspecs as $ss){
                             $items=  $ss['items'];
                             foreach($items as $it){
                                 if($it['id']==$itemid){
                                     $specs[] = $ss;
                                     break;
                                 }
                             }
                        }
                    }
                    
                    $html = '<table  class="spectable" style="border:1px solid #ccc;"><thead><tr>';

                    $len = count($specs);
                    $newlen = 1; //多少种组合
                    $h = array(); //显示表格二维数组
                    $rowspans = array(); //每个列的rowspan


                    for ($i = 0; $i < $len; $i++) {
                        //表头
                        $html.="<th>" . $specs[$i]['title'] . "</th>";

                        //计算多种组合
                        $itemlen = count($specs[$i]['items']);
                        if ($itemlen <= 0) {
                            $itemlen = 1;
                        }
                        $newlen*=$itemlen;

                        //初始化 二维数组
                        $h = array();
                        for ($j = 0; $j < $newlen; $j++) {
                            $h[$i][$j] = array();
                        }
                        //计算rowspan
                        $l = count($specs[$i]['items']);
                        $rowspans[$i] = 1;
                        for ($j = $i + 1; $j < $len; $j++) {
                            $rowspans[$i]*= count($specs[$j]['items']);
                        }
                    }

                    $html .= '<th>库存：<input type="text" class="span1 option_stock_all"  VALUE=""/><span class="add-on">&nbsp;<a href="javascript:;" class="icon-hand-down" title="批量设置" onclick="setCol(\'option_stock\');"></a></span></th>';
					$html.= '<th>销售价格：<input type="text" class="span1 option_marketprice_all"  VALUE=""/><span class="add-on">&nbsp;<a href="javascript:;" class="icon-hand-down" title="批量设置" onclick="setCol(\'option_marketprice\');"></a></span></th>';
					$html.='<th>市场价格：<input type="text" class="span1 option_productprice_all"  VALUE=""/><span class="add-on">&nbsp;<a href="javascript:;" class="icon-hand-down" title="批量设置" onclick="setCol(\'option_productprice\');"></a></span></th>';
					$html.='<th>重量(克)：<input type="text" class="span1 option_weight_all"  VALUE=""/><span class="add-on">&nbsp;<a href="javascript:;" class="icon-hand-down" title="批量设置" onclick="setCol(\'option_weight\');"></a></span></th>';
					$html.='</tr>';
                    for($m=0;$m<$len;$m++){
                        $k = 0;$kid = 0;$n=0;
                             for($j=0;$j<$newlen;$j++){
                                   $rowspan = $rowspans[$m]; //9
                                   if( $j % $rowspan==0){
                                        $h[$m][$j]=array("html"=> "<td rowspan='".$rowspan."'>".$specs[$m]['items'][$kid]['title']."</td>","id"=>$specs[$m]['items'][$kid]['id']);
                                       // $k++; if($k>count($specs[$m]['items'])-1) { $k=0; }
                                   }
                                   else{
                                       $h[$m][$j]=array("html"=> "","id"=>$specs[$m]['items'][$kid]['id']);
                                   }
                                   $n++;
                                   if($n==$rowspan){
                                     $kid++; if($kid>count($specs[$m]['items'])-1) { $kid=0; }
                                      $n=0;
                                   }
                        }
                     }
         
                    $hh = "";
                    for ($i = 0; $i < $newlen; $i++) {
                        $hh.="<tr>";
                        $ids = array();
                        for ($j = 0; $j < $len; $j++) {
                            $hh.=$h[$j][$i]['html'];
                            $ids[] = $h[$j][$i]['id'];
                        }
                        $ids = implode("_", $ids);

                        $val = array("id" => "","title"=>"", "stock" => "", "costprice" => "", "productprice" => "", "marketprice" => "", "weight" => "");
                        if(!empty($options))
                        {
	                        foreach ($options as $o) {
	                            if ($ids === $o['specs']) {
	                                $val = array("id" => $o['id'],
	                                    "title"=>$o['title'],
	                                    "stock" => $o['stock'],
	                                    "productprice" => $o['productprice'],
	                                    "marketprice" => $o['marketprice'],
	                                    "weight" => $o['weight']);
	                                break;
	                            }
	                        }
												}
                        $hh .= '<td>';
                        $hh .= '<input name="option_stock_' . $ids . '[]"  type="text" class="span1 option_stock option_stock_' . $ids . '" value="' . $val['stock'] . '"/></td>';
                        $hh .= '<input name="option_id_' . $ids . '[]"  type="hidden" class="span1 option_id option_id_' . $ids . '" value="' . $val['id'] . '"/>';
                        $hh .= '<input name="option_ids[]"  type="hidden" class="span1 option_ids option_ids_' . $ids . '" value="' . $ids . '"/>';
                        $hh .= '<input name="option_title_' . $ids . '[]"  type="hidden" class="span1 option_title option_title_' . $ids . '" value="' . $val['title'] . '"/>';
                        $hh .= '</td>';
                        $hh .= '<td><input name="option_marketprice_' . $ids . '[]" type="text" class="span1 option_marketprice option_marketprice_' . $ids . '" value="' . $val['marketprice'] . '"/></td>';
                        $hh .= '<td><input name="option_productprice_' . $ids . '[]" type="text" class="span1 option_productprice option_productprice_' . $ids . '" " value="' . $val['productprice'] . '"/></td>';
                        $hh .= '<td><input name="option_weight_' . $ids . '[]" type="text" class="span1 option_weight option_weight_' . $ids . '" " value="' . $val['weight'] . '"/></td>';
                        $hh .="</tr>";
                    }
                    $html.=$hh;
                    $html.="</table>";
                }
            }
            if (empty($area)) {
                  message('抱歉，请您先添加商品分类！', web_url('area', array('op' => 'post')), 'error');
            }

			
            if (checksubmit('submit')) {
                if (empty($_GP['pcate'])) {
                    message('请选择商品分类！');
                }
                //非一般商品时   以下isset要有，对于权限操作后不可以见，所加的。
                elseif(isset($_GP['type']) && intval($_GP['type'])!=0 && (empty($_GP['timestart']) || empty($_GP['timeend'])))
                {
                    message('请设置促销时间！');
                }
                //团购商品时
                elseif(isset($_GP['type']) && intval($_GP['type'])==1 && isset($_GP['team_buy_count']) && empty($_GP['team_buy_count']))
                {
                    message('请设置成团人数！');
                }
                elseif (isset($_GP['type']) && intval($_GP['type'])==1 && isset($_GP['draw']) && $_GP['draw'] == 1 && isset($_GP['team_draw_num']) && empty($_GP['team_draw_num'])) {
                    message('请设置抽奖人数！');
                }

                // 获取模板产品库的数据
				$shop_goods = mysqld_select("SELECT * FROM ". table('shop_goods') . " WHERE id = ".intval($_GP['c_goods'])." limit 1 ");
                //不要加类型转换，PHP本就是弱类型不用做类型转换，加了类型转化，会破坏原始数据格式,有些时候影响业务
                $data = array();
                if(empty($id)){    //这一步是为兼顾，权限，因为有些管理员不能修改价钱
                    //新添加数据
                    $timeprice    = !empty($_GP['timeprice']) && ($_GP['timeprice'] > 0) ?$_GP['timeprice']:$shop_goods['marketprice'];
                    $marketprice  = !empty($_GP['marketprice']) && ($_GP['marketprice'] > 0)?$_GP['marketprice']:$shop_goods['marketprice'];
                    $productprice = !empty($_GP['productprice']) && ($_GP['productprice'] > 0)?$_GP['productprice']:$shop_goods['productprice'];
                }else{
                    $timeprice    = $_GP['timeprice'];
                    $marketprice  = $_GP['marketprice'] == 0 ? $shop_goods['marketprice'] : $_GP['marketprice'];
                    $productprice = $_GP['productprice'];
                }
                $data = array(
                    'pcate' => intval($_GP['pcate']),
                    'ccate' => intval($_GP['ccate']),
					'taxid' => intval($_GP['taxid']),
					'timeprice'=> $timeprice,
					'gid'  => intval($_GP['c_goods']),
                    'status' => $_GP['status'],
                    'displayorder' => intval($_GP['displayorder']),
                    'title' =>  !empty($_GP['dishname'])?$_GP['dishname']:$shop_goods['title'],
                    'description' => $_GP['description'],
                    'content' => htmlspecialchars_decode($_GP['content']),
                    'dishsn' => $_GP['dishsn'],
                    'productsn' => $_GP['productsn'],
                    'marketprice' => $marketprice,
                    'weight' => $_GP['weight'],
                    'productprice' => $productprice,
                    'commision' => $_GP['commision']/100,
                    'total' => intval($_GP['total']),
                    'totalcnf' => intval($_GP['totalcnf']),
                    'credit' => intval($_GP['credit']),
                    'createtime' => TIMESTAMP,
                      'isnew' => intval($_GP['isnew']),
                    'isfirst' => intval($_GP['isfirst']),
                    'ishot' => intval($_GP['ishot']),
                    'isjingping' => intval($_GP['isjingping']),
                     'issendfree' => intval($_GP['issendfree']),
                    'type' => $_GP['type'],								//促销类型
                    'ishot' => intval($_GP['ishot']),
                    'isdiscount' => intval($_GP['isdiscount']),
                    'isrecommand' => intval($_GP['isrecommand']),
                    'istime' => $_GP['istime'],
                    'timestart' => strtotime($_GP['timestart']),
                    'hasoption' => intval($_GP['hasoption']),
                    'timeend' => strtotime($_GP['timeend']),
                	'max_buy_quantity' => (int)$_GP['max_buy_quantity']			//单笔最大购买数量
                    );

                //删除因为加入权限不可见后，一些字段没有对应数据则删除
                foreach($data as $key => $val){
                    if($val === null)  unset($data[$key]);

                    if($key == 'commision'){
                        if($val == 0) unset($data[$key]);
                    }
                    if($key == 'timestart' || $key == 'timeend'){
                        if($val == '')   unset($data[$key]);
                    }
                }
                
                //团购商品时
                if(intval($_GP['type'])==1)
                {
                	$data['team_buy_count'] = (int)$_GP['team_buy_count'];
                    $data['draw'] = (int)$_GP['draw'];
                    if ($data['draw'] == 1) {
                        $data['draw_num'] = (int)$_GP['team_draw_num'];
                    }else{
                        $data['draw_num'] = 0;
                    }
                }
				$c_p = mysqld_select("SELECT * FROM ".table("shop_goods")." WHERE id = ".$_GP['c_goods']);
                $data['p1'] = $c_p['pcate'];
				$data['p2'] = $c_p['ccate'];
				$data['p3'] = $c_p['ccate2'];
                if (!empty($_FILES['thumb']['tmp_name'])) {
                    $upload = file_upload($_FILES['thumb']);
                    if (is_error($upload)) {
                        message($upload['message'], '', 'error');
                    }
                    $data['thumb'] = $upload['path'];
                }
                if (empty($id)) {
                    $data['sales']=0;
                    mysqld_insert('shop_dish', $data);
                    $id = mysqld_insertid();
					if ( empty($id) ){
                        message('宝贝已存在，请勿重复添加');
					}
                } else {
                    unset($data['createtime']);
                     unset($data['sales']);
                    mysqld_update('shop_dish', $data, array('id' => $id));
                }
				$vip_data = array();
				if ( is_array($_GP['v2']) && !empty($_GP['v2']) ){
					foreach ( $_GP['v2'] as $key=>$v2_value ){
						 if ( ($v2_value != -1) && !empty($v2_value) && !empty($_GP['vip_price'][$key])){
							  $vip_data[] = array(
									'dish_id' => $id,
										'v2' => $v2_value,
								  'vip_price' => $_GP['vip_price'][$key]
							  );
						 }
					}
				}
				if ( !empty($vip_data) ){
				    setExtendPrice($vip_data);
				}else{
                    mysqld_delete('shop_dish_vip', array('dish_id'=>$id));
				}
                $warring = ( $shop_goods['marketprice'] - $marketprice ) / $shop_goods['marketprice'];
				if ( $warring >= 0.2 ){
                      send_warring($id);
				}
                //扩展分类的操作
                operateCategoryExtend($_GP['pcates_kuozhan'],$_GP['ccates_kuozhan'],$_GP['ccate2_kuozhan'],$_GP['extendids_kuozhan'],$_GP['delete_extend_ids'],$id);
                    
                    $hsdata=array();
                  if (!empty($_GP['attachment-new'])) {
                    foreach ($_GP['attachment-new'] as $index => $row) {
                        if (empty($row)) {
                            continue;
                        }
                        $hsdata[$index] = array(
                            'attachment' => $_GP['attachment-new'][$index],
                        );
                    }
                    $cur_index = $index + 1;
                }
                if (!empty($_GP['attachment'])) {
                    foreach ($_GP['attachment'] as $index => $row) {
                        if (empty($row)) {
                            continue;
                        }
                        $hsdata[$cur_index + $index] = array(
                            'attachment' => $_GP['attachment'][$index]
                        );
                    }
                }
                 mysqld_delete('shop_dish_piclist', array('goodid' => $id));
                 foreach ($hsdata as $row) {
                $data = array(
		                 'goodid' => $id,
		                 'picurl' =>$row['attachment']
		           			 );
                    mysqld_insert('shop_dish_piclist', $data);
                }
                
                
                
                 //处理商品规格
                $files = $_FILES;
                $spec_ids = $_POST['spec_id'];
                $spec_titles = $_POST['spec_title'];

                $specids = array();
                $len = count($spec_ids);
                $specids = array();
                $spec_items = array();
                for ($k = 0; $k < $len; $k++) {
                    $spec_id = "";
                    $get_spec_id = $spec_ids[$k];
                    $a = array(
                        "dishid" => $id,
                        "displayorder" => $k,
                        "title" => $spec_titles[$get_spec_id]
                    );
                   $tspec = mysqld_select("SELECT id FROM " . table('shop_dish_spec') . " WHERE id = :id", array(':id' => $get_spec_id));
              
                    if (is_numeric($get_spec_id)&&!empty($get_spec_id)&&!empty($tspec['id'])) {

                        mysqld_update("shop_dish_spec", $a, array("id" => $get_spec_id));
                        $spec_id = $get_spec_id;
                    } else {
                        mysqld_insert("shop_dish_spec", $a);
                        $spec_id = mysqld_insertid();
                    }
                    //子项
                    $spec_item_ids = $_POST["spec_item_id_".$get_spec_id];
                    $spec_item_titles = $_POST["spec_item_title_".$get_spec_id];
                    $spec_item_shows = $_POST["spec_item_show_".$get_spec_id];
                    
                    $spec_item_oldthumbs = $_POST["spec_item_oldthumb_".$get_spec_id];
                    $itemlen = count($spec_item_ids);
                    $itemids = array();
                    
          
                    for ($n = 0; $n < $itemlen; $n++) {
                    

                        $item_id = "";
                        $get_item_id = $spec_item_ids[$n];
                        $d = array(
                            "specid" => $spec_id,
                            "displayorder" => $n,
                            "title" => $spec_item_titles[$n],
                            "show" => $spec_item_shows[$n]
                        );
                        $f = "spec_item_thumb_" . $get_item_id;
                        $old = $_GP["spec_item_oldthumb_".$get_item_id];
                	
                        if (!empty($files[$f]['tmp_name'])) {
                            $upload = file_upload($files[$f]);
                            if (is_error($upload)) {
                                message($upload['message'], '', 'error');
                            }
                            $d['thumb'] = $upload['path'];
                        } else if (!empty($old)) {
                            $d['thumb'] = $old;
                        }
  $tspecitems = mysqld_select("SELECT id FROM " . table('shop_dish_spec_item') . " WHERE id = :id", array(':id' => $get_item_id));
              
                   
                        if (is_numeric($get_item_id)&&!empty($get_item_id)&&!empty($tspecitems['id'])) {
                            mysqld_update("shop_dish_spec_item", $d, array("id" => $get_item_id));
                            $item_id = $get_item_id;
                        } else {   
                            mysqld_insert("shop_dish_spec_item", $d);
                            $item_id = mysqld_insertid();
                        }
                        $itemids[] = $item_id;

                        //临时记录，用于保存规格项
                        $d['get_id'] = $get_item_id;
                        $d['id']= $item_id;
                        $spec_items[] = $d;
                    }
                    //删除其他的
                    if(count($itemids)>0){
                         mysqld_query("delete from " . table('shop_dish_spec_item') . " where 1=1 and specid=$spec_id and id not in (" . implode(",", $itemids) . ")");    
                    }
                    else{
                         mysqld_query("delete from " . table('shop_dish_spec_item') . " where 1=1 and specid=$spec_id");    
                    }
                    
                    //更新规格项id
                    mysqld_update("shop_dish_spec", array("content" => serialize($itemids)), array("id" => $spec_id));

                    $specids[] = $spec_id;
                }

                //删除其他的
                if( count($specids)>0){
                	mysqld_query("delete from " . table('shop_dish_spec') . " where 1=1 and dishid=$id and id not in (" . implode(",", $specids) . ")");
                }
                else{
                    mysqld_query("delete from " . table('shop_dish_spec') . " where 1=1 and dishid=$id");
                }


                //保存规格
           
                $option_idss = $_POST['option_ids'];
                $option_productprices = $_POST['option_productprice'];
                $option_marketprices = $_POST['option_marketprice'];
                $option_costprices = $_POST['option_costprice'];
                $option_stocks = $_POST['option_stock'];
                $option_weights = $_POST['option_weight'];
                $len = count($option_idss);
                $optionids = array();
                for ($k = 0; $k < $len; $k++) {
                    $option_id = "";
                    $get_option_id = $_GP['option_id_' . $ids][0];
             
                    $ids = $option_idss[$k]; $idsarr = explode("_",$ids);
                    $newids = array();
                    foreach($idsarr as $key=>$ida){
                        foreach($spec_items as $it){
                            if($it['get_id']==$ida){
                                $newids[] = $it['id'];
                                break;
                            }
                        }
                    }
                    $newids = implode("_",$newids);
                     
                    $a = array(
                        "title" => $_GP['option_title_' . $ids][0],
                        "productprice" => $_GP['option_productprice_' . $ids][0],
                        "costprice" => $_GP['option_costprice_' . $ids][0],
                        "marketprice" => $_GP['option_marketprice_' . $ids][0],
                        "stock" => $_GP['option_stock_' . $ids][0],
                        "weight" => $_GP['option_weight_' . $ids][0],
                        "dishid" => $id,
                        "specs" => $newids
                    );
                   
                    $totalstocks+=$a['stock'];

                    if (empty($get_option_id)) {
                        mysqld_insert("shop_dish_option", $a);
                        $option_id = mysqld_insertid();
                    } else {
                        mysqld_update("shop_dish_option", $a, array('id' => $get_option_id));
                        $option_id = $get_option_id;
                    }
                    $optionids[] = $option_id;
                }
                if (count($optionids) > 0) {
                    mysqld_query("delete from " . table('shop_dish_option') . " where dishid=$id and id not in ( " . implode(',', $optionids) . ")");
                }
                else{
                    mysqld_query("delete from " . table('shop_dish_option') . " where dishid=$id");
                }
                

                //总库存
                if ($totalstocks > 0) {
                    mysqld_update("shop_dish", array("total" => $totalstocks), array("id" => $id));
                }
                message('商品操作成功！',  'refresh', 'success');
            }
            // $mess = mysqld_selectall("SELECT id,title FROM " . table('shop_mess')." WHERE status = 1");
            //获得该商品的扩展分类
            $extend_category = mysqld_selectall("select id,p1,p2,p3,dishid from ". table('shop_category_extend') . " where dishid={$id}");
            if(empty($extend_category)){
                $extend_category[] = array('id'=>'','p1'=>'','p2'=>'','p3'=>'','dishid'=>'');
            }
            include page('dish');
        } elseif ($operation == 'comment') {
            //订单评论时用的是goods表中的id   详情页面的$_gp['id']是dish表中的id
			$pindex = max(1, intval($_GP['page']));
            $psize = 20;
            $total = 0;
            $where = '';
            if(!empty($_GP['system'])){
                $where  = ' where system='.$_GP['system'];
            }

            if(!empty($_GP['timestart']) && !empty($_GP['timeend'])){
                    $timestart = strtotime($_GP['timestart']);
                    $timeend   = strtotime($_GP['timeend']);
                    $where = "where comment.createtime >= {$timestart} and comment.createtime <= {$timeend}";
            }
            $keyword = $_GP['keyword'];
            if(!empty($keyword)){
                if(!empty($where)){
                    $where .= " and";
                }else{
                    $where = " where";
                }
                if(is_numeric($keyword)){
                    //说明要查找产品id
                    $where .= " shop_dish.id=$keyword}";
                }else{
                    //说明模糊查询标题
                    $where .= " shop_dish.title like '%{$keyword}%'";
                }
            }
//            ppd("SELECT comment.*,shop_dish.title,shop_dish.id as did FROM " . table('shop_goods_comment') . "   comment  left join " . table('shop_dish') . " shop_dish on shop_dish.gid=comment.goodsid {$where} ORDER BY comment.istop desc,comment.createtime DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
            $list = mysqld_selectall("SELECT comment.*,shop_dish.title,shop_dish.id as did FROM " . table('shop_goods_comment') . "   comment  left join " . table('shop_dish') . " shop_dish on shop_dish.gid=comment.goodsid {$where} ORDER BY comment.istop desc,comment.createtime DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
            $pager = '';
            if(!empty($list)){
                //获取评论对应的图片
                foreach($list as $key=> $row){
                     $list[$key]['piclist'] = mysqld_selectall("select img from ". table('shop_comment_img') ." where comment_id={$row['id']}");
                }
                // 获取评论数量
                $total = mysqld_selectcolumn('SELECT COUNT(*) FROM ' . table('shop_goods_comment')." as comment left join  " . table('shop_dish') . " shop_dish on shop_dish.gid=comment.goodsid {$where}" );
                $pager = pagination($total, $pindex, $psize);
            }
            include page('dish_comment');
		}elseif ($operation == 'list') {
			// 批量操作
			$list_op = array(
                  '请选择'=>-1,
				  '上架'=>1,
				  '下架'=>2
			);
			// 找出所有需要更新数据的信息
            $dish_error = mysqld_selectall("SELECT * FROM ".table('shop_dish'). " WHERE title = '' or marketprice <= 0 or timeprice <= 0 or productprice <= 0");
            if ( $dish_error ) {
            // 执行更新操作
                foreach ( $dish_error as $dish_date ){
					$shop_goods = mysqld_select("SELECT * FROM ". table('shop_goods') . " WHERE id = ".intval($dish_date['gid'])." limit 1 ");
					if ($shop_goods){
							$data = array(
							   'timeprice'=>!empty($dish_date['timeprice']) && ($dish_date['timeprice'] > 0) ?$dish_date['timeprice']:$shop_goods['marketprice'],
							   'title' =>  !empty($_GP['dishname'])?$_GP['dishname']:$shop_goods['title'],
							   'marketprice' => !empty($dish_date['marketprice']) && ($dish_date['marketprice'] > 0)?$dish_date['marketprice']:$shop_goods['marketprice'],
							   'productprice' => !empty($dish_date['productprice']) && ($dish_date['productprice'] > 0)?$dish_date['productprice']:$shop_goods['productprice'],
							);
							mysqld_update('shop_dish', $data, array('id' => $dish_date['id']));   
					}
				}
			}
            $pindex = max(1, intval($_GP['page']));
            $psize = 10;
            $condition = ' a.deleted=0 ';
			$sorturl = create_url('site', array('name' => 'shop','do' => 'dish','op'=>'list'));
            if (!empty($_GP['keyword'])) {
				$key_type = $_GP['key_type'];
				if ( $key_type == 'title' ){
                    $condition .= " AND a.title LIKE '%{$_GP['keyword']}%'";
				}else{
                    $condition .= " AND a.id = {$_GP['keyword']}";
				}
				$sorturl .= '&keyword='.$_GP['keyword'];
            }

            if (!empty($_GP['cate_2'])) {
                $cid = intval($_GP['cate_2']);
                $condition .= " AND a.ccate = '{$cid}'";
				$sorturl .= '&cate_2='.$_GP['cate_2'];
            } elseif (!empty($_GP['cate_1'])) {
                $cid = intval($_GP['cate_1']);
                $condition .= " AND a.pcate = '{$cid}'";
				$sorturl .= '&cate_1='.$_GP['cate_1'];
            }
            if ( isset($_GP['status'])) {
                $status = $_GP['status'];
                $condition .= " AND a.status = '{$status}'";
				$sorturl .= '&status='.$_GP['status'];
            }else{
                $_GP['status'] = 1;
            }
		    if (!empty($_GP['p2'])) {
                $cid = intval($_GP['p2']);
                $condition .= " AND a.p2 = '{$cid}'";
				$sorturl .= '&p2='.$_GP['p2'];
            } elseif (!empty($_GP['p1'])) {
                $cid = intval($_GP['p1']);
                $condition .= " AND a.p1 = '{$cid}'";
				$sorturl .= '&p1='.$_GP['p1'];
            }
			if ( $_GP['type'] != -1 && isset($_GP['type']) ){
                $type = intval($_GP['type']);
				$condition .= " AND a.type = '{$type}'";
				$sorturl .= '&type='.$_GP['type'];
			}

		   $orderby = '';
		   $oprice = $otprice = $otot = 'asc';
		   if ( isset($_GP['orderprice']) ){
                if ( $_GP['orderprice'] == 'asc' ){
                    $oprice = 'desc';
				}else{
                    $oprice = 'asc';
				}
				$orderby = "marketprice ".$_GP['orderprice'].' , ';
		   }
		    if ( isset($_GP['ordertprice']) ){
                if ( $_GP['ordertprice'] == 'asc' ){
                    $otprice = 'desc';
				}else{
                    $otprice = 'asc';
				}
				$orderby = "timeprice ".$_GP['ordertprice'].' , ';
		   }
		    if ( isset($_GP['ordertot']) ){
                if ( $_GP['ordertot'] == 'asc' ){
                    $otot = 'desc';
				}else{
                    $otot = 'asc';
				}
				$orderby = "total ".$_GP['ordertot'].' , ';
		   }
           if (!empty($_GP['report'])) {
					$list = get_goods(array(
						"table"=>"shop_dish",
						"where"=>$condition,
						"order"=> $orderby."gid, status DESC, displayorder DESC, id DESC"
					));
					foreach ( $list as $id => $item) {
						$identity = mysqld_select("SELECT * FROM " . table('member_identity') . " WHERE identity_id=:identity_id", array(':identity_id'=>$item['identity_id']));
						$list[$id]['identity']	= $identity['identity_number'];
					}
					$report='dishreport';
					require_once 'report.php';
					exit;
           	}
            $list = get_goods(array(
                "table"=>"shop_dish",
				"where"=>$condition,
				"limit" => ($pindex - 1) * $psize . ',' . $psize,
				"order"=> $orderby."gid, status DESC, displayorder DESC, id DESC"
			));
            // $list = mysqld_selectall("SELECT a.* FROM " . table('shop_dish') . " as a WHERE  a.deleted=0 $condition ORDER BY a.status DESC, a.displayorder DESC, a.id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
			//echo "SELECT * FROM " . table('shop_dish') . " WHERE  deleted=0 $condition ORDER BY status DESC, displayorder DESC, id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
			foreach($list as $key=>$val){
                    $count = mysqld_selectcolumn('SELECT COUNT(*) FROM ' . table('shop_dish_comment') . " WHERE dishid=".$val['id']);
					$list[$key]['count'] = $count;
					if ( empty($val['thumb'])){
					    $lists = mysqld_select("SELECT thumb FROM " . table('shop_goods') . " WHERE  deleted=0 and id = ".$val['gid']);
                        $list[$key]['imgs'] = $lists['thumb'];
				   }else{
                        $list[$key]['imgs'] = $val['thumb'];
				   }
				   $purchase = mysqld_selectall("SELECT a.vip_price,b.name FROM ".table('shop_dish_vip')." as a LEFT JOIN ".table('rolers')." as b on a.v2 = b.id WHERE dish_id = ".$val['id']);
				   if ( $purchase ){
                       $list[$key]['purchase'] = '批发';
					   $list[$key]['purchase_price'] = $purchase;
				   }
				   switch ( $val['type'] ){
                       case 1:
						   $list[$key]['typename'] = '团购商品';
						   break;
					   case 2:
						    $list[$key]['typename'] = '秒杀商品';
						   break;
					   case 3:
						    $list[$key]['typename'] = '今日特价商品';
						   break;
					   case 4:
						    $list[$key]['typename'] = '限时促销';
						   break;
					   default:
						    $list[$key]['typename'] = '一般商品';
						   break;
				   }
			 }
             $total = mysqld_selectcolumn('SELECT COUNT(*) FROM ' . table('shop_dish') . " as a WHERE $condition");
             $pager = pagination($total, $pindex, $psize);
             include page('dish_list');
        } elseif ($operation == 'delete') {
            $id = intval($_GP['id']);
            $row = mysqld_select("SELECT id, thumb FROM " . table('shop_dish') . " WHERE id = :id", array(':id' => $id));
            if (empty($row)) {
                message('抱歉，商品不存在或是已经被删除！');
            }
            mysqld_delete("shop_dish", array('id' => $id));
            if(mysqld_insertid()){
                //删除分销商关联的商品,不然分销商那边查询会出错
                $info = mysqld_select("select id from ". table('openshop_relation') . " where goodid={$id}");
                if(!empty($info)){
                    mysqld_delete('openshop_relation',array('goodid'=>$id));
                }
            }
            message('删除成功！', 'refresh', 'success');
        }elseif ($operation == 'home') {
             $pindex = max(1, intval($_GP['page']));
             $psize = 20;
             $condition = '';
             $id = intval($_GP['id']);
			 //member.realname,member.mobile, left join " . table('member') . " member on comment.openid=member.openid 
             $list = mysqld_selectall("SELECT comment.*,shop_goods.title messname, member.realname,member.mobile FROM " . table('user_advise') . " comment left join " . table('member') . " member on comment.openid=member.openid left join " . table('shop_mess') . " shop_goods on shop_goods.id=comment.mess_id  where comment.deleted=0 or comment.deleted is null ORDER BY comment.createtime DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
             $total = mysqld_selectcolumn('SELECT COUNT(*) FROM ' . table('user_advise') );
             $pager = pagination($total, $pindex, $psize);
             include page('dish_advise');
		}
		elseif ($operation == 'delete1') {
		    $id = intval($_GP['id']);
		    $row = mysqld_select("SELECT id, thumb FROM " . table('user_advise') . " WHERE deleted=0 and id = :id", array(':id' => $id));
		    if (empty($row)) {
		        message('抱歉，商品不存在或是已经被删除！');
		    }
		    //修改成不直接删除，而设置deleted=1
		    mysqld_update("user_advise", array("deleted" => 1), array('id' => $id));
		
		    message('删除成功！', 'refresh', 'success');
		}elseif ($operation == 'delcomment') {  //删除评论
		    $id = intval($_GP['id']);
		    mysqld_delete("shop_goods_comment", array('id' => $id));
            mysqld_delete('shop_comment_piclist',array('comment_id'=>$id));
		    message('删除成功！', 'refresh', 'success');

		}else if($operation == 'addcomment'){   //添加评论
            if(!empty($_GP['type']) && $_GP['type'] == 'new'){
                $dishid = $dish = $pager = $List = '';
                include page('dish_addcomment');

            }else{
                $pindex = max(1, intval($_GP['page']));
                $psize  = 20;
                $total  = 0;

                $dishid = $_GP['dishid'];
                $dish = mysqld_select("select * from ". table('shop_dish'). " where id={$dishid}");
                if(empty($dish)){
                    message('查无此宝贝商品',refresh(),'error');
                }

                //提交的表单
                if(!empty($_GP['add_sub']) && $_GP['add_sub'] == 'sub'){
                    if(empty($_GP['username']))
                        message('用户名不能为空！',refresh(),'error');
                    if(empty($_GP['comment']))
                        message('评论不能为空！',refresh(),'error');

                    $face  = '';
                    $ispic = 0;
                    if($_FILES['face']['error'] != 4){   //等于4没有内容
                        $upload = file_upload($_FILES['face']);
                        if (is_error($upload)) {
                            message($upload['message'], '', 'error');
                        }
                        $face  = $upload['path'];
                        $ispic = 1;
                    }


                    $data = array(
                        'createtime' => time(),
                        'username'   => $_GP['username'],
                        'comment'    => $_GP['comment'],
                        'rate'       => $_GP['rate'],
                        'goodsid'    => $dish['gid'],
                        'face'       => $face,
                        'ispic'      => $ispic
                    );
                    if($_GP['system'] == 0){
                        $rand = mt_rand(1,1000);   //随机取得系统设备3是ios 2安卓 1pc
                        $num = $rand%4;
                        if($num == 0)
                            $num = 1;
                    }else{
                        $num = $_GP['system'];
                    }
                    $data['system'] = $num;
                    mysqld_insert('shop_goods_comment',$data);
                    $lastid = mysqld_insertid();
                    $url    = web_url('dish',array('op'=>'addcomment','dishid'=>$dishid));
                    if($lastid){
                        if(!empty($_GP['picurl'])){
                            foreach($_GP['picurl'] as $picurl){
                                mysqld_insert('shop_comment_img',array('img'=>$picurl,'comment_id'=>$lastid));
                            }
                        }
                        message('操作成功！',$url,'success');
                    }else{
                        message('操作失败！',$url,'error');
                    }
                }

                $total = 0;
                $pager = '';
                $list  = mysqld_selectall("SELECT comment.*,shop_dish.title,shop_dish.id as did FROM " . table('shop_goods_comment') . "   comment  left join " . table('shop_dish') . " shop_dish on shop_dish.gid=comment.goodsid where shop_dish.id={$dishid} ORDER BY comment.istop desc, comment.createtime DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
//                pp("SELECT comment.*,shop_dish.title,shop_dish.id as did FROM " . table('shop_goods_comment') . "   comment  left join " . table('shop_dish') . " shop_dish on shop_dish.gid=comment.goodsid where shop_dish.id={$dishid} ORDER BY comment.istop desc, comment.createtime DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
//                ppd($list);
                if(!empty($list)){
                    //获取评论对应的图片
                    foreach($list as $key=> $row){
                        $list[$key]['piclist'] = mysqld_selectall("select img from ". table('shop_comment_img') ." where comment_id={$row['id']}");
                    }
                    // 获取评论数量
                    $total = mysqld_selectcolumn('SELECT COUNT(*) FROM ' . table('shop_goods_comment')." where goodsid={$list[0]['goodsid']}");
                    $pager = pagination($total, $pindex, $psize);
                }
                include page('dish_addcomment');
            }
        }else if($operation == 'topcomment') {  //置顶评论
            if($_GP['istop'] == 1)
                $istop = 0;    //取消置顶
            else
                $istop = 1;    //置顶评论
            mysqld_update('shop_goods_comment',array('istop'=>$istop),array('id'=>$_GP['id']));
            message('操作成功！',refresh(),'success');
        }else if($operation == 'downcomment'){
            //下沉沉到中下位置如第三页或者第四页，而不是沉到底，排在最后一页  一页算15个
            $id  = $_GP['id'];
            $gid = $_GP['gid'];
            $data = mysqld_selectall("select id,createtime from ".table('shop_goods_comment')." where goodsid={$gid} order by id desc");
            $num  = count($data)-1;
            $j = 0;
            foreach($data as $row){
                $j++;
                if($row['id'] == $id){
                    break;
                }
            }
            $zhong = floor($num / 2);
            $xia   = floor($zhong / 2);
            $key   = $zhong + $xia;
            $time  = $data[$key]['createtime'];
            $res   = mysqld_update("shop_goods_comment",array('createtime'=>$time),array('id'=>$id));
            if($res){
                message("操作成功！",refresh(),'success');
            }else{
                message("操作失败！",refresh(),'success');
            }

        }else if($operation == 'open_groupbuy'){
            //凑单开关 关闭或者开启
            //先判断是否有虚拟用户
            $member = mysqld_select("select openid from ".table('member')." where dummy=1");
            if(empty($member))
                message("对不起，请到会员管理注册批量的虚拟用户",refresh(),'error');

            if($_GP['act'] == 'open'){
                mysqld_update('shop_dish',array('open_groupbuy'=>1),array('id'=>$_GP['id']));
            }else if($_GP['act'] == 'close'){
                mysqld_update('shop_dish',array('open_groupbuy'=>0),array('id'=>$_GP['id']));
            }
            message('操作成功',refresh(),'success');
        }
