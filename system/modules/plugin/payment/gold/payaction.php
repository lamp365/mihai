<?php 

			$member=get_member_account();
				$openid = $member['openid'];
				
				$order = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE  id=:id limit 1", array(':id' =>$orderid));
          
				$getmember=member_get($openid);
				if($getmember['gold']>=$order['price'])
				{
					$mark = PayLogEnum::getLogTip('LOG_SHOPBUY_TIP');
					$usegold=member_gold($openid,$order['price'],'usegold',$mark,true,$order['id']);
					if($usegold)
					{
					   mysqld_update('shop_order', array('status' => '1','paytype' => '1'), array('id' => $orderid));
					     require_once WEB_ROOT.'/system/shopwap/class/mobile/order_notice_mail.php';  
             mailnotice($orderid);
            message('订单提交成功，收货后请验货！',WEBSITE_ROOT.mobile_url('myorder'), 'success');
          }else
          {
          	 message('付款失败！', WEBSITE_ROOT.mobile_url('myorder'), 'error');
          }
				}else
				{
					 message('余额不足，无法完成付款！', WEBSITE_ROOT.mobile_url('myorder'), 'error');
				}
         
			
?>