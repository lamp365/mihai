<?php
defined('SYSTEM_IN') or exit('Access Denied');

class publicAddons  extends BjSystemModule {
	public function do_control($name=''){
		if ( !empty($name) ){
			$this->__web($name);
		}else{
			exit('控制器不存在');
		}
	}
	public function check_verify($verify)
	{
		
		if(strtolower($_SESSION["VerifyCode"])==strtolower($verify))
		{
			unset($_SESSION["VerifyCode"]);
			return true;
		}
		return false;
	}

}


