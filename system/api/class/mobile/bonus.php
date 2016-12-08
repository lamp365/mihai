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
		$list = mysqld_selectall("SELECT a.bonus_sn, a.deleted as use_deleted, b.type_id, b.type_name, b.type_money, b.send_type, b.send_start_date, b.send_end_date, b.use_start_date, b.use_end_date, b.min_goods_amount, b.deleted as type_deleted FROM ".table('bonus_user')." as a left join ".table('bonus_type')." as b on a.bonus_type_id=b.type_id WHERE a.openid='".$member['openid']."' AND a.isuse=0 ORDER BY a.createtime DESC, b.type_money DESC");
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
		$bouns = mysqld_select("SELECT * FROM ".table('bonus_type')." WHERE type_id=".$type_id);
		
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

	echo apiReturn($result);
	exit;