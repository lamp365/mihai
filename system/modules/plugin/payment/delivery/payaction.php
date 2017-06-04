<?php 
       $member=get_member_account(true,true);
	   $openid = $member['openid'];
       $member=member_get($openid);
       $order = mysqld_select("SELECT * FROM " . table('shop_order')." where id=:id",array(":id"=>$orderid));
	   /*
       if($member['credit']<$order['credit']) {
            message("小物币不足无法兑换，您的小物币：".$member['credit']."，兑换所需小物币".$order['credit']);
       }
	   if(!empty($order['id'])){
			if($order['status']==0){
			    $goods = mysqld_selectall("SELECT * FROM " . table('shop_order_goods') . " WHERE orderid = :id", array(':id' =>$order['id']));
				$qte    = '';
				$e      = 0;
				foreach($goods as $value){
					$star = 0;
				    // 找出最近的一条记录
					$aid = mysqld_select("SELECT * FROM " . table('addon7_request') . " WHERE award_id = :id order by star_num desc", array(':id' =>$value['aid']));
					 // 开始业务逻辑计算
					// 找出剩余数量，并判断购买后是否为0，修改状态
					$award =  mysqld_select("SELECT * FROM " . table("addon7_award") . " WHERE id = :id ", array(":id" => $value['aid']));
					 // 判断购买的数量
                    if ( $value['total'] < $award['dicount'] ){
                          $award['dicount'] -= $value['total'];
				    }else{
						  $qte .= '产品ID: '.$goods['aid'].'不足'.$value['total'].'份,我们为您抢到了剩下的'.$award['dicount'].'份<br/>';
						  $e   += ($value['total'] - $award['dicount']) * $value['price'];
						  $value['total'] = $award['dicount'];
						  $award['dicount'] = 0;
						  $award['confirm_time'] = time();
						  $award['state'] = 1;
						  mysqld_update('shop_order_goods', array('total'=>$value['total']), array('id' =>  $value['id']));
					} 
					 // 更新产品记录
                    mysqld_update('addon7_award', $award, array('id' =>  $value['aid']));
                     // 开始计算
					if (!empty($aid)){
						$star =  $aid['count']+$aid['star_num'];
					}else{
                         $star = 1;
					 }
				    $date = array(
						 'award_id'=>$value['aid'],
						 'createtime'=>time(),
						 'count'=>$value['total'],
						 'star_num'=>$star,
						 'openid'=>$order['openid'],
						 'orderid'=>$order['id'],
						 'province'=>$order['address_province'],
						 'city'=>$order['address_city'],
						 'address'=>$order['address_area'].$order['address_address'],
						 'realname'=>$order['address_realname'],
						 'mobile'=>$order['address_mobile']);
					 mysqld_insert('addon7_request', $date);
		}
			}}
       $order['credit'] = $order['credit'] - $e;
	   member_credit($openid,$order['credit'],'usecredit','积分兑换消费积分,订单id:'.intval($orderid));
       mysqld_update('shop_order', array('status' => '1','paytype' => '3','credit'=>$order['credit']), array('id' => $orderid));
	   // require_once WEB_ROOT.'/system/shopwap/class/mobile/order_notice_mail.php';  
       // mailnotice($orderid);
	   */
       message('兑换成功！'.$qte, WEBSITE_ROOT.mobile_url('myorder'), 'success');

?>