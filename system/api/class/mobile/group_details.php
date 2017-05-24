<?php
	/**
	 * app 团购详情接口
	 * @author WZW
	 * 
	 */

	$result = array();
	
	$group_id = $_GP['group_id'];
	// $op = $_GP['op'];

	if (empty($group_id)) {
		$result['message'] 	= "团购ID为空!";
		$result['code'] 	= 0;
	}else{
		$group = mysqld_select("SELECT * FROM ".table('team_buy_group')." WHERE group_id=".$group_id);
		if (empty($group)) {
			$result['message'] 	= "查询团购失败!";
			$result['code'] 	= 0;
		}else{
			update_group_status($group['dish_id']);
			$group_member = mysqld_selectall("SELECT SQL_CALC_FOUND_ROWS a.id, a.openid, a.createtime as jointime, b.nickname, b.realname, b.mobile, b.avatar FROM ".table('team_buy_member')." as a left join ".table('member')." as b on a.openid=b.openid WHERE a.group_id=".$group_id." ORDER BY a.createtime ASC");
			// dump($group_member);
			$member_total = mysqld_select("SELECT FOUND_ROWS() as member_total;");
			
			$good = get_good(array(
                "table"=>"shop_dish",
				"where"=>"a.id = ".$group['dish_id'],
			));

			$group['p1'] = $good['p1'];
			$group['p2'] = $good['p2'];
			$group['p3'] = $good['p3'];
			$group['team_buy_count'] = $good['team_buy_count'];
			$group['residue_num'] = (int)$group['team_buy_count'] - (int)$member_total['member_total'];
			$group['good_title'] = $good['title'];
			$group['good_price'] = $good['timeprice'];
			$group['good_thumb'] = $good['thumb'];
			$group['team_buy_expiry'] = TEAM_BUY_EXPIRY;
			$group['good_url'] = get_wapgoods_url(NULL, $group['dish_id']);
			$group['timestart'] = $good['timestart'];
			$group['timeend'] = $good['timeend'];
			foreach ($group_member as &$gmv) {
				if (empty($gmv['nickname'])) {
					if (empty($gmv['realname'])) {
						$gmv['nickname'] = $gmv['mobile'];
					}else{
						$gmv['nickname'] = $gmv['realname'];
					}
				}
				$gmv['nickname'] = substr_cut($gmv['nickname']);
			}
			unset($gmv);
			$group['member'] = $group_member;
			// dump($group);
			$result['data']['group'] = $group;
			$result['code'] = 1;
		}
	}

	// dump($result);
	echo apiReturn($result);
	exit;