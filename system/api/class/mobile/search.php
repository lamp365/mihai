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
		
				$order = "IF( a.istime =  '1', a.timeprice, a.app_marketprice ) asc ,a.id desc";
		
				break;
		
			case 'price_high':  	//价格从高到低
		
				$order = "IF( a.istime =  '1', a.timeprice, a.app_marketprice ) DESC ,a.id desc";
		
				break;
					
			case 'collect':			//收藏排序
					
				$order = 'a.collect_num desc,a.id desc';
					
				break;
					
			case 'sales':			//销量排序
					
				$order = 'a.sales desc,a.id desc';
					
				break;
				
				
			case 'commision':		//佣金
				
				$order = 'a.commision*a.timeprice desc,a.id desc';
				
				break;
					
			default:
		
				$order = 'a.displayorder desc';
		
				break;
		}
		
		//限时促销商品不显示
		$where = " a.status=1 and a.deleted=0 ";
		
		//价格区间--开始价格
		if($_GP['price_start'])
		{
			$where.=" and IF( a.istime =  '1', a.timeprice, a.app_marketprice )>=".trim($_GP['price_start']);
		}
		
		//价格区间--结束价格
		if($_GP['price_end'])
		{
			$where.=" and IF( a.istime =  '1', a.timeprice, a.app_marketprice )<=".trim($_GP['price_end']);
		}
		
		$keySearch = " and a.title like '%".$keyword."%' ";
		$whereSearch = $where.$keySearch;
		$dish_list = get_goods(array('field'=>'a.id,a.p1,a.p2,a.p3,a.title,a.productprice,a.marketprice,a.app_marketprice,a.thumb,a.timeprice,a.type,a.timestart,a.timeend,a.team_buy_count,a.commision,a.shoper_num,a.sales,a.total,b.title as btitle,b.thumb as imgs,b.productprice as price, b.marketprice as market ',
									'table'	=>'shop_dish',
									'where'	=>$whereSearch,
									'order'	=> $order,
									'limit' =>(($page-1)*$limit).','.$limit
		));
		if ( empty($dish_list) && !empty($keyword) && function_exists('get_word') ){
             $word = get_word($keyword);
			 if ( !empty($word) && is_array($word) ){
		     foreach ($word as $word_value ) {
	               $keys[] = " a.title like '%".$word_value."%' ";
		      }
			 }
		     $keys = implode(' or ' , $keys);
		     $keySearch = ' and ('.$keys.')';
			 $whereSearch = $where.$keySearch;
			 $dish_list = get_goods(array('field'=>'a.id,a.p1,a.p2,a.p3,a.title,a.productprice,a.marketprice,a.app_marketprice,a.thumb,a.timeprice,a.type,a.timestart,a.timeend,a.team_buy_count,a.commision,a.shoper_num,a.sales,a.total,b.title as btitle,b.thumb as imgs,b.productprice as price, b.marketprice as market ',
									'table'	=>'shop_dish',
									'where'	=>$whereSearch,
									'order'	=> $order,
									'limit' =>(($page-1)*$limit).','.$limit
		     ));
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