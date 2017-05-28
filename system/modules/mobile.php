<?php
defined('SYSTEM_IN') or exit('Access Denied');
class modulesAddons  extends BjSystemModule {
	public function do_control($name=''){
		if ( !empty($name) ){
			global $_CMS;
			if($name == 'weixin_notify'){
				$_name = 'notify_url';
			}
			include_once("plugin/payment/weixin/{$_name}.php");
			$this->__mobile($name);
		}else{
			exit('控制器不存在');
		}
	}
}


