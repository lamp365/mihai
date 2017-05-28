<?php
/**
 * 获取用户免单的订单商品信息
 *
 * @param $freeConfig:array  免单配置数组
 * @param $openid:用户ID
 *
 * @return $arrDish: array 订单商品数组
 *
 */
function getFreeDish($freeConfig,$openid)
{
	$sql ='SELECT og.id as order_goods_id,og.price,og.total,og.free_status,og.free_explanation,d.title,d.id,g.thumb FROM ' . table('shop_order') . ' o,'. table('shop_order_goods').' og,'.table('shop_dish').' d,'.table('shop_goods').' g ';
	$sql.=' where o.id=og.orderid and og.goodsid=d.id and d.gid=g.id ';
	$sql.=' and o.status=3 ';
	$sql.=' and o.ordertype!=-2 ';			//批发订单除外
	$sql.=' and o.relation_uid=0 ';			//一键代发订单除外
	$sql.=' and og.status in (-2,-1,0) ';
	$sql.=' and o.completetime>= '.$freeConfig['free_starttime'];
	$sql.=' and o.completetime<= '.$freeConfig['free_endtime'];
	$sql.=" and o.openid= '".$openid."' ";
	$sql.=' and d.p1= '.$freeConfig['category_id'];
		
	$arrDish = mysqld_selectall($sql);

	return $arrDish;
}

/**
 * 根据免单期间，获得相应订单商品信息列表
 * 
 * @param $starttime:int 开始时间
 * @param $endtime:int 结束时间
 * @param $category_id:int 分类ID
 * @param $limitSql:string limit的ＳＱＬ语句
 * @param $whereSql:string 其他查询条件的ＳＱＬ语句，可选
 * 
 */
function getFreeDishListByPeriod($starttime,$endtime,$category_id,$limitSql,$whereSql='')
{
	$listSql ='SELECT SQL_CALC_FOUND_ROWS o.ordersn,o.id as orderid,o.address_realname,o.address_mobile,og.*,d.title FROM ' . table('shop_order') . ' o,'. table('shop_order_goods').' og,'.table('shop_dish').' d ';
	$listSql.=' where o.id=og.orderid and og.goodsid=d.id ';
	$listSql.=' and o.status=3 ';
	$listSql.=' and o.ordertype!=-2 ';			//批发订单除外
	$listSql.=' and o.relation_uid=0 ';			//一键代发订单除外
	$listSql.=' and og.status in (-2,-1,0) ';
	$listSql.=' and o.completetime>= '.$starttime;
	$listSql.=' and o.completetime<= '.$endtime;
	$listSql.=' and d.p1= '.$category_id;

	if($whereSql!='')
	{
		$listSql.= " and ".$whereSql;
	}
		
	$listSql.= " order by og.free_id asc,o.openid asc limit ".$limitSql;
	$list = mysqld_selectall ($listSql);
	
	return $list;
}