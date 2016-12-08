<?php
    function creatImg($mess_id) {
		$token = get_weixin_token();
		$url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token={$token}";
		//{"action_name": "QR_LIMIT_STR_SCENE", "action_info": {"scene": {"scene_str": "123"}}}
		$postData = array(
              'action_name'=>'QR_LIMIT_STR_SCENE',
			  'action_info'=>array(
			          'scene'=>array('scene_str'=>$mess_id)
			   )
		);
		$postData = json_encode($postData);
		$content = json_decode(http_post($url, $postData),true);
		$ticket = urlencode($content['ticket']);
		$url   = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket={$ticket}";
        return  "<img width='60' src='{$url}' />";
	}
 		$cfg = globaSetting();
        $area = mysqld_selectall("SELECT * FROM " . table('mess_list') . " where deleted=0 ORDER BY parentid ASC, displayorder DESC", array(), 'id');
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
        if ($operation == 'post') {
            $id = intval($_GP['id']);
            if (!empty($id)) {
                $item = mysqld_select("SELECT a.*,b.title as gname,b.id as gid,b.thumb as gthumb FROM " . table('shop_mess') . " AS a LEFT JOIN " . table('shop_dish') . " as b on a.gid = b.id WHERE a.id = :id", array(':id' => $id));
                if (empty($item)) {
                    message('抱歉，商品不存在或是已经删除！', '', 'error');
                }
            }
            if (empty($area)) {
                message('抱歉，请您先添加商品分类！', web_url('area', array('op' => 'post')), 'error');
            }
            if (checksubmit('submit')) {
                if (empty($_GP['pcate'])) {
                    message('请选择商品区间！');
                }
				$yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
                $orderSn = $yCode[intval(date('Y')) - 2011] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));
				$messsn = $orderSn;
				$c_p = mysqld_select("SELECT a.*,b.thumb as thumbs FROM ".table("shop_dish")." a left join " .table("shop_goods"). " b on a.gid = b.id WHERE a.id = ".$_GP['c_goods']);
				if ( !$c_p ){
                    message('宝贝库有误！');
				}
                $data = array(
                    'pcate' => intval($_GP['pcate']),
                    'ccate' => intval($_GP['ccate']),
					'gid'  => intval($_GP['c_goods']),
                    'status' => intval($_GP['status']),
                  // 'displayorder' => intval($_GP['displayorder']),
                    'description' => $_GP['description'],
                    'content' => htmlspecialchars_decode($c_p['content']),
                    'messsn' => $messsn,
                    'productsn' => $c_p['productsn'],
                    'marketprice' => $_GP['marketprice'],
                    'productprice' => $c_p['marketprice'],
                    'max_buy' => intval($_GP['max_buy']),
                    'total' => intval($_GP['total']),
                    'totalcnf' => intval($c_p['totalcnf']),
                    'credit' => intval($c_p['credit']),
                    'createtime' => TIMESTAMP,
                 //  'isnew' => intval($_GP['isnew']),
                 //   'isfirst' => intval($_GP['isfirst']),
                  //  'ishot' => intval($_GP['ishot']),
                  //  'isjingping' => intval($_GP['isjingping']),
                 //    'issendfree' => intval($_GP['issendfree']),
                  //  'type' => intval($c_p['type']),
                 //   'ishot' => intval($_GP['ishot']),
                 //   'isdiscount' => intval($_GP['isdiscount']),
                  //  'isrecommand' => intval($_GP['isrecommand']),
                  //  'istime' => intval($_GP['istime']),
                 //   'timestart' => strtotime($_GP['timestart']),
                 //   'hasoption' => intval($_GP['hasoption']),
                  //  'timeend' => strtotime($_GP['timeend'])
                 );
				
                $data['p1'] = $c_p['p1'];
				$data['p2'] = $c_p['p2'];
				$data['p3'] = $c_p['p3'];
				$data['title'] = $c_p['title'];
                $data['thumb'] = $c_p['thumbs'];
                if (empty($id)){
                    $data['sales']=0;
                    mysqld_insert('shop_mess', $data);
                    $id = mysqld_insertid();
					if ( $id <= 0 ){
                        message('商品操作失败！', web_url('mess', array('op' => 'post', 'id' => $id)), 'error');
					}
                } else {
                    unset($data['createtime']);
                    unset($data['sales']);
                    mysqld_update('shop_mess', $data, array('id' => $id));
                }           
                message('商品操作成功！', web_url('mess', array('op' => 'post', 'id' => $id)), 'success');
            }
            include page('mess');
        } elseif ($operation == 'display') {
            $pindex = max(1, intval($_GP['page']));
            $psize = 10;
            $condition = '';
            if (!empty($_GP['keyword'])) {
                $condition .= " AND title LIKE '%{$_GP['keyword']}%'";
            }
            
            if (!empty($_GP['cate_2'])) {
                $cid = intval($_GP['cate_2']);
                $condition .= " AND ccate = '{$cid}'";
            } elseif (!empty($_GP['cate_1'])) {
                $cid = intval($_GP['cate_1']);
                $condition .= " AND pcate = '{$cid}'";
            }
    
            $list = mysqld_selectall("SELECT * FROM " . table('shop_mess') . " WHERE  deleted=0 $condition ORDER BY status DESC, displayorder DESC, id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
			foreach ( $list as &$c ){
               $c['count'] = mysqld_selectcolumn('SELECT COUNT(*) FROM '. table('shop_mess_comment') .'WHERE messid = '.$c['id']);
			}
			foreach ( $list as $key => $value ){
				$lists = mysqld_select("SELECT * FROM " . table('mess_list') . " WHERE  id = ".$value['pcate']);
                $list[$key]['area'] = $lists['name'];
			}
            $total = mysqld_selectcolumn('SELECT COUNT(*) FROM ' . table('shop_mess') . " WHERE deleted=0 $condition");
            $pager = pagination($total, $pindex, $psize);
             include page('mess_list');
        } elseif ($operation == 'delete') {
            $id = intval($_GP['id']);
            $row = mysqld_select("SELECT id, thumb FROM " . table('shop_mess') . " WHERE id = :id", array(':id' => $id));
            if (empty($row)) {
                message('抱歉，商品不存在或是已经被删除！');
            }
            //修改成不直接删除，而设置deleted=1
            mysqld_update("shop_mess", array("deleted" => 1), array('id' => $id));

            message('删除成功！', 'refresh', 'success');
        } elseif ( $operation == 'query' ){
            $condition = '';  
			if (!empty($_GP['ccate2'])) {
                $cid = intval($_GP['ccate2']);
                $condition .= " AND p3 = '{$cid}'";
            } elseif (!empty($_GP['ccate'])) {
                $cid = intval($_GP['ccate']);
                $condition .= " AND p2 = '{$cid}'";
            } elseif (!empty($_GP['pcate'])) {
                $cid = intval($_GP['pcate']);
                $condition .= " AND p1 = '{$cid}'";
            }
			$list = mysqld_selectall("SELECT id,title FROM " . table('shop_dish') . " WHERE  deleted=0 $condition ORDER BY status DESC, displayorder DESC, id DESC  ");
			$option = '<option value="0">请选择产品</option>';
			foreach ( $list as $key => $value ){
                  $option .= "<option value='".$value['id']."'>".$value['title']."</option>";
			}
			echo $option;
	 }elseif ( $operation == 'comment' ){
		  if ( !empty($_GP['messid']) ){
             $where = ' and messid = '.$_GP['messid'];
		  }
		  // 对正在团购的进行分类筛选
		  $sql = "SELECT * FROM squdian_shop_mess WHERE id IN ( ".
                 "SELECT messid FROM squdian_shop_mess_comment ".
                 "GROUP BY messid) and status =1";
		  $select = array();
		  $select = mysqld_selectall($sql);
		  $list = mysqld_selectall("SELECT a.*,b.title,b.ccate FROM " . table('shop_mess_comment') . " as a left join ".table('shop_mess')." as b on a.messid = b.id WHERE a.messid > 0  $where ORDER BY a.createtime DESC");
		  foreach ( $list as &$c ){
               if ( $c['ccate'] == 0 ){
                   $c['city'] = '全国';
			   }else{
                   $city = mysqld_select("SELECT * FROM ". table("mess_list") . " WHERE id = ".$c['ccate']);
				   $c['city'] = $city['name'];
			   }
		  }
          include page('mess_comment');
	 }elseif ($operation == 'del') {
		    $id = intval($_GP['id']);
		    $row = mysqld_select("SELECT id FROM " . table('shop_mess_comment') . " WHERE id = :id", array(':id' => $id));
		    if (empty($row)) {
		        message('抱歉，记录不存在或是已经被删除！');
		    }
		    //修改成不直接删除，而设置deleted=1
		    mysqld_delete("shop_mess_comment", array('id' => $id));
		    message('删除成功！', 'refresh', 'success');
		}