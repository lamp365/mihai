<?php
	/**
	 * app 开店部分接口
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
	
	// $member['openid'] = 'www123465';
	// $member['openid'] = '_t100310534332980651';
	// $member['openid'] = '_t101815242553129705';
	// $member['openid'] = '2015111911924';
	// $member['openid'] = '2016112116879';
	// dump($member);
	// dump(strtotime('2016-10-15 00:00:00'));

	if ($op == 'all') {
		// 开店主页
		$today_e = 0;
		$month_e = 0;
		$month_o = 0;

		// 店铺信息
		$shop_info = mysqld_select("SELECT * FROM ".table('openshop')." WHERE openid='".$member['openid']."'");

		// 今日访客
		$view_report = mysqld_select("SELECT uv FROM ".table('openshop_viewreport')." WHERE seller_openid='".$member['openid']."' AND time=".strtotime(date("Y-m-d")));

		// 订单数统计
		$order_ary = mysqld_selectall("SELECT a.createtime as acreatetime, a.status as astatus, b.orderid, b.commision, b.createtime as bcreatetime FROM ".table('shop_order')." as a left join ".table('shop_order_goods')." as b on a.id=b.orderid WHERE b.seller_openid='".$member['openid']."' AND a.status=3 ORDER BY a.createtime DESC");
		
		if (!empty($order_ary)) {
			// 订单数
			$o_a = array();
			foreach ($order_ary as &$oray) {
				if (date('Y-m',$oray['acreatetime']) == date('Y-m') AND $oray['astatus'] == '3') {
					$o_a[] = $oray['orderid'];
				}
				if (date('Y-m',$oray['bcreatetime']) == date('Y-m')) {
					$month_e += $oray['commision'];
				}
				if (date('Y-m-d',$oray['bcreatetime']) == date('Y-m-d')) {
					$today_e += $oray['commision'];
				}
			}
			unset($oray);
			$month_o = count(array_unique($o_a));
		}

		$shop_all = array();
		// 店铺ID
		$shop_all = $shop_info;
		// 今日收入
		$shop_all['today_e'] = $today_e;
		// 本月收入
		$shop_all['month_e'] = $month_e;
		// 本月订单
		$shop_all['order_num'] = $month_o;
		// 今日访客
		$shop_all['visitor_num'] = $view_report['uv'];
		// 店铺URL
		$shop_all['shop_url'] = get_wapshoper_url($member['openid']);
		// 店铺名
		// $shop_all['shop_name'] = $shop_info['shopname'];
		// // 店铺地区
		// $shop_all['shop_area'] = $shop_info['area'];
		// // 店铺头像
		// $shop_all['shop_logo'] = $shop_info['logo'];
		// // 店铺等级
		// $shop_all['shop_level'] = $shop_info['level'];
		// // 店铺公告
		// $shop_all['shop_notice'] = $shop_info['notice'];
		// 统计访问量
		// countOpenshopView($member['openid']);

		$result['data']['shop'] = $shop_all;
		$result['code'] = 1;
	}elseif ($op == 'open') {
		// 开店
		$shop_info = mysqld_select("SELECT * FROM ".table('openshop')." WHERE openid='".$member['openid']."'");
		if (!empty($shop_info)) {
			$result['message'] 	= "该用户已开过店铺!";
			$result['code'] 	= 0;
			echo apiReturn($result);
			exit;
		}

		$round_name = '店铺'.date('YmdHis').rand(1000,9999);
		$data = array('shopname' => $round_name, 'openid' => $member['openid'], 'createtime' => time());
		mysqld_insert('openshop', $data);

		$result['message'] = "开店成功!";
		$result['code'] = 1;
	}elseif ($op == 'add_good') {
		// 添加代销商品
		$dish_id = intval($_GP['dish_id']);
		$status = intval($_GP['status']);

		if (empty($dish_id)) {
			$result['message'] 	= "dish_id为空!";
			$result['code'] 	= 0;
			echo apiReturn($result);
			exit;
		}

		$shop_info = mysqld_select("SELECT * FROM ".table('openshop')." WHERE openid='".$member['openid']."'");
		if (empty($shop_info)) {
			$result['message'] 	= "该用户未开店!";
			$result['code'] 	= 0;
			echo apiReturn($result);
			exit;
		}

		$good = get_good(array(
            "table"=>"shop_dish",
			"where"=>"a.id = ".$dish_id,
		));
		if ($good['status'] == 0) {
			$result['message'] 	= "该商品已下架!";
			$result['code'] 	= 0;
			echo apiReturn($result);
			exit;
		}

		$have_good = mysqld_select("SELECT * FROM ".table('openshop_relation')." WHERE openid='".$member['openid']."' AND goodid=".$dish_id);
		if (!empty($have_good)) {
			if ($status != 1 AND $status != 2) {
				$result['message'] 	= "操作状态错误!";
				$result['code'] 	= 0;
				echo apiReturn($result);
				exit;
			}

			$data_query = mysqld_query("UPDATE ".table('openshop_relation')." SET status=".$status." WHERE openid='".$member['openid']."' AND goodid=".$dish_id);

			if (!$data_query) {
				$result['message'] = "操作失败!";
				$result['code'] = 0;
			}else{
				$result['message'] = "操作成功!";
				$result['code'] = 1;
			}
			echo apiReturn($result);
			exit;
		}

		$data = array('openid' =>$member['openid'], 'openshopid' => $shop_info['id'], 'p1' => $good['p1'], 'p2' => $good['p2'], 'p3' => $good['p3'], 'goodid' => $dish_id, 'operatetime' => time());
		$data_query = mysqld_insert('openshop_relation', $data);

		if ($data_query) {
			// 增加商品商家在卖数量
			mysqld_query("UPDATE ".table('shop_dish')." SET shoper_num=shoper_num+1 WHERE id=".$dish_id);
			$result['message'] = "上架代销成功!";
			$result['code'] = 1;
		}else{
			$result['message'] = "上架代销失败!";
			$result['code'] = 0;
		}
	}elseif ($op == 'control') {
		// 店铺管理
		$status = intval($_GP['status'] ? $_GP['status'] : 1);
		$order = intval($_GP['order'] ? $_GP['order'] : 1);
		$order_mode = $_GP['order_mode'] ? $_GP['order_mode'] : 'DESC';
		$pindex = max(1, intval($_GP['page']));
    	$psize = 20;

		if ($status != 1 and $status != 2) {
			$result['message'] 	= "销售状态错误!";
			$result['code'] 	= 0;
			echo apiReturn($result);
			exit;
		}
		if ($order != 1 and $order != 2 and $order != 3 and $order != 4) {
			$result['message'] 	= "排序状态错误!";
			$result['code'] 	= 0;
			echo apiReturn($result);
			exit;
		}
		$orderby = '';
		if ($order == 1) {
			$orderby = 'operatetime';
		}elseif ($order == 2) {
			$orderby = 'sales';
		}elseif ($order == 3) {
			$orderby = 'commision';
		}elseif ($order == 4) {
			$orderby = 'collect_num';
		}
		
		$shop_info = mysqld_select("SELECT id, shopname, area, logo, shoppic, level, createtime FROM ".table('openshop')." WHERE openid='".$member['openid']."'");
		// dump($shop_info);

		if (empty($shop_info)) {
			$shop_info = NULL;
		}else{
			$shop_info['shop_url'] = get_wapshoper_url($member['openid']);
		}

		$shop_goods = mysqld_selectall("SELECT SQL_CALC_FOUND_ROWS goodid as id, status as shop_good_status, operatetime FROM ".table('openshop_relation')." WHERE openshopid=".$shop_info['id']." AND status=".$status." LIMIT ".($pindex - 1) * $psize . ',' . $psize);
		if (empty($shop_goods)) {
			$shop_goods = array();
		}
		// 总记录数
		$total = mysqld_select("SELECT FOUND_ROWS() as total;");

		foreach ($shop_goods as &$s_v) {
			$good = get_good(array(
                "table"=>"shop_dish",
				"where"=>"a.id = ".$s_v['id'],
			));
			$s_v['title'] = $good['title'];
			$s_v['thumb'] = $good['thumb'];
			$s_v['productprice'] = $good['productprice'];
			$s_v['marketprice'] = $good['marketprice'];
			$s_v['app_marketprice'] = $good['app_marketprice'];
			$s_v['timeprice'] = $good['timeprice'];
			// 库存
      		$s_v['total'] = $good['total'];
      		
      		// 活动商品并且处于活动中用timeprice价格
      		if ($good ['type']!=0 && ((TIMESTAMP < $good ['timeend']) && (TIMESTAMP >= $good ['timestart']))) {
      		
      			// 佣金
      			$s_v['commision'] = (float)((float)$good['timeprice']*(float)$good['commision']);
      		}
      		else{
      			// 佣金
      			$s_v['commision'] = (float)((float)$good['app_marketprice']*(float)$good['commision']);
      		}
      		
      		// 收藏数
      		$s_v['collect_num'] = $good['collect_num'];
      		// 销量
      		$s_v['sales'] = $good['sales'];
      		// 商品状态
      		$s_v['type'] = $good['type'];
      		// 商品分享URL
      		$s_v['good_url'] = get_wapgoods_url($member['openid'], $s_v['id']);
		}
		unset($s_v);
		if (!empty($shop_goods)) {
			$shop_goods = array_order($shop_goods, $orderby, 'SORT_'.$order_mode);
		}

		// $result['data']['info'] = $shop_info;
		$result['data']['goods'] = $shop_goods;
		$result['data']['total'] = $total['total'];
		$result['code'] = 1;
	}elseif ($op == 'get_setting') {
		// 获取设置信息
		$shop_info = mysqld_select("SELECT * FROM ".table('openshop')." WHERE openid='".$member['openid']."'");
		if (empty($shop_info)) {
			$result['message'] 	= "店铺信息获取失败!";
			$result['code'] 	= 0;
			echo apiReturn($result);
			exit;
		}
		$result['data']['info'] = $shop_info;
		$result['code'] = 1;
	}elseif ($op == 'set_setting') {
		// 店铺设置
		$shop_ary = array();
		// $shop_ary['logo'] = $_GP['logo'];
		$shop_ary['shopname'] = $_GP['shopname'];
		// $shop_ary['level'] = $_GP['level'];
		$shop_ary['mobile'] = $_GP['mobile'];
		$shop_ary['notice'] = $_GP['notice'];
		$shop_ary['area'] = $_GP['area'];

		foreach ($shop_ary as $s_k => &$s_v) {
			if (empty($s_v)) {
				unset($shop_ary[$s_k]);
			}
		}
		unset($s_v);

		if (!empty($_FILES['logo'])) {
			if ($_FILES['logo']['error']==0) {
				$upload = file_upload($_FILES['logo']);
				//出错时
				if (is_error($upload)) {
					$result['message'] 	= $upload['message'];
					$result['code'] 	= 0;
					echo apiReturn($result);
					exit;
				}else{
					$shop_ary['logo'] = $upload['path'];
					$result['data']['logo'] = $upload['path'];
				}
			}else{
				$result['message'] 	= "店铺头像上传失败。";
				$result['code'] 	= 0;
				echo apiReturn($result);
				exit;
			}
		}

		if (!empty($shop_ary)) {
			mysqld_update('openshop', $shop_ary, array('openid'=> $member['openid']));
		}

		$result['message'] = "设置成功!";
		$result['code'] = 1;
	}elseif ($op == 'sold_up' or $op == 'sold_down') {
		// 上下架
		$goods = json_decode($_REQUEST['goods']);
		// $goods = array(1);
		if (!is_array($goods) or empty($goods)) {
			$result['message'] 	= "商品ID数组为空!";
			$result['code'] 	= 0;
			echo apiReturn($result);
			exit;
		}

		$id_ary = "(";
		foreach ($goods as $g_v) {
			$id_ary .= $g_v.',';
		}
		$id_ary = substr($id_ary,0,strlen($id_ary)-1).')';

		if ($op == 'sold_up') {
			$status = 1;
		}else{
			$status = 2;
		}

		$data_query = mysqld_query("UPDATE ".table('openshop_relation')." SET status=".$status." WHERE openid='".$member['openid']."' AND goodid IN ".$id_ary);

		if (!$data_query) {
			$result['message'] = "操作失败!";
			$result['code'] = 0;
		}else{
			$result['message'] = "操作成功!";
			$result['code'] = 1;
		}
	}elseif ($op == 'del_goods') {
		// 删除商品
		$goods = json_decode($_REQUEST['goods']);
		// $goods = array(1);
		if (empty($goods)) {
			$result['message'] 	= "商品ID数组为空!";
			$result['code'] 	= 0;
			echo apiReturn($result);
			exit;
		}

		$id_ary = "(";
		foreach ($goods as $g_v) {
			$id_ary .= $g_v.',';
		}
		$id_ary = substr($id_ary,0,strlen($id_ary)-1).')';

		if ($op == 'sold_up') {
			$status = 1;
		}else{
			$status = 0;
		}

		mysqld_query("DELETE FROM ".table('openshop_relation')." WHERE openid='".$member['openid']."' AND goodid IN ".$id_ary);
		// 减去商品商家在卖数量
		mysqld_query("UPDATE ".table('shop_dish')." SET shoper_num=shoper_num-1 WHERE id IN ".$id_ary." AND shoper_num>0");
		$result['message'] = "删除成功!";
		$result['code'] = 1;
	}elseif ($op == 'order') {
		// 订单管理
		$status = intval($_GP['status'] ? $_GP['status'] : 1);  // 订单状态
		$search = $_GP['search'];  // 查询条件
		$pindex = max(1, intval($_GP['page'])); // 分页
    	$psize = 10;
		$where = '';
		$status_ary = array();
		if ($status == 1) {
			$status_ary = array(1, 2);
		}elseif ($status == 2) {
			$status_ary = array(0);
		}elseif ($status == 3) {
			// $status_ary = array(-2, -3, -4);
			$where .= " AND b.type != 0";
		}elseif ($status == 4) {
			$status_ary = array(3);
		}elseif ($status == 5) {
			$status_ary = array(-1, -5, -6);
		}elseif (empty($search)) {
			$result['message'] 	= "订单状态错误!";
			$result['code'] 	= 0;
			echo apiReturn($result);
			exit;
		}
		$status_a = "(";
		foreach ($status_ary as $g_v) {
			$status_a .= $g_v.',';
		}
		$status_a = substr($status_a,0,strlen($status_a)-1).')';

		if (!empty($search)) {
			if (is_numeric($search)) {
				// 手机号
				$where .= " AND c.mobile LIKE '%$search%'";
			}else{
				// 昵称
				$where .= " AND c.nickname LIKE '%$search%'";
			}
		}elseif ($status != 3) {
			$where .= " AND a.status IN ".$status_a;
		}
		
		$order = mysqld_selectall("SELECT SQL_CALC_FOUND_ROWS a.id as orderid, a.openid as buyerid, a.dispatchprice, a.price as order_price, a.status, b.goodsid as dishid, b.price as goodprice, b.commision, b.total as goods_total, b.iscomment, b.status as refund_status, c.nickname, c.realname, c.mobile FROM ".table('shop_order')." as a left join ".table('shop_order_goods')." as b on a.id=b.orderid left join ".table('member')." as c on a.openid=c.openid WHERE b.seller_openid='".$member['openid']."'".$where." ORDER BY b.createtime DESC LIMIT ".($pindex - 1) * $psize . ',' . $psize);
		// 总记录数
		$total = mysqld_select("SELECT FOUND_ROWS() as total;");
		// dump($total);
		$order_re = array();

		foreach ($order as &$o_v) {
			$uua = array();
			$uua['orderid'] = $o_v['orderid'];
			$uua['buyerid'] = $o_v['buyerid'];
			$uua['dispatchprice'] = $o_v['dispatchprice'];
			$uua['order_price'] = $o_v['order_price'];
			$uua['status'] = $o_v['status'];
			$uua['iscomment'] = $o_v['iscomment'];
			$uua['nickname'] = $o_v['nickname'];
			$uua['realname'] = $o_v['realname'];
			$uua['mobile'] = $o_v['mobile'];
			$uua['good'] = array();

			$good = get_good(array(
                "table"=>"shop_dish",
				"where"=>"a.id = ".$o_v['dishid'],
			));
			$ua = array();
			$ua['id'] = $o_v['dishid'];
			$ua['thumb'] = $good['thumb'];
			$ua['title'] = $good['title'];
			$ua['status'] = $o_v['refund_status'];
			$ua['productprice'] = $good['productprice'];
			$ua['marketprice'] = $good['marketprice'];
			$ua['timeprice'] = $good['timeprice'];
			$ua['goodprice'] = $o_v['goodprice'];
			$ua['commision'] = $o_v['commision'];
			$ua['total'] = $o_v['goods_total'];
			$uua['good'][] = $ua;
			$order_re[] = $uua;
		}
		unset($o_v);

		// 处理单订单多商品
		$orderid_ary = array();
		foreach ($order_re as $orrk => $orrv) {
			foreach ($orderid_ary as $ody) {
				if ($orrv['orderid'] == $ody['orderid']) {
					$order_re[$ody['key']]['good'][] = $orrv['good'][0];
					unset($order_re[$orrk]);
					continue 2;
				}
			}
			$oa = array();
			$oa['orderid'] = $orrv['orderid'];
			$oa['key'] = $orrk;
			$orderid_ary[] = $oa;
		}
		// 重新排列数组下标
		$order_re = array_merge($order_re);

		// 计算总佣金
		foreach ($order_re as &$oorv) {
			$oorv['sum_commission'] = 0;
			foreach ($oorv['good'] as $ogv) {
				$oorv['sum_commission'] += (float)$ogv['commision'];
			}
		}
		unset($oorv);

		$result['data']['order'] = $order_re;
		$result['data']['total'] = $total['total'];
		$result['code'] = 1;
	}elseif ($op == 'data_all') {
		// 报表总览
		$earning_order = mysqld_selectall("SELECT a.price, a.total, a.commision, a.createtime, a.orderid, b.createtime as b_ctime FROM ".table('shop_order_goods')." as a left join ".table('shop_order')." as b on a.orderid=b.id WHERE a.seller_openid='".$member['openid']."' AND b.status=3 ORDER BY a.createtime DESC");

		$seven_day_e = 0;
		$yesterday_o = 0;
		$today_n = 0;

		// 7日日期
		$date_ary = array();
        for($i = strtotime(date("Y-m-d",strtotime("-6 day"))); $i <= strtotime(date("Y-m-d")); $i += 86400) {
            $ThisDate = date("Y-m-d",$i);
            array_push($date_ary, $ThisDate);
        }
        // dump($date_ary);
        // date("Y-m-d",strtotime("-2 day"))

        $y = date("Y");
        $m = date("m");
        $d = date("d");
        $todayTime = mktime(0,0,0,$m,$d,$y);
        // 店铺信息
		$shop_count = mysqld_select("SELECT uv FROM ".table('openshop_viewreport')." WHERE seller_openid='".$member['openid']."' AND time=".$todayTime);
		$today_n = $shop_count['uv'];
		if (empty($today_n)) {
			$today_n = 0;
		}

		if (!empty($earning_order)) {
			$o_ary = array();
			foreach ($earning_order as $e_v) {
				foreach ($date_ary as $date) {
					if (date('Y-m-d',$e_v['createtime']) == $date) {
						$seven_day_e += (float)$e_v['commision'];
					}
				}
				if (date('Y-m-d',$e_v['b_ctime']) == date("Y-m-d",strtotime("-1 day"))) {
					$o_ary[] = $e_v['orderid'];
				}
			}
			$yesterday_o = count(array_unique($o_ary));
		}

		$ary = array();
		$ary['seven_day_e'] = (float)$seven_day_e;
		$ary['yesterday_o'] = $yesterday_o;
		$ary['today_n'] = $today_n;

		$result['data']['all'] = $ary;
		$result['code'] = 1;
	}elseif ($op == 'data_detail') {
		// 报表详细
		$type = $_GP['type'] ? $_GP['type'] : 'earning';  // 统计类型
		$time = $_GP['time'] ? $_GP['time'] : 'week';  // 时间区间

		if ($time == 'week') {
			$w = date('w',strtotime(date("Y-m-d")));
			// 开始
			$s_time = date('Y-m-d',strtotime(date("Y-m-d")."-".($w ? $w - 1 : 6).' days'));
			// 结束
			$e_time = date('Y-m-d',strtotime($s_time." +6 days"));
		}elseif ($time == 'month') {
			$s_time = date("Y-m-d",mktime(0, 0 , 0,date("m"),1,date("Y")));
			$e_time = date("Y-m-d",mktime(23,59,59,date("m"),date("t"),date("Y")));
		}elseif ($time == 'year') {
			$mouth_ary = array();
			for ($i=1; $i <= date('n'); $i++) { 
				$mouth_ary[] = $i;
			}
			$mouth_b_e_ary = array();
			foreach ($mouth_ary as $mv) {
				$mouth_b_e_ary[] = date("Y-m",mktime(0, 0 , 0,$mv,1,date("Y")));
			}
		}else{
			$result['message'] 	= "时间类型错误!";
			$result['code'] 	= 0;
			echo apiReturn($result);
			exit;
		}

		$result_data = array();
		if ($time != 'year') {
			$date_ary = array();
	        for($i = strtotime($s_time); $i <= strtotime($e_time); $i += 86400) {
	            $ThisDate = date("Y-m-d",$i);
	            array_push($date_ary, $ThisDate);
	        }

			if ($type == 'earning') {
				// 收入
				// $earning_order = mysqld_selectall("SELECT price, total, commision, createtime FROM ".table('shop_order_goods')." WHERE seller_openid='".$member['openid']."' ORDER BY createtime DESC");
				$earning_order = mysqld_selectall("SELECT a.status, b.price, b.total, b.commision, b.createtime FROM ".table('shop_order')." as a left join ".table('shop_order_goods')." as b on a.id=b.orderid WHERE b.seller_openid='".$member['openid']."' AND a.status=3 ORDER BY b.createtime DESC");
				if (!empty($earning_order)) {
					foreach ($date_ary as $date) {
						$result_data['result_data'][$date] = 0;
						foreach ($earning_order as $e_v) {
							if (date('Y-m-d',$e_v['createtime']) == $date) {
								$result_data['result_data'][$date] += round($e_v['commision'], 2);
								
							}
						}
						if ($date == date('Y-m-d')) {
							break;
						}
					}
	 				// 总收入
					$result_sum = 0;
					foreach ($result_data['result_data'] as $rdv) {
						$result_sum += $rdv;
					}
					$result_data['sum'] = $result_sum;
				}else{
					$result_data['sum'] = 0;
				}
			}elseif ($type == 'order_num') {
				// 订单数
				$order_ary = mysqld_selectall("SELECT a.status, b.orderid, b.createtime FROM ".table('shop_order')." as a left join ".table('shop_order_goods')." as b on a.id=b.orderid WHERE b.seller_openid='".$member['openid']."' ORDER BY b.createtime DESC");
				if (!empty($order_ary)) {
					$o_id_ary = array();
					foreach ($date_ary as $date) {
						$o_id_ary['all'][$date][] = 0;
						$o_id_ary['succeed'][$date][] = 0;
						foreach ($order_ary as $o_v) {
							if (date('Y-m-d',$o_v['createtime']) == $date) {
								$o_id_ary['all'][$date][] = intval($o_v['orderid']);
								if ($o_v['status'] == 3) {
									$o_id_ary['succeed'][$date][] = intval($o_v['orderid']);
								}
							}
						}
						if ($date == date('Y-m-d')) {
							break;
						}
					}
					
					foreach ($o_id_ary as $oik => $oiv) {
						foreach ($oiv as $o_oik => $o_oiv) {
							$uni_all = array_unique($o_oiv);
							$result_data['result_data'][$oik][$o_oik] = count($uni_all);
							foreach ($uni_all as $univ) {
								if ($univ == 0) {
									$result_data['result_data'][$oik][$o_oik] = count($uni_all)-1;
								}
							}
						}
					}
					// 总订单数
					$result_sum_all = 0;
					foreach ($result_data['result_data']['all'] as $rdv) {
						$result_sum_all += $rdv;
					}
					$result_sum_succeed = 0;
					foreach ($result_data['result_data']['succeed'] as $rds) {
						$result_sum_succeed += $rds;
					}
					$result_data['sum']['all'] = $result_sum_all;
					$result_data['sum']['succeed'] = $result_sum_succeed;
				}else{
					$result_data['sum']['all'] = 0;
					$result_data['sum']['succeed'] = 0;
				}
			}elseif ($type == 'view_num') {
				// 访客
				$view_num = mysqld_selectall("SELECT * FROM ".table('openshop_viewreport')." WHERE seller_openid='".$member['openid']."' ORDER BY time DESC");
				if (!empty($view_num)) {
					foreach ($date_ary as $date) {
						$result_data['result_data']['pv'][$date] = 0;
						$result_data['result_data']['uv'][$date] = 0;
						$result_data['result_data']['collect'][$date] = 0;
						foreach ($view_num as $v_v) {
							if (date('Y-m-d',$v_v['time']) == $date) {
								$result_data['result_data']['pv'][$date] += $v_v['pv'];
								$result_data['result_data']['uv'][$date] += $v_v['uv'];
								$result_data['result_data']['collect'][$date] += $v_v['collect_num'];
							}
						}
						if ($date == date('Y-m-d')) {
							break;
						}
					}
					// 总数
					$result_sum_pv = 0;
					foreach ($result_data['result_data']['pv'] as $rdp) {
						$result_sum_pv += $rdp;
					}
					$result_sum_uv = 0;
					foreach ($result_data['result_data']['uv'] as $rdu) {
						$result_sum_uv += $rdu;
					}
					$result_sum_col = 0;
					foreach ($result_data['result_data']['collect'] as $rdc) {
						$result_sum_col += $rdc;
					}
					$result_data['sum']['pv'] = $result_sum_pv;
					$result_data['sum']['uv'] = $result_sum_uv;
					$result_data['sum']['col'] = $result_sum_col;
				}else{
					$result_data['sum']['pv'] = 0;
					$result_data['sum']['uv'] = 0;
					$result_data['sum']['col'] = 0;
				}
			}else{
				$result['message'] 	= "统计类型错误!";
				$result['code'] 	= 0;
				echo apiReturn($result);
				exit;
			}
		}else{
			foreach ($mouth_b_e_ary as $m_v) {
				if ($type == 'earning') {
					// 收入
					$earning_order = mysqld_selectall("SELECT a.status, b.price, b.total, b.commision, b.createtime FROM ".table('shop_order')." as a left join ".table('shop_order_goods')." as b on a.id=b.orderid WHERE b.seller_openid='".$member['openid']."' AND a.status=3 ORDER BY b.createtime DESC");
					if (!empty($earning_order)) {
						$result_data['result_data'][$m_v] = 0;
						foreach ($earning_order as $e_v) {
							if (date('Y-m',$e_v['createtime']) == $m_v) {
								$result_data['result_data'][$m_v] += round($e_v['commision'], 2);
							}
						}
						// 总收入
						$result_sum = 0;
						foreach ($result_data['result_data'] as $rdd) {
							$result_sum += $rdd;
						}
						$result_data['sum'] = $result_sum;
					}else{
						$result_data['sum'] = 0;
					}
				}elseif ($type == 'order_num') {
					// 订单数
					$order_ary = mysqld_selectall("SELECT a.status, b.orderid, b.createtime FROM ".table('shop_order')." as a left join ".table('shop_order_goods')." as b on a.id=b.orderid WHERE b.seller_openid='".$member['openid']."' ORDER BY b.createtime DESC");
					if (!empty($order_ary)) {
						$o_id_ary = array();
						$o_id_ary['all'][$m_v][] = 0;
						$o_id_ary['succeed'][$m_v][] = 0;
						foreach ($order_ary as $o_v) {
							if (date('Y-m',$o_v['createtime']) == $m_v) {
								$o_id_ary['all'][$m_v][] = $o_v['orderid'];
								if ($o_v['status'] == 3) {
									$o_id_ary['succeed'][$m_v][] = $o_v['orderid'];
								}
							}
						}

						foreach ($o_id_ary as $oik => $oiv) {
							foreach ($oiv as $o_oik => $o_oiv) {
								$uni_all = array_unique($o_oiv);
								$result_data['result_data'][$oik][$o_oik] = count($uni_all);
								foreach ($uni_all as $univ) {
									if ($univ == 0) {
										$result_data['result_data'][$oik][$o_oik] = count($uni_all)-1;
									}
								}
							}
						}
						// 总订单数
						$result_sum_all = 0;
						foreach ($result_data['result_data']['all'] as $rdv) {
							$result_sum_all += $rdv;
						}
						$result_sum_succeed = 0;
						foreach ($result_data['result_data']['succeed'] as $rds) {
							$result_sum_succeed += $rds;
						}
						$result_data['sum']['all'] = $result_sum_all;
						$result_data['sum']['succeed'] = $result_sum_succeed;
					}else{
						$result_data['sum']['all'] = 0;
						$result_data['sum']['succeed'] = 0;
					}
				}elseif ($type == 'view_num') {
					// 访客
					$view_num = mysqld_selectall("SELECT * FROM ".table('openshop_viewreport')." WHERE seller_openid='".$member['openid']."' ORDER BY time DESC");
					if (!empty($view_num)) {
						$result_data['result_data']['pv'][$m_v] = 0;
						$result_data['result_data']['uv'][$m_v] = 0;
						$result_data['result_data']['collect'][$m_v] = 0;
						foreach ($view_num as $v_v) {
							if (date('Y-m',$v_v['time']) == $m_v) {
								$result_data['result_data']['pv'][$m_v] += $v_v['pv'];
								$result_data['result_data']['uv'][$m_v] += $v_v['uv'];
								$result_data['result_data']['collect'][$m_v] += $v_v['collect_num'];
							}
						}
						// 总数
						$result_sum_pv = 0;
						foreach ($result_data['result_data']['pv'] as $rdp) {
							$result_sum_pv += $rdp;
						}
						$result_sum_uv = 0;
						foreach ($result_data['result_data']['uv'] as $rdu) {
							$result_sum_uv += $rdu;
						}
						$result_sum_col = 0;
						foreach ($result_data['result_data']['collect'] as $rdc) {
							$result_sum_col += $rdc;
						}
						$result_data['sum']['pv'] = $result_sum_pv;
						$result_data['sum']['uv'] = $result_sum_uv;
						$result_data['sum']['col'] = $result_sum_col;
					}else{
						$result_data['sum']['pv'] = 0;
						$result_data['sum']['uv'] = 0;
						$result_data['sum']['col'] = 0;
					}
				}else{
					$result['message'] 	= "统计类型错误!";
					$result['code'] 	= 0;
					echo apiReturn($result);
					exit;
				}
			}
		}

		$result['data'] = $result_data;
		$result['code'] = 1;
	}else{
		$result['message'] 	= "操作类型不正确!";
		$result['code'] 	= 0;
	}

	// dump($result);
	echo apiReturn($result);
	exit;