<?php
/**
 * 免单配置接口
 * @var unknown
 */

$result = array();

$period = getLastWeekPeriod();					//上周一到周天的时间戳

$free_list = mysqld_selectall ( "SELECT f.*,c.name FROM " . table ( 'free_config' ) .' f,'.table('shop_category'). " c where c.id = f.category_id and free_starttime='".$period['monday_time']."' and free_endtime='".$period['sunday_time']."' ORDER BY f.createtime DESC " );

$result['data']['free_list']= $free_list;
$result['code'] 			= 1;

echo apiReturn($result);
exit;