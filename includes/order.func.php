<?php
/**
* 订单相关操作
*/


/**
 * 生成订单编号
 * 
 * @return string 订单编号
 */
function createOrdersns()
{
	return 'SN'.date('Ymd') . random(6, 1);
}


// 获取多个订单
function get_orders($where='', $pindex=1, $psize = 10) {
	if (where=='') {
		return false;
	}
	$result = mysqld_selectall("SELECT a.* FROM ".table('shop_order')." as a left join ".table('shop_order_goods')." as b on a.id=b.orderid WHERE ".$where." GROUP BY a.id ORDER BY a.createtime DESC LIMIT ".($pindex - 1) * $psize . ',' . $psize);
	// 总记录数
    // $total = mysqld_select("SELECT FOUND_ROWS() as total;");
    if (!empty($result)) {
        foreach ($result as $ok => &$ov) {
        	// $ov['createtime'] = date('Y-m-d H:i:s', $ov['createtime']);
        	$mem = mysqld_select("SELECT * FROM ".table('member')." WHERE openid='".$ov['openid']."'");
            $ov['buy_name'] = $mem['realname'];
            $ov['buy_mobile'] = $mem['mobile'];
            $ov['retag'] = json_decode($ov['retag']);
            // 店铺信息
            $shop = mysqld_select("SELECT a.*,b.free_dispatch as bfree,b.express_fee,b.limit_send FROM ".table('store_shop')." as a left join ".table('store_extend_info')." as b on a.sts_id=b.store_id WHERE a.sts_id=".$ov['sts_id']);
            $ov['shop_name'] = $shop['sts_name'];
            $ov['shop_avatar'] = $shop['sts_avatar'];
            // 订单关闭时长（秒）
            $ov['close_duration'] = 900;
            $order_goods = mysqld_selectall("SELECT * FROM ".table('shop_order_goods')." WHERE orderid=".$ov['id']." ORDER BY createtime desc");
            $ov['goods'] = $order_goods;
            if (!empty($ov['goods'])) {
            	$is_com = 1;
                foreach ($ov['goods'] as &$odgsv) {
                	if ($odgsv['iscomment'] == 0 OR $odgsv['iscomment'] == '0') {
                		$is_com = 0;
                	}
                    $odgsv['good'] = mysqld_select("SELECT * FROM ".table('shop_dish')." WHERE id=".$odgsv['dishid']);
                }
                unset($odgsv);
                if ($is_com == 0) {
                	$ov['order_comment'] = 0;
                }else{
                	$ov['order_comment'] = 1;
                }
            }
        }
        unset($ov);
    } else {
        return false;
    }
    
    return $result;
}

// 获取订单数量
function get_order_num($where='') {
	if ($where=='') {
		return false;
	}
	$result = mysqld_selectall("SELECT SQL_CALC_FOUND_ROWS a.* FROM ".table('shop_order')." as a left join ".table('shop_order_goods')." as b on a.id=b.orderid WHERE ".$where." GROUP BY a.id ORDER BY a.createtime DESC LIMIT 1");
	// 总记录数
    $total = mysqld_select("SELECT FOUND_ROWS() as total;");

    return $total['total'];
}

// 获取单个订单
function get_order($array=array()) {
    if (!empty($array['where'])){
        $where = $array['where'];
    }
    $list = mysqld_select("SELECT id, status, taxprice, dispatchprice, price, isprize, isdraw, addressid, address_address, address_province, address_realname, address_city, address_mobile, address_area, openid, ordersn, dispatch, goodsprice, hasbonus, bonusprice, has_balance, balance_sprice, freeorder_price, updatetime, express, expresssn, expresscom, createtime, paytime, sendtime, completetime, closetime, ifcustoms FROM ".table('shop_order')." WHERE $where");
    if ($list['has_balance'] == '1') {
    	// (string)$list['price'] += $list['balance_sprice'];
    	// $list['price'] = (string)$list['price'];
    }else{
    	$list['balance_sprice'] = '0';
    }

    $group = mysqld_select("SELECT a.group_id, a.status as group_status FROM ".table('team_buy_group')." as a left join ".table('team_buy_member')." as b on a.group_id=b.group_id WHERE b.order_id=".$list['id']);

    $order_goods = mysqld_selectall("SELECT id, goodsid, optionid, status, type, shop_type, total, commision, seller_openid, price as goodprice , iscomment FROM ".table('shop_order_goods')." WHERE orderid=".$list['id']);

    $member = mysqld_select("SELECT openid, realname, nickname, mobile FROM ".table('member')." WHERE openid='".$list['openid']."'");

    if (empty($order_goods)) {
    	return false;
    }

    $list['sum_commision'] = 0;

    foreach ($order_goods as $ok => &$ov) {
    	update_group_status($ov['goodsid']);
    	$good = get_good(array(
                "table"=>"shop_dish",
				"where"=>"a.id = ".$ov['goodsid'],
			));
    	$ov['title'] = $good['title'];
    	$ov['thumb'] = $good['thumb'];
    	$ov['p1'] = $good['p1'];
    	$ov['p2'] = $good['p2'];
    	$ov['p3'] = $good['p3'];
    	$ov['productprice'] = (float)$good['productprice'];
		$ov['marketprice'] =(float)$good['marketprice'];
		$ov['timeprice'] = (float)$good['timeprice'];
		if (empty($ov['goodprice'])) {
			$ov['goodprice'] = $ov['marketprice'];
		}
		$ov['draw'] = $good['draw'];
		$list['sum_commision'] += $ov['commision'];
		$shop = mysqld_select("SELECT shopname FROM ".table('openshop')." WHERE openid='".$ov['seller_openid']."'");
		$ov['shopname'] = $shop['shopname'];
		//属性	
        $option = mysqld_select("select title,marketprice,weight,stock from " . table("shop_goods_option") . " where id=:id limit 1", array(
            ":id" => $ov['optionid']
        ));
        if ($option) {
            $ov['title'] = "[" . $option['title'] . "]" . $ov['title'];
            $ov['marketprice'] = $option['marketprice'];
        }
    }
    unset($ov);
    
    // $goodsid = mysqld_selectall("SELECT goodsid,total FROM " . table('shop_order_goods') . " WHERE orderid = '{$list['id']}'", array() , 'goodsid');
    $list['group'] = $group;
    $list['goods'] = $order_goods;
    // $list['total'] = $goodsid;
    $list['member'] = $member;
    $dispatch = fetch_expressage($list['express'], $list['expresssn']);
    if ($list['express'] == 'beihai') {
    	$b_num = count($dispatch['data']) - 1;
    	$list['dispatch_message'] =  $dispatch['data'][$b_num];
    }else{
    	$list['dispatch_message'] =  $dispatch['data'][0];
    }
    return $list;
}

// 修改订单状态
function update_order_status($id, $status) {
    if (empty($id) or empty($status)) {
        return false;
    }

    $order_goods = mysqld_selectall("SELECT * FROM ".table('shop_order_goods')." WHERE orderid=".$id);
    $order = mysqld_select("SELECT * FROM ".table('shop_order')." WHERE id=".$id);

    if ($status == -1) {
    	// 取消订单
    	foreach ($order_goods as $ogv) {
    		operateStoreCount($ogv['dishid'],$ogv['total'],$ogv['ac_dish_id'],2);
    	}
    	// 反还优惠券
    	if (!empty($order['hasbonus'])) {
    		mysqld_query("UPDATE ".table('store_coupon_member')." SET status=0,use_time =0,order_price='',ordersn='' WHERE scmid=".$order['hasbonus']);
    	}
    	// 记录订单关闭时间
    	mysqld_query("UPDATE ".table('shop_order')." SET status=-1,closetime=".time()." WHERE id=".$id);
    }elseif ($status == 3) {
    	// 确认收货
    	mysqld_query("UPDATE ".table('shop_order')." SET status=3,completetime=".time()." WHERE id=".$id);
		if($order['price']>0)
		{
			$setting = globaSetting();
			//商家所得去除费率  录入一笔冻结资金  退款要扣掉  完成收货要变成余额
			$pay_rate    = $setting['pay_rate']/100;
			$store_money = intval($order['price'] - $pay_rate*$order['price']);  //单位是分
			$store = member_store_getById($order['sts_id'],'freeze_money,recharge_money');
			$freeze_money    = max(0,$store['freeze_money'] - $store_money);  //冻结扣除
			$recharge_money  = $store['recharge_money'] + $store_money;       //余额添加
			mysqld_update('store_shop', array(
				'freeze_money'   => $freeze_money,
				'recharge_money' => $recharge_money
			), array('sts_id' => $order['sts_id']));
		}

		//推荐人所属的店铺 得到一笔冻结的推广佣金    退款要扣掉  完成收货要变成余额
		if(!empty($order['recommend_sts_id']) && !empty($order['store_earn_price'])){
			$earn_price = $order['store_earn_price']; //单位分
			$store = member_store_getById($order['recommend_sts_id'],'freeze_money,recharge_money');
        	$freeze_money    = max(0,$store['freeze_money'] - $earn_price);  //冻结扣除
			$recharge_money  = $store['recharge_money'] + $earn_price;       //余额添加
        	mysqld_update('store_shop', array(
				'freeze_money'   => $freeze_money,
				'recharge_money' => $recharge_money
			), array('sts_id' => $order['recommend_sts_id']));
		}
		//推荐人得到跟店铺 所承诺的 提成收入 冻结收入   退款要扣掉  完成收货要变成未结算
		if(!empty($order['recommend_openid']) && !empty($order['member_earn_price'])){
			$earn_price  = $order['member_earn_price']; //单位分
			$member = member_get($order['recommend_openid'],'freeze_gold,wait_glod');
			//以免扣掉时为负数
        	$freeze_gold  = max(0,$member['freeze_gold'] - $earn_price);  //冻结扣除
        	$wait_glod    = $member['wait_glod'] + $earn_price;           //未结算添加
        	mysqld_update('member', array(
				'freeze_gold' => $freeze_gold,
				'wait_glod'   => $wait_glod,
			), array('openid' => $order['recommend_openid']));
		}
		//paylog的订单 更新为已经收获
		mysqld_update('member_paylog',array('order_status'=>1),array('openid'=>$order['openid'],'orderid'=>$id));
    }
}

/**
 * 支付成功后，操作佣金、记录账单，平台费率 等处理
 * 佣金抽的是商品的 已经计算好了  就是order_goods中的商品价 - 推广价
 * 费率是总支付中抽的 那么去除费率后，剩下的就是 商家所得
 * 商家会自行看到佣金提成，会自己打款给推广用户
 */
function paySuccessProcess($orderid,$setting)
{
	$order   = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE id=:orderid", array(':orderid'=>$orderid));
	if(empty($order['id'])) {
		return '';
	}
	if($order['status'] !=0 ) {
		return '';
	}
	//更新订单为支付
	mysqld_update('shop_order', array('status'=>1,'paytime'=>time()), array('id' =>  $order['id']));
	//有实付金额（这里从数据库取出来的单位是分）  记录用户支付的账单
	if($order['price']>0)
	{
		//增加账单记录  扣除一笔资金 不是真扣余额 只是记录账单，因为扣款发生在第三方如支付宝
		$mark = LANG('LOG_SHOPBUY_TIP','paylog');
		member_gold($order['openid'],$order['price'],'-1',$mark,0,$order['id']);

		//商家所得去除费率  录入一笔冻结资金  退款要扣掉  完成收货要变成余额
		$pay_rate    = $setting['pay_rate']/100;
		$store_money = intval($order['price'] - $pay_rate*$order['price']);  //单位是分
		$mark = LANG('LOG_SHOPBUY_TIP_SELLER','paylog');
		store_freeze_gold($order['sts_id'],$store_money,1,$mark);
	}

	//推荐人所属的店铺 得到一笔冻结的推广佣金    退款要扣掉  完成收货要变成余额
	if(!empty($order['recommend_sts_id']) && !empty($order['store_earn_price'])){
		$earn_price = $order['store_earn_price']; //单位分
		$remark     = LANG('LOG_SHOPBUY_TIP_SELLER','paylog');
		store_commisiongold($order['recommend_sts_id'],$order['recommend_openid'],$earn_price,3,$order['id'],$remark);
	}
	//推荐人得到跟店铺 所承诺的 提成收入 冻结收入   退款要扣掉  完成收货要变成未结算
	if(!empty($order['recommend_openid']) && !empty($order['member_earn_price'])){
		$earn_price  = $order['member_earn_price']; //单位分
		$remark      = LANG('LOG_SHOPBUY_TIP_SELLER','paylog');
		//注意该方法的使用  推荐人要加入佣金，第一个参数是 推荐人的openid
		member_commisiongold($order['recommend_openid'],$order['openid'],$earn_price,3,$order['id'],$remark);
	}
}


#############################################订单生成前的信息  start#############################################

/**
 * app 1.0 返回从购物车下单的订单信息
 *
 * @param $cart_ids : array 购物车ID
 * @param $openid : 用户ID
 * @param $orderBy : 购物车排序SQL(库存不足时，该商品分配给最早添加到购物车的店铺使用)
 *
 */
function getConfirmOrderInfoByCart($cart_ids, $openid,$orderBy) {

	$goodsprice = 0; 			// 商品总金额
	$taxtotal 	= 0; 			// 税收总额
	$ships 		= 0; 			// 运费总额
	$allgoods 	= array (); 	// 商品列表
	$result 	= array (); 	// 返回值数组
	$issendfree	= 0;			// 是否免邮
	$ifcustoms  = 0;			// 是否需要清关材料

	$cartSql =  "SELECT c.id,c.goodsid,c.total,c.seller_openid,s.id as shop_id,s.shopname FROM " . table ( 'shop_cart' ) . " c ";
	$cartSql.= " left join " . table('openshop') . " s on s.openid=c.seller_openid ";
	$cartSql.= " WHERE c.id in(" . implode ( ',', $cart_ids ) . ") and c.session_id='" . $openid . "' ";
	$cartSql.= $orderBy;

	$list = mysqld_selectall ( $cartSql );

	if (! empty ( $list )) {

		$total 		= 0;			//商品件数
		$arrOutStock= array();		//超出库存的商品ID数组

		foreach ( $list as $g ) {
			$item = get_good (array('table' => 'shop_dish',
					'where' => 'a.id=' . $g ['goodsid'] . ' and a.deleted=0 and a.status=1 and a.total>0 ',
					'field' => 'a.id,a.gid,a.taxid,a.title,a.productprice,a.marketprice,a.thumb,a.istime,a.timeprice,a.type,a.timestart,a.timeend,a.team_buy_count,a.max_buy_quantity,a.status,a.total as quantity,a.issendfree,a.pcate,a.commision,b.title as btitle,b.thumb as imgs,b.productprice as price, b.marketprice as market, b.description as desc2'
			) );

			if (empty ( $item )) {
				continue;
			}
			//同一个商品所属不同店家超出库存时
			elseif(in_array($item['id'], $arrOutStock))
			{
				continue;
			}

			// 初始化税率金额
			$taxprice = 0;

			// 对购买数量进行处理，如果购买数量大于库存，则将购买数量设置为库存
			if ($g ['total'] > $item ['quantity']) {
				$g ['total'] = $item ['quantity'];

				$arrOutStock[] = $item ['id'];
			}
			// 有设置限购件数并且大于限购件数
			elseif ($item ['max_buy_quantity'] > 0 && $g ['total'] > $item ['max_buy_quantity']) {
				$g ['total'] = $item ['max_buy_quantity'];

				$arrOutStock[] = $item ['id'];
			}

			$item ['total'] = $g ['total'];
				
				
			switch($item['type'])
			{
				case '1':		//团购商品
				case '2':		//秒杀商品
						
					$item ['marketprice'] = $item ['marketprice'];		//以一般商品价格购买

					break;
						
				case '3':		//今日特价
				case '4':		//限时促销

					// 获取商品的下单价格
					if ((empty ( $item ['timeend'] ) || (TIMESTAMP < $item ['timeend'])) && (TIMESTAMP >= $item ['timestart'])) {
						$item ['marketprice'] = $item ['timeprice'];
					}
						
					break;
						
				default:		//一般商品
						
					$item ['marketprice'] = $item ['timeprice'];
					break;
			}
				
			// 获得单品总价
			$item ['totalprice'] = $item ['total'] * $item ['marketprice'];
			// 打包税率费用初始化
			$taxarray = array (
					array (
							'taxid' => $item ['taxid'],
							'id' 	=> $item ['id'],
							'count' => $item ['total'],
							'price' => $item ['marketprice']
					)
			);
			$taxprice = get_taxs ( $taxarray );
			$taxprice = $taxprice ['all_sum_tax'];
			$item ['taxprice'] = $taxprice;
			$taxtotal += $taxprice; // 税收总额
				
			// 设置积分
			$item ['credit'] 		= $item ['total'] * $item ['credit_cost'];
			//设置卖家openid
			$item['seller_openid'] 	= $g['seller_openid'];
			//卖家店铺名
			$item['shopname'] 		= $g['shopname'];
				
			// 商品列表
			$allgoods [] = $item;
			// 获得订单总额
			$goodsprice += $item ['totalprice'];
			//订单商品总件数
			$total += $item ['total'];
		}

		if (! empty ( $allgoods )) {
				
			########### 获取运费     start ################
			$issendfree = $item['issendfree'];
			if (empty ( $issendfree )) {
				$issendfree = isPromotionFreeShips($goodsprice,$total);
			}
			// 获取运费
			$shipCost = shipcost($allgoods);
				
			//非免邮
			if(empty ( $issendfree ))
			{
				$ships = $shipCost['price'];
			}
			########### 获取运费     end  ################
				
			//清关材料
			$ifcustoms = $shipCost['ifcustoms'];
				
				
			$result ['data'] ['dish_list'] 	= $allgoods; 		// 商品列表
			$result ['data'] ['goodsprice'] = $goodsprice; 		// 商品总金额(不含税和运费)
			$result ['data'] ['taxtotal'] 	= $taxtotal; 		// 税收总额
			$result ['data'] ['ships'] 		= $ships; 			// 运费总额
			$result ['data'] ['ifcustoms'] 	= $ifcustoms; 		// 清关材料
			$result ['code'] 				= 1;
		} else {
			$result ['message'] = "没有可以购买的商品!";
			$result ['code'] 	= 0;
		}
	} else {

		$result ['message'] = "购物车商品不存在!";
		$result ['code'] 	= 0;
	}

	return $result;
}

/**
 * app3.0 返回从购物车下单的订单信息
 *
 * @param $cart_ids : array 购物车ID
 * @param $openid : 用户ID
 * @param $orderBy : 购物车排序SQL(库存不足时，该商品分配给最早添加到购物车的店铺使用)
 * 
 */
function getConfirmOrderInfoByCart3($cart_ids, $openid,$orderBy) {
	
	$goodsprice = 0; 			// 商品总金额
	$taxtotal 	= 0; 			// 税收总额
	$ships 		= 0; 			// 运费总额
	$allgoods 	= array (); 	// 商品列表
	$result 	= array (); 	// 返回值数组
	$issendfree	= 0;			// 是否免邮
	$ifcustoms  = 0;			// 是否需要清关材料

	$cartSql =  "SELECT c.id,c.goodsid,c.total,c.seller_openid,s.id as shop_id,s.shopname FROM " . table ( 'shop_cart' ) . " c ";
	$cartSql.= " left join " . table('openshop') . " s on s.openid=c.seller_openid ";
	$cartSql.= " WHERE c.id in(" . implode ( ',', $cart_ids ) . ") and c.session_id='" . $openid . "' ";
	$cartSql.= $orderBy;
	
	$list = mysqld_selectall ( $cartSql );

	if (! empty ( $list )) {
		
		$total 		= 0;			//商品件数
		$arrOutStock= array();		//超出库存的商品ID数组
		
		foreach ( $list as $g ) {
			$item = get_good (array('table' => 'shop_dish',
									'where' => 'a.id=' . $g ['goodsid'] . ' and a.deleted=0 and a.status=1 and a.total>0 ',
									'field' => 'a.id,a.gid,a.taxid,a.title,a.productprice,a.marketprice,a.app_marketprice,a.thumb,a.istime,a.timeprice,a.type,a.timestart,a.timeend,a.team_buy_count,a.max_buy_quantity,a.status,a.total as quantity,a.issendfree,a.pcate,a.commision,b.title as btitle,b.thumb as imgs,b.productprice as price, b.marketprice as market, b.description as desc2'
			) );
				
			if (empty ( $item )) {
				continue;
			}
			//同一个商品所属不同店家超出库存时
			elseif(in_array($item['id'], $arrOutStock))
			{
				continue;
			}
				
			// 初始化税率金额
			$taxprice = 0;
				
			// 对购买数量进行处理，如果购买数量大于库存，则将购买数量设置为库存
			if ($g ['total'] > $item ['quantity']) {
				$g ['total'] = $item ['quantity'];
				
				$arrOutStock[] = $item ['id'];
			}
			// 有设置限购件数并且大于限购件数
			elseif ($item ['max_buy_quantity'] > 0 && $g ['total'] > $item ['max_buy_quantity']) {
				$g ['total'] = $item ['max_buy_quantity'];
				
				$arrOutStock[] = $item ['id'];
			}
				
			$item ['total'] = $g ['total'];
			
			
			switch($item['type'])
			{
				case '1':		//团购商品
				case '2':		//秒杀商品
					
					$item ['app_marketprice'] = $item ['app_marketprice'];		//以一般商品价格购买
				
					break;
					
				case '3':		//今日特价
				case '4':		//限时促销
				
					// 获取商品的下单价格
					if((TIMESTAMP < $item ['timeend']) && (TIMESTAMP >= $item ['timestart'])) {
					//if ((empty ( $item ['timeend'] ) || (TIMESTAMP < $item ['timeend'])) && (TIMESTAMP >= $item ['timestart'])) {
						$item ['app_marketprice'] = $item ['timeprice'];
					}
					
					break;
					
				default:		//一般商品
					
					$item ['app_marketprice'] = $item ['app_marketprice'];
					break;
			}		
			
			// 获得单品总价
			$item ['totalprice'] = $item ['total'] * $item ['app_marketprice'];
			// 打包税率费用初始化
			$taxarray = array (
					array (
							'taxid' => $item ['taxid'],
							'id' 	=> $item ['id'],
							'count' => $item ['total'],
							'price' => $item ['app_marketprice']
					)
			);
			$taxprice = get_taxs ( $taxarray );
			$taxprice = $taxprice ['all_sum_tax'];
			$item ['taxprice'] = $taxprice;
			$taxtotal += $taxprice; // 税收总额
			
			// 设置积分
			$item ['credit'] 		= $item ['total'] * $item ['credit_cost'];
			//设置卖家openid
			$item['seller_openid'] 	= $g['seller_openid'];
			//卖家店铺名
			$item['shopname'] 		= $g['shopname'];
			
			// 商品列表
			$allgoods [] = $item;
			// 获得订单总额
			$goodsprice += $item ['totalprice'];
			//订单商品总件数
			$total += $item ['total'];
		}
		
		if (! empty ( $allgoods )) {
			
			########### 获取运费     start ################
			$issendfree = $item['issendfree'];
			if (empty ( $issendfree )) {
				$issendfree = isPromotionFreeShips($goodsprice,$total);
			}
			// 获取运费
			$shipCost = shipcost($allgoods);
			
			//非免邮
			if(empty ( $issendfree ))
			{
				$ships = $shipCost['price'];
			}
			########### 获取运费     end  ################
			
			//清关材料
			$ifcustoms = $shipCost['ifcustoms'];
			
			
			$result ['data'] ['dish_list'] 	= $allgoods; 		// 商品列表
			$result ['data'] ['goodsprice'] = $goodsprice; 		// 商品总金额(不含税和运费)
			$result ['data'] ['taxtotal'] 	= $taxtotal; 		// 税收总额
			$result ['data'] ['ships'] 		= $ships; 			// 运费总额
			$result ['data'] ['ifcustoms'] 	= $ifcustoms; 		// 清关材料
			$result ['code'] 				= 1;
		} else {
			$result ['message'] = "没有可以购买的商品!";
			$result ['code'] 	= 0;
		}
	} else {

		$result ['message'] = "购物车商品不存在!";
		$result ['code'] 	= 0;
	}

	return $result;
}


/**
 * app1.0 返回立即购买时的订单信息
 *
 * @param $dish_id 商品ID
 * @param $total 购买的商品件数
 * @param $buy_type 购买方式(0:单独购买  1:团购)
 * @param $seller_openid 卖家openid
 *
 * @return $result: array
 */
function getConfirmOrderInfoByNow($dish_id, $total,$buy_type,$seller_openid) {

	$goodsprice = 0; 			// 商品总金额
	$taxtotal 	= 0; 			// 税收总额
	$ships 		= 0; 			// 运费总额
	$allgoods 	= array (); 	// 商品列表
	$result 	= array (); 	// 返回值数组
	$issendfree	= 0;			// 是否免邮
	$ifcustoms  = 0;			// 是否需要清关材料

	// 获得产品信息
	$item = get_good (array('table' => 'shop_dish',
			'where' => 'a.id=' . $dish_id . ' and a.deleted=0 ',
			'field' => 'a.id,a.gid,a.taxid,a.title,a.productprice,a.marketprice,a.thumb,a.istime,a.timeprice,a.type,a.timestart,a.timeend,a.team_buy_count,a.max_buy_quantity,a.status,a.total as quantity,a.issendfree,a.pcate,a.commision,b.title as btitle,b.thumb as imgs,b.productprice as price, b.marketprice as market, b.description as desc2'
	) );

	//店铺信息
	$shop = mysqld_select ( "SELECT shopname FROM " . table ( 'openshop' ) . " WHERE openid='" . $seller_openid . "' " );

	if (empty ( $item )) {
		$result ['message'] = "抱歉，该商品不存在!";
		$result ['code'] = 0;

	} elseif (empty($item ['quantity'])) {

		$result ['message'] = "库存不足，无法购买！";
		$result ['code'] = 0;

	} elseif (! $item ['status']) {

		$result ['message'] = "抱歉，该商品已经下架，无法购买了！";
		$result ['code'] = 0;
	} else {
		// 获取数量如果为空则数量为1
		if (empty ( $total )) {
			$total = 1;
		}

		// 对购买数量进行处理，如果购买数量大于库存，则将购买数量设置为库存
		if ($total > $item ['quantity']) {
			$total = $item ['quantity'];
		}
		// 有设置限购件数并且大于限购件数
		elseif ($item ['max_buy_quantity'] > 0 && $total > $item ['max_buy_quantity']) {
			$total = $item ['max_buy_quantity'];
		}
		$item ['total'] = $total;

		// 进行促销价格和正常价格的比对
		if ((empty ( $item ['timeend'] ) || (TIMESTAMP < $item ['timeend'])) && (TIMESTAMP >= $item ['timestart'])) {

			//团购商品时
			if($item ['type']==1)
			{
				//以团购方式购买时
				if($buy_type)
				{
					$item ['marketprice'] = $item ['timeprice'];
				}
			}
			else{
				$item ['marketprice'] = $item ['timeprice'];
			}
		}
		//app端一般商品也用timeprice字段
		elseif($item ['type']==0)
		{
			$item ['marketprice'] = $item ['timeprice'];
		}

		// 获得单品总价
		$item ['totalprice'] = $total * $item ['marketprice'];
		// 打包税率费用初始化
		$taxarray = array (
				array (
						'taxid' => $item ['taxid'],
						'id' 	=> $item ['id'],
						'count' => $total,
						'price' => $item ['marketprice']
				)
		);
		$taxprice = get_taxs ( $taxarray );
		$taxprice = $taxprice ['all_sum_tax'];
		$item ['taxprice'] = $taxprice;
		$taxtotal = $taxprice;
		// 设置积分
		$item ['credit'] 		= $total * $item ['credit_cost'];
		//设置卖家openid
		$item['seller_openid']	= $seller_openid;
		//卖家店铺名
		$item['shopname']		= !empty($shop) ? $shop['shopname'] : null;

		// 商品列表
		$allgoods [] = $item;
		// 获得订单总额
		$goodsprice += $item ['totalprice'];

		########### 获取运费     start ################
		$issendfree = $item['issendfree'];
		if (empty ( $issendfree )) {
			$issendfree = isPromotionFreeShips($goodsprice,$total);
		}
		// 获取运费
		$shipCost = shipcost($allgoods);

		//非免邮
		if(empty ( $issendfree ))
		{
			$ships = $shipCost['price'];
		}
		########### 获取运费     end  ################

		//清关材料
		$ifcustoms = $shipCost['ifcustoms'];

		$result ['data'] ['dish_list'] 		= $allgoods; 		// 商品列表
		$result ['data'] ['goodsprice'] 	= $goodsprice; 		// 商品总金额(不含税和运费)
		$result ['data'] ['taxtotal'] 		= $taxtotal; 		// 税收总额
		$result ['data'] ['ships'] 			= $ships; 			// 运费总额
		$result ['data'] ['ifcustoms'] 		= $ifcustoms; 		// 清关材料
		$result ['code'] 					= 1;
	}

	return $result;
}

/**
 * app3.0 返回立即购买时的订单信息
 *
 * @param $dish_id 商品ID
 * @param $total 购买的商品件数
 * @param $buy_type 购买方式(0:单独购买  1:团购)
 * @param $seller_openid 卖家openid
 * 
 * @return $result: array
 */
function getConfirmOrderInfoByNow3($dish_id, $total,$buy_type,$seller_openid) {
	
	$goodsprice = 0; 			// 商品总金额
	$taxtotal 	= 0; 			// 税收总额
	$ships 		= 0; 			// 运费总额
	$allgoods 	= array (); 	// 商品列表
	$result 	= array (); 	// 返回值数组
	$issendfree	= 0;			// 是否免邮
	$ifcustoms  = 0;			// 是否需要清关材料
	 
	// 获得产品信息
	$item = get_good (array('table' => 'shop_dish',
							'where' => 'a.id=' . $dish_id . ' and a.deleted=0 ',
							'field' => 'a.id,a.gid,a.taxid,a.title,a.productprice,a.marketprice,a.app_marketprice,a.thumb,a.istime,a.timeprice,a.type,a.timestart,a.timeend,a.team_buy_count,a.max_buy_quantity,a.status,a.total as quantity,a.issendfree,a.pcate,a.commision,b.title as btitle,b.thumb as imgs,b.productprice as price, b.marketprice as market, b.description as desc2'
						) );
	
	//店铺信息	
	$shop = mysqld_select ( "SELECT shopname FROM " . table ( 'openshop' ) . " WHERE openid='" . $seller_openid . "' " );

	if (empty ( $item )) {
		$result ['message'] = "抱歉，该商品不存在!";
		$result ['code'] = 0;
		
	} elseif (empty($item ['quantity'])) {
		
		$result ['message'] = "库存不足，无法购买！";
		$result ['code'] = 0;

	} elseif (! $item ['status']) {

		$result ['message'] = "抱歉，该商品已经下架，无法购买了！";
		$result ['code'] = 0;
	} else {
		// 获取数量如果为空则数量为1
		if (empty ( $total )) {
			$total = 1;
		}

		// 对购买数量进行处理，如果购买数量大于库存，则将购买数量设置为库存
		if ($total > $item ['quantity']) {
			$total = $item ['quantity'];
		}		
		// 有设置限购件数并且大于限购件数
		elseif ($item ['max_buy_quantity'] > 0 && $total > $item ['max_buy_quantity']) {
			$total = $item ['max_buy_quantity'];
		}
		$item ['total'] = $total;
		
		// 进行促销价格和正常价格的比对
		if ($item ['type']!=0 && ((TIMESTAMP < $item ['timeend']) && (TIMESTAMP >= $item ['timestart']))) {
		//if ((empty ( $item ['timeend'] ) || (TIMESTAMP < $item ['timeend'])) && (TIMESTAMP >= $item ['timestart'])) {
				
			//团购商品时
			if($item ['type']==1)
			{
				//以团购方式购买时
				if($buy_type)
				{
					$item ['app_marketprice'] = $item ['timeprice'];
				}
			}
			else{
				$item ['app_marketprice'] = $item ['timeprice'];	
			}
		}
		
		// 获得单品总价
		$item ['totalprice'] = $total * $item ['app_marketprice'];
		// 打包税率费用初始化
		$taxarray = array (
				array (
						'taxid' => $item ['taxid'],
						'id' 	=> $item ['id'],
						'count' => $total,
						'price' => $item ['app_marketprice']
				)
		);
		$taxprice = get_taxs ( $taxarray );
		$taxprice = $taxprice ['all_sum_tax'];
		$item ['taxprice'] = $taxprice;
		$taxtotal = $taxprice;
		// 设置积分
		$item ['credit'] 		= $total * $item ['credit_cost'];
		//设置卖家openid
		$item['seller_openid']	= $seller_openid;
		//卖家店铺名
		$item['shopname']		= !empty($shop) ? $shop['shopname'] : null;
		
		// 商品列表
		$allgoods [] = $item;
		// 获得订单总额
		$goodsprice += $item ['totalprice'];
		
		########### 获取运费     start ################
		$issendfree = $item['issendfree'];
		if (empty ( $issendfree )) {
			$issendfree = isPromotionFreeShips($goodsprice,$total);
		}
		// 获取运费
		$shipCost = shipcost($allgoods);
		
		//非免邮
		if(empty ( $issendfree ))
		{
			$ships = $shipCost['price'];
		}
		########### 获取运费     end  ################
		
		//清关材料
		$ifcustoms = $shipCost['ifcustoms'];

		$result ['data'] ['dish_list'] 		= $allgoods; 		// 商品列表
		$result ['data'] ['goodsprice'] 	= $goodsprice; 		// 商品总金额(不含税和运费)
		$result ['data'] ['taxtotal'] 		= $taxtotal; 		// 税收总额
		$result ['data'] ['ships'] 			= $ships; 			// 运费总额
		$result ['data'] ['ifcustoms'] 		= $ifcustoms; 		// 清关材料
		$result ['code'] 					= 1;
	}

	return $result;
}

/**
 * 是否促销免运费
 * 
 * @param $totalprice:商品总金额
 * @param $total:商品总件数
 * @return boolean
 */
function isPromotionFreeShips($totalprice,$total)
{
	$issendfree = 0;
	
	// ========促销活动===============
	$promotion = mysqld_selectall ( "select * from " . table ( 'shop_pormotions' ) . " where starttime<=:starttime and endtime>=:endtime", array (':starttime' => TIMESTAMP,':endtime' => TIMESTAMP) );
	
	
	foreach ( $promotion as $pro ) {
		//满额免运费
		if ($pro ['promoteType'] == 1) {
			if ($totalprice >= $pro ['condition']) {
					$issendfree = 1;
			}
		} 
		//满件免运费
		else if ($pro ['promoteType'] == 0) {
			if ($total >= $pro ['condition']) {
					$issendfree = 1;
			}
		}
	}
	
	return $issendfree;
}

#############################################订单生成前的信息  end#############################################

/**
 * @param $status
 * @param $list
 * @content 获取退货或者退款的数据
 */
function getBackMonryOrGoodData($status,&$list){
	if(!empty($list)){
		switch($status){
			case '-2'://退款中
				$arr = array(1,2);
				foreach($list as &$item){
					foreach($item['goods'] as $key => $goods){
						if($goods['order_type'] == 3 && in_array($goods['order_status'],$arr)){
							//不用操作  为了减少if 次数故这样比较好些
						}else{
							unset($item['goods'][$key]);
						}

					}
				}
				break;


			case '-4': //退货中
				$arr = array(1,2,3);
				foreach($list as &$item){
					foreach($item['goods'] as $key => $goods){
						if($goods['order_type'] == 1 && in_array($goods['order_status'],$arr)){

						}else{
							unset($item['goods'][$key]);
						}
					}
				}
				break;


			case '14': //退货完成
				foreach($list as &$item){
					foreach($item['goods'] as $key => $goods){
						if($goods['order_type'] == 1 && $goods['order_status'] == 4){

						}else{
							unset($item['goods'][$key]);
						}
					}
				}
				break;


			case '34'://退款完成
				foreach($list as &$item){
					foreach($item['goods'] as $key => $goods){
						if($goods['order_type'] == 3 && $goods['order_status'] == 4){

						}else{
							unset($item['goods'][$key]);
						}
					}
				}
				break;


			case '-321': //退款关闭
				$arr = array(-1,-2);
				foreach($list as &$item){
					foreach($item['goods'] as $key => $goods){
						if($goods['order_type'] == 3 && in_array($goods['order_status'],$arr)){
							//不用操作  为了减少if 次数故这样比较好些
						}else{
							unset($item['goods'][$key]);
						}

					}
				}
				break;

			case '-121': //退货关闭
				$arr = array(-1,-2);
				foreach($list as &$item){
					foreach($item['goods'] as $key => $goods){
						if($goods['order_type'] == 1 && in_array($goods['order_status'],$arr)){
							//不用操作  为了减少if 次数故这样比较好些
						}else{
							unset($item['goods'][$key]);
						}

					}
				}
				break;
		}
	}

}

function getOrderAfterSlaseUrl($show,$order_good_id,$order_id,$type){
//	op=aftersale_detail&order_good_id=78&type=money&orderid=59&name=shop&do=order
	$url = web_url('order',array(
		'op'		    => 'aftersale_detail',
		'order_good_id' => $order_good_id,
		'orderid' 		=> $order_id,
		'type'    		=> $type
	));
	return "<a href='{$url}'>{$show}</a>";
}

/**
 * @param $orderGoodInfo
 * @return bool
 * @content 存在物品有存在 退货中的不让确认收货
 */
function isSureGetGoods($orderGoodInfo,$type='type',$status='status'){
	$backing   = 0; //正在退货中个数
	$backed    = 0; //已经退货成功的
	$arr2 = array(1,2,3);
	foreach($orderGoodInfo as $item){
		if(in_array($item[$status],$arr2)){ //退款中的
			$backing ++;
		}
		if($item[$status] == 4)
			$backed++;
	}
	if($backing == 0){
		return true;
	}else if($backed == count($orderGoodInfo)){// 全部都退成功了 不用确认收货了
		return false;
	}else{
		return false;
	}
}

/**
 * @param $orderGoodInfo
 * @param string $type
 * @param string $status
 * @return bool
 * @content 取认发货前，如果有有一个商品还在退款中则 不操作
 */
function isSureSendGoods($orderGoodInfo,$type='type',$status='status'){
	$backmoney   = 0;
	$arr2 = array(1,2,3);
	foreach($orderGoodInfo as $item){
		if(in_array($item[$status],$arr2)){ //退款中的
			$backmoney ++;
		}
	}
	if($backmoney == 0){
		return true;
	}else{
		return false;
	}
}

/**
 * @param $orderGoodInfo
 * @param string $type
 * @param string $status
 * @return bool
 * @取消发货前 如果有有一个商品还在退款中 或者退款完成 则 不操作取消发货
 */
function isSureCancleGoods($orderGoodInfo,$type='type',$status='status'){
	$backing   = 0;
	$backed    = 0;
	$arr2 = array(1,2,3,4);
	foreach($orderGoodInfo as $item){
		if(in_array($item[$status],$arr2)){ //退款中的
			$backing ++;
		}
	}
	if($backing == 0){
		return true;
	}else{
		return false;
	}
}

function isSureOpenGoods($orderGoodInfo,$type='type',$status='status'){
	$backed = 0;
	foreach($orderGoodInfo as $item){
		if($item[$status] == 4)
			$backed++;
	}
	if($backed == count($orderGoodInfo)){
		return false;
	}else{
		return true;
	}
}

/**
 * 显示抽奖状态
 */
function showDrawOrderStatue($order){
	if(empty($order)){
		return false;
	}else if($order['status'] == 0){
		//未支付
		return false;
	}else{
		$stat = false;
		if($order['isdraw'] == 1){

			switch($order['isprize']){
				case 3:
					$stat = '待开奖';
					break;
				case 2:
					$stat = '未中奖';
					break;
				case 1:
					$stat = '已中奖';
					break;
			}
		}
		return $stat;
	}
}

/**
 * @param $form
 * @param $openid
 * @param $msg
 * @content 后台操作订单状态时  实时推送APP用户先关信息
 */
function pushOrderImMessage($form,$openid,$msg)
{
	$immsg     = array();
	$objOpenIm = new OpenIm();
	$immsg['from_user']	= $form;
	$immsg['to_users']	= $openid;
	$immsg['context']	= $msg;
	$objOpenIm->imMessagePush($immsg);
}

/**
 * @param $orderGoodInfo
 * @param $orderInfo
 * 团购失败 ，每一个order_goods进行退款时写入售后日志信息
 */
function group_buy_aftersale($orderGoodInfo,$orderInfo){
	$returnMoney = $orderInfo['price'];
	$data1 = array(
		'order_goods_id' => $orderGoodInfo['id'],
		'reason'         =>'组团失败，商家主动退款！',
		'description'    => '',
		'refund_price'   => $returnMoney,
		'createtime'     => date ( 'Y-m-d H:i:s' ),
		'modifiedtime'   => date ( 'Y-m-d H:i:s' ),
	);

	$arrLogContent['description']   = '组团失败，商家主动退款！';

	$data2 = array(
		'order_goods_id'  =>  $orderGoodInfo['id'],
		'status'          => 2,
		'title'           => "商家同意本次退款",
		'content'         => serialize($arrLogContent),
		'createtime'      => date ( 'Y-m-d H:i:s' )
	);
	mysqld_insert('aftersales',$data1);
	$aftersales_id = mysqld_insertid();
	if($aftersales_id) {
		$data2['aftersales_id'] = $aftersales_id;
		mysqld_insert('aftersales_log', $data2);
	}
}

/**
 * @param 
 * @param 
 * 自动处理订单列表数据
 *一般商品 和 今日特价 72小时自动关闭，
 * 活动商品 15分钟 自动关闭
 * 为方便操作，如果不填写$openid 则全表订单同时处理
 */
function order_auto_close(){
	//尽量处理当前有请求了，其他请求不用再挤进来操作
	$file = WEB_ROOT."/logs/order_file.txt";
	if(!file_exists($file)){
		return '';
	}
	//删掉 不让其他请求 一起来操作，当前操作过一次就行了
	unlink($file);
	$normal = 72 * 60 * 60;
	$where = ' status = 0 and ordertype !=4 and createtime <= '.(time()-$normal);
	$sql   = "select id,ordertype from ".table('shop_order')." where {$where}";
	$data  = mysqld_selectall($sql);

	$active = 15*60;
	$where2 = ' status = 0 and ordertype =4 and createtime <= '.(time()-$active);
	$sql2   = "select id,ordertype from ".table('shop_order')." where {$where2}";
	$data2  = mysqld_selectall($sql2);
	if($data){
		//每条每条 进行关闭，并退回优惠卷和余额
		foreach($data as $orderid){
			update_order_status($orderid['id'],-1);
		}
	}
	if($data2){
		//每条每条 进行关闭，并退回优惠卷和余额
		foreach($data2 as $orderid2){
			update_order_status($orderid2['id'],-1);
		}
	}
	file_put_contents($file,'请不要删除该文件，用于订单整站更新，防止并发处理');
}

/**
 * @param $orderlist  操作获取的所有的订单进行是否移除
 * @return mixed
 * @content 对于团购组团中 已经支付，不在待发货中显示
 * @content 同时团购成功但属于开奖中的也不在待发货中显示  仓库人员会误以为要发货
 */
function remove_ongroup_order($orderlist){
	$remove_num = 0;
	if(!empty($orderlist)){
		foreach($orderlist as $key => $row){
			if($row['ordertype'] == '1'){
				//为团购的开始判断该订单是否是组团中的 ，是的话要删掉暂时不显示在待发货中
				$sql = "select g.status,g.dish_id from ".table('team_buy_group')." as g left join ".table('team_buy_member')." as m";
				$sql .= " on g.group_id=m.group_id where m.order_id = {$row['id']}";
				$group = mysqld_select($sql);
				if(!empty($group)){
					if($group['status'] == '2'){
						$remove_num ++;
						unset($orderlist[$key]);
					}else if($group['status'] == '1'){
						//组团成功的订单，但是处于开奖中的则要移除掉该订单，不在待发货中显示
						$dish = mysqld_select("select draw from ".table('shop_dish')." where id={$group['dish_id']}");
						if(!empty($dish) && $dish['draw'] == 1){
							$remove_num ++;
							unset($orderlist[$key]);
						}
					}
				}
			}
		}
	}
	return array('list'=>$orderlist,'remove_num'=>$remove_num);
}

/**
 * @param $order  操作判断单个订单
 * @return bool
 * @content 对于团购组团中 已经支付，不能显示发货按钮， 和对应发货操作
 * @content 同时团购成功但属于开奖中的也不显示发货按钮 和对应发货操作
 * @content 未中奖的不显示发货按钮  和对应发货操作
 */
function checkGroupBuyCanSend($order){
	if($order['isprize'] == 2 || $order['isprize']==3){
		//未中奖单子 或者 待抽奖
		return false;
	}else if($order['isprize'] == 1){
		//中奖单子
		return true;
	}else if($order['isprize'] == 0){
		//未抽奖，不知道是否是团购单子还是普通单子 或者是不是抽奖团的单子
		if($order['ordertype']== '1'){
			//团购订单  中 组团中的已经支付，不显示发货按钮
			//组团成功，但是是属于抽奖团的不显示发货按钮
			$sql = "select g.status,g.dish_id from ".table('team_buy_group')." as g left join ".table('team_buy_member')." as m";
			$sql .= " on g.group_id=m.group_id where m.order_id = {$order['id']}";
			$group = mysqld_select($sql);
			if(!empty($group)){
				if($group['status'] == '2' && $order['status']==1){
					//组团中  已付款
					return false;
				}else if($group['status'] == '1'){
					//组团成功的订单，但是处于开奖中  不能发货
					$dish = mysqld_select("select draw from ".table('shop_dish')." where id={$group['dish_id']}");
					if(!empty($dish) && $dish['draw'] == 1){
						return false;
					}
				}
			}
		}

		return true;
	}

}

/**
 * @param $order
 * @return 返回数据格式 2016/12/30 15:50:00
 * 得到未付款的商品 最后应该付款的时间点，并返回给js 做倒计时用
 */
function getOrderTopayLastTime($order){
	if($order['status'] == 0){
		//等待付款的情况下，才显示倒计时时间
		if($order['ordertype'] == 0){
			//0为一般订单  一般订单三天内还可以支付
			$endtime = $order['createtime']+7200;
		}else{
			//活动商品 随着活动时间结束就关闭  不能支付
			$info = mysqld_select("select goodsid from ".table('shop_order')." where orderid={$order['id']}");
			$dish = mysqld_select("select timeend from ".table()." where id={$info['goodsid']}");
			if(!empty($dish['timeend']) && $dish['timeend'] > $order['createtime']){
				$endtime = $dish['timeend'];
		}else{
				$endtime = $order['createtime']+7200;
			}
		}
	}else{
		//不是代付款的不显示倒计时，则只返回下单时间，自然倒计时会跑动不起来
		$endtime = $order['createtime'];
	}
	return date("Y/m/d H:i:s",$endtime);
}

/**
 * @content 该方法用来记录后台订单操作的日志，存在retag字段中  数据格式
 * retag格式 {beizhu:送袜子,recoder:{支付人uid-理由-时间;发货人uid-信息-时间}},可扩展用分号分开
 * recode就是订单操作的整个记录，每次操作的记录用分号进行拼接进去。
 * @param $order_retag  订单中的retag
 * @param $reason       要存入记录中的文字信息
 */
function setOrderRetagInfo($order_retag,$reason){
	$retag = '';
	if(!empty($order_retag)){
		$retag = json_decode($order_retag,true);
	}

	$reason  = str_replace('-','',$reason);
	//$log     = $_SESSION['account']['id'].'-'.$reason.'-'.time();
	//现在是获取member表的openid
	$member = get_member_account(1);
	$log     = $member['openid'].'-'.$reason.'-'.time();
	if(empty($retag['recoder'])){
		$retag['recoder'] = $log;
	}else{
		//之前已经有日志了，直接拼接
		$retag['recoder'] .= ";".$log;
	}
	return json_encode($retag);
}

/**
 * 更新第三方平台订单的所有者
 * 
 * @param $mobile:手机号码
 * @param $openid:用户ID
 */
function updateThirdOrderOwner($mobile,$openid)
{
	//更改第三方订单的openid，只能更改openid值为空的记录
	return mysqld_query("UPDATE ".table('third_order')." SET openid='{$openid}' WHERE address_mobile = '{$mobile}' and (openid='' or openid IS NULL) and status=3 ");
}

/**
 * 确认收货方法提取  统一调用
 * @param $orderid
 * @return array  返回 数组中 errno不为200,则是操作失败
 */
function hasFinishGetOrder($orderid){
	$order         = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE id=:id",array(":id"=>$orderid));
	$orderGoodInfo = mysqld_selectall("select * from ". table('shop_order_goods') ." where orderid={$orderid}");
	if(!isSureGetGoods($orderGoodInfo)){
		return array('errno'=>1002,'message'=>'对不起，该订单有商品等待处理中，暂不允许完成订单!');
	}

	$json_retag = setOrderRetagInfo($order['retag'], '完成订单：确认收货完成订单');
	$res = mysqld_update('shop_order', array('status' => 3,'retag'=>$json_retag,'completetime'=>time()), array('id' => $orderid));
	if($res){
		//确认收获后 卖家得到佣金金额 买家收到积分  同时记录账单和APP消息推送
		// sureUserCommisionToMoney($orderGoodInfo,$order);
	}else{
		return array('errno'=>'1002','message'=>'订单操作失敗！');
	}
	return array('errno'=>'200','message'=>'订单操作成功！');
}

/**
 * 根据用户openid获取推荐人的openid
 * 以及推荐人从属的店铺id 以及店铺给子账户的提成比例
 * 有种情况就是 可能该推广员已经不做了。被移除了，之前的客户全部归属店铺
 * @param $openid
 * @return array
 */
function getRecommendOpenidAndStsid($openid){
	//获取推荐人openid  从属店铺id
	$recommend        = mysqld_select("select p_openid,p_sid from ".table('member_blong_relation')." where m_openid='{$openid}' and type=2");
	$recommend_openid = $recommend['p_openid'] ?: 0;
	$recommend_sts_id = $recommend['p_sid'] ?: 0;

	//根据推荐人的openidz 再次找到该用户当前管理的 店铺id  判断是否已经换店铺了
	$earn_rate = 0;
	if(!empty($recommend_openid)){
		$recommend_sts    = mysqld_select("select sts_id,earn_rate from ".table('seller_rule_relation')." where openid='{$recommend_openid}'");
		if($recommend_sts['sts_id'] == $recommend_sts_id){
			//该推荐人 就属于在该店铺工作
			$earn_rate  = $recommend_sts['earn_rate'] ?: 0;
			empty($earn_rate) && $recommend_openid = 0;
		}else{
			//该推荐人 已经不再该店铺工作了
			$recommend_openid = 0;
		}
	}
	return array(
		'recommend_openid' => $recommend_openid,
		'recommend_sts_id' => $recommend_sts_id,
		'earn_rate' 	   => $earn_rate,
	);
}

/**
 * 库存操作
 * @param $dishid
 * @param $buy_num
 * @param $ac_dish_id
 * @param $type  1 表示卖出扣掉库存  2表示 关闭 等 库存回放回去
 */
function operateStoreCount($dishid,$buy_num,$ac_dish_id,$type){
	if($type == 1){
		$sql1 = "update ".table('shop_dish')." set store_count=store_count-{$buy_num},sales_num=sales_num+{$buy_num}";
		$sql1.= " where id={$dishid}";
		mysqld_query($sql1);
		if($ac_dish_id){
			$sql = "update ".table('activity_dish')." set ac_dish_total=ac_dish_total-{$buy_num},ac_dish_sell_total=ac_dish_sell_total+{$buy_num}";
			$sql .= " where ac_dish_id={$ac_dish_id}";
			mysqld_query($sql);
		}
	}else if($type == 2){
		$sql1 = "update ".table('shop_dish')." set store_count=store_count+{$buy_num},sales_num=sales_num-{$buy_num}";
		$sql1.= " where id={$dishid}";
		mysqld_query($sql1);
		//当释放库存的时候 活动商品 有一种现象。。活动未开始，但是dish总库存不断少了，会影响到活动商品的库存被更新减少掉
		//所以当发生 库存再次添加回来，要判断活动商品中的库存是否 加回去 加回去要加多少
		if($ac_dish_id){
			$sql = "update ".table('activity_dish')." set ac_dish_total=ac_dish_total+{$buy_num},ac_dish_sell_total=ac_dish_sell_total-{$buy_num}";
			$sql .= " where ac_dish_id={$ac_dish_id}";
			mysqld_query($sql);
		}else{
			//当为空，可能是活动时间点还没到 可能下午16点的才开始，重新判断一下  取出该活动商品
			$active = getCurrentAct();
			if(!empty($active)) {
				//那么看看本次大场活动时间开始了么
				if ($active['ac_time_str'] < time()) {
					//有活动，那么判断该商品是不是属于限时购商品
					$sql = "select * from " . table('activity_dish');
					$sql .= " where ac_action_id={$active['ac_id']} and ac_shop_dish={$dishid}";
					$find = mysqld_select($sql);
					if(!empty($find)){
						//原始库存 和 当前库存
						$ac_dish_totalcount = $find['ac_dish_totalcount'];
						$ac_dish_total      = $find['ac_dish_total'] +$buy_num ;
						$buy_total  = min($ac_dish_totalcount,$ac_dish_total);  //库存释放 不能超过原始库存
						$sell_total = max(0,$find['ac_dish_sell_total']-$buy_num);  //去掉卖出件数，但不能低于0
						$sql = "update ".table('activity_dish')." set ac_dish_total={$buy_total},ac_dish_sell_total={$sell_total}";
						$sql .= " where ac_dish_id={$find['ac_dish_id']}";
						mysqld_query($sql);
					}
				}
			}
		}
	}
}

/**
 * 每次下单对比售卖的价格是否是历史最低，是的话，跟新历史最低价格
 * @param $dishid
 * @param $lower_price  传进来 是分
 * @param $time_price  传进来是分
 */
function compareDsihHistoryPrice($dishid,$lower_price,$time_price){
	if($lower_price == 0 || $time_price < $lower_price){
		$time_price = FormatMoney($time_price,1);  //转为分
		mysqld_update('shop_dish',array('history_lower_prcie'=>$time_price),array('id'=>$dishid));
	}
}