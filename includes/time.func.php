<?php
function get_open_time($time, $type='m-d'){
    $now = getdate($time);
    if ($now['wday'] >= 5 || $now['wday']  ==0 ){
       $open = strtotime('Mon',$time);  //周一的那个时间点
	}else{
       $open = strtotime('+1 d',$time);  //明天的那个时间点

	}
    $zero_time = strtotime(date("Y-m-d",$open));  //凌晨时间点
    $open = $zero_time+3600*15;  //开奖时间是那天的三点
	return $open;
}