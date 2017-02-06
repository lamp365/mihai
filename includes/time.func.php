<?php
function get_open_time($time, $type='m-d'){
    $now = getdate($time);
    if ($now['wday'] >= 5 || $now['wday']  ==0 ){
       $open = strtotime('Mon',$time);
	}else{
       $open = strtotime('+1 d',$time);
	}
    $open = $open+3600*15;  //开奖时间是那天的三点
	return $open;
}