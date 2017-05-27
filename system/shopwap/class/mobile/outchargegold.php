<?php
	   $member  = get_member_account(true,true);
        $openid = $member['openid'];
       $member  =member_get($openid);



      if(empty( $member['outgoldinfo']))
       {
       		message('请设置您的提款账户！',mobile_url('member'),'error');
       }
	   $m_info = unserialize($member['outgoldinfo']);
		if($m_info['outgold_paytype'] == 1 && empty($m_info['outgold_bankcardcode'])){
			message('请设置您的提款账户！',mobile_url('member'),'error');
		}else if($m_info['outgold_paytype'] == 2 && empty($m_info['outgold_alipay'])){
			message('请设置您的提款账户！',mobile_url('member'),'error');
		}else if($m_info['outgold_paytype'] == 3 && empty($m_info['outgold_weixin'])){
			message('请设置您的提款账户！',mobile_url('member'),'error');
		}

       	$op = $_GP['op']?$_GP['op']:'display';
       	if($op=='display')
       	{
			if (!strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {
				//如果不是微信端操作，直接提示 体现流程
				if(is_mobile_request()){
					include themePage('outcharge_guide');
					exit;
				}else{
					include themePage('outchargegold');
					exit;
				}

			}


			if (checksubmit('submit')) {

					if(empty($_GP['charge'])||round($_GP['charge'],2)<=0)
					{
						message("请输入要充值的金额",refresh(),'error');
					}

				    $fee=round($_GP['charge'],2);
					if($fee>$member['gold'])
					{
						message('账户余额不足,最多能提取'.$member['gold'].'元',refresh(),'error');
					}

				   //提款最小限制
				   $teller_limit = bankSetting('teller_limit');
				   $teller_limit = intval($teller_limit);
				   if($member['gold'] < $teller_limit || $fee < $teller_limit){
					  message("最低提款限制{$teller_limit}",refresh(),'error');
				   }

					$ordersn= 'rg'.date('Ymd') . random(6, 1);
	 				$gold_order = mysqld_select("SELECT * FROM " . table('gold_teller') . " WHERE ordersn = '{$ordersn}'");
					 if(!empty($gold_order['ordersn']))
					 {
							$ordersn= 'rg'.date('Ymd') . random(6, 1);
					 }
       				 	//提款在审核前不打入paylog
       				  	$res = mysqld_insert('gold_teller',array('openid'=>$openid,'fee'=>$fee,'status'=>0,'ordersn'=>$ordersn,'createtime'=>time()));
						if($res){
							//扣除余额
							$gold = $member['gold'] - $fee;
							mysqld_update('member', array( 'gold' => $gold), array(
								'openid' => $openid
							));
							message('余额提取申请成功！','refresh','success');
						}else{
							message('余额提取申请失败！','refresh','error');
						}
						exit;
			  }


       		 $applygold = mysqld_selectcolumn("select sum(fee) from ".table("gold_teller")." where status=0 and openid=".	$openid);
       		 $outgold   = mysqld_selectcolumn("select sum(fee) from ".table("gold_teller")." where status<>0 and openid=".	$openid);

			$applygold  = empty($applygold)? 0 : $applygold;
			$outgold    = empty($outgold) ? 0 : $outgold;
			include themePage('outchargegold');
			exit;
		}

		if($op=='history')
		{
		   $pindex = max(1, intval($_GP['page']));
		   $psize = 20;
		   $list = mysqld_selectall("select * from ".table("gold_teller")." where openid=:openid order by createtime desc LIMIT " . ($pindex - 1) * $psize . ',' . $psize ,array(":openid"=>$openid));
		   $total = mysqld_selectcolumn('SELECT COUNT(*) FROM ' . table('gold_teller') . " where  openid=:openid ",array(":openid"=>$openid));
		   $pager = pagination($total, $pindex, $psize);

			include themePage('outchargegold_history');
			exit;
		}