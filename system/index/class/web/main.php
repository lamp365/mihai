<?php
// +----------------------------------------------------------------------
// | WE CAN DO IT JUST FREE
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.squdian.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 小物社区 <QQ:119006873> <http://www.squdian.com>
// +----------------------------------------------------------------------
$allrule    = mysqld_selectall('SELECT * FROM '.table('rule'));
$account    = mysqld_select('SELECT * FROM '.table('user')." WHERE  id=:id" , array(':id'=> $_CMS['account']['id']));
$userRule       = getAdminHasRule($_CMS['account']['id']);
$parentMenuList = '';
$menurule 		= array();
if(!empty($userRule)){
	foreach($userRule as  $rule){
		$str = $rule['modname']."-".$rule['moddo'];
		if(!empty($rule['modop'])){
			$str .= "-{$rule['modop']}";
		}
		$menurule[]= $str;
		if($rule['pid'] == 0)
			$parentMenuList[] = $rule;
	}
	$result         = getCatRuleUrl($menurule,$userRule,$parentMenuList);
	$menurule       = $result['menuRule'];
	$parentMenuList = $result['parentMenuList'];
//		ppd($parentMenuList);
}

//得到快捷菜单
$top_menu = '';
if(!empty($parentMenuList)){
	foreach($parentMenuList as $m_list){
		foreach($m_list as $t_menu){
			if($t_menu['top_menu'] == 1){
				$top_menu[] = $t_menu;
			}
		}
	}

}


$module_allow = array(
	MenuEnum::DATA_REPORT_MANGE	=> 'addon6',
	MenuEnum::YUN_GOU_MANGE		=> 'addon7',
	MenuEnum::ARTICLE_MANGE 	=> 'addon8'
);	//控制要显示的几个
$module_allow_fan = array_flip($module_allow);

$username     =	$_CMS['account']['username'];
$settings     = globaSetting();
$condition    = '';
$modulelist   = '';
if(mysqld_fieldexists('modules', 'isdisable')) {
	$condition=' and `isdisable`=0 ';
}
$result = mysqld_selectall("SELECT *,'' as menus FROM " . table('modules') . " where 1=1 $condition order by displayorder");
foreach($result as $index => $module)  {
	if(!in_array($module['name'],$module_allow)){
		continue;
	}
	$key 				= $module_allow_fan[$module['name']];
	$module['menus'] 	= mysqld_selectall("SELECT * FROM " . table('modules_menu') . " WHERE `module`=:module order by id",array(':module'=>$module['name']));
	$modulelist[$key]   = $module;
}
$exchange_rate = mysqld_select("SELECT * FROM " .table('config'). " WHERE  name = 'exchange_rate' limit 1");
include page('main');
