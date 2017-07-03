<?php

/**
 * 当支付的时候 根据店铺以及价格来选出 结算的时候 可以使用的优惠卷
 * @param $sts_id
 * @param $totalprice
 * @param $dishid_arr
 * @return array
 */
function getCouponByPriceOnpay($sts_id,$totalprice,$dishid_arr){
    $meminfo    = get_member_account();
    $openid     = $meminfo['openid'];
    $totalprice = FormatMoney($totalprice,1);  //金额转为分
    $bonus_sql = "select m.*,c.usage_mode,c.coupon_amount,c.amount_of_condition,c.use_start_time,c.use_end_time,c.coupon_name,c.store_shop_id,c.store_shop_dishid from ".table('store_coupon_member')." as m left join ".table('store_coupon')." as c";
    $bonus_sql.= " on m.scid=c.scid where m.openid='{$openid}' and m.status=0 and c.store_shop_id={$sts_id} and c.amount_of_condition <= '{$totalprice}'";
    $bonus  = mysqld_selectall($bonus_sql);

    //去除时间还没开始的 或者已经过期的
    foreach($bonus as $key => &$item){
        //金额转为元
        $item['coupon_amount']       = FormatMoney($item['coupon_amount'],0);
        $item['amount_of_condition'] = FormatMoney($item['amount_of_condition'],0);
        if(time() < $item['use_start_time'] || time() > $item['use_end_time']){
            unset($bonus[$key]);
            continue;
        }
        //如果优惠卷针对的是单品，则判断是否在购买的列表中
        if($item['usage_mode'] == 3 ){
            $store_shop_dishid = json_decode($item['store_shop_dishid'],true);
            if(empty($store_shop_dishid)){
                unset($bonus[$key]);
                continue;
            }
            $can_use_bonus = false;
            foreach($dishid_arr as $d_id){
                if(in_array($d_id,$store_shop_dishid)){
                    $can_use_bonus = true;
                }
            }
            if($can_use_bonus == false){
                unset($bonus[$key]);
                continue;
            }
        }
    }

    if(empty($bonus)){
        return array();
    }else{
        return array_values($bonus);
    }
}

/**
 * 当支付的时候 通过用户领取优惠卷后的 主键id 和店铺ID 得到 优惠卷信息
 * @param $scmid
 * @param $sts_id
 * @return bool|mixed
 */
function getCouponByMemidOnPay($scmid,$sts_id,$dishlist,$return_field=''){
    $meminfo    = get_member_account();
    $openid     = $meminfo['openid'];
    $bonus_sql = "select m.*,c.usage_mode,c.coupon_amount,c.amount_of_condition,c.use_start_time,c.use_end_time,c.coupon_name,c.store_shop_id,c.store_shop_dishid from ".table('store_coupon_member')." as m left join ".table('store_coupon')." as c";
    $bonus_sql.= " on m.scid=c.scid where m.scmid = {$scmid} and m.openid='{$openid}' and m.status=0 and c.store_shop_id={$sts_id}";
    $bonus  = mysqld_select($bonus_sql);
    if(empty($bonus)){
        return array();
    }else{
        //去除时间还没开始的 或者已经过期的
        if(time() < $bonus['use_start_time'] || time() > $bonus['use_end_time']){
           return array();
        }
        //如果优惠卷针对的是单品，则判断是否在购买的列表中
        if($bonus['usage_mode'] == 3 ){
            $store_shop_dishid = json_decode($bonus['store_shop_dishid'],true);
            if(empty($store_shop_dishid)){
                return array();
            }
            $can_use_bonus = false;
            foreach($dishlist as $item){
                $d_id = $item['id'];
                if(in_array($d_id,$store_shop_dishid)){
                    $can_use_bonus = true;
                }
            }
            if($can_use_bonus == false){
                return array();
            }

        }
        if(!empty($return_field)){
            return $bonus[$return_field];
        }else{
            return $bonus;
        }
    }
}