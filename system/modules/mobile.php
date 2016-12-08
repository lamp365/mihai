<?php
defined('SYSTEM_IN') or exit('Access Denied');
class modulesAddons  extends BjSystemModule {
	public function do_weixin_notify()
	{		
		global $_CMS;
					include_once("plugin/payment/weixin/notify_url.php");
					exit;
	}
		public function do_weixin_native_notify()
	{		
		global $_CMS;
					include_once("plugin/payment/weixin/weixin_native_notify.php");
					exit;
	}
		public function do_alipay_notify()
	{		
		global $_CMS;
					include_once("plugin/payment/alipay/alipay_notify.php");
					exit;
	}
		public function do_alipay_return_url()
	{		
		global $_CMS;
					include_once("plugin/payment/alipay/alipay_return_url.php");
					exit;
	}
		
}


