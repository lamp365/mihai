<?php
	/**
	 * app 收藏的商品
	 * @author WZW
	 * 
	 */

	$result = array();
	
	$op = $_GP['op'];

	// 预留APP账户验证接口
	$member = get_member_account(true, true);
	if (empty($member)) {
		$result['message'] 	= "用户验证失败!";
		$result['code'] 	= 2;
		echo apiReturn($result);
		exit;
	}elseif ($member == 3) {
		$result['message'] 	= "该账号已在别的设备上登录！";
		$result['code'] 	= 3;
		echo apiReturn($result);
		exit;
	}

	if ($op == 'get') {
		$pindex = max(1, intval($_GP['page']));
    	$psize = 20;
		$collection = mysqld_selectall("SELECT SQL_CALC_FOUND_ROWS id, dish_id, openshop_id, createtime FROM ".table('goods_collection')." WHERE openid='".$member['openid']."' AND deleted=0 ORDER BY createtime DESC LIMIT ".($pindex - 1) * $psize . ',' . $psize);
		$total = mysqld_select("SELECT FOUND_ROWS() as total;");
		foreach ($collection as &$c_v) {
			$good = get_good(array(
                "table"=>"shop_dish",
				"where"=>"a.id = ".$c_v['dish_id'],
			));
			// dump($good);
			$c_v['thumb'] = $good['thumb'];
			// 产品库价格
			$c_v['productprice'] = (float)$good['productprice'];
			// 价格
			$c_v['marketprice'] =(float)$good['marketprice'];
			// 团购价/秒杀价（手机价）
			$c_v['timeprice'] = (float)$good['timeprice'];
			$c_v['title'] = $good['title'];
			$c_v['p1'] = $good['p1'];
			$c_v['p2'] = $good['p2'];
			$c_v['p3'] = $good['p3'];
			$c_v['type'] = $good['type'];
			$c_v['timestart'] = $good['timestart'];
			$c_v['timeend'] = $good['timeend'];
			$c_v['createtime'] = date('Y-m-d H:i:s', $c_v['createtime']);
		}
		unset($c_v);
		// dump($collection);
		$result['data']['collection'] = $collection;
		$result['data']['total'] = $total['total'];
		$result['code']    = 1;
	}elseif ($op == 'add') {
		$good = $_GP['dish_id'];
		// 店铺ID
		$openshop_id = $_GP['openshop_id'];
		if (empty($good)) {
			$result['message'] = "商品ID为空!";
			$result['code']    = 0;
			echo apiReturn($result);
			exit;
		}
		$where = "openid='".$member['openid']."' AND dish_id=".$good;
		if (!empty($openshop_id)) {
			$where.=" AND openshop_id=".$openshop_id;
		}
		$have_c = mysqld_select("SELECT * FROM ".table('goods_collection')." WHERE ".$where);
		if (!empty($have_c)) {
			if ($have_c['deleted'] == 0) {
				$result['message'] = "该商品已加入收藏!";
				$result['code']    = 0;
			}else{
				$data = array('deleted' => 0, 'createtime' => time());
				mysqld_update('goods_collection', $data, array('id'=> $have_c['id']));
				// 商品收藏数+1
				mysqld_query("UPDATE ".table('shop_dish')." SET collect_num=collect_num+1 WHERE id=".$have_c['dish_id']);
				$result['message'] = "加入收藏成功!";
				$result['code']    = 1;
			}
		}else{
			$data = array('openid' => $member['openid'], 'dish_id' => $good, 'createtime' => time());
			if (!empty($openshop_id)) {
				$data['openshop_id'] = $openshop_id;
			}
			mysqld_insert('goods_collection', $data);
			// 商品收藏数+1
			mysqld_query("UPDATE ".table('shop_dish')." SET collect_num=collect_num+1 WHERE id=".$good);
			$result['message'] = "加入收藏成功!";
			$result['code']    = 1;
		}
	}elseif ($op = 'del') {
		$good = $_GP['dish_id'];
		// 店铺ID
		$openshop_id = $_GP['openshop_id'];
		if (empty($good)) {
			$result['message'] = "商品ID为空!";
			$result['code']    = 0;
			echo apiReturn($result);
			exit;
		}
		$where = "openid='".$member['openid']."' AND dish_id=".$good." AND deleted=0";
		if (!empty($openshop_id)) {
			$where.=" AND openshop_id=".$openshop_id;
		}
		$have_c = mysqld_select("SELECT * FROM ".table('goods_collection')." WHERE ".$where);
		if (empty($have_c)) {
			$result['message'] = "该收藏不存在!";
			$result['code']    = 0;
		}else{
			$data = array('deleted' => 1);
			mysqld_update('goods_collection', $data, array('id'=> $have_c['id']));
			// 商品收藏数-1
			mysqld_query("UPDATE ".table('shop_dish')." SET collect_num=if(collect_num>=1,collect_num-1,0) WHERE id=".$have_c['dish_id']);
			$result['message'] = "取消收藏成功!";
			$result['code']    = 1;
		}
	}

	// $result['message'] = "操作类型错误!";
	// $result['code']    = 0;
	// dump($result);
	echo apiReturn($result);
	exit;
