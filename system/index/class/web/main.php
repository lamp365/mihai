<?php
// +----------------------------------------------------------------------
// | WE CAN DO IT JUST FREE
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.squdian.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 小物社区 <QQ:119006873> <http://www.squdian.com>
// +----------------------------------------------------------------------

$account        = mysqld_select('SELECT * FROM '.table('user')." WHERE  id=:id" , array(':id'=> $_CMS['account']['id']));
//所有权限
$allrule        = getSystemRule();
//所拥有的权限就是需要被 禁止的
$userRule       = getAdminHasRule($_CMS['account']['id']);
//从所有的权限中移除掉 被禁止的
$diff_rule      = diffUserRule($allrule,$userRule);

$parentMenuList = '';
$menurule 		= array();
if(!empty($diff_rule)){
	foreach($diff_rule as  $rule){
		$str = $rule['modname']."-".$rule['moddo'];
		if(!empty($rule['modop'])){
			$str .= "-{$rule['modop']}";
		}
		$menurule[]= $str;
		if($rule['pid'] == 0)
			$parentMenuList[] = $rule;
	}

	$result         = getCatRuleUrl($menurule,$diff_rule,$parentMenuList);
//	ppd($result);
	$menurule       = $result['menuRule'];
	$parentMenuList = $result['parentMenuList'];
//		ppd($parentMenuList);
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

include page('main');
