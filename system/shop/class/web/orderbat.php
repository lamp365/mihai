<?php
     $operation = !empty($_GP['op']) ? $_GP['op'] : 'display';
	 if ($operation == 'display') {
			  $status =1;
		 	  $condition .= " AND status = '" . intval($status) . "'";
		      $list = mysqld_selectall("SELECT * FROM " . table('shop_order') . " WHERE 1=1 $condition ");
		      //部分商品发生退款的不显示  删除
		      foreach($list as $key=>$row){
					$order_goods = mysqld_selectall("select type,status from ".table('shop_order_goods')." where orderid={$row['id']}");
			  		foreach($order_goods as $item){
						if($item['type'] == 3 && in_array($item['status'],array(1,2))){
							//类型是3 表示退款  状态处于 正在申请 和审核通过  还为结束则进行先不发货
							unset($list[$key]);
						}
					}
			  }
          	  $total = count($list);
              $dispatchs = mysqld_selectall("SELECT * FROM " . table('shop_dispatch') );
		      $dispatchdata=array();
			  if(is_array($dispatchs)) {
				  foreach($dispatchs as $disitem) {
					  	$dispatchdata[$disitem['id']]=$disitem;
				   }
			  }
							 
           if (checksubmit('sendbatexpress')) {
           	 	$index=0;
				if(!empty($_GP['check']))
				{
					 foreach ($_GP['check'] as $k ) {
						$item = mysqld_select("SELECT status,ordersn FROM " . table('shop_order') . " WHERE id = :id", array(':id' => $k));
						     
						$isexpress=$_GP['express'.$k];
						if ($isexpress!='-1' && empty($_GP['expressno'.$k])) {
									message('订单'.$item['ordersn'].'没有快递单号，请填写完整！');
						}
						  if($item['status']!=1)
						  {
							 message('订单'.$item['ordersn'].'状态不是待发货状态，请重新点击”批量发货“后进行操作。');
						  }
					 }
					 foreach ($_GP['check'] as $k ) {
							 $item = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE id = :id", array(':id' => $k));
							 $express=$_GP['express'.$k];
							if($express=='-1')
							{
								$express=='';
							 }

							mysqld_update('shop_order', array(
								'status' => 2,
								'express' => $express,
								'expresscom' => $_GP['expresscom'.$k],
								'expresssn' => $_GP['expressno'.$k],
								'sendtime'   =>time()  //发货时间
							 ), array('id' => $k));

							$index++;

						 /****不需要发邮件通知
						 $member = array();
						 $member = mysqld_select("SELECT mobile,mess_id from " . table('member') . " where  openid=:openid ",array(':openid' => $item['openid']));
						 $mess_name = mysqld_selectcolumn("SELECT title from " . table('shop_mess') . " where  id= ".$member['mess_id']);
						 $Recive_Phone_Number = $member['mobile'];

						 $URL='http://userinterface.vcomcn.com/Opration.aspx';
						$pwd = strtoupper(md5("ab8888"));
						$account="mslsw";
						$ctime=date("Y-m-d h:i:s",time());
						//要发送的内容
						$content=iconv("UTF-8","GBK","尊敬的用户，您所预定的新鲜美食正在准备，单号:{$item['ordersn']},请您于周五16:30分到 {$mess_name} 提货");
						$data_string="<Group Login_Name='".$account."' Login_Pwd='".$pwd."' OpKind='0' InterFaceID='0'><E_Time>".$ctime."</E_Time><Item><Task><Recive_Phone_Number>".$Recive_Phone_Number."</Recive_Phone_Number><Content><![CDATA[".$content."]]></Content><Search_ID>abdsdd</Search_ID></Task></Item></Group>";
						//发送的POST数据
						$ch = curl_init();
						curl_setopt($ch, CURLOPT_POST, 1);
						curl_setopt($ch, CURLOPT_HEADER, 0);
						curl_setopt($ch, CURLOPT_URL,$URL);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
						//为了支持cookie
						curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
						curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
						curl_exec($ch);
						curl_close($ch);
						  * */
					}
					message('批量发货操作完成,成功处理'.$index.'条订单', refresh(), 'success');
				}else{
					message('对不起，你没有选择要操作的订单', refresh(), 'error');
				}

			}


		 	$dispatchlist = mysqld_selectall("SELECT * FROM " . table('dispatch')." where sendtype=0" );
					
		     include page('orderbat');
		}