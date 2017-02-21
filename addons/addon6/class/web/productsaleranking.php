<?php
// +----------------------------------------------------------------------
// | WE CAN DO IT JUST FREE
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.squdian.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 百家威信 <QQ:2752555327> <http://www.squdian.com>
// +----------------------------------------------------------------------
$condition="";
if(!empty($_GP['start_time'])&&!empty($_GP['end_time']))
{
    $start_time= strtotime($_GP['start_time']);
	$end_time= strtotime($_GP['end_time']);
}else
{
	$start_time= strtotime(date('Y-m-01 00:00:01',time()));
	$end_time= strtotime(date('Y-m-t 23:59:59',time()));
}
$brand = mysqld_selectall("SELECT * FROM ".table('shop_brand'));
$condition=" ordergoods.createtime>=".$start_time." and ordergoods.createtime<=".$end_time;
if (!empty($_GP['brand']) && ($_GP['brand'] != -1)){
    $condition .= " and shopgoods.brand = ".$_GP['brand'];
}
$condition .= " and shoporder.status >= 1 ";
// 找出销售的产品进行统计
$sql = " SELECT ordergoods.goodsid, ordergoods.price, shopdish.title, sum(ordergoods.total) as totals, sum(ordergoods.total * ordergoods.price) totprice FROM ".table('shop_order_goods')." as ordergoods left join ".table('shop_dish')." as shopdish on ordergoods.goodsid = shopdish.id left join ".table('shop_goods')." as shopgoods on shopdish.gid = shopgoods.id left join ".table('shop_order')." as shoporder on ordergoods.orderid = shoporder.id where $condition group by ordergoods.goodsid order by totprice desc , totals desc";
$list = mysqld_selectall($sql);
if(!empty($_GP['productsalerankingEXP01']))
{
 	$report="productsaleranking";
    require_once 'report.php';
    exit;
}
                   	 
		               		
 include addons_page('productsaleranking');