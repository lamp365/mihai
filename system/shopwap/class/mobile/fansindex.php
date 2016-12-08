<?php
		$member = get_member_account(true, true);
		$member = member_get($member['openid']);
		$openid = $member['openid'];
		$op = $_GP['op'];
		if ( $_GP['type'] == 'ajax' ){
             if(!empty($_GP['img'])){
				mysqld_update('member',array('avatar'=>$_GP['img']),array('openid'=>$openid));
				echo 0;
				exit;
			}else{
				echo 1;
				exit;
			} 
		}
		if(empty($op)) {
			$weixinfans = get_weixin_fans_byopenid($member['openid'], $member['openid']);
			if(empty($member['avatar'])){
				if (!empty($weixinfans) && !empty($weixinfans['avatar'])) {
					$avatar = $weixinfans['avatar'];
				}
			}else{
				$avatar = $member['avatar'];
			}
			
			if ($is_login) {
				$member_rank_model = member_rank_model($member['experience']);
			} else {
				$is_login = is_login_account();
			}
			$fansindex_menu_list = mysqld_selectall("SELECT * FROM " . table('shop_diymenu') . " where menu_type='fansindex' order by torder desc");
			$cart_list = mysqld_selectall("SELECT a.*,b.id as bid , c.thumb,c.id as cid FROM " . table('shop_cart') . " a left join " . table('shop_dish') . " b on a.goodsid = b.id left join " . table('shop_goods') . " c on c.id = b.gid WHERE  session_id = '" . $openid . "'");
			$totlist = count($cart_list);
			$state0 = mysqld_selectcolumn('SELECT COUNT(*) FROM ' . table('shop_order') . " WHERE status = 0 and openid=:beid ", array(':beid' => $openid));
			$state1 = mysqld_selectcolumn('SELECT COUNT(*) FROM ' . table('shop_order') . " WHERE status = 1 and openid=:beid ", array(':beid' => $openid));
			$state2 = mysqld_selectcolumn('SELECT COUNT(*) FROM ' . table('shop_order') . " WHERE status = 2 and openid=:beid ", array(':beid' => $openid));
			$state3 = mysqld_selectcolumn('SELECT COUNT(*) FROM ' . table('shop_order') . " WHERE status = 3 and openid=:beid ", array(':beid' => $openid));
			$bonuslist = mysqld_selectall("select bonus_user.*,bonus_type.type_name,bonus_type.type_money,bonus_type.use_start_date,bonus_type.use_end_date from " . table("bonus_user") . " bonus_user left join  " . table("bonus_type") . " bonus_type on bonus_type.type_id=bonus_user.bonus_type_id where bonus_type.deleted=0 and bonus_user.isuse = 0 and bonus_user.deleted=0  and bonus_user.openid=:openid and bonus_type.use_end_date > :use_end_date", array(':openid' => $openid,':use_end_date'=>time()));
			$best_goods = cs_goods('', 1, 1, 6);
			include themePage('fansindex');
		}else if($op == 'editface'){
			if(!empty($_GP['avatar'])){
				mysqld_update('member',array('avatar'=>$_GP['avatar']),array('openid'=>$openid));
				message('修改成功!',refresh(),'success');
			}else{
				message("对不起，操作有误！",refresh(),'error');
			}
		}
		