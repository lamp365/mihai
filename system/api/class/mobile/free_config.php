<?php
/**
 * 免单配置接口
 * @var unknown
 */

$result = array();

$period = getLastWeekPeriod();					//上周一到周天的时间戳

$free_list = mysqld_select ( "SELECT f.*,c.name FROM " . table ( 'free_config' ) .' f,'.table('shop_category'). " c where c.id = f.category_id and free_starttime='".$period['monday_time']."' and free_endtime='".$period['sunday_time']."' ORDER BY f.createtime DESC " );


//加入虚假数据
$free_list['free_member_count'] = $free_list['free_member_count']+intval($free_list['free_starttime'] / 2896753);
$free_list['free_amount'] 		= $free_list['free_amount']+intval($free_list['free_endtime'] / 2896753);


$result['data']['free_list']= $free_list;
$result['data']['url']		= '';				//活动说明URL
$result['code'] 			= 1;

echo apiReturn($result);
exit;