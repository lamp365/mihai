<?php
/**
 * 免单配置
 * @var unknown
 */

$operation = ! empty ( $_GP ['op'] ) ? $_GP ['op'] : '';

switch($operation){

	case 'insert':				//新增免单配置

		$category_id= intval ( $_GP ['category_id'] );
		$period 	= getLastWeekPeriod();					//上周一到周天的时间戳
		
		//不是周一时
		if(date('N')!=1){
			
			message ( '请在允许的时间内进行免单配置！', web_url ( 'free_order'), 'error' );
		}
		//免单分类为空
		elseif(empty($category_id))
		{
			message ( '请选择免单分类！', web_url ( 'free_order',array('op' =>'new')), 'error' );
		}

		$data = array('category_id' 		=> $category_id,
						'free_starttime'	=> $period['monday_time'],
						'free_endtime'		=> $period['sunday_time'],
						'free_amount'		=> getFreeAmount($category_id,$period['monday_time'],$period['sunday_time']),
						'free_member_count'	=> getFreeMemberCount($category_id,$period['monday_time'],$period['sunday_time']),
						'createtime'		=> time()
		);
		
		if (mysqld_insert ( 'free_config', $data )) {
				
			message ( '新增免单配置成功！', web_url ( 'free_order'), 'success' );
		}
		else{
			message ( '新增免单配置失败！', web_url ( 'free_order',array('op' =>'new_list')), 'error' );
		}

		break;

	case 'new_list':			//新增页
		
		$period 		= getLastWeekPeriod();		//上周一到周天的时间戳
		$categoryIds  	= array();					//已配置的免单类目ID
	
		$arrFreeConfig= mysqld_selectall('SELECT free_id,category_id FROM ' . table('free_config') . " where free_starttime='".$period['monday_time']."' and free_endtime='".$period['sunday_time']."' ");
		
		if(!empty($arrFreeConfig))
		{
			foreach($arrFreeConfig as $value)
			{
				$categoryIds[] = $value['category_id'];
			}
		}
		
		$sql ='SELECT id,name FROM ' . table('shop_category') . ' where deleted=0 and enabled=1 and parentid=0 ';
		
		if(!empty($categoryIds))
		{
			$sql.= ' and id not in('.implode(",", $categoryIds).')';
		}
		
		$arrCategory= mysqld_selectall($sql);

		include page ( 'free_order_new' );

		break;
		
	case 'free_detail':			//免单详情
		
		$free_id 	= (int)$_GP['free_id'];
		$period 	= getLastWeekPeriod();		//上周一到周天的时间戳
		
		$free_config = mysqld_select ( "SELECT f.*,c.name FROM " . table ( 'free_config' ) .' f,'.table('shop_category'). " c where c.id = f.category_id and f.free_id=$free_id" );
		
		if($free_config)
		{
			$list = mysqld_selectall ( "SELECT o.ordersn,og.*,d.title FROM " . table ( 'shop_order_goods' ).' og,'.table ( 'shop_order' ).' o,' .table('shop_dish'). " d where og.orderid=o.id and og.goodsid=d.id and free_id = $free_id ");
			
			include page ( 'free_order_detail' );
		}
		else{
			message ( '非法参数！', web_url ( 'free_order'), 'error' );
		}
		
		break;
		
	case 'free_process':		//处理免单申请
		
		$free_id 		= (int)$_GP['free_id'];
		$order_goods_id = (int)$_GP['order_goods_id'];
		$free_status	= (int)$_GP['free_status'];
		
		
		$data = array(	'free_status' 		=>$free_status,
						'free_explanation' 	=>trim($_GP['free_explanation'])
		);
		
		if(mysqld_update('shop_order_goods',$data,array('id'=>$order_goods_id,'free_id'=>$free_id,'free_status'=>1)))
		{
			//审核通过时，给用户返现
			if($free_status==2)
			{
				$orderInfo = mysqld_select( "SELECT o.openid,og.price,og.total FROM " . table ( 'shop_order_goods' ).' og,'.table ( 'shop_order' )." o where og.orderid=o.id and og.id= $order_goods_id ");
				$memberInfo= mysqld_select( "SELECT freeorder_gold,freeorder_gold_endtime FROM " . table ( 'member' )." where openid='".$orderInfo['openid']."' ");
					
				$freeorder_gold_endtime = strtotime('Sunday')+24*3600-1;	//周天的23:59:59
					
				//已有本期免单金额时
				if($memberInfo['freeorder_gold_endtime']==$freeorder_gold_endtime)
				{
					$memberData = array('freeorder_gold' 		=> $orderInfo['price']*$orderInfo['total']+$memberInfo['freeorder_gold'],
										'freeorder_gold_endtime'=> $freeorder_gold_endtime
					);
				}
				else{
					$memberData = array('freeorder_gold' 		=> $orderInfo['price']*$orderInfo['total'],
										'freeorder_gold_endtime'=> $freeorder_gold_endtime
					);
				}
					
				//用户免单金额更新
				mysqld_update('member',$memberData,array('openid'=>$orderInfo['openid']));
			}
			
			message ( '免单处理成功！', web_url ( 'free_order',array('op' =>'free_detail','free_id'=>$free_id)), 'success' );
		}
		else{
			message ( '免单处理失败！', web_url ( 'free_order',array('op' =>'free_detail','free_id'=>$free_id)), 'error' );
		}
		
		break;

	default:					//列表页
		
		$pindex = max(1, intval($_GP['page']));
		$psize 	= 10;

		$list 	= mysqld_selectall ( "SELECT SQL_CALC_FOUND_ROWS f.*,c.name FROM " . table ( 'free_config' ) .' f,'.table('shop_category'). " c where c.id = f.category_id ORDER BY f.createtime DESC limit ".($pindex - 1) * $psize . ',' . $psize );

		$total = mysqld_select("SELECT FOUND_ROWS() as total;");
		$pager = pagination($total['total'], $pindex, $psize);
		
		include page ( 'free_order_list' );

		break;
}


/**
 * 
 * 计算免单金额
 * @param $category_id:int 分类ID
 * @param $starttime:int 开始时间戳
 * @param $endtime:int 结束时间戳
 * 
 * @return $freeAmount:免单金额
 * 
 */
function getFreeAmount($category_id,$starttime,$endtime)
{
	$freeAmount = 0;		//免单金额
	
	$sql ='SELECT sum(og.price*og.total) as total_price FROM ' . table('shop_order') . ' o,'. table('shop_order_goods').' og,'.table('shop_dish').' d ';
	$sql.=' where o.id=og.orderid and og.goodsid=d.id ';
	$sql.=' and o.status=3 ';
	$sql.=' and og.status in (-2,-1,0) ';
	$sql.=' and o.completetime>= '.$starttime;
	$sql.=' and o.completetime<= '.$endtime;
	$sql.=' and d.p1= '.$category_id;

	$totalPrice= mysqld_select($sql);
	
	if(!empty($totalPrice['total_price']))  $freeAmount = $totalPrice['total_price'];
	
	return $freeAmount;
}

/**
 *
 * 计算免单人数
 * @param $category_id:int 分类ID
 *
 * @return $freeMemeberCount:免单人数
 */
function getFreeMemberCount($category_id,$starttime,$endtime)
{
	$freeMemeberCount = 0;		//免单金额

	$sql ='SELECT count(DISTINCT o.openid) as cnt FROM ' . table('shop_order') . ' o,'. table('shop_order_goods').' og,'.table('shop_dish').' d ';
	$sql.=' where o.id=og.orderid and og.goodsid=d.id ';
	$sql.=' and o.status=3 ';
	$sql.=' and og.status in (-2,-1,0) ';
	$sql.=' and o.completetime>= '.$starttime;
	$sql.=' and o.completetime<= '.$endtime;
	$sql.=' and d.p1= '.$category_id;

	$total= mysqld_select($sql);

	if(!empty($total['cnt']))  $freeMemeberCount = $total['cnt'];

	return $freeMemeberCount;
}

