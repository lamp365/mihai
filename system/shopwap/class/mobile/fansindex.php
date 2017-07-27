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
				$member_rank_model = member_rank_model($member['experience']);
				
			}
			//余额等于 现金余额加免单余额
			$member['gold'] = getMemberBalance($member['gold'],$member['freeorder_gold'],$member['freeorder_gold_endtime']);

			$fansindex_menu_list = mysqld_selectall("SELECT * FROM " . table('shop_diymenu') . " where menu_type='fansindex' order by torder desc");
            $totlist = getCartTotal(2);
			$state99 = mysqld_selectcolumn('SELECT COUNT(*) FROM ' . table('shop_order') . " WHERE status >= 0 and openid=:beid ", array(':beid' => $openid));
			$state0 = mysqld_selectcolumn('SELECT COUNT(*) FROM ' . table('shop_order') . " WHERE status = 0 and openid=:beid ", array(':beid' => $openid));
			$state1 = mysqld_selectcolumn('SELECT COUNT(*) FROM ' . table('shop_order') . " WHERE status = 1 and openid=:beid ", array(':beid' => $openid));
			$state2 = mysqld_selectcolumn('SELECT COUNT(*) FROM ' . table('shop_order') . " WHERE status = 2 and openid=:beid ", array(':beid' => $openid));
			$state3 = mysqld_selectcolumn('SELECT COUNT(*) FROM ' . table('shop_order') . " WHERE status = 3 and openid=:beid ", array(':beid' => $openid));
			$bonuslist = mysqld_selectall("select bonus_user.*,bonus_type.type_name,bonus_type.type_money,bonus_type.use_start_date,bonus_type.use_end_date from " . table("bonus_user") . " bonus_user left join  " . table("bonus_type") . " bonus_type on bonus_type.type_id=bonus_user.bonus_type_id where bonus_type.deleted=0 and bonus_user.isuse = 0 and bonus_user.deleted=0  and bonus_user.openid=:openid and bonus_type.use_end_date > :use_end_date", array(':openid' => $openid,':use_end_date'=>time()));

			if(is_mobile_request()){
				//wap端需要数据  个人喜好
				$hobby  = mysqld_select("SELECT * FROM ".table('member_info')." WHERE openid = :openid ", array(":openid"=>$openid));
				// 增加地址
				$address  = mysqld_select("SELECT * FROM ".table('shop_address')." WHERE openid = :openid ", array(":openid"=>$openid));
				if ( !empty($address) ) {
                    $address  = $address['address'];
				}else{
                    $address  = '您还未设置配送地址';
				}
				$buyArr = array('me'=>'自己','friend'=>'朋友','elder'=>'长辈','partner'=>'伴侣');
				$funArr = array('price'=>'价格','effect'=>'功能','brand'=>'品牌','publicity'=>'知名度');
				$cat_arr = array();
				if(!empty($hobby['category'])){
					$category = explode(',',$hobby['category']);
					foreach($category as $cate_id){
						$cat_info  = mysqld_select("select name from ".table('shop_category')." where id={$cate_id}");
						$cat_arr[] = $cat_info['name'];
					}
				}
			}else{
				//pc端需要数据
				$best_goods = cs_goods('', 1, 1, 6);
			}
			include themePage('fansindex');

		}else if($op == 'editface'){
			if(!empty($_GP['avatar'])){
				mysqld_update('member',array('avatar'=>$_GP['avatar']),array('openid'=>$openid));
				message('修改成功!',refresh(),'success');
			}else{
				message("对不起，操作有误！",refresh(),'error');
			}
		}
		