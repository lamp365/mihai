<?php
	/**
	 * app 优惠券
	 * @author WZW
	 * 
	 */

	$result = array();
	
	$op = $_GP['op'];
	// 账户验证
	$member = get_member_account(true, true);
	
	//领劵中心列表
	if ($op == 'display_list') {
		
		$sql = "SELECT type_id,type_name,type_money,use_start_date,use_end_date,min_goods_amount,send_max FROM " . table('bonus_type');
		$sql.= " where send_type IN (1,2) and deleted = 0 ";		//非新手礼优惠券
		$sql.= " and send_start_date<=".time()." and send_end_date >=".time();
		
		//显示新手礼之外的可领优惠券
		$bonus = mysqld_selectall($sql);
		
		//已登录用户
		if (!empty($member) AND $member != 3) {
			
			//已领过的优惠劵
			$arrBonusUser = mysqld_selectall('SELECT bonus_type_id,count(openid) as cnt FROM ' . table('bonus_user') . " where openid='".$member['openid']."' group by bonus_type_id");
			
			//过滤已领取的优惠券
			if(!empty($bonus))
			{
				foreach($bonus as $bk=>$bv)
				{
					//有最大领取数量限制
					if($bv['send_max']!=0 && !empty($arrBonusUser))
					{
						foreach($arrBonusUser as $uk=>$uv)
						{
							if($uv['bonus_type_id'] = $bv['type_id'] &&  $uv['cnt']>=$bv['send_max'])
							{
								unset($bonus[$bk]);
								
								break;
							}
						}
					}
				}
			}
		}
		
		$result['data']['bonus']= $bonus;
		$result['code'] 		= 1;
		
		echo apiReturn($result);
		exit;
	}
	
	if (empty($member)) {
		$result['message'] 	= "用户验证失败!";
		$result['code'] 	= 2;
		echo apiReturn($result);
		exit;
	}elseif ($member == 3) {
		$result['message'] 	= "该账号已在别的设备上登录！";
		$result['code'] 	= 3;
		echo apiReturn($result);
		exit;
	}
	
	if ($op == 'list') {
		// 获取该用户所有已领取的优惠券列表
		$list = mysqld_selectall("SELECT a.bonus_sn, a.deleted as use_deleted, b.type_id, b.type_name, b.type_money, b.send_type, b.send_start_date, b.send_end_date, b.use_start_date, b.use_end_date, b.min_goods_amount, b.deleted as type_deleted FROM ".table('bonus_user')." as a left join ".table('bonus_type')." as b on a.bonus_type_id=b.type_id WHERE a.openid='".$member['openid']."' AND a.isuse=0 AND b.send_type IN (0,1,2) ORDER BY a.createtime DESC, b.type_money DESC");
		$bonus = array();
		if (!empty($list)) {
			foreach ($list as $lv) {
				if ($lv['use_deleted'] == 1 or $lv['type_deleted'] == 1 or ($lv['use_end_date'] < time())) {
					// 已失效
					$bonus['failed'][] = $lv;
				}else{
					// 可用
					$bonus['effective'][] = $lv;
				}
			}
		}
		$result['data']['bonus'] = $bonus;
		$result['code'] = 1;
	}elseif ($op == 'get_bouns') {
		// 领取优惠券
		$type_id = intval($_GP['type_id']);
		$have_bouns = mysqld_select("SELECT COUNT(*) as num FROM ".table('bonus_user')." WHERE openid='".$member['openid']."' AND bonus_type_id=".$type_id);
		$bouns = mysqld_select("SELECT * FROM ".table('bonus_type')." WHERE type_id=".$type_id." AND send_type IN (0,1,2)");
		if (empty($bouns)) {
			$result['message'] 	= "未找到可用的优惠券";
			$result['code'] 	= 0;
			echo apiReturn($result);
			exit;
		}
		
		if (($have_bouns['num'] >= $bouns['send_max']) AND $bouns['send_max'] != '0') {
			$result['message'] 	= "已达到领取最大次数!";
			$result['code'] 	= 0;
			echo apiReturn($result);
			exit;
		}else{
			if ($bouns['send_start_date'] < time() AND $bouns['send_end_date'] > time()) {
				$bonus_sn = date("Ymd",time()).$type_id.rand(1000000,9999999);
				$bonus_user = mysqld_select("SELECT * FROM " . table('bonus_user')."where bonus_sn='".$bonus_sn."'" );
				while(!empty($bonus_user['bonus_id'])) {
					$bonus_sn = date("Ymd",time()).$type_id.rand(1000000,9999999);
				}
				$data = array('bonus_type_id' => $type_id, 'bonus_sn' => $bonus_sn, 'openid' => $member['openid'], 'createtime' => time());
				$in = mysqld_insert('bonus_user', $data);
				if ($in) {
					$result['message'] = "领取成功!";
					$result['code'] = 1;
				}else{
					$result['message'] = "领取失败!";
					$result['code'] = 0;
				}
			}else{
				$result['message'] 	= "不符合领取时间范围!";
				$result['code'] 	= 0;
				echo apiReturn($result);
				exit;
			}
		}
	}
	//新手礼
	elseif ($op == 'new_member') {
		
		//是否有订单
		$order = mysqld_selectall("SELECT id FROM " . table('shop_order')." where openid='".$member['openid']."' ");
		if($order)
		{
			$result['message'] 	= "非新手会员，无法领取新手";
			$result['code'] 	= 0;
		}
		//是否已经领过券
		elseif(mysqld_selectall("SELECT u.bonus_id FROM " . table('bonus_user')." u left join ". table('bonus_type')." t on t.type_id=u.bonus_type_id where u.openid='".$member['openid']."' and t.send_type=0 "))
		{
			$result['message'] 	= "您已经领取过了";
			$result['code'] 	= 0;
		}
		else{
			$bonus = mysqld_selectall("SELECT type_id,type_name,type_money,use_start_date,use_end_date,min_goods_amount FROM " . table('bonus_type')." where send_type=0 and deleted = 0 ");
			
			if ( !$bonus ){

				$result['message'] 	= "当前优惠券已失效";
				$result['code'] 	= 0;
			}
			else{
			
				foreach($bonus as $bv)
				{
					$bonus_sn=date("Ymd",time()).$id.rand(1000000,9999999);
					$bonus_user = mysqld_select("SELECT * FROM " . table('bonus_user')."where bonus_sn='".$bonus_sn."'" );
					while(!empty($bonus_user['bonus_id']))
					{
						$bonus_sn=date("Ymd",time()).$id.rand(1000000,9999999);
						$bonus_user = mysqld_select("SELECT * FROM " . table('bonus_user')."where bonus_sn='".$bonus_sn."'" );
					}
					
					$data=array('createtime'	=> time(),
								'openid'		=> $member['openid'],
								'bonus_sn'		=> $bonus_sn,
								'deleted'		=> 0,
								'isuse'			=> 0,
								'bonus_type_id'	=> $bv['type_id']);
						
					mysqld_insert('bonus_user',$data);
				}
				
				$result['data']['bonus']= $bonus;
				$result['code'] 		= 1;
			}
		}
	}

	echo apiReturn($result);
	exit;