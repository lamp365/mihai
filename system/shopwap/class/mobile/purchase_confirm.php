<?php
header("Cache-control:no-cache,no-store,must-revalidate");
header("Pragma:no-cache");
header("Expires:0");
// 控制非登录状态下下单安全
$member=get_vip_member_account(true,true);
// 获取用户的opeinid;
$openid =$member['openid'] ;
// 判断是否存在关联的业务来来决定是否重新获取
if ( empty( $member['relation_uid'] ) ){
    $relation =  mysqld_select("SELECT relation_uid FROM ".table('member')." WHERE openid = ".$openid." limit 1");
    $member['relation_uid'] = $relation['relation_uid'];
}
// 初始化订单金额
$totalprice = 0;
$purchase_goods = new LtCookie();
$goods = $purchase_goods->getCookie('purchase');
if ( !empty($goods) ){
      $goods = unserialize($goods);
}else{
     die(showAjaxMess('1002', '产品数据异常'));
}
//定义结算页面的各个接口
switch ( $_GP['api'] ){
	// 生产订单信息
    case 'get_order':
		// 获取收货地址信息并进行验证
	    if ( empty( $_GP['address_id'] ) ){
            die(showAjaxMess('1002', '收货地址数据异常'));
		}
	    $defaultAddress   =   mysqld_select("SELECT * FROM " . table('shop_address') . " WHERE  deleted = 0 and id =:id and openid = :openid order by isdefault desc ", array(':openid' => $openid,':id'=>$_GP['address_id']));
        if ( !$defaultAddress ){
            die(showAjaxMess('1002', '收货地址数据异常')); 
		}
        // 分离有货无货状态
        $had_goods_total = array();
		$had_goods_price = 0;
        foreach( $goods as $key=>$goods_value ){
             $dish_vip_good = mysqld_select("SELECT A.total,A.gid, B.vip_price FROM ".table('shop_dish')." as A LEFT JOIN ".table('shop_dish_vip')." as B ON A.id = B.dish_id WHERE A.id = ".$goods_value['id']." AND B.v1= ".$member['parent_roler_id']." AND B.v2 = ".$member['son_roler_id']." limit 1");
			 if ( $dish_vip_good ){
				 // 更新产品批发价格，不以缓存为主
				 $goods_value['price'] = $dish_vip_good['vip_price'];
				 $goods_value['gid'] = $dish_vip_good['gid'];
                 $had_goods_total[] = $goods_value;
			     $had_goods_price += $goods_value['price'] * $goods_value['num'];
			 }else{
                 continue;
			 }
		}
		$ordersns= 'SN'.date('Ymd') . random(6, 1);
		$randomorder = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE  ordersn=:ordersn limit 1", array(':ordersn' =>$ordersns));
		while ( !empty($randomorder['ordersn']) ){
             $ordersns= 'SN'.date('Ymd') . random(6, 1);   
			 $randomorder = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE  ordersn=:ordersn limit 1", array(':ordersn' =>$ordersns));
		}
		$data = array(
                'openid' => $openid,	
                'ordersn' => $ordersns,
                'price' => $had_goods_price, // 产品金额+运费
                'dispatchprice' => 0,
                'goodsprice' => $had_goods_price,
				'ordertype' => -2,   // 订单类型，默认为一般订单72小时关闭
                'status' => 0,
                'paytype'=> 2,
                'sendtype' => 0,
                'paytypecode' => 'alipay',
				'relation_uid'=> $member['relation_uid'] ,// 业务员的ID
                'paytypename' => '支付宝',
                'addressid'=> $defaultAddress['id'],
                'address_mobile' => $defaultAddress['mobile'],
                'address_realname' => $defaultAddress['realname'],
                'address_province' => $defaultAddress['province'],
                'address_city' => $defaultAddress['city'],
                'address_area' => $defaultAddress['area'],
                'address_address' => $defaultAddress['address'],
                'createtime' => time()		
            );
        mysqld_insert('shop_order', $data);
        $orderid = mysqld_insertid();
		if ( $orderid ){
			 foreach ($had_goods_total as $row) {
					if (empty($row)) {
						continue;
					}
					$d = array(
						'goodsid' => $row['id'],
						'shopgoodsid' => $row['gid'],
						'aid'    =>  $row['id'],
						'orderid' => $orderid,
						'shop_type'=>-2,
						'total' => $row['num'],
						'price' => $row['price'],
						'createtime' => time()
					);
					mysqld_insert('shop_order_goods', $d);
			 }
			 $order_info = array(
                'address_mobile' => $defaultAddress['mobile'],
                'address_realname' => $defaultAddress['realname'],
                'address_province' => $defaultAddress['province'],
                'address_city' => $defaultAddress['city'],
                'address_area' => $defaultAddress['area'],
                'address_address' => $defaultAddress['address'],
				'order_id'=> $orderid,
				'order_sn'=> $ordersns,
			    'order_price' => $had_goods_price,
				'order_goods'=> $had_goods_total
			);
			 die(showAjaxMess('200', $order_info)); 
		}else{
             die(showAjaxMess('1002', '订单数据获取异常')); 
		}
        // 订单信息的数据格式
		/* array (
		      'errno'=> '200',成功 '其它',失败
			  'message'=>'信息通知'
			  'address'=>'全部地址信息'
			  'order_price'=>'订单总金额'
			  'order_goods'=>'订单产品列表'  // 跟获取选择的产品一致
			)	    
		*/
		break;
	default :
		break;
}