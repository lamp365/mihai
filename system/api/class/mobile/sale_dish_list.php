<?php
	/**
	 * app 特卖商品(团购、秒杀、今日特价)列表接口
	 * @var unknown
	 *
	 */

	$type 		= (int)$_GP['type'];							//促销类型
	$page 		= $_GP['page'] ? (int)$_GP['page'] : 1;			//页码
	$limit 		= (int)$_GP['limit'];							//每页记录数
	
	
	$order 		= 'a.displayorder desc ';
	$whereSql = " a.status=1 and a.deleted=0 and a.type={$type} ";
	
	//今日特价
	if($type==3)
	{
		//只显示当天的
		$whereSql.= " and a.timestart>=".strtotime(date('Y-m-d').' 00:00:00');						
		$whereSql.= " and a.timeend<=".strtotime(date('Y-m-d').' 23:59:59');
		$whereSql.= " and a.timeend>=".time();
	}
	else{
		$whereSql.= " and a.timeend>=".time();						//显示现在+将来的
	}
	
	$dish_list = get_goods(array('field'=>'a.id,a.p1,a.p2,a.p3,a.title,a.productprice,a.marketprice,a.app_marketprice,a.thumb,a.timeprice,a.type,a.timestart,a.timeend,a.team_buy_count,a.total,a.draw,b.title as btitle,b.thumb as imgs,b.productprice as price, b.marketprice as market ',
									'table'=>'shop_dish',
									'where'	=>$whereSql,
									'order'	=> $order,
									'limit' =>(($page-1)*$limit).','.$limit
							));
	
	$total = mysqld_select("SELECT FOUND_ROWS() as total;");	//总记录数
	
	$result['data']['dish_list']	= $dish_list;
	$result['data']['total'] 		= $total['total'];
	$result['code'] 				= 1;
	
	echo apiReturn($result);
	exit;