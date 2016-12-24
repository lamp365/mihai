<?php
header("Cache-control:no-cache,no-store,must-revalidate");
header("Pragma:no-cache");
header("Expires:0");
// 控制非登录状态下下单安全
$member=get_vip_member_account(true,true);
// 获取用户的opeinid;
$openid =$member['openid'] ;
$user_a = get_user_identity($member['mobile']);
// 判断是否存在关联的业务来来决定是否重新获取
if ( empty( $member['relation_uid'] ) ){
    $relation =  mysqld_select("SELECT relation_uid FROM ".table('member')." WHERE openid = ".$openid." limit 1");
    $member['relation_uid'] = $relation['relation_uid'];
}
// 初始化订单金额
$totalprice = 0;
$goods = get_purchase_cart($openid,$user_a['type']);
if ( empty($goods) ){
     die(showAjaxMess('1002', '产品数据异常'));
}
// 设置汇率
$exchange_rate = mysqld_select("SELECT * FROM ".table('config')." WHERE name = 'exchange_rate' limit 1 ");
if ( $exchange_rate ){
    $exchange_rate_value =  $exchange_rate['value'] > 5 ? $exchange_rate['value'] : 6.8972;
}else{
    $exchange_rate_value = 6.8972;
}
// 批发或一件代发状态
if ( $user_a['type'] == 2 ){
    $currency = 2;
	$ordertype = -2;
}else{
    $currency = 1;
	$ordertype = 0;
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
		// 获取运费状态
		if ( $currency == 2 ){
			if ( !isset($_GP['freight']) ){
				die(showAjaxMess('1002', '配送方式获取异常'));
			}
		}
        $had_goods_total = array();
		$had_goods_price = 0;
		$shiprice = 0;
		// 购物车里的产品数据
		// 要考虑一件代发的库存问题
        foreach ( $goods as $goods_value ){
			 $good_template = mysqld_select("SELECT a.*,b.weight,b.coefficient,b.thumb as good_img FROM ".table('shop_dish')." as a LEFT JOIN ".table('shop_goods')." as b on a.gid = b.id WHERE a.id = ".$goods_value['goodsid']." limit 1");
			 $goods_value['price'] = $good_template['marketprice'];
			 $goods_value['id']    = $goods_value['goodsid'];
			 $goods_value   = price_check($goods_value,$member['parent_roler_id'],$member['son_roler_id'],$user_a['type']);
             if ( $user_a['type'] == 2 ){
                 if ( $_GP['freight'] == 1 ){
					 $freight = 0;
					 $sendtype = 1;
			     }else{
					 $sendtype = 0;
					 // 设置配送运费
					 $good_template['coefficient'] = $good_template['coefficient'] > 0 ? $good_template['coefficient'] : 1.12;
					 $freight = $good_template['weight'] * $goods_value['total'] * $good_template['coefficient'] * 2.2046 * 3.25 * $exchange_rate_value;
			      } 
				  $goods_value['price'] = round($goods_value['price'] * $exchange_rate_value,2);
				  $had_goods_price += $goods_value['price'] * $goods_value['total'];
				  $shiprice += $freight;
			}else{
				 $goods_value['issendfree'] = $good_template['issendfree'];
				 $goods_value['pcate'] = $good_template['pcate'];
				 $had_goods_price += $goods_value['price'] * $goods_value['total'];
			}
			$goods_value['title']   = $good_template['title'];
			$goods_value['img']   = $good_template['good_img'];
			$goods_value['num']   = $goods_value['total'];
			$goods_value['gid']   = $good_template['gid'];
			$had_goods_total[] = $goods_value;
		}
		if ( $user_a['type'] == 3 ){
			    $sendtype = 0;
				$issendfree = 0;
				if(empty($issendfree)){
					   $promotion=mysqld_selectall("select * from ".table('shop_pormotions')." where type =1 and starttime<=:starttime and endtime>=:endtime",array(':starttime'=>TIMESTAMP,':endtime'=>TIMESTAMP));
					   //========运费计算===============
							foreach($promotion as $pro){
								if($pro['promoteType']==1){
									if(($had_goods_price)>=$pro['condition']){
										$issendfree=1;		
									}
								} else if($pro['promoteType']==0){
									if($had_goods_price>=$pro['condition']){
										$issendfree=1;	
									}
								}		
						}
				} 			
				if ( $issendfree == 1 ){
                    $shiprice = 0;
				}else{
                    $shiprice = shipcost($had_goods_total);
				    $shiprice = $shiprice['price'];
				}
		}else{
               if ( $had_goods_price  < 2000 ) {
                     die(showAjaxMess('1002', '批发金额不足2000'));
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
                'price' => $had_goods_price + $shiprice, // 产品金额+运费
                'dispatchprice' => $shiprice,
                'goodsprice' => $had_goods_price,
				'ordertype' => $ordertype,   // 订单类型，默认为一般订单72小时关闭
                'status' => 0,
				'remark'=> isset($_GP['remark'])?addslashes($_GP['remark']):'',
                'paytype'=> 2,
                'sendtype' => $sendtype,
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
				'remark'=> $data['remark'],
				'freight' => $shiprice,
				'order_sn'=> $ordersns,
			    'order_price' => $had_goods_price,
				'order_total_price' => $had_goods_price + $shiprice,
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