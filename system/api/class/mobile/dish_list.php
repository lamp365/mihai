<?php
	/**
	 * app 类目下的商品列表接口
	 * @var unknown
	 *
	 */

	$cate_id= (int)$_GP['cate_id'];							//类目ID
	$page 	= $_GP['page'] ? (int)$_GP['page'] : 1;			//页码
	$limit 	= $_GP['limit'] ? (int)$_GP['limit'] : 10;		//每页记录数

	switch ($_GP['order'])
	{
		case 'price_low':  		//价格从低到高
			
			$order = "IF( a.istime =  '1', a.timeprice, a.app_marketprice ) asc ,a.id desc";
			
			break;
			
		case 'price_high':  	//价格从高到低

			$order = "IF( a.istime =  '1', a.timeprice, a.app_marketprice ) DESC ,a.id desc";
					
			break;
			
		case 'commision':		//佣金
			
			$order = 'a.commision*a.app_marketprice desc,a.id desc';
			
			break;
			
		case 'sales':			//销量排序
					
			$order = 'a.sales desc,a.id desc';
				
			break;
		
		default:
			
			$order = 'a.displayorder desc,a.id desc';
			
			break;
	}
	
	//类目信息
	$categoryInfo = mysqld_select("SELECT id,name,parentid FROM " . table('shop_category') . "  where deleted=0 and enabled=1 and id=".$cate_id);
	
	$dishIds = getCategoryExtendDishId($cate_id);
	
	if(!empty($dishIds))
	{
		$where ='(a.p1='.$cate_id.' or a.p2='.$cate_id.' or a.p3='.$cate_id.' or a.id in('.implode(",", $dishIds).') ) and a.status=1 and a.deleted=0 and a.type in(0,1,2,3)';
	}
	else{
		$where ='(a.p1='.$cate_id.' or a.p2='.$cate_id.' or a.p3='.$cate_id.') and a.status=1 and a.deleted=0 and a.type in(0,1,2,3)';
	}
	
	$dish_list = get_goods(array('field'=>'a.id,a.p1,a.p2,a.p3,a.title,a.productprice,a.marketprice,a.app_marketprice,a.thumb,a.timeprice,a.type,a.timestart,a.timeend,a.team_buy_count,a.commision,a.sales,a.total,b.title as btitle,b.thumb as imgs,b.productprice as price, b.marketprice as market ',
								'table'	=>'shop_dish',
								'where'	=> $where,
								'order'	=> $order,
								'limit' =>(($page-1)*$limit).','.$limit
							));
	
	$total = mysqld_select("SELECT FOUND_ROWS() as total;");	//总记录数
	
	
	
	$result['data']['dish_list']	= $dish_list;
	$result['data']['total'] 		= $total['total'];
	$result['data']['categoryInfo'] = $categoryInfo;
	$result['code'] 				= 1;
	
	echo apiReturn($result);
	exit;