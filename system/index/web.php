<?php
// +----------------------------------------------------------------------
// | WE CAN DO IT JUST FREE
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.squdian.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 小物社区 <QQ:119006873> <http://www.squdian.com>
// +----------------------------------------------------------------------
defined('SYSTEM_IN') or exit('Access Denied');

class indexAddons  extends BjSystemModule {
	public function do_control($name=''){
		if ( !empty($name) ){
			$this->__web($name);
		}else{
			exit('控制器不存在');
		}
	}
	public function dateToWeekday($dateindex)
	{
		if($dateindex==1)
		{
			return '周一';
		}
			if($dateindex==2)
		{
			return '周二';
		}
			if($dateindex==3)
		{
			return '周三';
		}
			if($dateindex==4)
		{
			return '周四';
		}
			if($dateindex==5)
		{
			return '周五';
		}
			if($dateindex==6)
		{
			return '周六';
		}
			if($dateindex==7)
		{
			return '周日';
		}
		return "";
	}
	


	
}


