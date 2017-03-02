<?php
/**
 * app3.0提交订单接口
 * 
 */
require_once WEB_ROOT.'/system/modules/plugin/payment/alipay/common.php';

$result = array ();

$member = get_member_account ( true, true );

if (!empty($member) AND $member != 3) {

	$operation 			= $_GP ['op'];
	$openid 			= $member ['openid']; 				// 用户ID
	$payment_id 		= intval ( $_GP ['payment_id'] ); 	// 支付方式ID
	$address_id 		= intval ( $_GP ['address_id'] ); 	// 地址ID
	$bonus_id			= intval ( $_GP ['bonus_id'] ); 	// 优惠券ID
	$team_buy_member_id = 0;								// 团购成员ID
	$group_id 			= intval ( $_GP ['group_id'] ); 	// 团购的group_id
	$use_balance 		= intval($_GP['use_balance'] ? $_GP['use_balance'] : 0);	// 是否使用余额抵扣

	// 支付方式ID
	if (empty ( $payment_id )) {
		
		$result ['message'] = "请选择支付方式";
		$result ['code'] = 0;
	} 
	elseif(empty($address_id))
	{
		$result ['message'] = "请选择收货地址";
		$result ['code'] = 0;
	}
	else {
		$address = mysqld_select ( "SELECT * FROM " . table ( 'shop_address' ) . " WHERE openid = :openid and deleted=0 and id=:id ", array (':openid' => $openid,':id'=>$address_id) );
		
		$identity = mysqld_select ( "SELECT * FROM " . table ( 'member_identity' ) . " WHERE openid = :openid and isdefault=1 and status=0", array (
				':openid' => $openid
		) );
		$payment = mysqld_select ( "SELECT * FROM " . table ( 'payment' ) . " WHERE id = :id and enabled=1 ", array (':id' => $payment_id) );
	
		// 地址不存在
		if (empty ( $address )) {
			$result ['message'] = "收货地址不存在";
			$result ['code'] 	= 0;
		}
		// 默认身份证不存在
		elseif (empty ( $identity )) {
			$result ['message'] = "请添加默认身份证";
			$result ['code'] 	= 0;
		}
		// 支付方式不存在
		elseif (empty ( $payment )) {
				
			$result ['message'] = "支付方式不存在";
			$result ['code'] 	= 0;
				
		} else {
			
			switch ($operation)
			{
				case 'buy_now':				//立即购买
						
					$dish_id 		= intval ( $_GP ['dish_id'] ); 		// 商品ID
					$total 			= intval ( $_GP ['total'] ); 		// 购买的商品件数
					$buy_type 		= intval ( $_GP ['buy_type'] ); 	// 购买方式(0:单独购买  1:团购)
					$seller_openid	= !empty($_GP ['seller_openid']) ? trim($_GP ['seller_openid']) : 0 ;		//卖家openid
					
					$result = getConfirmOrderInfoByNow3 ( $dish_id, $total,$buy_type,$seller_openid );
					
					//为团购商品时
					if ($buy_type==1 && isset($result ['data'] ['dish_list'][0]) && $result ['data'] ['dish_list'][0]['type']==1) {

						//活动有效期已结束
						if($result ['data'] ['dish_list'][0]['timeend']<time())
						{
							unset($result);				//清除返回数据中的订单信息
						
							$result ['message'] = "团购活动有效期已结束";
							$result ['code'] 	= 0;
						}
						//是否已经加入同商品的其他团
						elseif(isAddedTeamBuyGroup($dish_id,$openid))
						{
							unset($result);				//清除返回数据中的订单信息
								
							$result ['message'] = "不要贪心，不能重复参团哦";
							$result ['code'] 	= 0;
						}
						else{
							//参团
							if($group_id)
							{
								//参团失败
								if(!AddToTeamBuyGroup($group_id,$openid,$result ['data'] ['dish_list'][0]['team_buy_count']))
								{
									unset($result);		//清除返回数据中的订单信息
										
									$result ['message'] = "参团失败";
									$result ['code'] 	= 0;
								}
								else{
									$team_buy_member_id = mysqld_insertid();
								}
							}
							//库存数量小于成团人数时不允许开团
							elseif($result ['data'] ['dish_list'][0]['quantity']<$result ['data'] ['dish_list'][0]['team_buy_count'])
							{
								unset($result);				//清除返回数据中的订单信息
								
								$result ['message'] = "库存数量小于成团人数时不允许开团";
								$result ['code'] 	= 0;
							}
							//独立建团
							else{
								
								if(floor($result ['data'] ['dish_list'][0]['quantity']/$result ['data'] ['dish_list'][0]['team_buy_count'])<1)
								{
									unset($result);				//清除返回数据中的订单信息
									
									$result ['message'] = "开团数已满,请加入其他团友的团继续购买喔";
									$result ['code'] 	= 0;
								}
								else{
									$teamBuyInfo = createTeamBuyGroup($dish_id,$openid);
									
									$team_buy_member_id = $teamBuyInfo['team_buy_member_id'];
									$group_id 			= $teamBuyInfo['group_id'];
								}
							}
						}
					}
					
					break;
						
				case 'buy_cart':			//从购物车购买
						
					$cart_ids = $_REQUEST ['cart_id']; 			// 购物车ID数组
					
					// 购物车ID为空时
					if (empty ( $cart_ids )) {
						$result ['message'] = "请选择购物车商品";
						$result ['code'] 	= 0;
					} else {
							
						$cart_ids = json_decode($cart_ids, true);		//购物车ID数组
							
						if (is_array ( $cart_ids )) {
							$result = getConfirmOrderInfoByCart3 ( $cart_ids,$openid,'order by c.id ');
						}
						else{
							$result ['message'] = "购物车参数格式不正确";
							$result ['code'] 	= 0;
						}
					}
					
					break;
					
				default:
					
					$result ['message'] = "操作不合法";
					$result ['code'] 	= 0;
					
					break;
			}
			
			
			//优惠券
			$bonusPrice = getBonusPrice($bonus_id,$openid);
			
			if(!empty($bonusPrice))
			{
				//没有错误时
				if ($result ['code']) {
				
					//订单总额小于优惠券金额
					if(($result['data']['goodsprice'] + $result['data'] ['taxtotal'] + $result['data'] ['ships'])<$bonusPrice)
					{
						unset($result);		//清除返回数据中的订单信息
							
						$result ['message'] = "订单总额不能小于优惠券金额";
						$result ['code'] 	= 0;
					}
				}
			}
			
			########################################  以下生成订单操作 #########################################
			//没有错误时
			if ($result ['code']) {
			
				$orderInfo = $result ['data'];
			
				// 订单编号
				$ordersns = createOrdersns ();
				$randomorder = mysqld_select ( "SELECT id FROM " . table ( 'shop_order' ) . " WHERE ordersn=:ordersn limit 1", array (
						':ordersn' => $ordersns
				) );
				if (! empty ( $randomorder ['ordersn'] )) {
					$ordersns = createOrdersns ();
				}
			
				$paytype = $this->getPaytypebycode ( $payment ['code'] );
				
				$ifcustoms = $orderInfo ['ifcustoms'];
				
				if (!empty($identity['identity_front_image']) && !empty($identity['identity_back_image'])){
					$ifcustoms = 2;
				}
			
				$order_data = array (
									'openid' 			=> $openid,
									'ordersn' 			=> $ordersns,
									'price' 			=> $orderInfo ['goodsprice'] + $orderInfo ['taxtotal'] + $orderInfo ['ships'], // 总金额
									'goodsprice' 		=> $orderInfo ['goodsprice'],
									'taxprice' 			=> $orderInfo ['taxtotal'], 		// 税收金额
									'dispatchprice'		=> $orderInfo ['ships'],			//运费
									'status' 			=> 0,
									'paytype' 			=> $paytype,
									'sendtype' 			=> 0,
									'paytypecode' 		=> $payment ['code'],
									'paytypename' 		=> $payment ['name'],
									'remark' 			=> trim($_GP ['remark']), 			// 订单备注
									'ifcustoms'			=> $ifcustoms,						//是否需要清关材料
									'addressid' 		=> $address ['id'],
									'address_mobile' 	=> $address ['mobile'],
									'address_realname' 	=> $address ['realname'],
									'address_province' 	=> $address ['province'],
									'address_city' 		=> $address ['city'],
									'address_area' 		=> $address ['area'],
									'address_address' 	=> $address ['address'],
									'createtime' 		=> time (),
									'identity_id' 		=> $identity ['identity_id'],
									'source'			=> get_mobile_type(1)				//设备源
				);
				
				if(!empty($bonusPrice))
				{
					$order_data['price'] 		= $order_data['price']-$bonusPrice;
					$order_data['bonusprice'] 	= $bonusPrice;
					$order_data['hasbonus'] 	= 1;
				}
				// 使用余额抵扣
				if ($use_balance == 1) {
					$use_member = mysqld_select("SELECT * FROM ".table('member')." WHERE openid='".$member['openid']."'");
					
					############## 免单余额抵扣 start ##############
					
					$freeorder_gold = 0;
					
					//免单金额未过期时
					if(	time()<=$use_member['freeorder_gold_endtime'])
					{
						$freeorder_gold = $use_member['freeorder_gold'];
							
						if ($freeorder_gold >= (float)$order_data['price']) {
							$freeorder_gold = (float)$order_data['price'];
						}
							
						$order_data['price'] = (float)$order_data['price'] - $freeorder_gold;
						if ($order_data['price'] <= 0) {
							$order_data['price'] = 0;
						}
							
						$order_data['freeorder_price'] = $freeorder_gold;
						
						//记录用户账单的免单金额收支情况
						$price = -1 * $freeorder_gold;
						insertMemberPaylog($member['openid'], $price,$use_member['freeorder_gold'] - $freeorder_gold, 'usegold', '订单编号：'.$order_data['ordersn'].';免单余额抵扣'.$freeorder_gold.'元');
					}
					############## 免单余额抵扣 end ##############
					
					
					$balance = (float)$use_member['gold'];
					if ($balance >= (float)$order_data['price']) {
						$balance = (float)$order_data['price'];
					}
					if ($balance < 0) {
						$balance = 0;
					}
					$order_data['price'] = (float)$order_data['price'] - $balance;
					if ($order_data['price'] <= 0) {
						$order_data['price'] = 0;
					}
					$order_data['has_balance'] = 1;
					$order_data['balance_sprice'] = $balance;
					// 扣除账户余额
					$member_ary = array('gold' => (float)$use_member['gold'] - $balance,'freeorder_gold' => (float)$use_member['freeorder_gold'] - $freeorder_gold);
					mysqld_update ('member',$member_ary,array('openid' =>$openid));
				}
				
				
				//团购订单时
				if (! empty ( $team_buy_member_id )) {
					
					$order_data['ordertype'] 	= 1;
				}
				else{
					$order_data['ordertype'] 	= 0;
				}
				
				mysqld_insert ( 'shop_order', $order_data );		//新增订单记录
				$orderid = mysqld_insertid ();
				$order_data['orderid'] = $orderid;
			
			
				$payBody = '';		//支付时的商品详情
				
				 // 插入订单商品
				 foreach ( $orderInfo ['dish_list'] as $row ) {
				 	
				 		$payBody = $row['title'];
				 				
				 		$d = array (
					 				'goodsid' 		=> $row ['id'],			//商品dish_id
				 					'aid' 			=> $row ['id'],			//商品dish_id
				 					'shopgoodsid'	=> $row ['gid'],		//产品ID
					 				'taxprice' 		=> $row ['taxprice'],
					 				'orderid' 		=> $orderid,
					 				'total' 		=> $row ['total'],
					 				'price' 		=> $row ['app_marketprice'],
					 				'taxprice' 		=> $row ['taxprice'],
					 				'seller_openid' => $row ['seller_openid'],
				 					'shop_type'		=> ($row ['app_marketprice']==$row ['timeprice']) ? $row ['type'] : 0,
				 					'commision'		=> $row ['app_marketprice']*$row ['total']*$row ['commision'],
					 				'createtime'	=> time ()
				 		);

				 		//新增商品订单记录
				 		mysqld_insert ( 'shop_order_goods', $d );
				 		
				 		//更新商品数据
				 		updateDishData($row ['id'],$row ['total']);
				 }
			
				 // 清除购物车商品记录
				 if (! empty ( $cart_ids )) {
				 				
				 	mysqld_query ( "delete FROM " . table ( 'shop_cart' ) . " WHERE session_id = '{$openid}' and id in(" . implode ( ',', $cart_ids ) . ") " );
				 }
				 
				 //更新团购信息
				 if (! empty ( $team_buy_member_id )) {
				 	
				 	mysqld_update ( 'team_buy_member', array('order_id'=>$orderid),array('id' =>$team_buy_member_id) );
				 }
				 
				 //更新优惠券信息
				 if(!empty($bonusPrice))
				 {
				 	mysqld_update ( 'bonus_user', array ('isuse' => 1,'used_time' => time(),'order_id'=>$orderid),array('bonus_id' =>$bonus_id) );
				 }
			
				 unset ( $result ['data'] );
				 
				 $aliParam = array('out_trade_no' 	=> $order_data['ordersn'].'-'.$orderid,				//商户网站唯一订单号
							 		'subject' 		=> $order_data['ordersn'],							//商品名称
							 		'total_fee' 	=> $order_data['price'],							//总金额
							 		'body' 			=> preg_replace("/[\&\+]+/", '', $payBody)			//商品详情
				 );
				 
				 $result ['code'] 					= 1;
				 $result ['data']['order'] 			= $order_data;

				 // 使用余额抵扣全额之后的处理
				 if ($use_balance == 1 AND $order_data['price'] == 0) {
				 	//支付成功后的处理
				 	$order = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE id=".$orderid);
					mysqld_update('shop_order', array('status'=>1,'paytime'=>time()), array('id' =>  $orderid));
      
					mysqld_insert('paylog', array('typename'=>'支付成功','ptype'=>'success','paytype'=>'balance','createtime'=>date('Y-m-d H:i:s')));
					
					paySuccessProcess($order);	
				 }else{
				 	//支付宝支付
					 if($payment ['code']=='alipay')
					 {
					 	$result ['data']['aliPayParam']	= buildRequestRsaParaToString($aliParam);		//支付宝的参数数组
					 }
					 //微信支付
					 elseif($payment ['code']=='weixin'){
					 	$result ['data']['weixinPayParam']	= weixinPayData($order_data['ordersn'],$aliParam['body'],$aliParam['total_fee']);
					 }
				 }
			}
		}
	}
}elseif ($member == 3) {
	$result['message'] 	= "该账号已在别的设备上登录";
	$result['code'] 	= 3;
}else {
	$result ['message'] = "用户还未登陆";
	$result ['code'] 	= 2;
}

echo apiReturn ( $result );
exit ();

/**
 * 更新商品数据（扣库存，更新销量等）
 * 
 * @param $dish_id: 商品ID
 * @param $total: 购买件数
 */
function updateDishData($dish_id,$total)
{
	$sql = "SELECT d.total,d.totalcnf,d.sales FROM " . table('shop_dish') . " d ";
	$sql.= " left join " . table('shop_goods') . " g on d.gid=g.id ";
	$sql.= " WHERE d.status = 1 ";
	$sql.= " and d.id = {$dish_id} ";
	$sql.= " and d.deleted = 0 ";
	$sql.= " and g.status = 1 ";
	$sql.= " and g.deleted = 0 ";
	$sql.= " and d.total > 0 ";
	
	$productInfo = mysqld_select($sql);
	
	if($productInfo)
	{
		$data = array ('sales' => $productInfo ['sales']+$total);
		
		//减库存时
		if($productInfo['totalcnf']==0)
		{
			$data['total'] = $productInfo['total']-$total;
		}
		
		//新增商品订单记录
		mysqld_update ( 'shop_dish', $data,array('id' =>$dish_id) );
	}
}

/**
 * 获得优惠券金额
 * @param int $bonus_id : 优惠券ID
 * @param string $openid : 用户ID
 * 
 * @return $bonusPrice :优惠券金额
 */
function getBonusPrice($bonus_id,$openid)
{
	$bonusPrice = 0;
	
	if(!empty($bonus_id))
	{
		$bonusUserInfo = mysqld_select("SELECT bonus_type_id,bonus_id FROM " . table('bonus_user') . " where bonus_id={$bonus_id} and isuse=0 and deleted=0 and openid=:openid",array(':openid'=>$openid));
		
		//用户拥有的优惠券存在时
		if($bonusUserInfo)
		{
			$bonusInfo = mysqld_select("SELECT type_money FROM " . table('bonus_type') . " where type_id={$bonusUserInfo['bonus_type_id']} and deleted=0 and use_start_date <= " .time(). " and use_end_date > ".time());
		
			//优惠券存在时
			if($bonusInfo)
			{
				$bonusPrice = $bonusInfo['type_money'];
			}
		}
	}
	
	return $bonusPrice;
}
