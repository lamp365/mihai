<?php
        $member = get_member_account(True,True);
		$openid = $member['openid'] ;
        $op     = empty($_GP['op']) ? 'list' : $_GP['op'];

		if($op == 'list'){
			$period = getLastWeekPeriod();		//上周一到周天的时间戳
			$arrDish= array();
			$configSql = 'SELECT f.category_id,f.free_starttime,f.free_endtime,c.name FROM ' . table('free_config').' f,' .table('shop_category'). " c ";
			$configSql.= " where f.category_id=c.id ";
			$configSql.= " and f.free_starttime='".$period['monday_time']."' and f.free_endtime='".$period['sunday_time']."' ";
			$freeConfig    = mysqld_select($configSql);   //免单的那个分类
			$dish_list_new = $dish_list_old = array();
			if(!empty($freeConfig)){
				$dish_list_new = getFreeDish($freeConfig,$openid);
			}


			$period = getLastWeekPeriod();		//上周一到周天的时间戳
			$configSql = 'SELECT f.category_id,f.free_starttime,f.free_endtime,c.name FROM ' . table('free_config').' f,' .table('shop_category'). " c ";
			$configSql.= " where f.category_id=c.id ";
			$configSql.= " and f.free_endtime<='".$period['monday_time']."' ";
			$configSql.= " order by f.free_endtime desc";
			$configSql.= " limit 0,12 ";		//固定最近12期记录
			//最近12期的免单记录
			$arrFreeConfig = mysqld_selectall($configSql);
			if(!empty($arrFreeConfig)){
				foreach($arrFreeConfig as $key => $value){
					$arrFreeConfig[$key]['dish_list'] = getFreeDish($value,$member['openid']);
				}
			}
			include themePage('freepay');

		}else if($op == 'free_apply'){
			//申请免单
			$period = getLastWeekPeriod();		//上周一到周天的时间戳

			//超过周3申请
			if(date('N')>3) {
				die(showAjaxMess(1002,"请于本周三(含)前申请免单"));
			}

			$configSql = 'SELECT free_id,category_id,free_starttime,free_endtime FROM ' . table('free_config');
			$configSql.= " where free_starttime='".$period['monday_time']."' and free_endtime='".$period['sunday_time']."' ";

			$freeConfig = mysqld_select($configSql);
			if(empty($freeConfig)){
				die(showAjaxMess(1002,"上期该免单不存在，免单申请失败"));
			}

			//免单订单商品
			$arrDish = getFreeDish($freeConfig,$member['openid']);
			if(empty($arrDish)){
				//正常不会为空的 以免
				die(showAjaxMess(1002,"您的订单不存在，免单申请失败"));
			}

			$arrOrderGoodsId = array();

			foreach($arrDish as $value)
			{
				$arrOrderGoodsId[] = $value['order_goods_id'];
			}

			if(empty($arrOrderGoodsId)){
				//正常不会为空的 以免
				die(showAjaxMess(1002,"您的订单不存在，免单申请失败"));
			}


			$res = mysqld_query( "update " . table ( 'shop_order_goods' ) . " SET free_id=".$freeConfig['free_id'].",free_status=1 WHERE id in(".implode(",", $arrOrderGoodsId).") and free_status = 0 " );
			if($res)
			{
				die(showAjaxMess('200', "免单申请成功！"));
			}else{
				die(showAjaxMess('200', "免单申请失败，请稍后再试！"));
			}
		}else if($op == 'rule'){
			//规则
			include themePage('free_charge_rule');
		}
