<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2016/9/23 0023
 * Time: 15:14
 */
/**
 * 订单状态  '-6已退款 -5已退货 -4退货中， -3换货中， -2退款中，-1取消状态，0普通状态，1为已付款，2为已发货，3为成功'
 * http://dev-hinrc.com/index.php?mod=mobile&name=shopwap&do=openshop_order&status=payed&page=2
 * shop_order表       status: payed等待发货   nopay 等待支付  back 退货  complete已完成   close已关闭
 * shop_order_goods表 type: 1 为客户申请退货   2为申请换货
 */

$member  = get_member_account(true,true);
$statusArr = array('payed'=>'1','nopay'=>'0','back'=>'-2','complete'=>'3','close'=>'99');
if(empty($_GP['status']) || !array_key_exists($_GP['status'],$statusArr)){
    message('对不起，访问参数有误！','','error');
}
$status = $statusArr[$_GP['status']];
if($status == 99){
    $where = "a.openid ={$member['openid']} and (b.status= -5 or b.status= -6)";
}else{
    $where = "a.openid ={$member['openid']} and b.status={$status}";
}

//是否有查询 搜索
$isContinue = true;
if(!empty($_GP['keyword'])){
    if(is_numeric($_GP['keyword'])){
        $user = mysqld_select("select openid from ". table('member') . " where mobile=:mobile",array(
            'mobile' => $_GP['keyword']
        ));
    }else{
        $user = mysqld_select("select openid from ". table('member') . "where realname=:realname",array(
            'realname' => $_GP['keyword']
        ));
    }
    if(empty($user)){
        $isContinue = false;
    }else{
        $where .= " and b.openid={$user['openid']}";
    }
}

if(!$isContinue){
    //如果查询不到这样的检索，则不用查其他的订单了
    echo '不存在这样的人';

}else{
    $pindex = max(1, intval($_GP["page"]));
    $psize = 12;
    $start = ($pindex -1) * $psize;
    $limit = "limit {$start},{$psize}";

    $sql = "select a.orderid,a.goodsid, a.type, a.price, a.commision, b.id, b.dispatchprice, b.status, b.goodsprice  from ". table('shop_order_goods') ." as a left join ". table('shop_order'). "
    as b on a.orderid=b.id where {$where} order by a.createtime desc {$limit}";

    $list = mysqld_selectall($sql);

    $total = mysqld_selectcolumn('SELECT COUNT(a.id) FROM ' . table('shop_order_goods') . "as a left join ". table('shop_order'). " as b on a.orderid=b.id where {$where} ");
    $pager = pagination($total, $pindex, $psize,'.os_box_list');

//关联查询出来后可能有一些是一个订单号 多个物品，所以进行归类
//同时查询每个物品的具体dish信息
    $result = array();
    foreach($list as $arr){
        $goodid = $arr['shopgoodsid'];
        $dish   = mysqld_select("select thumb,title from ".table('shop_goods')." where id=:id",array(
            'id' => $goodid
        ));
        $arr['thumb'] = $dish['thumb'];
        $arr['title'] = $dish['title'];
        $result[$arr['orderid']][] = $arr;
    }
    ppd($result);
}


