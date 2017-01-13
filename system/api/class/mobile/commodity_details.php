<?php
	/**
	 * app 商品详情接口
	 * @author WZW
	 * 
	 */

	$result = array();
	
	$dish_id = intval($_GP['id']);
	$op = $_GP['op'];
	// 预留APP账户验证接口
	$member = get_member_account(true, true);
	if ($member == 3) {
		$result['message'] 	= "该账号已在别的设备上登录！";
		$result['code'] 	= 3;
		echo apiReturn($result);
		exit;
	}

	if (empty($dish_id)) {
		$result['message'] 	= "商品ID为空!";
		$result['code'] 	= 0;
	}else{
		$good = get_good(array(
                "table"=>"shop_dish",
				"where"=>"a.id = ".$dish_id,
			));
		if (empty($good['title'])) {
			$result['message'] 	= "商品查询失败!";
			$result['code'] 	= 0;
		}else{
			// 获取主图
			$piclist = array();
			$piclist[] = $good['thumb'];
			// 获取细节图
			$goods_piclist = mysqld_selectall("SELECT * FROM " . table('shop_goods_piclist') . " WHERE goodid = :goodid", array(':goodid' => $good['gid']));

			$goods_piclist_count = mysqld_selectcolumn("SELECT count(*) FROM " . table('shop_goods_piclist') . " WHERE goodid = :goodid", array(':goodid' => $good['gid']));
			if($goods_piclist_count > 0) {
	      	    foreach ($goods_piclist as &$item) {
	        		$piclist[] = $item['picurl'];
	            }
	      	}

	      	// 获取仓库信息
	      	$depot = mysqld_select("SELECT name FROM " . table('dish_list') . "  WHERE id=:depotid", array(':depotid' => $good['pcate']));

	      	$list = array();
	      	// 当前是否已登陆
	      	if (empty($member)) {
	      		$list['login'] = 0;
	      	}else{
	      		$list['login'] = 1;
	      	}
	      	// 商品ID 
	      	$list['id'] = $dish_id;
	      	// 销量
	      	$list['sales'] = $good['sales'];
	      	// 标题
			$list['title'] = $good['title'];
			// 简单描述
			$list['description'] = $good['description'];
			// 产品库价格
			$list['productprice'] = (float)$good['productprice'];
			// 价格
			$list['marketprice'] =(float)$good['marketprice'];
			// 团购价/秒杀价（手机价）
			$list['timeprice'] = (float)$good['timeprice'];
			// 佣金
			$list['commision'] = ((float)$good['commision']*(float)$good['timeprice']);
			// 库存
			$list['total'] = $good['total'];
			// 展示图片
			$list['piclist'] = $piclist;
			// 仓库
			$list['depot'] = $depot['name'];
			$use_tax = get_tax($good['taxid']);
			// 税率
			$list['tax'] = $use_tax['tax'];
			// type
			$list['type'] = $good['type'];
			// timestart
			$list['timestart'] = $good['timestart'];
			// timeend
			$list['timeend'] = $good['timeend'];
			// 单笔最大购买数量
			$list['max_buy_quantity'] = $good['max_buy_quantity'];
			// 品牌
			$brand = mysqld_select("SELECT * FROM ".table('shop_brand')." WHERE id=".$good['brand']);
			$list['brand'] = $brand['brand'];
			// 国家
			$country = mysqld_select("SELECT * FROM ".table('shop_country')." WHERE id=".$brand['country_id']);
			$list['country'] = $country['name'];
			$list['country_icon'] = download_pic($country['icon']);
			// 购物车商品数量
			if (!empty($member)) {
				$list['shoppingcart_num'] = countCartProducts($member['openid']);
			}
			// 是否收藏
			if (!empty($member['openid'])) {
				$where = "openid='".$member['openid']."' AND dish_id=".$good['id'];
				$have_c = mysqld_select("SELECT * FROM ".table('goods_collection')." WHERE ".$where);
				if (!empty($have_c)) {
					if ($have_c['deleted'] == 0) {
						$list['is_collection'] = 1;
					}else{
						$list['is_collection'] = 0;
					}
				}else{
					$list['is_collection'] = 0;
				}
			}else{
				$list['is_collection'] = 0;
			}
			// 活动
			$list['activity'] = array();
			$promotion = mysqld_selectall("select * from ".table('shop_pormotions')." where starttime<=:starttime and endtime>=:endtime",array(':starttime'=>TIMESTAMP,':endtime'=>TIMESTAMP));
			if (!empty($promotion)) {
				foreach ($promotion as &$pro) {
					if ($pro['promoteType'] == 1) {
						// 满金额包邮
						$ar = array();
						$ar['promoteType'] = 1;
						$ar['condition'] = $pro['condition'];
						$list['activity'][] = $ar;
					}elseif ($pro['promoteType'] == 0) {
						// 满件数包邮
						$ar = array();
						$ar['promoteType'] = 0;
						$ar['condition'] = $pro['condition'];
						$list['activity'][] = $ar;
					}
				}
			}
			// 商品状态(上架/下架)
			$list['status'] = $good['status'];
			// 详情图
			if ($op == 'content') {
				// 通用详情头尾
				$head = mysqld_select("SELECT * FROM ".table('config')." where name=:uname", array('uname' => 'detail_head'));
				$foot = mysqld_select("SELECT * FROM ".table('config')." where name=:uname", array('uname' => 'detail_foot'));
				preg_match_all("<img.*?src=\"(.*?.*?)\".*?>",$head['value'],$match_head);
				$list['content_head'] = $match_head[1];
				preg_match_all("<img.*?src=\"(.*?.*?)\".*?>",$foot['value'],$match_foot);
				$list['content_foot'] = $match_foot[1];

				preg_match_all("<img.*?src=\"(.*?.*?)\".*?>",$good['content'],$match);
				$list['content'] = $match[1];
			}
			// 团购
			if ($good['type'] == 1) {
				update_group_status($dish_id);
				// if (!empty($member['openid'])) {
				// 	$isa = isAddedTeamBuyGroup($good['id'], $member['openid']);
				// }else{
				// 	$isa = false;
				// }
				$isa = false;
				if (!$isa) {
					$group = mysqld_selectall("SELECT a.*, b.nickname, b.avatar, b.mobile, b.realname FROM ".table('team_buy_group')." as a left join ".table('member')." as b on a.creator=b.openid WHERE a.dish_id=".$dish_id." AND a.status=2 ORDER BY a.createtime DESC");
					foreach ($group as &$g_v) {
						if (empty($g_v['nickname'])) {
							if (empty($g_v['mobile'])) {
								$g_v['nickname'] = $g_v['realname'];
							}else{
								$g_v['nickname'] = $g_v['mobile'];
							}
						}
						$g_v['nickname'] = substr_cut($g_v['nickname']);
						$group_man = mysqld_select("SELECT COUNT(*) as num FROM ".table('team_buy_member')." WHERE group_id=".$g_v['group_id']);
						$g_v['residue_num'] = (int)$good['team_buy_count'] - $group_man['num'];
						$g_v['now_num'] = (int)$group_man['num'];
					}
					unset($g_v);
				}
				// 是否抽奖团
				$list['draw'] = $good['draw'];
				// 抽奖人数
				$list['draw_num'] = $good['draw_num'];
				// 团购有效期
				$list['team_buy_expiry'] = TEAM_BUY_EXPIRY;
				// 该商品单个团参团人数上限
				$list['team_buy_count'] = $good['team_buy_count'];
				// 该商品总参团人数
				$all_group_num = mysqld_select("SELECT COUNT(*) as num FROM ".table('team_buy_group')." as a left join ".table('team_buy_member')." as b on a.group_id=b.group_id WHERE a.status IN (1,2) AND a.dish_id=".$dish_id);
				// dump($all_group_num);
				$list['all_group_num'] = $all_group_num['num'];
			}
			// 团购信息
			$list['group'] = $group;
			// 卖家openid
			if (!empty($_GP['shopid'])) {
				$seller_openid = mysqld_select("SELECT openid FROM ".table('openshop')." WHERE id=".intval($_GP['shopid']));
				$list['seller_openid'] = $seller_openid['openid'];
				$list['good_url'] = get_wapgoods_url($seller_openid['openid'], $dish_id);
				$shop_good = mysqld_select("SELECT status as shop_good_status FROM ".table('openshop_relation')." WHERE openshopid=".intval($_GP['shopid'])." AND goodid=".$dish_id);
				if ($shop_good) {
					$list['shop_good_status'] = $shop_good['shop_good_status'];
				}else{
					$list['shop_good_status'] = NULL;
				}
			}else{
				// 当前用户
				if (!empty($member['openid'])) {
					$shop_sta = mysqld_select("SELECT b.status as bs FROM ".table('openshop')." as a left join ".table('openshop_relation')." as b on a.id=b.openshopid WHERE a.openid='".$member['openid']."' AND b.goodid=".$dish_id);
					$seller_openid = mysqld_select("SELECT openid FROM ".table('openshop')." WHERE openid='".$member['openid']."'");
					$list['seller_openid'] = $seller_openid['openid'];
					$list['shop_good_status'] = $shop_sta['bs'];
				}else{
					$list['seller_openid'] = NULL;
					$list['shop_good_status'] = NULL;
				}
				$list['good_url'] = get_wapgoods_url(NULL, $dish_id);
			}
			// 优惠券信息
			$list['bonus'] = array();
			// 首先查找针对该商品的优惠券
			$good_bouns = mysqld_selectall("SELECT b.type_id, b.type_name, b.type_money, b.send_type, b.send_start_date, b.send_end_date, b.use_start_date, b.use_end_date, b.min_goods_amount FROM ".table('bonus_good')." as a left join ".table('bonus_type')." as b on a.bonus_type_id=b.type_id WHERE a.good_id=".$dish_id." AND b.deleted=0 AND b.app_show=1 AND b.send_type=1 AND b.send_start_date<".time()." AND b.send_end_date>".time()." ORDER BY b.type_money ASC");
			if (!empty($good_bouns)) {
				// $gb_ary = "(";
				foreach ($good_bouns as $gbv) {
					$list['bonus'][] = $gbv;
					// $gb_ary .= $gbv['type_id'].',';
				}
				// $gb_ary = substr($gb_ary,0,strlen($gb_ary)-1).')';
			}
			// 查找满减的优惠卷
			$other_bouns = mysqld_selectall("SELECT type_id, type_name, type_money, send_type, send_start_date, send_end_date, use_start_date, use_end_date, min_goods_amount FROM ".table('bonus_type')." WHERE deleted=0 AND send_type=2 AND b.app_show=1 AND send_start_date<".time()." AND send_end_date>".time()." ORDER BY type_money ASC");
			if (!empty($other_bouns)) {
				foreach ($other_bouns as $obv) {
					$list['bonus'][] = $obv;
				}
			}
			if (!empty($member['openid'])) {
				// 标记已领取的优惠券
				$is_get_bouns = mysqld_selectall("SELECT bonus_type_id FROM ".table('bonus_user')." WHERE deleted=0 AND openid='".$member['openid']."'");
				foreach ($list['bonus'] as &$lbv) {
					$lbv['is_get'] = 0;
					foreach ($is_get_bouns as $igv) {
						if ($igv['bonus_type_id'] == $lbv['type_id']) {
							$lbv['is_get'] = 1;
						}
					}
				}
				unset($lbv);
			}

			if ($good['status'] == 0) {
				$result['message'] 	= "商品已下架!";
			}
			$result['data']['detail_list'] = $list;
			$result['code'] = 1;
			// dump($result);
		}
	}

	// dump($result);
	echo apiReturn($result);
	exit;