<?php
	/**
	 * app 个人信息接口
	 * @var unknown
	 *
	 */

	$result = array();

	$member = get_member_account ( true, true );
	$operation = $_GP ['op'];
	
	if(!empty($member) AND $member != 3)
	{
		$openid = $member ['openid'];
		
		$objValidator 	= new Validator();
		
		switch ($operation)
		{
			case 'avatar_update':		//头像更新
				
				if ($_FILES['avatar']['error']==0) {
					
					$upload = file_upload($_FILES['avatar']);
					
					//出错时
					if (is_error($upload)) {
							
						$result['message'] 	= $upload['message'];
						$result['code'] 	= 0;
					}
					else{
						$data = array('avatar' 	=> $upload['path']);
							
						if(mysqld_update('member', $data,array('openid' =>$openid)))
						{
							$result['message'] 			= "头像更新成功。";
							$result['data']['avatar'] 	= $upload['path'];
							$result['code'] 			= 1;
						}
						else{
							$result['message'] 	= "头像更新失败。";
							$result['code'] 	= 0;
						}
					}
				}
				else{
					
					$result['message'] 	= "头像上传失败。";
					$result['code'] 	= 1;
				}
				
				break;
			case 'birthday_update':
                // 废弃从缓存中获取数据
			    $mem = member_get($openid);
				if ( !empty($mem['birthday']) ){
                    $result['message'] 	= "生日填写后不支持修改哦";
					$result['code'] 	    = 0;  
				}else{
                     $unixTime = strtotime($_GP['birthday']);
					 if ( ! $unixTime ){
                           $result['message'] 	= "日期格式不对";
					       $result['code'] 	    = 0;  
					 }else{
                          if (date('Y-m-d', $unixTime) != $_GP['birthday']){
                               $result['message'] 	    = "日期格式不对";
					           $result['code'] 	        = 0;  
						  }else{
							   $data = array('birthday' 	=> $unixTime);
                               if(mysqld_update('member', $data,array('openid' =>$openid)))
								{
									$result['message'] 	= "您的生日设置成功。";
									$result['code'] 	= 1;
								}
						  }
					 }
				}
				break;
			case 'nickname_update':		//昵称更新
				
				if(!$objValidator->lengthValidator($_GP['nickname'], '0,16'))
				{
					$result['message'] 	= "昵称最多不超过16个字符。";
					$result['code'] 	= 0;
				}
				else{
					$data = array('nickname' 	=> $_GP['nickname']);
					
					if(mysqld_update('member', $data,array('openid' =>$openid)))
					{
						$result['message'] 	= "昵称更新成功。";
						$result['code'] 	= 1;
					}
					else{
						$result['message'] 	= "昵称更新失败。";
						$result['code'] 	= 0;
					}
				}
				
				break;
				
			case 'description_update':		//个人介绍更新
				
				if(!$objValidator->lengthValidator($_GP['member_description'], '0,30'))
				{
					$result['message'] 	= "个人介绍最多不超过30个字符。";
					$result['code'] 	= 0;
				}
				else{
					$data = array('member_description' 	=> $_GP['member_description']);
					
					if(mysqld_update('member', $data,array('openid' =>$openid)))
					{
						$result['message'] 	= "个人介绍更新成功。";
						$result['code'] 	= 1;
					}
					else{
						$result['message'] 	= "个人介绍更新失败。";
						$result['code'] 	= 0;
					}
				}
				
				break;
				
			default:					//个人信息详情
				
				$objOpenIm 	= new OpenIm();
				$period 	= getLastWeekPeriod();					//上周一到周天的时间戳
				
				$memberinfo = mysqld_select("SELECT birthday,openid,realname,nickname,member_description,avatar,mobile,gold,freeorder_gold,freeorder_gold_endtime,experience FROM " . table('member') . " where openid=:openid ", array(':openid' => $openid));

				if (intval($memberinfo['freeorder_gold_endtime']) < time()) {
					$memberinfo['freeorder_gold'] = 0;
				}
				
				$resp = $objOpenIm->getUserInfo($openid); 

				$order_ary = array();
				for ($i=1; $i < 6; $i++) { 
					$order_ary[$i] = get_member_order($i, $openid);
				}
				
				//被人关注的数量（粉丝）
				$followedCount 	= mysqld_select("SELECT count(follow_id) as cnt FROM " . table('follow') . " where followed_openid=:openid", array(':openid' => $openid));
				
				//关注别人的数量（关注）
				$followerCount 	= mysqld_select("SELECT count(follow_id) as cnt FROM " . table('follow') . " where follower_openid=:openid", array(':openid' => $openid));
				//发布的笔记数量
				$noteCount 		= mysqld_select("SELECT count(note_id) as cnt FROM " . table('note') . " where openid=:openid and deleted=0 ", array(':openid' => $openid));
				//发布的头条数量
				//$headlineCount 	= mysqld_select("SELECT count(headline_id) as cnt FROM " . table('headline') . " where openid=:openid and deleted=0 ", array(':openid' => $openid));
				//地址
				$arrAddress = mysqld_selectall("SELECT id,realname,mobile,province,city,area,address,isdefault FROM " . table('shop_address') . " WHERE deleted=0 and openid = :openid ", array(':openid' => $openid));
				
				$free_config = mysqld_select ( "SELECT c.name FROM " . table ( 'free_config' ) .' f,'.table('shop_category'). " c where c.id = f.category_id and free_starttime='".$period['monday_time']."' and free_endtime='".$period['sunday_time']."' ORDER BY f.createtime DESC " );
				
				if(!empty($free_config))
				{
					$free_category_name = $free_config['name'];
				}
				else{
					$free_category_name = '';
				}
				
				
				$member_rank_model = member_rank_model($memberinfo['experience']);
				if(empty($member_rank_model)) { 
					$memberinfo['rank'] = '无';
				}else{
					$memberinfo['rank'] = $member_rank_model['rank_name']; 
				}
				$memberinfo['fansCnt'] 		= $followedCount['cnt'];		//粉丝数
				$memberinfo['followCnt'] 	= $followerCount['cnt'];		//关注别人的数量
				$memberinfo['noteCnt'] 		= $noteCount['cnt'];			//发布的笔记数量
				//$memberinfo['headlineCnt'] 	= $headlineCount['cnt'];		//发布的头条数量
				// 优惠券数量
				$bouns = mysqld_selectall("SELECT SQL_CALC_FOUND_ROWS b.* FROM ".table('bonus_user')." as a left join ".table('bonus_type')." as b on a.bonus_type_id=b.type_id WHERE a.openid='".$openid."' AND a.isuse=0 AND b.send_type IN (0,1,2) AND a.deleted<>1 AND b.deleted<>1 AND b.use_end_date>".time());
				$bouns_n = mysqld_select("SELECT FOUND_ROWS() as total;");
				$memberinfo['bounsNum'] = $bouns_n['total'];
				if (!empty($memberinfo['birthday'])){
                $memberinfo['birthday'] = date('Y-m-d', $memberinfo['birthday']);
				}else{
                $memberinfo['birthday'] = '';
				}
				// 自定义菜单
				$diymenu = mysqld_selectall("SELECT icon,url,tname,remark FROM " . table('shop_diymenu')." WHERE app_use=1 and menu_type='fansindex' ORDER BY torder DESC");

				
				$result['data']['member_info'] 			= $memberinfo;
				$result['data']['im_userinfo']			= $resp->userinfos;				//im用户信息
				$result['data']['order_num']			= $order_ary;					//订单数量
				$result['data']['diy_menu']				= $diymenu; 					// 自定义菜单
				$result['data']['free_category_name']	= $free_category_name;			//本期免单分类
				$result['data']['has_address']			= !empty($arrAddress) ? 1 :0;	//是否有地址
				
				$result['code'] 						= 1;
				
				break;
		}
	}elseif ($member == 3) {
		$result['message'] 	= "该账号已在别的设备上登录！";
		$result['code'] 	= 3;
	}else{
		$result['message'] 	= "用户还未登陆。";
		$result['code'] 	= 2;
	}
	// dump($result);
	echo json_encode($result);
	exit;
	
	// 订单筛选方法
	function get_member_order($status, $openid) {
		$where = "a.openid='".$openid."' AND a.deleted=0";
		if ($status == 1) {
			// 待付款
    		$u_status = 0;
    		$where.= " AND a.status=$u_status";
    	}elseif ($status == 3) {
    		// 待发货
    		$u_status = 1;
    		$where.= " AND a.status=$u_status";
    	}elseif ($status == 4) {
    		// 待收货
    		$u_status = 2;
    		$where.= " AND a.status=$u_status";
    	}elseif ($status == 2) {
    		// 团购中
    		$where.= " AND e.status<>0 AND e.finish=0";
    	}elseif ($status == 5) {
    		// 待评价
    		$u_status = 3;
    		$where.= " AND a.status=$u_status AND b.iscomment=0";
    	}else{
    		return false;
    	}
		$order = mysqld_selectall("SELECT SQL_CALC_FOUND_ROWS a.id,a.isdraw, a.isprize, e.status as group_status, a.status, b.iscomment FROM ".table('shop_order')." as a left join ".table('shop_order_goods')." as b on a.id=b.orderid left join ".table('team_buy_member')." as c on a.id=c.order_id left join ".table('team_buy_group')." as e on c.group_id=e.group_id WHERE ".$where);
		// .table('shop_order_goods')." as b on a.id=b.orderid left join "
		// 总记录数
		$total = mysqld_select("SELECT FOUND_ROWS() as total;");
		$total['total'] = intval($total['total']);
		if (!empty($order)) {
			foreach ($order as $ok => $ov) {
				if ($status == 2) {
		    		// 团购商品需判断订单是否抽奖订单
		    		if (intval($ov['isdraw']) == 0) {
		    			if ($ov['group_status'] == '1') {
		    				$total['total'] -= 1;
		    				continue;
		    			}
		    		}
		    	}
		    	if ($status == 3) {
		    		// 未成团不在待发货
		    		if ($ov['group_status'] != '1' AND $ov['group_status'] != NULL) {
	    				$total['total'] -= 1;
	    				continue;
		    		}
		    		// 抽奖团只有中奖之后才到待发货
		    		if ($ov['isdraw'] == '1') {
		    			if ($ov['isprize'] != '1') {
		    				$total['total'] -= 1;
		    				continue;
		    			}
		    		}
		    	}
			}

			// 处理单订单多商品
			$orderid_ary = array();
			foreach ($order as $orrk => $orrv) {
				foreach ($orderid_ary as $ody) {
					if ($orrv['id'] == $ody['orderid']) {
						$order[$ody['key']]['goods'][] = $orrv['goods'][0];
						// unset($order[$orrk]);
						$total['total'] -= 1;
						continue 2;
					}
				}
				$oa = array();
				$oa['orderid'] = $orrv['id'];
				$oa['key'] = $orrk;
				$orderid_ary[] = $oa;
			}
			// 重新排列数组下标
			$order = array_merge($order);
		}
		
		// return count($order);
		return $total['total'];
	}