<?php
	/**
	 * app 分享订单--添加订单列表接口
	 * @var unknown
	 *
	 */

	$result = array();

	$member = get_member_account ( true, true );

	if(!empty($member) AND $member != 3)
	{
		$openid = $member ['openid'];
		
		switch ($_GP['op'])
		{
			case 'add_order':  				//订单确认添加分享
		
				$from_platform 	= trim($_GP['from_platform']);		//订单来源
				$ordersn 		= trim($_GP['ordersn']);			//订单编号
				
				$inviteSetting = mysqld_select ( "SELECT value FROM " . table ( 'config' ) . " where name='invite_setting' " );		//邀请收益配置信息
				$thirdOrderInfo= mysqld_select("SELECT ordersn FROM " . table('third_order')." where openid='{$openid}' and share_status=1 ");
				$shopOrderInfo = mysqld_select("SELECT ordersn FROM " . table('shop_order')." where openid='{$openid}' and share_status=1 ");
				
				$arrInviteSetting= unserialize($inviteSetting['value']);
				
				if(empty($from_platform))
				{
					$result['message'] 	= "订单来源不能为空";
					$result['code'] 	= 0;
				}
				elseif(empty($ordersn))
				{
					$result['message'] 	= "订单编号不能为空";
					$result['code'] 	= 0;
				}
				elseif(!empty($thirdOrderInfo) || !empty($shopOrderInfo))
				{
					$result['message'] 	= "已添加分享的订单";
					$result['code'] 	= 0;
				}
				else{
					$updateData = array('share_status'=>1);
					
					//自有平台订单
					if($from_platform=='self')
					{
						$orderInfo = mysqld_select ( "SELECT price FROM " . table ( 'shop_order' ) . " where ordersn='{$ordersn}' " );
						
						//单笔分享收益大于订单实付金额时
						if($arrInviteSetting['order_share_price']>$orderInfo['price'])
						{
							$result['message'] 	= "亲，您的订单实付金额小于单笔分享收益,无法添加分享订单";
							$result['code'] 	= 0;
						}
						else{
							if(mysqld_update ('shop_order',$updateData,array('ordersn' =>$ordersn,'share_status'=>0,'status'=>3,'openid'=>$openid)))
							{
								$result['message'] 	= "添加分享订单成功";
								$result['code'] 	= 1;
							}
							else{
								$result['message'] 	= "添加分享订单失败";
								$result['code'] 	= 0;
							}
						}
					}
					else{
						$orderInfo = mysqld_select ( "SELECT price FROM " . table ( 'third_order' ) . " where ordersn='{$ordersn}' " );
						
						//单笔分享收益大于订单实付金额时
						if($arrInviteSetting['order_share_price']>$orderInfo['price'])
						{
							$result['message'] 	= "亲，您的订单实付金额小于单笔分享收益,无法添加分享订单";
							$result['code'] 	= 0;
						}
						else{
							if(mysqld_update ('third_order',$updateData,array('ordersn' =>$ordersn,'share_status'=>0,'status'=>3,'openid'=>$openid)))
							{
								$result['message'] 	= "添加分享订单成功";
								$result['code'] 	= 1;
							}
							else{
								$result['message'] 	= "添加分享订单失败";
								$result['code'] 	= 0;
							}
						}
					}
				}
				
				break;
				
				
			case 'apply':					//申请减免
				
				$from_platform 	= trim($_GP['from_platform']);		//订单来源
				$ordersn 		= trim($_GP['ordersn']);			//订单编号
				
				if(empty($from_platform))
				{
					$result['message'] 	= "订单来源不能为空";
					$result['code'] 	= 0;
				}
				elseif(empty($ordersn))
				{
					$result['message'] 	= "订单编号不能为空";
					$result['code'] 	= 0;
				}
				else{
					$updateData = array('share_status'=>2,'updatetime'=>time());
						
					//自有平台订单
					if($from_platform=='self')
					{
						if(mysqld_update ('shop_order',$updateData,array('ordersn' =>$ordersn,'share_status'=>1,'status'=>3,'openid'=>$openid)))
						{
							$result['message'] 	= "已申请订单减免,审核中请耐心等待";
							$result['code'] 	= 1;
						}
						else{
							$result['message'] 	= "申请订单减免失败";
							$result['code'] 	= 0;
						}
					}
					else{
						if(mysqld_update ('third_order',$updateData,array('ordersn' =>$ordersn,'share_status'=>1,'status'=>3,'openid'=>$openid)))
						{
							$result['message'] 	= "已申请订单减免,审核中请耐心等待";
							$result['code'] 	= 1;
						}
						else{
							$result['message'] 	= "申请订单减免失败";
							$result['code'] 	= 0;
						}
					}
					
				}
				
				break;
				
			case 'history_list':			//查看历史分享列表
				
				$thirdSql = "SELECT t.ordersn,t.price,t.from_platform,t.share_status,t.createtime,t.updatetime,count(p.friend_openid) as friend_cnt,sum(p.fee) as order_fee FROM " . table('third_order')." t ";
				$thirdSql.= " left join ".table('member_paylog_detail')." p on p.ordersn=t.ordersn and p.type='addgold_byinvite' ";
				$thirdSql.= " where t.openid='".$openid."' ";
				$thirdSql.= " and t.status=3 ";				//交易成功订单
				$thirdSql.= " and t.share_status!=0 and t.share_status!=1 ";
				$thirdSql.= " group by t.ordersn ";
				$thirdSql.= " order by t.updatetime desc ";
				
				//第三方平台订单
				$thirdOrderList = mysqld_selectall($thirdSql);
			
				$sql = "SELECT o.ordersn,o.price,o.createtime,o.share_status,'self' as from_platform,o.updatetime,count(p.friend_openid) as friend_cnt,sum(p.fee) as order_fee FROM " . table('shop_order')." o ";
				$sql.= " left join ".table('member_paylog_detail')." p on p.ordersn=o.ordersn and p.type='addgold_byinvite' ";
				$sql.= " where o.openid='".$openid."' ";
				$sql.= " and o.status=3 ";					//交易成功订单
				$sql.= " and o.share_status!=0 and o.share_status!=1 ";
				$sql.= " group by o.ordersn ";
				$sql.= " order by o.updatetime desc ";
		
				//自有平台订单
				$orderList = mysqld_selectall($sql);
				
				
				if(!empty($orderList) || !empty($thirdOrderList))
				{
					$list = array_merge($orderList,$thirdOrderList);
					
					//排序
					$list = arraySequence($list, 'updatetime', 'SORT_DESC');
				}
				else{
					
					$list = array();
				}
				
				$result['data']['list'] = $list;
				$result['code'] 		= 1;
				
				break;
					
			case 'list':					//添加分享订单列表

				$thirdSql = "SELECT ordersn,price,from_platform,createtime FROM " . table('third_order');
				$thirdSql.= " where openid='".$openid."' ";
				$thirdSql.= " and status=3 ";
				$thirdSql.= " and share_status=0 ";
				$thirdSql.= " order by createtime desc ";
				
				//第三方平台订单
				$thirdOrderList = mysqld_selectall($thirdSql);
				
				
				$sql = "SELECT ordersn,price,createtime,'self' as from_platform FROM " . table('shop_order');
				$sql.= " where openid='".$openid."' ";
				$sql.= " and status=3 ";
				$sql.= " and share_status=0 ";
				$sql.= " order by createtime desc ";
				
				//自有平台订单
				$orderList = mysqld_selectall($sql);
				
				if(!empty($orderList) || !empty($thirdOrderList))
				{
					$list = array_merge($orderList,$thirdOrderList);
					
					//排序
					$list = arraySequence($list, 'createtime', 'SORT_DESC');
				}
				else{
					$list = array();
				}
				
				$result['data']['list'] = $list;
				$result['code'] 		= 1;
				
				break;
					
			default:		//分享挣钱主页
				
				$ordersn 	= '';						//订单编号
				$myIncome 	= array();					//我的收益
				$shareOrder	= array();					//我分享中的订单信息
				
				
				$inviteSetting = mysqld_select ( "SELECT value FROM " . table ( 'config' ) . " where name='invite_setting' " );		//邀请收益配置信息
				$thirdOrderInfo= mysqld_select("SELECT ordersn,price,from_platform FROM " . table('third_order')." where openid='{$openid}' and share_status=1 ");
				$shopOrderInfo = mysqld_select("SELECT ordersn,price,'self' as from_platform FROM " . table('shop_order')." where openid='{$openid}' and share_status=1 ");
				
				$arrInviteSetting= unserialize($inviteSetting['value']);
				
				if(!empty($thirdOrderInfo))
				{
					$shareOrder['ordersn'] 		= $thirdOrderInfo['ordersn'];
					$shareOrder['price'] 		= $thirdOrderInfo['price'];
					$shareOrder['from_platform']= $thirdOrderInfo['from_platform'];
				}
				elseif(!empty($shopOrderInfo))
				{
					$shareOrder['ordersn'] 		= $shopOrderInfo['ordersn'];
					$shareOrder['price'] 		= $shopOrderInfo['price'];
					$shareOrder['from_platform']= $shopOrderInfo['from_platform'];
				}
				
				//分享中的订单不为空时
				if(!empty($shareOrder))
				{
					$sharePaylog = mysqld_select("SELECT count(*) as share_cnt,sum(fee) as share_fee FROM " . table('member_paylog_detail')." where openid='{$openid}' and ordersn='{$shareOrder['ordersn']}' and status!=-1");
					
					$shareOrder['share_cnt'] = $sharePaylog['share_cnt'];
					$shareOrder['share_fee'] = $sharePaylog['share_fee'];
					
					$ordersn = $shareOrder['ordersn'];
				}
				
				
				//用户详情
				$memberInfo = mysqld_select("SELECT openid,gold,nickname,friend_count,avatar FROM " . table('member') . " where openid=:openid ", array(':openid' => $openid));
				
				$inviteSql = "SELECT sum(fee) as invite_fee FROM " . table('member_paylog_detail');
				$inviteSql.= " where openid= '{$openid}' and type='addgold_byinvite' and status!=-1";
				//好友邀请收入
				$inviteFee = mysqld_select($inviteSql);
				
				
				$orderSql = "SELECT sum(fee) as order_fee FROM " . table('member_paylog');
				$orderSql.= " where openid= '{$openid}' and type='addgold_byorder' ";
				//订单佣金收入
				$orderFee = mysqld_select($orderSql);
				
				$myIncome['friend_count'] 	= $memberInfo['friend_count'];		//我的好友数
				$myIncome['invite_income']	= $inviteFee['invite_fee'];			//我的分享奖励
				$myIncome['order_income']	= $orderFee['order_fee'];			//我的佣金
				
				
				
				$rankingSql = "SELECT m.openid,m.nickname,m.friend_count,m.avatar,sum(p.fee) as invite_fee FROM " . table('member')." m, ".table('member_paylog_detail')." p ";
				$rankingSql.= " where p.openid=m.openid and p.type='addgold_byinvite'  ";
				$rankingSql.= " and p.status!=-1 ";
				$rankingSql.= " group by m.openid ";
				$rankingSql.= " order by invite_fee desc";
				$rankingSql.= " limit 0,10 ";
			
				//分享达人列表
				$rankingList = mysqld_selectall($rankingSql);
				
				
				$result['data']['direct_share_price']	= $arrInviteSetting['direct_share_price'];				//直接分享单价
				$result['data']['order_share_price'] 	= $arrInviteSetting['order_share_price'];				//订单分享单价
				$result['data']['myIncome'] 			= $myIncome;										//我的收益
				$result['data']['rankingList'] 			= $rankingList;										//分享达人列表
				$result['data']['shareOrder'] 			= $shareOrder;
				$result['data']['shareUrl'] 			= getMiyouShareUrl($openid,$ordersn);				//分享的URL
				
				$result['code'] 		= 1;
					
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
