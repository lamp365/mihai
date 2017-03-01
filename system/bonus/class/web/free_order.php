<?php
/**
 * 免单配置
 * @var unknown
 */

$operation = ! empty ( $_GP ['op'] ) ? $_GP ['op'] : '';

switch($operation){
	
	case 'order_finish':		//本周交易完成的订单
		
		$pindex= max(1, intval($_GP['page']));
		$psize = 10;
		
		//如果是周一时
		if(date('N')==1)
		{
			$starttime = strtotime(date('Y-m-d').' 00:00:00');
		}
		else{
			$starttime = strtotime('last Monday');
		}
		
		$orderSql = "SELECT SQL_CALC_FOUND_ROWS id,ordersn,address_realname,address_mobile,createtime,completetime,price,has_balance,balance_sprice,freeorder_price,hasbonus,bonusprice,dispatchprice,taxprice FROM " . table('shop_order');
		$orderSql.=" WHERE status = 3 and ordertype!=-2 ";
		$orderSql.=" and relation_uid = 0 ";		//一键代发订单除外
		$orderSql.=' and completetime>= '.$starttime;
		$orderSql.=' and completetime<= '.time();
		$orderSql.=' ORDER BY openid asc,createtime DESC ';
		$orderSql.=" LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
		
		$list  = mysqld_selectall($orderSql);
		$total = mysqld_select("SELECT FOUND_ROWS() as total;");
		$pager = pagination($total['total'], $pindex, $psize);
		
		foreach ( $list as $id => $item) {
			
			$sql  = "select og.total,og.aid, og.price,og.shopgoodsid, ";
			$sql .= " h.pcate,h.title,h.thumb,h.p1,c.name as category_name from ".table('shop_order_goods')." as og ";
			$sql .= " left join ".table('shop_dish')." as h ";
			$sql .= " on og.goodsid=h.id ";
			$sql .= " left join ".table('shop_category')." as c ";
			$sql .= " on h.p1=c.id ";
			$sql .= " where og.orderid={$item['id']}";
			$sql .= " and og.status in (-2,-1,0) ";		//非退款的订单商品
			
			$dishs = mysqld_selectall($sql);
			
			$list[$id]['dishs'] = $dishs;
		}
		
		include page ( 'free_order_finish' );
		
		break;

	case 'insert':				//新增免单配置

		$category_id= intval ( $_GP ['category_id'] );
		$period 	= getLastWeekPeriod();					//上周一到周天的时间戳
		
		$signSql = "SELECT free_sign_id,sign_username1,sign_username2,sign_username3 FROM " . table ( 'free_sign' );
		$signSql.= " where category_id = {$category_id} and free_starttime='".$period['monday_time']."' and free_endtime='".$period['sunday_time']."' ";
		$signSql.= " and sign_username1 IS NOT NULL and sign_username2 IS NOT NULL and sign_username3 IS NOT NULL";
		
		//签名信息
		$free_sign = mysqld_select ($signSql);
		
		//本期的免单配置
		$arrFreeConfig= mysqld_selectall('SELECT free_id,category_id FROM ' . table('free_config') . " where free_starttime='".$period['monday_time']."' and free_endtime='".$period['sunday_time']."' ");
		
		
		//不是周一时
		if(date('N')!=1){
			
			message ( '请在允许的时间内进行免单配置！', web_url ( 'free_order'), 'error' );
		}
		//免单分类为空
		elseif(empty($category_id))
		{
			message ( '请选择免单分类！', web_url ( 'free_order',array('op' =>'new_list')), 'error' );
		}
		//无签名记录时
		elseif(empty($free_sign))
		{
			message ( '亲，尚未通过签名认证，无法进行免单配置哦！', web_url ( 'free_order',array('op' =>'new_list')), 'error' );
		}
		//本期的免单已配置时
		elseif(!empty($arrFreeConfig))
		{
			message ( '亲，每期只能设置一个免单分类！', web_url ( 'free_order',array('op' =>'new_list')), 'error' );
		}

		$data = array('category_id' 		=> $category_id,
						'free_starttime'	=> $period['monday_time'],
						'free_endtime'		=> $period['sunday_time'],
						'free_amount'		=> getFreeAmount($category_id,$period['monday_time'],$period['sunday_time']),
						'free_member_count'	=> getFreeMemberCount($category_id,$period['monday_time'],$period['sunday_time']),
						'createtime'		=> time()
		);
		
		if (mysqld_insert ( 'free_config', $data )) {
			
			mysqld_query('TRUNCATE TABLE '.table ( 'free_sign' ));		//清空免单签名表
				
			message ( '新增免单配置成功！', web_url ( 'free_order'), 'success' );
		}
		else{
			message ( '新增免单配置失败！', web_url ( 'free_order',array('op' =>'new_list')), 'error' );
		}

		break;
		
	case 'sign':				//签名
		
		$period 	= getLastWeekPeriod();		//上周一到周天的时间戳
		$category_id= intval ( $_GP ['category_id'] );
		
		//免单分类为空
		if(empty($category_id))
		{
			message ( '请选择免单分类！', web_url ( 'free_order',array('op' =>'new_list')), 'error' );
		}
		
		$free_sign = mysqld_select ( "SELECT free_sign_id,sign_username1,sign_username2,sign_username3 FROM " . table ( 'free_sign' ) ." where category_id = {$category_id} and free_starttime='".$period['monday_time']."' and free_endtime='".$period['sunday_time']."' " );
		
		//有签名信息时
		if($free_sign)
		{
			if ($free_sign['sign_username1'] == $_SESSION['account']['username'] || $free_sign['sign_username2'] == $_SESSION['account']['username'] || $free_sign['sign_username3'] == $_SESSION['account']['username']) {
			
				message ( '您不能重复签名！', web_url ( 'free_order',array('op' =>'new_list')), 'success' );
			}
			else{
				
				if(empty($free_sign['sign_username2']))
					$data['sign_username2'] = $_SESSION['account']['username'];
				elseif(empty($free_sign['sign_username3'])) 
				 	$data['sign_username3'] = $_SESSION['account']['username'];
				
				//更新
				if(mysqld_update('free_sign',$data,array('free_sign_id'=>$free_sign['free_sign_id']))){
					
					message ( '签名成功！', web_url ( 'free_order',array('op' =>'new_list')), 'success' );
				}
				else{
					
					message ( '签名失败！', web_url ( 'free_order',array('op' =>'new_list')), 'error' );
				}
			}
		}
		else{
			
			$data = array('category_id' 		=> $category_id,
							'free_starttime'	=> $period['monday_time'],
							'free_endtime'		=> $period['sunday_time'],
							'sign_username1'	=> $_SESSION['account']['username'],
							'createtime'		=> time()
			);
			
			//新增
			if (mysqld_insert ( 'free_sign', $data )) {
			
				message ( '签名成功！', web_url ( 'free_order',array('op' =>'new_list')), 'success' );
			}
			else{
				message ( '签名失败！', web_url ( 'free_order',array('op' =>'new_list')), 'error' );
			}
		}
		
		break;
		
	case 'new_detail':			//待配置免单详情
		
		$period 	= getLastWeekPeriod();				//上周一到周天的时间戳
		$category_id= intval ( $_GP ['category_id'] );
		$pindex 	= max(1, intval($_GP['page']));		//页码
		$psize 		= 10;								//每页显示记录数
		
		$categoryInfo = mysqld_select ( "SELECT name FROM ".table('shop_category'). " where id=$category_id" );
		
		$list = getFreeDishListByPeriod($period['monday_time'],$period['sunday_time'],$category_id,($pindex - 1) * $psize . ',' . $psize);

		$total = mysqld_select("SELECT FOUND_ROWS() as total;");
		$pager = pagination($total['total'], $pindex, $psize);
		
		include page ( 'free_order_new_detail' );
		
		break;

	case 'new_list':			//新增页
		
		$period 		= getLastWeekPeriod();		//上周一到周天的时间戳
		$categoryIds  	= array();					//已配置的免单类目ID
	
		//本期的免单配置
		$arrFreeConfig= mysqld_selectall('SELECT free_id,category_id FROM ' . table('free_config') . " where free_starttime='".$period['monday_time']."' and free_endtime='".$period['sunday_time']."' ");
		
		if(!empty($arrFreeConfig))
		{
			foreach($arrFreeConfig as $value)
			{
				$categoryIds[] = $value['category_id'];
			}
		}
		
		$sql ='SELECT c.id,c.name,s.sign_username1,s.sign_username2,s.sign_username3 FROM ' . table('shop_category') . ' c left join '.table('free_sign')." s on c.id=s.category_id and s.free_starttime='".$period['monday_time']."' and free_endtime='".$period['sunday_time']."' ";
		$sql.= ' where c.deleted=0 and c.enabled=1 and c.parentid=0 ';
		
		if(!empty($categoryIds))
		{
			$sql.= ' and c.id not in('.implode(",", $categoryIds).')';
		}
	
		$arrCategory= mysqld_selectall($sql);

		include page ( 'free_order_new' );

		break;
		
	case 'free_detail':			//免单详情
		
		$free_id 	= (int)$_GP['free_id'];
		$period 	= getLastWeekPeriod();				//上周一到周天的时间戳
		$pindex 	= max(1, intval($_GP['page']));		//页码
		$psize 		= 10;								//每页显示记录数
		
		$free_config = mysqld_select ( "SELECT f.*,c.name FROM " . table ( 'free_config' ) .' f,'.table('shop_category'). " c where c.id = f.category_id and f.free_id=$free_id" );
		
		if($free_config)
		{
			$whereSql = '';
			
			if($_GP['free_status']!='')
			{
				$whereSql= " og.free_status=".$_GP['free_status'];
			}
			
			$list = getFreeDishListByPeriod($free_config['free_starttime'],$free_config['free_endtime'],$free_config['category_id'],($pindex - 1) * $psize . ',' . $psize,$whereSql);
			
			$total = mysqld_select("SELECT FOUND_ROWS() as total;");
			$pager = pagination($total['total'], $pindex, $psize);
			
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
				
				//记录用户账单的免单金额收支情况
				insertMemberPaylog($orderInfo['openid'],$orderInfo['price']*$orderInfo['total'],$memberData['freeorder_gold'], 'addgold', '免单申请审核通过后，免单余额充值'.$orderInfo['price']*$orderInfo['total'].'元');
			}
			
			message ( '免单处理成功！', web_url ( 'free_order',array('op' =>'free_detail','free_id'=>$free_id)), 'success' );
		}
		else{
			message ( '免单处理失败！', web_url ( 'free_order',array('op' =>'free_detail','free_id'=>$free_id)), 'error' );
		}
		
		break;

	default:					//已配置免单列表页
		
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
	$sql.=' and o.ordertype!=-2 ';			//批发订单除外
	$sql.=' and o.relation_uid=0 ';			//一键代发订单除外
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
 * @param $starttime:int 开始时间戳
 * @param $endtime:int 结束时间戳
 *
 * @return $freeMemeberCount:免单人数
 */
function getFreeMemberCount($category_id,$starttime,$endtime)
{
	$freeMemeberCount = 0;		//免单金额

	$sql ='SELECT count(DISTINCT o.openid) as cnt FROM ' . table('shop_order') . ' o,'. table('shop_order_goods').' og,'.table('shop_dish').' d ';
	$sql.=' where o.id=og.orderid and og.goodsid=d.id ';
	$sql.=' and o.status=3 ';
	$sql.=' and o.ordertype!=-2 ';			//批发订单除外
	$sql.=' and o.relation_uid=0 ';			//一键代发订单除外
	$sql.=' and og.status in (-2,-1,0) ';
	$sql.=' and o.completetime>= '.$starttime;
	$sql.=' and o.completetime<= '.$endtime;
	$sql.=' and d.p1= '.$category_id;

	$total= mysqld_select($sql);

	if(!empty($total['cnt']))  $freeMemeberCount = $total['cnt'];

	return $freeMemeberCount;
}

