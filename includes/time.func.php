<?php
function get_open_time($time, $type='m-d'){
    $now = getdate($time);
    if ($now['wday'] >= 5 || $now['wday']  ==0 ){
        $open = strtotime('Mon',$time);    //周一的凌晨
    }else{
        $date1  = date("Y-m-d",$time);
        $open   = strtotime("$date1 +1 day"); //明天的凌晨
    }

    $open = $open+3600*15;  //开奖时间是那天的三点
    return $open;
}