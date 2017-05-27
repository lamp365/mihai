<?php
/**
 * 首页免单配置接口
 * @var unknown
 */

$result = array();

//免单是否开启
$freeOrderEnabledConfig = mysqld_select ( "SELECT value FROM " . table ( 'config' ) . " where name='free_order_enabled' " );
$freeOrderImageConfig   = mysqld_select ( "SELECT value FROM " . table ( 'config' ) . " where name='free_order_image' " );
if(!empty($freeOrderEnabledConfig))
{
	$freeOrderEnabled = $freeOrderEnabledConfig['value'];
}
else{
	$freeOrderEnabled = 0;
}

//免单开启时
if($freeOrderEnabled==1)
{
	$period = getLastWeekPeriod();					//上周一到周天的时间戳
	
	$free_list = mysqld_select ( "SELECT f.*,c.name FROM " . table ( 'free_config' ) .' f,'.table('shop_category'). " c where c.id = f.category_id and free_starttime='".$period['monday_time']."' and free_endtime='".$period['sunday_time']."' ORDER BY f.createtime DESC " );
	
	if(!empty($free_list))
	{
		//加入虚假数据
		$free_list['free_member_count'] = $free_list['free_member_count']+intval($free_list['free_starttime'] / 2896753);
		$free_list['free_amount'] 		= $free_list['free_amount']+intval($free_list['free_endtime'] / 16453);
	}
	if ( !empty($freeOrderImageConfig) ){
        $result['data']['image'] = $freeOrderImageConfig['value'];
	}else{
        $result['data']['image'] = '';
	}
	$result['data']['free_list']= $free_list;
	$result['data']['url']		= WEBSITE_ROOT.'index.php?mod=mobile&op=rule&name=shopwap&do=free_charge_rule';				//活动说明URL
}

$result['data']['free_order_enabled']	= $freeOrderEnabled;		//免单是否开启
$result['code'] 						= 1;

echo apiReturn($result);
exit;