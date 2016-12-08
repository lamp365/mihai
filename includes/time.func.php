<?php
function get_open_time($time, $type='m-d'){
    $now = getdate($time);
    if ($now['wday'] >= 5 || $now['wday']  ==0 ){
       $open = strtotime('Mon',$time);
	}else{
       $open = strtotime('+1 d',$time);
	}
	$open = date($type,$open);
	return $open;
}