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

/**
 * 获得上周的起始时间
 * 
 * @return $period: array 周一到周日
 * 
 */
function getLastWeekPeriod()
{
	//周一时间戳
	$period['monday_time'] = mktime(0, 0 , 0,date("m"),date("d")-date("N")+1-7,date("Y"));
	//周日时间戳
	$period['sunday_time'] = mktime(23,59,59,date("m"),date("d")-date("N")+7-7,date("Y"));
	
	return $period;
}
