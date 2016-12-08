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
$userRule   = mysqld_selectall('SELECT * FROM '.table('user_rule')." WHERE  uid={$_CMS['account']['id']} and menu_db_type =1 order by cat_id asc,id asc");
$sql 		= "select a.*,b.moddescription from ". table('user_rule') ." as a left join ". table('rule') ." as b on a.role_id=b.id where b.pid=0 and a.uid={$_CMS['account']['id']} order by a.cat_id asc,a.id asc";
$parentMenuList = mysqld_selectall($sql);
$menurule 		= array();
if(!empty($userRule)){
	foreach($userRule as  $rule){
		$str = $rule['modname']."-".$rule['moddo'];
		if(!empty($rule['modop'])){
			$str .= "-{$rule['modop']}";
		}
		$menurule[]= $str;

	}
	$result         = getCatRuleUrl($menurule,$userRule,$parentMenuList);
	$menurule       = $result['menuRule'];  //暂时记为null
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
