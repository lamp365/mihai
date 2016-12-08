<?php
//每次有显示的地方就进行跟新团购状态
update_all_shop_status();
$operation  = !empty($_GP['op']) ? $_GP['op'] : 'list';

if ($operation == 'list') {
    $group_status = $_GP['group_status'] == null ? 2 : $_GP['group_status'];
    $pindex       = max(1, intval($_GP['page']));
    $psize        = 10;
    $selectCondition=" LIMIT " . ($pindex - 1) * $psize . ',' . $psize;

    $sql = "select t.group_id,t.status as group_status,t.createtime as group_createtime,m.order_id from ".table('team_buy_group') ." as t left join ".table('team_buy_member'). " as m ";
    $sql .= " on m.group_id=t.group_id and m.openid=t.creator ";
    $sql .= " where  t.status={$group_status} and finish=0";  //组团中
    $sql .= " ORDER BY  t.createtime DESC ".$selectCondition;
    $list = mysqld_selectall($sql);

    $sqlNum = 'SELECT COUNT(t.group_id) FROM ' . table('team_buy_group') . " t left join ". table('team_buy_member') ." as m ";
    $sqlNum .= "  on m.group_id=t.group_id and m.openid=t.creator ";
    $sqlNum .= " where  t.status={$group_status}";  //组团中

    $total = $pager = '';
    if(!empty($list)){
        $total = mysqld_selectcolumn($sqlNum);
        $pager = pagination($total, $pindex, $psize);
        foreach ( $list as $id => &$item) {
            $orderinfo  = mysqld_select("select * from ".table('shop_order')." where id={$item['order_id']}");
            foreach($orderinfo as $key => $val){
                $item[$key] = $val;
            }
            $sql  = "select o.total,o.aid,o.optionname, o.id as order_id,o.optionid,o.price as orderprice, o.status as order_status, o.type as order_type,o.shop_type ";
            $sql .= " ,h.team_buy_count,h.marketprice as dishprice,h.pcate,h.title,h.thumb,h.gid from ".table('shop_order_goods')." as o ";
            $sql .= " left join ".table('shop_dish')." as h ";
            $sql .= " on o.goodsid=h.id ";
            $sql .= " where o.orderid={$item['order_id']}";
            $goods = mysqld_selectall($sql);
            $list[$id]['goods'] = $goods;
        }
    }

    $time = TEAM_BUY_EXPIRY/60;
    include page('groupbuy_list');

}else if($operation=='detail'){
    $group_id = $_GP['group_id'];
    $sql = "select o.* from ".table('team_buy_member')." as t left join ".table('shop_order')." as o on o.id=t.order_id ";
    $sql .= " where t.group_id={$group_id} and t.order_id<>0";
    $list = mysqld_selectall($sql);
    if(!empty($list)){
        foreach ( $list as $id => $item) {
            $sql  = "select o.total,o.aid,o.optionname, o.id as order_id,o.optionid,o.price as orderprice, o.status as order_status, o.type as order_type,o.shop_type ";
            $sql .= " ,h.team_buy_count,h.marketprice as dishprice,h.pcate,h.title,h.thumb,h.gid from ".table('shop_order_goods')." as o ";
            $sql .= " left join ".table('shop_dish')." as h ";
            $sql .= " on o.goodsid=h.id ";
            $sql .= " where o.orderid={$item['id']}";
            $goods = mysqld_selectall($sql);
           /* $goods = mysqld_selectall("SELECT g.*,h.team_buy_count,h.marketprice as dishprice,h.pcate,o.total,o.aid,g.type,o.optionname, o.id as order_id,o.optionid,o.price as orderprice, o.status as order_status, o.type as order_type FROM " . table('shop_order_goods') . " o left join " . table('shop_goods') . " g on o.shopgoodsid=g.id "
                . " left join ". table('shop_dish'). " h on o.goodsid = h.gid  WHERE o.orderid='{$item['id']}'");*/
            $list[$id]['goods'] = $goods;
        }
    }

    include page('groupbuy_detail');
}else if($operation == 'finish'){   //目前没用到，之后可以删掉
    //找出所有没有未标记结束的团
    $group    = mysqld_selectall("SELECT createtime,dish_id, group_id,status FROM ".table('team_buy_group')." WHERE finish=0");
    if(!empty($group)){
        foreach($group as $item){
            if($item['status'] == 2)
                message('对不起，还有在组团中的团，不能结束！',refresh(),'error');

            $dish = mysqld_select("select id,timestart,timeend from ".table('shop_dish')." where id={$item['dish_id']}");
            if($dish['timeend']>= time())
                message("对不起，宝贝id{$dish['id']}该商品活动未结束！",refresh(),'error');
        }

        //以上检测完，没有错，就可以进行更新
        mysqld_update('team_buy_group',array('finish'=>1),array('finish'=>0));
        message('操作成功！',refresh(),'success');
    }
}