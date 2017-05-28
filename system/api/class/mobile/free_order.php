<?php
/**
 * 免单接口
 * @var unknown
 */

	$result = array();

	$member = get_member_account ( true, true );

	$operation = $_GP ['op'];
	
	if(!empty($member) AND $member != 3)
	{
		switch ($operation)
		{
			case 'current_list':	//本期免单
				
				$period = getLastWeekPeriod();		//上周一到周天的时间戳
				$arrDish= array();
				
				$configSql = 'SELECT f.category_id,f.free_starttime,f.free_endtime,c.name FROM ' . table('free_config').' f,' .table('shop_category'). " c ";
				$configSql.= " where f.category_id=c.id ";
				$configSql.= " and f.free_starttime='".$period['monday_time']."' and f.free_endtime='".$period['sunday_time']."' ";
				
				$freeConfig = mysqld_select($configSql);
		
				if(!empty($freeConfig))
				{
					$arrDish = getFreeDish($freeConfig,$member['openid']);
				}
				
				$result['data']['free_info']= $freeConfig;
				$result['data']['dish_list']= $arrDish;
				$result['data']['url']		= WEBSITE_ROOT.'index.php?mod=mobile&op=rule&name=shopwap&do=free_charge_rule';				//活动说明URL
				$result['code'] 			= 1;
				
				break;
				
			case 'history_list':	//往期免单记录
				
				$period = getLastWeekPeriod();		//上周一到周天的时间戳
				
				$configSql = 'SELECT f.category_id,f.free_starttime,f.free_endtime,c.name FROM ' . table('free_config').' f,' .table('shop_category'). " c ";
				$configSql.= " where f.category_id=c.id ";
				$configSql.= " and f.free_endtime<='".$period['monday_time']."' ";
				$configSql.= " order by f.free_endtime desc";
				$configSql.= " limit 0,50 ";		//固定最近50期记录
				
				//最近50期的免单记录
				$arrTmp = mysqld_selectall($configSql);
				
				$arrFreeConfig = array();
				
				if(!empty($arrTmp))
				{
					foreach($arrTmp as $key => $value)
					{
						$arrTmp[$key]['dish_list'] = getFreeDish($value,$member['openid']);
						
						//如果有命中的订单商品时
						if(!empty($arrTmp[$key]['dish_list']))
						{
							$arrFreeConfig[] = $arrTmp[$key];
						}
					}
				}
				
				unset($arrTmp);
				
				$result['data']['list'] = $arrFreeConfig;
				$result['code'] 		= 1;
				
				break;
				
			case 'free_apply':		//免单申请
				
				$period = getLastWeekPeriod();		//上周一到周天的时间戳
				
				$configSql = 'SELECT free_id,category_id,free_starttime,free_endtime FROM ' . table('free_config');
				$configSql.= " where free_starttime='".$period['monday_time']."' and free_endtime='".$period['sunday_time']."' ";
				
				$freeConfig = mysqld_select($configSql);
				
				//超过周3申请
				if(date('N')>3)
				{
					$result['message'] 	= "请于本周三(含)前申请免单";
					$result['code'] 	= 0;
				}
				else{
					$result['message'] 	= "免单申请失败";
					$result['code'] 	= 0;
					
					if(!empty($freeConfig))
					{
						//免单订单商品
						$arrDish = getFreeDish($freeConfig,$member['openid']);
							
						if(!empty($arrDish))
						{
							$arrOrderGoodsId = array();
					
							foreach($arrDish as $value)
							{
								$arrOrderGoodsId[] = $value['order_goods_id'];
							}
					
					
							if(!empty($arrOrderGoodsId))
							{
								if(mysqld_query( "update " . table ( 'shop_order_goods' ) . " SET free_id=".$freeConfig['free_id'].",free_status=1 WHERE id in(".implode(",", $arrOrderGoodsId).") and free_status = 0 " ))
								{
									$result['message'] 	= "免单申请成功";
									$result['code'] 	= 1;
								}
							}
						}
					}
				}
				
				break;
		}
	}
	elseif ($member == 3) {
		$result['message'] 	= "该账号已在别的设备上登录！";
		$result['code'] 	= 3;
	}else{
		$result['message'] 	= "用户还未登陆。";
		$result['code'] 	= 2;
	}

	echo json_encode($result);
	exit;