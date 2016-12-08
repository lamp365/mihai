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
// function get_orders($array=array()) {
//     if (!empty($array['where'])){
//         $where = $array['where'];
//     }
//     if (!empty($array['limit'])){
//         $limit = $array['limit'];
//     }
//     $list = mysqld_selectall("SELECT * FROM " . table('shop_order') . " WHERE $where ORDER BY id DESC LIMIT " .$limit, array() , 'id');

//     if (!empty($list)) {
//         foreach ($list as & $row) {
//             $goods = mysqld_selectall("SELECT g.id, g.title, g.thumb, o.price as marketprice,o.total,o.optionid,g.productprice FROM " . table('shop_order_goods') . " o left join " . table('shop_goods') . " g on o.goodsid=g.id " . " WHERE o.orderid='{$row['id']}'");
//             foreach ($goods as & $item) {
//                 //属性
//                 $option = mysqld_select("select title,marketprice,weight,stock from " . table("shop_goods_option") . " where id=:id limit 1", array(
//                     ":id" => $item['optionid']
//                 ));
//                 if ($option) {
//                     $item['title'] = "[" . $option['title'] . "]" . $item['title'];
//                     $item['marketprice'] = $option['marketprice'];
//                 }
//             }
//             unset($item);
//             $goodsid = mysqld_selectall("SELECT goodsid,total FROM " . table('shop_order_goods') . " WHERE orderid = '{$row['id']}'", array() , 'goodsid');
//             $row['goods'] = $goods;
//             $row['total'] = $goodsid;
//             $row['dispatch'] = mysqld_select("select id,dispatchname from " . table('shop_dispatch') . " where id=:id limit 1", array(
//                 ":id" => $row['dispatch']
//             ));
//         }
//     }else{
//         return array();
//     }

//     return $list;
// }

// 获取单个订单
function get_order($array=array()) {
    if (!empty($array['where'])){
        $where = $array['where'];
    }
    $list = mysqld_select("SELECT id, status, taxprice, dispatchprice, price, isprize, addressid, address_address, address_province, address_realname, address_city, address_mobile, address_area, openid, ordersn, dispatch, goodsprice, hasbonus, bonusprice, updatetime, express, expresssn, expresscom, createtime, paytime, sendtime, completetime, closetime, ifcustoms FROM ".table('shop_order')." WHERE $where");

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
function update_order_status($id, $status,$dishinfo='') {
    if (empty($id) or empty($status)) {
        return false;
    }
    $objOpenIm = new OpenIm();
    $order_goods = mysqld_selectall("SELECT * FROM ".table('shop_order_goods')." WHERE orderid=".$id);
    $order = mysqld_select("SELECT * FROM ".table('shop_order')." WHERE id=".$id);
    if (empty($dishinfo)) {
    	$dishinfo = mysqld_select("select * from ".table('shop_dish')." where id={$order_goods[0]['goodsid']}");
    }
    if ($status == -1) {
    	// 取消订单
    	foreach ($order_goods as $ogv) {
    		// 释放库存减掉销量
			$total = $dishinfo['total'] + $ogv['total'];
			$sales = $dishinfo['sales'] - $ogv['total'];
    		mysqld_query("UPDATE ".table('shop_dish')." SET total=".$total.", sales=".$sales." WHERE id=".$ogv['goodsid']);
    		// 如果已付款 订单有卖家openid，则扣除冻结佣金
    		if ($order['status'] == 1 && !empty($ogv['seller_openid'])) {
				$remark = "订单:{$order['ordersn']}已经取消了";
				member_goldinfo($ogv['seller_openid'],$ogv['commision'],'usegold',$remark,'freeze_gold',true);
			}
    	}
    	// 反还优惠券
    	mysqld_query("UPDATE ".table('bonus_user')." SET isuse=0 WHERE order_id=".$order['id']);
    	// 记录订单关闭时间
    	mysqld_query("UPDATE ".table('shop_order')." SET closetime=".time()." WHERE id=".$id);
    }elseif ($status == 3) {
    	// 确认收货
    	// 记录完成时间
    	mysqld_query("UPDATE ".table('shop_order')." SET completetime=".time()." WHERE id=".$id);
    	$seller_ary = array();
    	foreach ($order_goods as $ogv) {
    		if (!empty($ogv['seller_openid']) AND ($ogv['status'] == 0 OR $ogv['status'] == -1)) {
    			// 将卖家冻结资金移入余额
				$remark = "订单:{$order['ordersn']}已经确认收货";
				member_goldinfo($ogv['seller_openid'],$ogv['commision'],'addgold',$remark,'gold',true);
				member_goldinfo($ogv['seller_openid'],$ogv['commision'],'usegold',$remark,'freeze_gold',true);
	    		// 累计卖家积分
	    		$seller_ary[$ogv['seller_openid']]['num'] += 1;
	    	}
	    	$good = get_good(array(
                "table"=>"shop_dish",
				"where"=>"a.id = ".$ogv['goodsid'],
			));
	    	// 推送消息给卖家
	    	if(!empty($ogv['seller_openid']) && $objOpenIm->isImUser($ogv['seller_openid'])) {
	    		// 订单消息
	    		$immsg['from_user']	= IM_ORDER_FROM_USER;
				$immsg['to_users']	= $ogv['seller_openid'];
				$immsg['context']	= "老板 顾客确认收货啦～
订单编号:{$order['ordersn']}
购买商品:{$good['title']}
支付金额:{$order['price']}
实际收入:{$ogv['commision']}
成交时间:".date('Y-m-d H:i:s');
				
				$objOpenIm->imMessagePush($immsg);

				// 财富消息
				$immsg2['from_user'] = IM_WEALTH_FROM_USER;
				$immsg2['to_users']	= $ogv['seller_openid'];
				$immsg2['context'] = "老板 您的佣金{$ogv['commision']}已到账
订单编号:{$order['ordersn']}
购买商品:{$good['title']}
支付金额:{$order['price']}
实际收入:{$ogv['commision']}
成交时间:".date('Y-m-d H:i:s');
				
				$objOpenIm->imMessagePush($immsg2);
	    	}
    	}
    	// 计算积分
    	if (!empty($seller_ary)) {
    		foreach ($seller_ary as $sak => $sav) {
	    		if ($sav['num'] > 6) {
	    			$sav['num'] = 6;
	    		}
				$openshop = mysqld_select("select id,level from ".table('openshop')." where openid={$sak}");
				$level    = $openshop['level']+$sav['num'];
	    		mysqld_query("UPDATE ".table('openshop')." SET level=".$level." WHERE id=".$openshop['id']);
	    	}
    	}
    }elseif ($status == -2) {
    	// 团购失败 退款
		foreach ($order_goods as $ogv) {
			if ($order['status'] == 1) {
				// 如果已付款 修改退款状态
				// 反还优惠券
    			mysqld_query("UPDATE ".table('bonus_user')." SET isuse=0 WHERE order_id=".$id);
				mysqld_query("UPDATE ".table('shop_order_goods')." SET status=2,type=3 WHERE orderid=".$id);
				//记录售后日志
				group_buy_aftersale($ogv,$order);
			}else{
				//未付款的直接关闭
				mysqld_update("shop_order",array('closetime'=>time(),'status'=>-1) ,array('id'=>$id));
			}
    	}
    	// 不执行下面的更新
		return true;

    }
    $data = array('status' => $status);
    mysqld_update('shop_order', $data, array('id'=> $id));
}

/**
 * 支付成功后，推送相应信息、更新卖家佣金、记录账单等处理
 * 
 * @param $orderInfo: array 订单数组
 * 
 */
function paySuccessProcess($orderInfo)
{
	$objOpenIm = new OpenIm();

	//订单商品列表
	$arrGoods = mysqld_selectall("SELECT id,orderid,goodsid,seller_openid,commision FROM " . table('shop_order_goods') . " WHERE orderid = :orderid ", array(':orderid' => $orderInfo['id']));
	
	//商品名称
	$dishTitle= '';
	
	foreach ($arrGoods as $g) {
		
		$item = get_good (array('table' => 'shop_dish',
								'where' => 'a.id=' . $g ['goodsid'],
								'field' => 'a.id,a.taxid,a.title,a.productprice,a.marketprice,a.thumb,a.istime,a.timeprice,a.type,a.timestart,a.timeend,a.team_buy_count,a.max_buy_quantity,a.status,a.total,a.issendfree,a.pcate,b.title as btitle,b.thumb as imgs,b.productprice as price, b.marketprice as market, b.description as desc2'
						) );
		
		$dishTitle = $item['title'].'等商品';
		
		//从分销商处购买的商品
		if(!empty($g['seller_openid']))
		{
			//卖家佣金更新
			mysqld_query("UPDATE ".table('member')." SET freeze_gold=freeze_gold+'".$g['commision']."',earning=earning+'".$g['commision']."' WHERE openid ='".$g['seller_openid']."' ");
			
			$arrSellerBill = array('order_id'		=> $orderInfo['id'],
									'order_goods_id'=> $g['id'],
									'type'			=> 1,						//收入佣金
									'openid'		=> $g['seller_openid'],
									'money'			=> $g['commision'],
									'createtime'	=> time(),
									'modifiedtime'	=> time()
			);
			
			//卖家账单记录
			mysqld_insert ( 'bill', $arrSellerBill );
			
			if($objOpenIm->isImUser($g['seller_openid']))
			{
				$immsg['from_user']	= IM_ORDER_FROM_USER;
				$immsg['to_users']	= $g['seller_openid'];
				$immsg['context']	= "老板 顾客付款下单啦～
订单编号:{$orderInfo['ordersn']}
购买商品:{$item['title']}
下单时间:".date('Y-m-d H:i:s',$orderInfo['createtime'])."
付款方式:{$orderInfo['paytypename']}
支付金额:{$orderInfo['price']}
实际收入:{$g['commision']}
付款时间:".date('Y-m-d H:i:s');
				
				$objOpenIm->imMessagePush($immsg);
			}
		}
	}
	
	
	$arrBuyerBill = array('order_id'		=> $orderInfo['id'],
							'type'			=> 0,						//购买商品
							'openid'		=> $orderInfo['openid'],
							'money'			=> '-'.$orderInfo['price'],
							'createtime'	=> time(),
							'modifiedtime'	=> time()
	);
		
	//买家账单记录
	mysqld_insert ( 'bill', $arrBuyerBill );
	
	if($objOpenIm->isImUser($orderInfo['openid']))
	{
		//向买家推送消息
		$immsg['from_user']	= IM_ORDER_FROM_USER;
		$immsg['to_users']	= $orderInfo['openid'];
		$immsg['context']	= "客官，您好！我们已收到您的货款，开始为您打包商品，请耐心等待～
订单编号:{$orderInfo['ordersn']}
购买商品:{$dishTitle}
支付金额:{$orderInfo['price']}
下单时间:".date('Y-m-d H:i:s',$orderInfo['createtime']);
		
		$objOpenIm->imMessagePush($immsg);
	}
}


#############################################订单生成前的信息  start#############################################
/**
 * 返回从购物车下单的订单信息
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
 * 返回立即购买时的订单信息
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
 * @param $order_goods
 * @param $order
 * @conetent 确认收获后 冻结佣金变为金额 买家收到积分  同时记录账单和APP消息推送
 */
function sureUserCommisionToMoney($order_goods,$order){
	$arr          = array(-1,-2,0); //這些狀態才是發貨出去的，用戶能確認收貨
	$usermoney    = $list = array();
	$time         = date('Y-m-d H:i:s',$order['createtime']);
	$total_credit = 0;
	foreach($order_goods as $row){
		if(empty($row['commision']) || empty($row['seller_openid'])){
			//不做处理
		}else if(in_array($row['status'],$arr)){    //记录佣金是哪家商铺的
			if(array_key_exists($row['seller_openid'], $usermoney)){
				$usermoney[$row['seller_openid']][] = $row;
				$usermoney[$row['seller_openid']]['seller_commision'] += $row['commision'];
			}else{
				$usermoney[$row['seller_openid']][] = $row;
				$usermoney[$row['seller_openid']]['seller_commision'] = $row['commision'];
			}
		}

		$list[] = $row;

		if(!empty($row['credit']))
			$total_credit += $row['credit'];
	}

	//收货成功，买家得到积分   并记录账单
	if ($total_credit != 0) {
		member_credit($order['openid'], $total_credit, 'addcredit', '订单:'.$order['ordersn'].'完成新增积分');
	}

	//卖家冻结资金转为 金额  并且推送消息
	if(!empty($usermoney)){
		foreach($usermoney as $openid=>$data){
			$member  = mysqld_select("select freeze_gold,gold from ".table('member')." where openid='{$openid}'");
			if(empty($member))
				continue;

			$info = array(
				'freeze_gold' => $member['freeze_gold']-$data['seller_commision'],
				'gold' 		  => $member['gold']+$data['seller_commision'],
			);
			$res = mysqld_update('member',$info,array('openid'=>$openid));
			//记录账单，卖家佣金变为余额
			$data_log = array(
				'remark' 		=> '订单:'.$order['ordersn'].'佣金已转为余额',
				'type' 			=> '佣金转为余额',
				'fee' 			=> $data['seller_commision'],
				'account_fee'   => $member['gold'] + $data['seller_commision'],
				'createtime' 	=> TIMESTAMP,
				'openid' 		=> $openid
			);
			mysqld_insert('member_paylog', $data_log);


			//给卖家APP推送消息
			$total_yongjin = $data['seller_commision'];
			unset($data['seller_commision']);
			if($res){
				$num = 0;
				foreach($data as $row){
						$dishInfo = mysqld_select( "SELECT title FROM " . table ( 'shop_dish' )." WHERE id = {$row['goodsid']}");
						$title = $dishInfo['title'];
						$num++;

				}

				$msg = "老板，你的佣金{$total_yongjin}已到账
订单编号：{$order['ordersn']}
购买商品：{$title}等{$num}件商品
支付金额：{$order['price']}元
实际收入：{$total_yongjin}元
下单时间：{$time}";
				pushOrderImMessage(IM_WEALTH_FROM_USER,$openid,$msg);
			}
		}
	}


	//给买家推送APP消息
	if(empty($title)){
		$dishInfo = mysqld_select( "SELECT title FROM " . table ( 'shop_dish' )." WHERE id = {$list[0]['goodsid']}");
		$title = $dishInfo['title'];
	}
	$jian = count($order_goods);
	$msg  = "客官，你的订单已经确认收获~~
订单编号：{$order['ordersn']}
购买商品：{$title}等{$jian}件商品
下单时间：{$time}";
	pushOrderImMessage(IM_ORDER_FROM_USER,$order['openid'],$msg);
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

		//给APP卖家推送消息
		if(!empty($orderGoodInfo['seller_openid']) && !empty($orderGoodInfo['commision'])){
			$time     = date("Y-m-d H:i:s",$orderInfo['createtime']);
			$dishInfo = mysqld_select( "SELECT title FROM " . table ( 'shop_dish' )." WHERE id = {$orderGoodInfo['goodsid']}");
			$msg  = "老板，买家团购退款了！
订单编号：{$orderInfo['ordersn']}
退款商品：{$dishInfo['title']}
退款原因：成团失败
下单时间：{$time}";
			pushOrderImMessage(IM_ORDER_FROM_USER,$orderGoodInfo['seller_openid'],$msg);
		}
	}
}

/**
 * @param 
 * @param 
 * 自动处理订单列表数据
 *一般商品 和 今日特价 72小时自动关闭，团购秒杀关闭是在活动结束时关闭，不在这里处理
 * 为方便操作，如果不填写$openid 则全表订单同时处理
 */
function order_auto_close($openid = ''){
	$normal = 72 * 60 * 60;
	$where = ' (ordertype = 0 or ordertype=3) and status = 0 and createtime <= '.(time()-$normal);
    $info   = array(
		'status'=> -1,
		'closetime'=>time()
	);
	mysqld_update('shop_order',$info,$where);
}

/**
 * @param $orderlist
 * @return mixed
 * @content 移除掉组团中的订单 因为组团中已经支付的订单暂时不能显示在待发货中，仓库人员会误以为要发货
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
 * @param $order
 * @return bool
 * @content 对于团购 已经支付，不能显示发货按钮，同时团购属于开奖中的也不显示发货按钮  不能发货
 */
function checkGroupBuyCanSend($order){
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