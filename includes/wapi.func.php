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
?>