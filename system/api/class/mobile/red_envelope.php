<?php
	/**
	 * app 红包
	 * 
	 */

	$result = array();
	
	$op = $_GP['op'];

	if ($op == 'check') {
		$setting = mysqld_select("SELECT * FROM ".table('redenvelope')." WHERE begintime<".time()." AND endtime>".time());
		if (empty($setting)) {
			$result['data']['activity'] = 0;
			$result['message'] 	= "没有正在进行的红包活动";
			$result['code'] 	= 1;
			
		}else{
			$result['data']['activity'] = 1;
			$result['code'] 	= 1;
		}
		echo apiReturn($result);
		exit;
	}
	
	// 预留APP账户验证接口
	$member = get_member_account(true, true);
	// $member['openid'] = '2016112116879';
	// $member['openid'] = '2015111911924';
	// $member['openid'] = '_t101815242553129705';
	// $member['openid'] = '_t100310534332980651';
	// $member['openid'] = 'www123494';
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

	$setting = mysqld_select("SELECT * FROM ".table('redenvelope')." WHERE begintime<".time()." AND endtime>".time());
	if (empty($setting)) {
		$result['message'] 	= "没有正在进行的红包活动";
		$result['code'] 	= 0;
	}else{
		// 本次活动的红包记录
		$record = mysqld_selectall("SELECT * FROM ".table('redenvelope_user')." WHERE redid=".$setting['id']);
		// 该用户今日领取数
		$get_num = 0;
		$amount_num = 0;
		if (!empty($record)) {
			foreach ($record as $rev) {
				if (date('Y-m-d',$rev['createtime']) == date('Y-m-d') AND $rev['openid'] == $member['openid']) {
					$get_num += 1;
				}
				$amount_num += (float)$rev['sendgold'];
			}
		}
		if ($get_num >= $setting['sendmax']) {
			$result['data']['getmax'] = 1;
			$result['data']['sendmax'] = $setting['sendmax'];
			$result['message'] 	= "今日领红包次数已达到上限";
			$result['code'] 	= 1;
			echo apiReturn($result);
			exit;
		}else{
			// 领红包流程
			// 1.计算剩余的奖金池
			$amount = (float)$setting['amount'];
			$residue = $amount-$amount_num;
			if ($residue <= 0) {
				// 奖金池已领完
				$result['data']['allmax'] = 1;
				$result['message'] 	= "本次红包已派完，请下次再来";
				$result['code'] 	= 1;
				echo apiReturn($result);
				exit;
			}elseif ($residue < (float)$setting['goldmax']) {
				// 剩余奖金池小于单个红包
				$result['data']['allmax'] = 1;
				$result['message'] 	= "本次红包已派完，请下次再来";
				$result['code'] 	= 1;
				echo apiReturn($result);
				exit;
			}else{
				// 2.计算是否抽中
				$winrate = ((float)$setting['winrate']) * 100;
				$lostrate = 100 - $winrate;
				$rand = get_rand(array('1'=>$winrate, '0'=>$lostrate));
				if ($rand == '1') {
					// 中奖
					$wingold = sprintf("%.2f",randFloat(0.01, (float)$setting['goldmax']));
				}else{
					// 没中
					$wingold = '0';
				}
				// 3.将红包金额移入余额，记录账单
				if ($wingold != '0') {
					mysqld_query("UPDATE ".table('member')." SET gold=gold+$wingold WHERE openid='".$member['openid']."'");
					// 记录账单
					$bill_data = array('type' => 6, 'openid' => $member['openid'], 'money' => $wingold, 'modifiedtime' => time(), 'createtime' => time(), 'remark' => '红包收入');
					mysqld_insert('bill', $bill_data);
				}
				// 4.记录抽奖历史
				$ru_data = array('redid' => $setting['id'], 'openid' => $member['openid'], 'mobile' => $member['mobile'], 'realname' => $member['realname'], 'nickname' => $member['nickname'], 'sendgold' => $wingold, 'createtime' => time());
				mysqld_insert('redenvelope_user', $ru_data);

				$result['data']['sendmax'] = $setting['sendmax'];
				$result['data']['wingold'] = $wingold;
				$result['data']['getmax']  = 0;
				$result['data']['allmax']  = 0;
				$result['code'] = 1;
			}
		}
	}
	
	// dump($result);
	echo apiReturn($result);
	exit;

// 抽奖概率算法
function get_rand($proArr) { 
    $rand_re = ''; 
    //概率数组的总概率精度 
    $proSum = array_sum($proArr); 
    //概率数组循环 
    foreach ($proArr as $key => $proCur) { 
        $randNum = mt_rand(1, $proSum);             //抽取随机数
        if ($randNum <= $proCur) { 
            $rand_re = $key;                         //得出结果
            break; 
        } else { 
            $proSum -= $proCur;                     
        } 
    } 
    unset($proArr); 
    return $rand_re; 
}

// 生成随机小数
function randFloat($min=0, $max=1){
    return $min + mt_rand()/mt_getrandmax() * ($max-$min);
}