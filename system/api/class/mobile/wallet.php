<?php
	/**
	 * app 钱包
	 * @author WZW
	 * 
	 */

	$result = array();
	
	$op = $_GP['op'];

	// 预留APP账户验证接口
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
	// $member['openid'] = '2015112022249';
	// $member['openid'] = '2015111911924';
	// dump($member);
	$objValidator = new Validator();

	if ($op == 'all') {
		// 总览
		$money = mysqld_select("SELECT a.earning, a.gold, a.freeze_gold, COUNT(b.id) as bank_num FROM ".table('member')." as a left join ".table('bank')." as b on a.openid=b.openid WHERE a.openid='".$member['openid']."'");
		if (empty($money)) {
			$result['message'] 	= "总览获取失败!";
			$result['code'] 	= 0;
			echo apiReturn($result);
			exit;
		}
		$all_earning = 0;
		$earning_order = mysqld_selectall("SELECT b.price, b.total, b.commision, b.createtime FROM ".table('shop_order')." as a left join ".table('shop_order_goods')." as b on a.id=b.orderid WHERE b.seller_openid='".$member['openid']."' AND a.status IN (1,2,3) ORDER BY b.createtime DESC");
		foreach ($earning_order as $e_v) {
			$all_earning += $e_v['commision'];
		}
		$money['earning'] = round($all_earning, 2);

		$money['sum_gold'] = (float)$money['gold'] + (float)$money['freeze_gold'];

		$result['data']['pandect'] = $money;
		$result['code'] = 1;
	}elseif ($op == 'earning') {
		// 收入详细
		$earning = mysqld_select("SELECT earning, freeze_gold, gold FROM ".table('member')." WHERE openid='".$member['openid']."'");
		$earning_order = mysqld_selectall("SELECT b.price, b.total, b.commision, b.createtime FROM ".table('shop_order')." as a left join ".table('shop_order_goods')." as b on a.id=b.orderid WHERE b.seller_openid='".$member['openid']."' AND a.status IN (1,2,3) ORDER BY b.createtime DESC");
		
		$today_e = 0;
		$month_e = 0;
		$all_earning = 0;

		foreach ($earning_order as $e_v) {
			if (date('Y-m-d',$e_v['createtime']) == date('Y-m-d')) {
				$today_e += $e_v['commision'];
			}
			if (date('Y-m',$e_v['createtime']) == date('Y-m')) {
				$month_e += $e_v['commision'];
			}
			$all_earning += $e_v['commision'];
		}
		$earning['today_earning'] = round($today_e, 2);
		$earning['month_earning'] = round($month_e, 2);
		$earning['earning'] = round($all_earning, 2);
		// dump($earning);

		$result['data']['earning'] = $earning;
		$result['code'] = 1;
	}elseif ($op == 'get_bank') {
		// 获取银行卡
		$bank = mysqld_selectall("SELECT * FROM ".table('bank')." WHERE openid='".$member['openid']."' ORDER BY id DESC");
		
		foreach ($bank as $bk => &$bv) {
			$bv['card_length'] = strlen($bv['card_id']);
			$bv['after_four'] = substr($bv['card_id'], -4);
			unset($bv['card_id']);
			$bank_img = mysqld_select("SELECT * FROM ".table('bank_img')." WHERE bank='".$bv['bank']."'");
			$bv['card_icon'] = $bank_img['card_icon'];
			$bv['card_bg'] = $bank_img['card_bg'];
			$bv['bg_color'] = $bank_img['bg_color'];
		}
		unset($bv);

		// 获取支付宝
		$alipay = mysqld_select("SELECT alipay FROM ".table('member')." WHERE openid='".$member['openid']."'");

		$result['data']['bank'] = $bank;
		$result['data']['alipay'] = $alipay['alipay'];
		$result['code'] = 1;
	}elseif ($op == 'binding_bank') {
		// 绑定银行卡
		$card_id = addslashes($_GP['card_id']);
		$real_name = addslashes($_GP['real_name']);

		if (!luhn($card_id) or empty($real_name)) {
			$result['message'] 	= "银行卡号不正确或姓名为空!";
			$result['code'] 	= 0;
			echo apiReturn($result);
			exit;
		}
		if (!bankInfo($card_id)) {
			$result['message'] 	= "银行卡暂未录入!";
			$result['code'] 	= 0;
			echo apiReturn($result);
			exit;
		}else{
			$re = insert_bank($card_id, $real_name, $member['openid']);
			if ($re == 1) {
				$result['message'] 	= "银行卡绑定成功!";
				$result['code'] 	= 1;
			}else{
				$result['message'] 	= $re;
				$result['code'] 	= 0;
			}
		}
	}elseif ($op == 'extract') {
		// 提现
		$gold = (float)$_GP['gold'];
		$card_id = $_GP['id'];

		if (empty($gold) or empty($card_id)) {
			$result['message'] 	= "提现金额或提现方式为空!";
			$result['code'] 	= 0;
			echo apiReturn($result);
			exit;
		}

		$can_use_gold = mysqld_select("SELECT gold FROM ".table('member')." WHERE openid='".$member['openid']."'");
		if ($gold > $can_use_gold['gold'] or $gold < 10) {
			$result['message'] 	= "提现金额不能超过可用余额并且不能低于10元!";
			$result['code'] 	= 0;
		}else{
			if ($card_id == 'alipay') {
				// 获取支付宝
				$alipay = mysqld_select("SELECT alipay FROM ".table('member')." WHERE openid='".$member['openid']."'");
				if (empty($alipay['alipay'])) {
					$result['message'] 	= "未设置支付宝账号!";
					$result['code'] 	= 0;
					echo apiReturn($result);
					exit;
				}

				// 申请提现
				$data = array('createtime' => time(), 'fee' => $gold, 'bank_id' => $alipay['alipay'], 'openid' => $member['openid']);
				$extract = mysqld_insert('gold_teller', $data);
				if ($extract) {
					// 余额扣除提现金额
					$jian = mysqld_query("UPDATE ".table('member')." SET gold=gold-".$gold." WHERE openid='".$member['openid']."'");
					if (!$jian) {
						$result['message'] 	= "余额扣除提现金额失败!";
						$result['code'] 	= 0;
						echo apiReturn($result);
						exit;
					}
					// 记录账单
					$bill_data = array('type' => 4, 'openid' => $member['openid'], 'money' => $gold, 'modifiedtime' => time(), 'createtime' => time());
					mysqld_insert('bill', $bill_data);
					$result['message'] 	= "申请提现成功!";
					$result['code'] 	= 1;
					echo apiReturn($result);
					exit;
				}else{
					$result['message'] 	= "申请提现失败!";
					$result['code'] 	= 0;
					echo apiReturn($result);
					exit;
				}
			}
			$have_bank = mysqld_select("SELECT * FROM ".table('bank')." WHERE id=$card_id");
			if (empty($have_bank)) {
				$result['message'] 	= "银行卡不存在!";
				$result['code'] 	= 0;
			}else{
				// 申请提现
				$data = array('createtime' => time(), 'fee' => $gold, 'bank_id' => $card_id, 'openid' => $member['openid']);
				$extract = mysqld_insert('gold_teller', $data);
				if ($extract) {
					// 余额扣除提现金额
					$jian = mysqld_query("UPDATE ".table('member')." SET gold=gold-".$gold." WHERE openid='".$member['openid']."'");
					if (!$jian) {
						$result['message'] 	= "余额扣除提现金额失败!";
						$result['code'] 	= 0;
						echo apiReturn($result);
						exit;
					}
					// 记录账单
					$bill_data = array('type' => 4, 'openid' => $member['openid'], 'money' => $gold, 'modifiedtime' => time(), 'createtime' => time());
					mysqld_insert('bill', $bill_data);
					$result['message'] 	= "申请提现成功!";
					$result['code'] 	= 1;
				}else{
					$result['message'] 	= "申请提现失败!";
					$result['code'] 	= 0;
				}
			}
		}
	}elseif ($op == 'check_extract') {
		// 验证可否提现(0全否，1支付方式没有，2支付密码没有，3可以)
		// 支付宝，支付密码
		$alipay = mysqld_select("SELECT alipay, paymentcode FROM ".table('member')." WHERE openid='".$member['openid']."'");
		// 银行卡
		$bank = mysqld_select("SELECT * FROM ".table('bank')." WHERE openid='".$member['openid']."'");
		
		if (empty($alipay['alipay']) AND empty($alipay['paymentcode']) AND empty($bank['id'])) {
			$result['data']['status'] = 0;
			$result['code'] = 1;
		}elseif (empty($alipay['alipay']) AND empty($bank['id'])) {
			$result['data']['status'] = 1;
			$result['code'] = 1;
		}elseif (empty($alipay['paymentcode'])) {
			$result['data']['status'] = 2;
			$result['code'] = 1;
		}else{
			$result['data']['status'] = 3;
			$result['code'] = 1;
		}
	}elseif ($op == 'bill') {
		// 账单
		$status = intval($_GP['status']);
		$pindex = max(1, intval($_GP['page']));
    	$psize = 20;
		
		$where = "openid='".$member['openid']."'";
		if ($status == '0') {

		}elseif ($status == '1') {
			$where.=" AND type IN (1,2,3,6)";
		}elseif ($status == '2') {
			$where.=" AND type IN (0,-1)";
		}elseif ($status == '3') {
			$where.=" AND type=4";
		}else{
			$result['message'] 	= "账单状态错误!";
			$result['code'] 	= 0;
			echo apiReturn($result);
			exit;
		}

		$bill = mysqld_selectall("SELECT SQL_CALC_FOUND_ROWS * FROM ".table('bill')." WHERE ".$where." ORDER BY createtime DESC LIMIT ".($pindex - 1) * $psize . ',' . $psize);
		// 总记录数
		$data_total = mysqld_select("SELECT FOUND_ROWS() as total;");

		if (empty($bill)) {
			$result['data']['bill'] = array();
			$result['data']['total'] = 0;
			$result['code'] = 1;
		}else{
			$orderid_ary = array();
			foreach ($bill as $bk => &$bv) {
				if ($bv['type'] == 0 or $bv['type'] == 3) {
					// 买家购买的订单商品数量
					$order_goods = mysqld_selectall("SELECT goodsid, total FROM ".table('shop_order_goods')." WHERE orderid=".$bv['order_id']);
					$good = get_good(array(
		                "table"=>"shop_dish",
						"where"=>"a.id = ".$order_goods[0]['goodsid'],
					));
					$total = 0;
					foreach ($order_goods as $ogv) {
						$total += $ogv['total'];
					}
					$bv['thumb'] = $good['thumb'];
					$bv['goods_total'] = $total;
				}elseif ($bv['type'] == 1 or $bv['type'] == -1) {
					foreach ($orderid_ary as $ora_k => $ora_v) {
						if ($ora_v['order_id'] == $bv['order_id'] AND $ora_v['type'] == $bv['type']) {
							$money = $bill[$ora_v['key']]['money'];
							(float)$money += (float)$bv['money'];
							$bill[$ora_v['key']]['money'] = $money;
							$orgid = $bill[$ora_v['key']]['order_goods_id'];
							$bill[$ora_v['key']]['order_goods_id'] = array();
							$bill[$ora_v['key']]['order_goods_id'][] = $orgid;
							$bill[$ora_v['key']]['order_goods_id'][] = $bv['order_goods_id'];
							unset($bill[$bk]);
							continue 2;
						}
					}
					$oa = array();
					$oa['order_id'] = $bv['order_id'];
					$oa['type'] = $bv['type'];
					$oa['key'] = $bk;
					$orderid_ary[] = $oa;

					// 卖家收入的佣金商品数量
					$order_goods = mysqld_selectall("SELECT a.orderid, a.total, b.openid FROM ".table('shop_order_goods')." as a left join ".table('shop_order')." as b on a.orderid=b.id WHERE a.orderid=".$bv['order_id']." AND a.seller_openid='".$bv['openid']."'");
					$member = mysqld_select("SELECT nickname, avatar FROM ".table('member')." WHERE openid='".$order_goods[0]['openid']."'");
					$total = 0;
					foreach ($order_goods as $ogv) {
						$total += $ogv['total'];
					}
					$bv['nickname'] = $member['nickname'];
					$bv['avatar'] = $member['avatar'];
					$bv['goods_total'] = $total;
				}elseif ($bv['type'] == 4 or $bv['type'] == 2) {
					// 提现
					$member = mysqld_select("SELECT avatar FROM ".table('member')." WHERE openid='".$bv['openid']."'");
					$bv['avatar'] = $member['avatar'];
				}
			}
			unset($bv);
			// 重新排列数组下标
			$bill = array_merge($bill);

			$result['data']['bill'] = $bill;
			$result['data']['total'] = $data_total['total'];
			$result['code'] = 1;
		}
	}elseif ($op == 'del_bank') {
		// 删除银行卡
		$bank_id = $_GP['bank_id'];

		$d_b = mysqld_delete('bank', array('id' => $bank_id));
		if ($d_b) {
			$result['message'] 	= "删除银行卡成功!";
			$result['code'] 	= 1;
		}else{
			$result['message'] 	= "删除银行卡失败!";
			$result['code'] 	= 0;
		}
	}elseif ($op == 'alipay') {
		// 用户支付宝账号
		$alipay = $_GP['alipay'];

		if (empty($alipay)) {
			$result['message'] 	= "支付宝账号为空!";
			$result['code'] 	= 0;
			echo apiReturn($result);
			exit;
		}
		$set_alipay = mysqld_query("UPDATE ".table('member')." SET alipay='".$alipay."' WHERE openid='".$member['openid']."'");

		if ($set_alipay) {
			$result['message'] 	= "设置支付宝账号成功!";
			$result['code'] 	= 1;
		}else{
			$result['message'] 	= "设置支付宝账号失败!";
			$result['code'] 	= 0;
		}
	}elseif ($op == 'check_bank') {
		// 检查银行卡类型
		$card_id = addslashes($_GP['card_id']);

		if (!luhn($card_id)) {
			$result['message'] 	= "银行卡号不正确!";
			$result['code'] 	= 0;
			echo apiReturn($result);
			exit;
		}

		if (!bankInfo($card_id)) {
			$result['message'] 	= "银行卡暂未录入!";
			$result['code'] 	= 0;
			echo apiReturn($result);
			exit;
		}else{
			$card_type = str_replace(array('-'), ' ', bankInfo($card_id));
			$result['message'] 	= $card_type;
			$result['code'] 	= 1;
		}
	}elseif ($op == 'set_paymentcode') {
		// 设置支付密码
		$paymentcode = $_GP['paymentcode'];
		if (empty($paymentcode)) {
			$result['message'] 	= "支付密码为空!";
			$result['code'] 	= 0;
			echo apiReturn($result);
			exit;
		}

		$member = mysqld_select("SELECT * FROM ".table('member')." WHERE openid='".$member['openid']."'");
		if (!$objValidator->is($paymentcode,'num') or !$objValidator->lengthValidator($paymentcode,'6,6')) {
			$result['message'] 	= "支付密码格式错误，应为6位纯数字!";
			$result['code'] 	= 0;
			echo apiReturn($result);
			exit;
		}else{
			$data = array('paymentcode' => md5($paymentcode));
			$mq = mysqld_update('member', $data, array('openid' => $member['openid']));
			// $mq = mysqld_query("UPDATE ".table('member')." SET paymentcode= WHERE openid='".$member['openid']."'");
			if ($mq) {
				$result['message'] 	= "支付密码设置成功!";
				$result['code'] 	= 1;
			}else{
				$result['message'] 	= "支付密码设置失败,可能是和旧密码重复!";
				$result['code'] 	= 0;
			}
		}
	}elseif ($op == 'update_paymentcode') {
		// 修改支付密码
		$old_pay = $_GP['old_paymentcode'];
		$new_pay = $_GP['new_paymentcode'];
		if ($old_pay == $new_pay) {
			$result['message'] 	= "密码不能和旧密码一样!";
			$result['code'] 	= 0;
			echo apiReturn($result);
			exit;
		}

		$member = mysqld_select("SELECT * FROM ".table('member')." WHERE openid='".$member['openid']."'");
		if (empty($member['paymentcode'])) {
			$result['message'] 	= "请先设置支付密码!";
			$result['code'] 	= 0;
			echo apiReturn($result);
			exit;
		}else{
			if (md5($old_pay) == $member['paymentcode']) {
				if (!$objValidator->is($new_pay,'num') or !$objValidator->lengthValidator($new_pay,'6,6')) {
					$result['message'] 	= "支付密码格式错误，应为6位纯数字!";
					$result['code'] 	= 0;
					echo apiReturn($result);
					exit;
				}else{
					$data = array('paymentcode' => md5($new_pay));
					$mq = mysqld_update('member', $data, array('openid' => $member['openid']));
					if ($mq) {
						$result['message'] 	= "支付密码修改成功!";
						$result['code'] 	= 1;
					}else{
						$result['message'] 	= "支付密码修改失败!";
						$result['code'] 	= 0;
					}
				}
			}else{
				$result['message'] 	= "旧密码不正确!";
				$result['code'] 	= 0;
				echo apiReturn($result);
				exit;
			}
		}
	}elseif ($op == 'paymentcode') {
		// 验证支付密码
		$paymentcode = $_GP['paymentcode'];
		if (empty($paymentcode)) {
			$result['message'] 	= "支付密码为空!";
			$result['code'] 	= 0;
			echo apiReturn($result);
			exit;
		}

		$member = mysqld_select("SELECT * FROM ".table('member')." WHERE openid='".$member['openid']."'");
		if (empty($member['paymentcode'])) {
			$result['message'] 	= "请先设置支付密码!";
			$result['code'] 	= 0;
			echo apiReturn($result);
			exit;
		}else{
			if (!$objValidator->is($paymentcode,'num') or !$objValidator->lengthValidator($paymentcode,'6,6')) {
				$result['message'] 	= "支付密码格式错误，应为6位纯数字!";
				$result['code'] 	= 0;
				echo apiReturn($result);
				exit;
			}else{
				if (md5($paymentcode) == $member['paymentcode']) {
					$result['message'] 	= "验证成功!";
					$result['code'] 	= 1;
				}else{
					$result['message'] 	= "验证失败!";
					$result['code'] 	= 0;
				}
			}
		}
	}elseif ($op == 'reset_paymentcode') {
		// 判断验证码
		if (!check_verify(trim($_GP ['VerifyCode']),trim($_GP['telephone']))) {
			// dump(trim($_GP ['VerifyCode']));
			// dump(trim($_GP['telephone'])));
			$result['message'] 	= '手机验证码错误！';
			$result['code'] 	= 0;
		}else{
			$result['message'] 	= '验证成功！';
			$result['code'] 	= 1;
		}
	}elseif ($op == 'paymentcode_exist') {
		// 判断当前用户是否有支付密码
		$member = mysqld_select("SELECT * FROM ".table('member')." WHERE openid='".$member['openid']."'");
		if (empty($member['paymentcode'])) {
			$result['data']['status'] = 0;
			$result['code'] = 1;
		}else{
			$result['data']['status'] = 1;
			$result['code'] = 1;
		}
	}else{
		$result['message'] 	= "操作类型不正确!";
		$result['code'] 	= 0;
	}
	
	// dump($result);
	echo apiReturn($result);
	exit;

	/**
	 * 验证码验证
	 *
	 * @param $verify 验证码
	 * @param $telephone 手机号码
	 *
	 * @return boolean
	 */
	function check_verify($verify,$telephone) {
		
		logRecord('telephone:'.$telephone,'resetpwdlog');
		logRecord('verify:'.$verify,'resetpwdlog');

		//验证码未过期
		if(isset($_SESSION['api']['sms_code_expired']) && $_SESSION['api']['sms_code_expired']>time())
		{
			//验证码是否正确
			if (isset($_SESSION['api'][$telephone]) && strtolower ( $_SESSION['api'][$telephone] ) == strtolower ( $verify )) {
				
				return true;
			}
		}
		else{
			unset ( $_SESSION['api'][$telephone] );
			unset ( $_SESSION['api']['sms_code_expired'] );
		}
	
		return false;
	}
	