<?php
defined('SYSTEM_IN') or exit('Access Denied');
class modulesAddons  extends BjSystemModule {
	public function do_control($name=''){
		if ( !empty($name) ){
			global $_CMS;
			if($name == 'weixin_notify'){
				$_name = 'notify_url';
				$dir   = 'weixin';
			}else{
				$_name = $name;
				$name_arr = explode('_',$name);
				$dir   = $name_arr[0];
			}

			include_once("plugin/payment/{$dir}/{$_name}.php");
			die();
		}else{
			exit('控制器不存在');
		}
	}
}


