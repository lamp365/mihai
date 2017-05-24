<?php
defined('SYSTEM_IN') or exit('Access Denied');
abstract class BjSystemModule {
		public function __web($f_name){
			global $_CMS,$_GP;
			include_once  SYSTEM_ROOT.$_CMS['module'].'/class/web/'.strtolower(substr($f_name,3)).'.php';
		}
}