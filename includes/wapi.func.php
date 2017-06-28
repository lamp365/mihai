<?php 
/**
 * 取出当前活动
 *   */
function getCurrentAct(){
    $now = time();
    $where = " ac_status=1 and ac_time_end > $now  and ac_time_str < $now ";
    $sql = "SELECT ac_id,ac_title,ac_time_str,ac_time_end,ac_area FROM ".table('activity_list')." where ".$where;
    $list = mysqld_selectall($sql);
    if($list){
        return $list[0];//默认只有一个
    }
}

/**
 * 判断商品是否在限时购中:正在限时购，或者还没有开始的限时购
 */
function getDishIsOnActive($dishid){
    if (empty($dishid)) return '';
    $now = time();
    $where = " ac_status=1 and ac_time_end > $now ";
    $sql = "SELECT ac_id,ac_title,ac_time_str,ac_time_end,ac_area FROM ".table('activity_list')." where ".$where;
    $list = mysqld_selectall($sql);
    $find   = array();
    if(!empty($list)){
        $acIdArr = array();
        foreach ($list as $key=>$v){
            $acIdArr[] = " ac_action_id =".$v['ac_id'];
        }
        $acIdStr = implode(" or ", $acIdArr);
        
        $where = " ac_shop_dish={$dishid} ";
        $where .= ' and ('.$acIdStr.')';
        //有活动，那么判断该商品是不是属于限时购商品
        $sql = "select ac_dish_id,ac_dish_status,ac_dish_total,ac_dish_price,ac_action_id from ".table('activity_dish')." where ";
        $sql .= $where;
        $find = mysqld_select($sql);
    }
    return $find;
}
/**
 * 同步库存
 * 如果shop_dish的库存小于activity_dish的库存，则需要同步一下库存
 *   */
 function synchroscope_store_count($store_cont,$act_count,$ac_dish_id){
     if (!empty($find)) {
         //校验一下活动表的库存跟dish表的库存
         if($store_cont < $act_count){
             mysqld_update('activity_dish',array('ac_dish_total'=>$store_cont),array('ac_dish_id'=>$ac_dish_id));
         }
     }
 }
?>