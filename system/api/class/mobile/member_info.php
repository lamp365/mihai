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
				
				$objOpenIm = new OpenIm();
				
				$memberinfo = mysqld_select("SELECT openid,realname,nickname,member_description,avatar,mobile FROM " . table('member') . " where openid=:openid ", array(':openid' => $openid));
				
				$resp = $objOpenIm->getUserInfo($openid); 

				$order_ary = array();
				for ($i=1; $i < 5; $i++) { 
					$order_ary[$i] = get_member_order($i, $openid);
				}
				
				//被人关注的数量（粉丝）
				$followedCount 	= mysqld_select("SELECT count(follow_id) as cnt FROM " . table('follow') . " where followed_openid=:openid", array(':openid' => $openid));
				
				//关注别人的数量（关注）
				$followerCount 	= mysqld_select("SELECT count(follow_id) as cnt FROM " . table('follow') . " where follower_openid=:openid", array(':openid' => $openid));
				//发布的笔记数量
				$noteCount 		= mysqld_select("SELECT count(note_id) as cnt FROM " . table('note') . " where openid=:openid and deleted=0 ", array(':openid' => $openid));
				//发布的头条数量
				$headlineCount 	= mysqld_select("SELECT count(headline_id) as cnt FROM " . table('headline') . " where openid=:openid and deleted=0 ", array(':openid' => $openid));
				
				
				$memberinfo['fansCnt'] 		= $followedCount['cnt'];		//粉丝数
				$memberinfo['followCnt'] 	= $followerCount['cnt'];		//关注别人的数量
				$memberinfo['noteCnt'] 		= $noteCount['cnt'];			//发布的笔记数量
				$memberinfo['headlineCnt'] 	= $headlineCount['cnt'];		//发布的头条数量
				
				$result['data']['member_info'] 	= $memberinfo;
				$result['data']['im_userinfo']	= $resp->userinfos;				//im用户信息
				$result['data']['order_num']	= $order_ary;					//订单数量
				$result['code'] 				= 1;
				
				break;
		}
	}elseif ($member == 3) {
		$result['message'] 	= "该账号已在别的设备上登录！";
		$result['code'] 	= 3;
	}else{
		$result['message'] 	= "用户还未登陆。";
		$result['code'] 	= 2;
	}
	
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
    	}else{
    		return false;
    	}
		$order = mysqld_selectall("SELECT SQL_CALC_FOUND_ROWS a.isdraw, a.isprize, e.status as group_status, a.status FROM ".table('shop_order')." as a left join ".table('shop_order_goods')." as b on a.id=b.orderid left join ".table('team_buy_member')." as c on a.id=c.order_id left join ".table('team_buy_group')." as e on c.group_id=e.group_id WHERE ".$where);
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
		}
		
		return $total['total'];
	}