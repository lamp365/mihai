<?php 
/**
 * 取出当前活动
 *   */
function getCurrentAct(){
    $now = time();
    $where = " ac_status=1 and ac_time_end > $now ";
    $sql = "SELECT ac_id,ac_title,ac_time_str,ac_time_end,ac_area FROM ".table('activity_list')." where ".$where;
    $list = mysqld_selectall($sql);
    if($list){
        return $list[0];//默认只有一个
    }
}

function getDishIsOnActive($dishid,$store_count){
    //找出本次活动的场次
    $active = getCurrentAct();
    $find   = array();
    if(!empty($active)){
        //有活动，那么判断该商品是不是属于限时购商品
        $sql = "select ac_dish_id,ac_dish_status,ac_dish_total,ac_dish_price,ac_action_id from ".table('activity_dish');
        $sql .= " where ac_action_id={$active['ac_id']} and ac_shop_dish={$dishid}";
        $find = mysqld_select($sql);
        if (!empty($find)) {
            //校验一下活动表的库存跟dish表的库存
            if($store_count < $find['ac_dish_total']){
                mysqld_update('activity_dish',array('ac_dish_total'=>$store_count),array('ac_dish_id'=>$find['ac_dish_id']));
                $find['ac_dish_total'] = $store_count;
            }
        }
    }
    return $find;
}
?>