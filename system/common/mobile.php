<?php
defined('SYSTEM_IN') or exit('Access Denied');
// 客户端主要控制器抽象方法
abstract class BjSystemModule {
		public function __mobile($f_name){
			global $_CMS,$_GP;
			$name = strtolower($f_name);	
			$file = SYSTEM_ROOT.$_CMS['module'].'/class/mobile/'.$name.'.php';
			if (is_file($file)){
		        include_once  $file;
				$name = "{$_CMS['module']}\\controller\\{$name}";
				if(class_exists($name)){
					$obj = new $name();
					$_GP['op'] = $_GP['op'] ?: 'index';
					$obj->request = $_GP;
					call_user_func(array($obj, $_GP['op']));
				}
			}else{
				if(DEVELOPMENT){
					//开发环境提示错误
					exit("控制器文件{$file}不存在");
				}else{
					//线上环境可以引导到404友好页面
					exit('ContRoller Not Existed');
				}
			}
	}
}