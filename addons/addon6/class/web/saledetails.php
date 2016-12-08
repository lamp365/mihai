<?php
// +----------------------------------------------------------------------
// | WE CAN DO IT JUST FREE
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.squdian.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: �ټ����� <QQ:2752555327> <http://www.squdian.com>
// +----------------------------------------------------------------------
$condition = "";
$pindex = max(1, intval($_GP['page']));
$psize = 20;
$mess_list = array();
$_mess = mysqld_selectall("SELECT * FROM " . table('shop_mess'));
if (! empty($_GP['start_time']) && ! empty($_GP['end_time'])) {
    $start_time = strtotime($_GP['start_time'] . " 00:00:01");
    $end_time = strtotime($_GP['end_time'] . " 23:59:59");
} else {
    $start_time = strtotime(date('Y-m-01 00:00:01', time()));
    $end_time = strtotime(date('Y-m-t 23:59:59', time()));
}
$condition = " and orders.createtime>=" . $start_time . " and orders.createtime<=" . $end_time;
if (! empty($_GP['mess'])) {
    $members = mysqld_selectall("SELECT openid FROM " . table('member') . " WHERE mess_id = " . $_GP['mess']);
    $mem_list = array();
    if (is_array($members)) {
        foreach ($members as $value) {
            $mem_list[] = $value['openid'];
        }
    }
    $mem_obj = "'" . implode("','", $mem_list) . "'";
    $condition .= " AND orders.openid in ({$mem_obj}) ";
}
if (! empty($_GP['saledetailsEXP01'])) {
    
    $psize = 9999;
    $pindex = 1;
}



$list = mysqld_selectall("SELECT ordergoods.price,ordergoods.total,(select title from " . table('shop_goods') . " goods where ordergoods.shopgoodsid=goods.id) titles, (select Supplier from " . table('shop_goods') . " goods where ordergoods.shopgoodsid=goods.id) Suppliers,orders.createtime,orders.ordersn from  " . table('shop_order_goods') . " ordergoods left join " . table('shop_order') . " orders  on orders.id=ordergoods.orderid where 1=1 $condition order by ordergoods.total  desc  LIMIT " . ($pindex - 1) * $psize . ',' . $psize);

$total = mysqld_selectcolumn("SELECT count(ordergoods.id) from  " . table('shop_order_goods') . " ordergoods left join " . table('shop_order') . " orders  on orders.id=ordergoods.orderid where 1=1 $condition order by orders.createtime desc");
$pager = pagination($total, $pindex, $psize);

if (! empty($_GP['saledetailsEXP01'])) {
    $report = "saledetails";
    
    require_once 'report.php';
    exit();
}
include addons_page('saledetails');