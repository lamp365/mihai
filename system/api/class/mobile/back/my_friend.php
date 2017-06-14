<?php
	/**
	 * app 我的觅友接口
	 * @var unknown
	 *
	 */

	$result = array();

	$member = get_member_account ( true, true );

	if(!empty($member) AND $member != 3)
	{
		$openid = $member ['openid'];
		
		$page 	= $_GP['page'] ? (int)$_GP['page'] : 1;			//页码
		$limit 	= $_GP['limit'] ? (int)$_GP['limit'] : 10;		//每页记录数
		
		
		$memberInfo = mysqld_select("SELECT recommend_openid FROM " . table('member') . " where openid=:openid ", array(':openid' => $openid));
		
		//推荐人信息
		$recommendInfo = mysqld_select("SELECT openid,gold,nickname,experience,friend_count,avatar FROM " . table('member') . " where openid=:openid ", array(':openid' => $memberInfo['recommend_openid']));
		
		if(!empty($recommendInfo))
		{
			$member_rank_model = member_rank_model($recommendInfo['experience']);
			
			//积分等级名称
			$recommendInfo['rank_name'] = $member_rank_model['rank_name'];
			
			$sql = "SELECT sum(fee) as invite_fee FROM " . table('member_paylog');
			$sql.= " where openid='".$recommendInfo['openid']."' and type='addgold_byinvite' ";
			
			//通过邀请获得的收入
			$inviteFee = mysqld_select($sql);
			
			$recommendInfo['invite_fee'] = (float)$inviteFee['invite_fee'];
		}
		else{
			$recommendInfo = array();
		}
		
		
		
		switch ($_GP['order'])
		{
					
			case 'order_fee':  				//佣金从高到低
		
				$order = " order_fee desc";
					
				break;
					
			case 'friend_count':		//好友数从高到低
					
				$order = ' m.friend_count desc';
					
				break;
					
			default:
					
				$order = ' m.createtime desc';
					
				break;
		}
		
		
		
		$friendSql = "SELECT SQL_CALC_FOUND_ROWS m.openid,m.nickname,m.experience,m.friend_count,m.avatar,p1.fee as invite_fee,sum(p2.fee) as order_fee FROM " . table('member')." m ";
		$friendSql.= " left join ".table('member_paylog_detail')." p1 on p1.friend_openid=m.openid and p1.type='addgold_byinvite' and p1.status!=-1";
		$friendSql.= " left join ".table('member_paylog')." p2 on p2.friend_openid=m.openid and p2.type='addgold_byorder' ";
		$friendSql.= " where m.recommend_openid='".$openid."' ";
		$friendSql.= " group by m.openid ";
		$friendSql.= " order by {$order} ";
		$friendSql.= " limit ".(($page-1)*$limit).','.$limit;
	
		//我邀请的好友
		$friendList = mysqld_selectall($friendSql);
		
		$friendTotal = mysqld_select("SELECT FOUND_ROWS() as total;");	//总记录数
		
		if(!empty($friendList))
		{
			foreach($friendList as $key=>$value)
			{
				$member_rank_model = member_rank_model($value['experience']);
					
				//积分等级名称
				$friendList[$key]['rank_name'] = $member_rank_model['rank_name'];
			}
		}
				
		$result['data']['recommend_info'] 	= $recommendInfo;
		$result['data']['friend_list'] 		= $friendList;
		$result['data']['total'] 			= $friendTotal['total'];
		$result['code'] 					= 1;
				
		
	}elseif ($member == 3) {
		$result['message'] 	= "该账号已在别的设备上登录！";
		$result['code'] 	= 3;
	}else{
		$result['message'] 	= "用户还未登陆。";
		$result['code'] 	= 2;
	}
	
	echo json_encode($result);
	exit;
	