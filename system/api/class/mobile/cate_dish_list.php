<?php
	/**
	 * app 首页专题商品列表接口
	 * @var unknown
	 *
	 */

	$limit 	= $_GP['limit'] ? (int)$_GP['limit'] : 10;		//显示的记录数

	//类目信息
	$arrCategory = mysqld_selectall("SELECT id,name,adv_wap FROM " . table('shop_category') . "  where deleted=0 and enabled=1 and isrecommand=1 order by displayorder desc");

	if(!empty($arrCategory))
	{
		foreach($arrCategory as $key => $value)
		{
			$dishIds = getCategoryExtendDishId($value['id']);
			
			if(!empty($dishIds))
			{
				$where ='(a.p1='.$value['id'].' or a.p2='.$value['id'].' or a.p3='.$value['id'].' or a.id in('.implode(",", $dishIds).') ) and a.status=1 and a.deleted=0 and a.type in(0,1,2,3)';
			}
			else{
				$where ='(a.p1='.$value['id'].' or a.p2='.$value['id'].' or a.p3='.$value['id'].') and a.status=1 and a.deleted=0 and a.type in(0,1,2,3)';
			}
			
			$dish_list = get_goods(array('field'=>'a.id,a.p1,a.p2,a.p3,a.title,a.productprice,a.marketprice,a.app_marketprice,a.thumb,a.timeprice,a.type,a.timestart,a.timeend,a.team_buy_count,a.commision,a.sales,a.total,b.title as btitle,b.thumb as imgs,b.productprice as price, b.marketprice as market ',
					'table'	=>'shop_dish',
					'where'	=> $where,
					'order'	=> ' a.displayorder desc ',
					'limit' =>'0,'.$limit
			));
			
			$arrCategory[$key]['dish_list'] = $dish_list;
		}
	}
	
	$result['data']['category_list']= $arrCategory;
	$result['code'] 				= 1;
	
	echo apiReturn($result);
	exit;