<?php

function getCouponByPrice($sts_id,$totalprice){
    $meminfo = get_member_account();
    $openid  = $meminfo['openid'];
    $bonus_sql = "select m.*,c.coupon_amount,c.amount_of_condition,c.use_start_time,c.use_end_time,c.coupon_name from ".table('store_coupon_member')." as m left join ".table('store_coupon')." as c";
    $bonus_sql.= " on m.scid=c.scid where m.openid='{$openid}' and m.status=0 and c.store_shop_id={$sts_id} and c.amount_of_condition <= '{$totalprice}'";
    $bonus  = mysqld_selectall($bonus_sql);
    //去除时间还没开始的 或者已经过期的
    foreach($bonus as $item){
//        if()
    }
}