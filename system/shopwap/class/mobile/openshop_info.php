<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2016/9/20 0020
 * Time: 15:03
 */

/**
 * http://dev-hinrc.com/index.php?mod=mobile&name=shopwap&do=openshop_info
 */
//先根据用户openid来找商铺是否开过
$member     = get_member_account(true,true);

//$memberinfo = member_get($member['openid']);

if(empty($member['openshop_id'])){
    $shopData   = mysqld_select("select id,openid,shopname,area,logo  from ".table('openshop')." where openid = ".$member['openid']);
}else{
    $shopData   = mysqld_select("select id,openid,shopname,area,logo  from ".table('openshop')." where id = ".$member['openshop_id']);
}

if(empty($shopData)){
    $todayView   = 0;   //今日访问
    $todayMoney  = 0;   //今日收入
    $monthOrder  = 0;  //本月订单
    $monthMoney  = 0;  //本月收入
    $isOpenShop  = 0;  //是否开启了商铺

}else{
    //开店信息加入到mobile_account  session中
    $openshop_id = $shopData['id'];
    $_SESSION['mobile_account']['openshop_id'] = $openshop_id;
    $shopData['shopname']   = empty($shopData['shopname']) ? '店铺名称' : $shopData['shopname'];
    $shopData['area']       = empty($shopData['area']) ? '地区' : $shopData['area'];
    $isOpenShop  = 1;  //是否开启了商铺

    $nowdate  = strtotime(date('Y-m-d'));    //当天时间戳
    $nowmonth = mktime(0,0,0,date('m'),1,date('Y'));  //本月的时间戳

    //今日收入
    $todayMoney = mysqld_selectcolumn("SELECT sum(a.commision) FROM " . table('shop_order_goods') . " as a left join " .table('shop_order'). "  as b on a.orderid=b.id WHERE a.seller_openid= ".$member['openid']." and b.status=3 and a.createtime >=".$nowdate);
    //本月订单数量
    $monthOrder = mysqld_selectcolumn("SELECT count(a.id) FROM " . table('shop_order_goods') . " as a left join " .table('shop_order')." as b on a.orderid=b.id WHERE a.seller_openid= ".$member['openid']." and b.status=3 and a.createtime >=".$nowmonth);
    //本月收入
    $monthMoney = mysqld_selectcolumn("SELECT sum(a.commision) FROM " . table('shop_order_goods') . " as a left join " .table('shop_order'). " as b on   a.orderid=b.id  WHERE a.seller_openid= ".$member['openid']." and b.status=3 and a.createtime >=".$nowmonth);
    //今日访问量
    $result    = mysqld_select("select pv,uv from ". table('openshop_viewreport') . " where seller_openid='{$member['openid']}' and time={$nowdate}");
    $todayView = 0;
    if(!empty($result))
        $todayView = $result['uv'];

    $accesskey = getOpenshopAccessKey($member['openid']);

    if(!$todayMoney) $todayMoney = 0.00;
    if(!$monthMoney) $monthMoney = 0.00;
}

include  themePage('openshop_info');


