<?php
defined('SYSTEM_IN') or exit('Access Denied');
abstract class BjSystemModule {
		public function __web($f_name){
			global $_CMS,$_GP;
			$name = strtolower($f_name);
			$file = SYSTEM_ROOT.$_CMS['module'].'/class/web/'.$name.'.php';
			if (is_file($file)){
				include_once  $file;
				$name = "{$_CMS['module']}\\controller\\{$name}";
				if(class_exists($name)){
					$obj = new $name();
					$obj->request = $_GP;
					$op = $_GP['op'] ?: 'index';
					call_user_func(array($obj, $op));
				}
			}else{
				if(DEVELOPMENT){
					//开发环境提示错误
					exit("控制器文件{$file}不存在！");
				}else{
					//线上环境可以引导到404友好页面
					exit('ContRoller Not Existed');
				}
			}

		}
}