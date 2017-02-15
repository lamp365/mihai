<?php
/**
 * 订单确认页（结算页）
 * 
 */
	$result = array ();
	
	$member = get_member_account ( true, true );
	
	if (!empty($member) AND $member != 3) {
		$openid 	= $member ['openid']; 						// 用户ID
		$operation 	= $_GP ['op'];
		
		switch ($operation)
		{
			case 'buy_now':				//立即购买
				
				$dish_id 		= intval ( $_GP ['dish_id'] ); 												// 商品ID
				$total 			= intval ( $_GP ['total'] ); 												// 购买的商品件数
				$buy_type 		= intval ( $_GP ['buy_type'] ); 											// 购买方式(0:单独购买  1:团购)
				$seller_openid	= !empty($_GP ['seller_openid']) ? trim($_GP ['seller_openid']) : 0 ;		//卖家openid
				
				$result = getConfirmOrderInfoByNow ( $dish_id, $total,$buy_type,$seller_openid );
				
				break;
				
			case 'buy_cart':			//从购物车购买
				
				$cart_ids = $_REQUEST ['cart_id'] ; 				// 购物车ID json数组
				
				// 购物车ID为空时
				if (empty ( $cart_ids )) {
					$result ['message'] = "请选择购物车商品!";
					$result ['code'] 	= 0;
				} else {
				
					$cart_ids = json_decode($cart_ids, true);		//购物车ID数组
				
					if(is_array($cart_ids))
					{
						$result = getConfirmOrderInfoByCart ( $cart_ids,$openid,' order by c.seller_openid ');
					}
					else{
						$result ['message'] = "购物车参数格式不正确!";
						$result ['code'] 	= 0;
					}
				}
				
				break;
				
			default:
				
				$result ['message'] = "操作不合法!";
				$result ['code'] 	= 0;
				
				break;
		}
		
	}elseif ($member == 3) {
		$result['message'] 	= "该账号已在别的设备上登录！";
		$result['code'] 	= 3;
	}else {
		$result ['message'] = "用户还未登陆。";
		$result ['code'] 	= 2;
	}
	
	//没有错误时
	if($result['code']==1)
	{
		//默认地址信息
		$addressInfo= mysqld_select("SELECT id,realname,mobile,province,city,area,address,isdefault FROM " . table('shop_address') . " WHERE openid = :openid and isdefault=1", array(':openid' => $openid));
		//默认身份证
		$identity 	= mysqld_select ( "SELECT identity_id,identity_number,identity_name,isdefault FROM " . table ( 'member_identity' ) . " WHERE openid = :openid and isdefault=1 and status=0", array (':openid' => $openid) );
		
		if(empty($addressInfo))
		{
			$result['data']['address'] 	= array();
		}
		else{
			$result['data']['address'] 	= $addressInfo;
		}
		
		if(empty($identity))
		{
			$result['data']['identity'] = array();
		}
		else{
			$result['data']['identity'] = $identity;
		}
		
		
		$result ['data']['totalprice'] 	= $result ['data']['goodsprice']+$result ['data']['taxtotal']+$result ['data']['ships'];
		$result ['data']['payment_list']= getPayment (); 					// 支付方式
		$result ['data']['bonus_list']	= get_bonus_list (array('openid'=>$openid,'goods'=>$result ['data']['dish_list'],'price'=>$result ['data']['goodsprice'])); 	// 优惠券
	}
	
	echo apiReturn ( $result );
	exit ();