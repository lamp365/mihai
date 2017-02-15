<?php
	/**
	 * app 类目下的商品列表(app2.1以后的版本)接口
	 * @var unknown
	 *
	 */

	$cate_id= (int)$_GP['cate_id'];							//类目ID
	$page 	= $_GP['page'] ? (int)$_GP['page'] : 1;			//页码
	$limit 	= $_GP['limit'] ? (int)$_GP['limit'] : 10;		//每页记录数

	switch ($_GP['order'])
	{
		case 'price_low':  		//价格从低到高
			
			$order = "IF( d.type !=  '0', d.timeprice, d.app_marketprice ) asc ,d.id desc";
			
			break;
			
		case 'price_high':  	//价格从高到低

			$order = "IF( d.type !=  '0', d.timeprice, d.app_marketprice ) DESC ,d.id desc";
					
			break;
			
		case 'commision':		//佣金
			
			$order = 'd.commision*d.app_marketprice desc,a.id desc';
			
			break;
			
		case 'sales':			//销量排序
					
			$order = 'd.sales desc,a.id desc';
				
			break;
		
		default:
			
			$order = 'd.displayorder desc,d.id desc';
			
			break;
	}
	
	//类目信息
	$categoryInfo = mysqld_select("SELECT id,name,parentid FROM " . table('shop_category') . "  where deleted=0 and enabled=1 and id=".$cate_id);
	
	$dishIds = getCategoryExtendDishId($cate_id);
	
	$sql = "SELECT SQL_CALC_FOUND_ROWS d.id,d.title,d.app_marketprice,d.timeprice,d.type,d.timestart,d.timeend,d.commision,g.thumb,b.id as brand_id,c.name as country_name,c.icon as country_icon FROM " . table('shop_dish');
	$sql.= " as d LEFT JOIN ". table('shop_goods') ." as g on d.gid = g.id ";
	$sql.= " LEFT JOIN ".table('shop_brand')." as b on b.id = g.brand ";
	$sql.= " LEFT JOIN ".table('shop_country')." as c on b.country_id = c.id ";
	
	if(!empty($dishIds))
	{
		$sql.=' where (d.p1='.$cate_id.' or d.p2='.$cate_id.' or d.p3='.$cate_id.' or d.id in('.implode(",", $dishIds).') ) and d.status=1 and d.deleted=0 and d.type in(0,1,2,3)';
	}
	else{
		$sql.=' where (d.p1='.$cate_id.' or d.p2='.$cate_id.' or d.p3='.$cate_id.') and d.status=1 and d.deleted=0 and d.type in(0,1,2,3)';
	}
	
	$sql.= ' ORDER BY '.$order.' limit '.(($page-1)*$limit).','.$limit;
	
	$dish_list = mysqld_selectall($sql);
	
	$total = mysqld_select("SELECT FOUND_ROWS() as total;");	//总记录数
	
	$result['data']['dish_list']	= $dish_list;
	$result['data']['total'] 		= $total['total'];
	$result['data']['categoryInfo'] = $categoryInfo;
	$result['code'] 				= 1;
	
	echo apiReturn($result);
	exit;