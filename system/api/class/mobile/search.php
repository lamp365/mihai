<?php
/**
 * app 搜索页搜索结果接口
 */

	$keyword 	= trim($_GP['keyword']);						//搜索关键字
	$page 		= $_GP['page'] ? (int)$_GP['page'] : 1;			//页码
	$limit 		= $_GP['limit'] ? (int)$_GP['limit'] : 10;		//每页记录数
	if($keyword!='')
	{
		switch ($_GP['order'])
		{
			case 'price_low':  		//价格从低到高
		
				$order = "IF( d.type !=  '0', d.timeprice, d.app_marketprice ) asc ,d.id desc";
		
				break;
		
			case 'price_high':  	//价格从高到低
		
				$order = "IF( d.type !=  '0', d.timeprice, d.app_marketprice ) DESC ,d.id desc";
		
				break;
					
			case 'collect':			//收藏排序
					
				$order = 'd.collect_num desc,d.id desc';
					
				break;
					
			case 'sales':			//销量排序
					
				$order = 'd.sales desc,d.id desc';
					
				break;
				
				
			case 'commision':		//佣金
				
				$order = 'd.commision*d.timeprice desc,d.id desc';
				
				break;
					
			default:
		
				$order = 'd.displayorder desc';
		
				break;
		}
		
		//限时促销商品不显示
		$where = " where d.status=1 and d.deleted=0 ";
		
		//价格区间--开始价格
		if($_GP['price_start'])
		{
			$where.=" and IF( d.istime =  '1', d.timeprice, d.app_marketprice )>=".trim($_GP['price_start']);
		}
		
		//价格区间--结束价格
		if($_GP['price_end'])
		{
			$where.=" and IF( d.istime =  '1', d.timeprice, d.app_marketprice )<=".trim($_GP['price_end']);
		}
		
		
		if(isset($_REQUEST['brand_id']))
		{
			$where.= " and g.brand in(".implode(",", json_decode($_REQUEST['brand_id'], true)).") ";
		}
		
		if(isset($_REQUEST['country_id']))
		{
			$where.= " and b.country_id in(".implode(",", json_decode($_REQUEST['country_id'], true)).") ";
		}
		
		$keySearch = " and d.title like '%".$keyword."%' ";
		$whereSearch = $where.$keySearch;
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS d.id,d.title,d.app_marketprice,d.timeprice,d.type,d.timestart,d.timeend,d.commision,d.team_buy_count,d.shoper_num,d.sales,d.total,g.thumb,b.id as brand_id,c.name as country_name,c.icon as country_icon,ca.name as category_name FROM " . table('shop_dish');
		$sql.= " as d LEFT JOIN ". table('shop_goods') ." as g on d.gid = g.id ";
		$sql.= " LEFT JOIN ".table('shop_brand')." as b on b.id = g.brand ";
		$sql.= " LEFT JOIN ".table('shop_country')." as c on b.country_id = c.id ";
		$sql.= " LEFT JOIN ".table('shop_category')." as ca on d.p1 = ca.id ";
		$sql.= $whereSearch;
		$sql.= ' ORDER BY '.$order.' limit '.(($page-1)*$limit).','.$limit;
		
		$dish_list = mysqld_selectall($sql);
		
		if ( empty($dish_list) && !empty($keyword) && function_exists('scws_new') ){
             $word = get_word($keyword);
			 if ( !empty($word) && is_array($word) ){
		     foreach ($word as $word_value ) {
	               $keys[] = " d.title like '%".$word_value."%' ";
		      }
			 }
		     $keys = implode(' or ' , $keys);
		     $keySearch = ' and ('.$keys.')';
			 $whereSearch = $where.$keySearch;
			 
			$sql = "SELECT SQL_CALC_FOUND_ROWS d.id,d.title,d.app_marketprice,d.timeprice,d.type,d.timestart,d.timeend,d.commision,d.team_buy_count,d.shoper_num,d.sales,d.total,g.thumb,b.id as brand_id,c.name as country_name,c.icon as country_icon,ca.name as category_name FROM " . table('shop_dish');
			$sql.= " as d LEFT JOIN ". table('shop_goods') ." as g on d.gid = g.id ";
			$sql.= " LEFT JOIN ".table('shop_brand')." as b on b.id = g.brand ";
			$sql.= " LEFT JOIN ".table('shop_country')." as c on b.country_id = c.id ";
			$sql.= " LEFT JOIN ".table('shop_category')." as ca on d.p1 = ca.id ";
			$sql.= $whereSearch;
			$sql.= ' ORDER BY '.$order.' limit '.(($page-1)*$limit).','.$limit;
			
			$dish_list = mysqld_selectall($sql);
		}
		$total = mysqld_select("SELECT FOUND_ROWS() as total;");	//总记录数
		
		$result['data']['dish_list']	= $dish_list;
		$result['data']['total'] 		= $total['total'];
		$result['code'] 				= 1;
	}
	else{
		$result['message']	= '请输入搜索关键字!';
		$result['code'] 	= 0;
	}
	
	echo apiReturn($result);
	exit;