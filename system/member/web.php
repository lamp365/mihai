<?php
defined('SYSTEM_IN') or exit('Access Denied');
require WEB_ROOT.'/system/member/lib/rank.php';
class memberAddons  extends BjSystemModule {
	public function do_control($name=''){
		if ( !empty($name) ){
			$this->__web($name);
		}else{
			exit('控制器不存在');
		}
	}
}


