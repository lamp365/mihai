<?php
/**
 * 获取免单的订单商品信息
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
	$sql.=' and o.ordertype!=-2 ';		//批发订单除外
	$sql.=' and og.status in (-2,-1,0) ';
	$sql.=' and o.completetime>= '.$freeConfig['free_starttime'];
	$sql.=' and o.completetime<= '.$freeConfig['free_endtime'];
	$sql.=" and o.openid= '".$openid."' ";
	$sql.=' and d.p1= '.$freeConfig['category_id'];
		
	$arrDish = mysqld_selectall($sql);

	return $arrDish;
}