<?php
defined('SYSTEM_IN') or exit('Access Denied');
$operation = !empty($_GP['op']) ? $_GP['op'] : 'listuser';

if ($operation == 'listuser') {
	if(empty($_GP['id'])){
		$list = mysqld_selectall("select * from " . table('user'));
	}else{
		$sql = "select u.* from ".table('rolers_relation')." as r left join ".table('user')." as u on u.id=r.uid where r.rolers_id={$_GP['id']}";
		$list = mysqld_selectall($sql);
	}

	$rolers = mysqld_selectall("select name,id from ".table('rolers')." where type=1");
	include page('listuser');
}

if ($operation == 'rule') {
	//设置权限
	$id      = $_GP['id'];
	$rule    = mysqld_select('SELECT * FROM '.table('rolers')." WHERE  id={$id} and type=1");
	if(empty($id) || empty($rule))
	{
		message('该角色不存在！',refresh(),'error');
	}
	if (checksubmit('submit')) {
		//清空掉之前的缓存规则
		cleanAdminHasRule($_GP['id']);
		if(!empty($_GP['role_ids']))
		{
			$rule_ids = implode(',',$_GP['role_ids']);
			mysqld_update('rolers', array('rule'=>$rule_ids), array('id'=>$_GP['id']));
		}else{
			mysqld_update('rolers', array('rule'=>''), array('id'=>$_GP['id']));
		}
		message('权限修改成功！',refresh(),'succes');

	}else{

		$allrule    = getSystemRule();
		$roler_name = $rule['name'];

		$userRule = !empty($rule['rule']) ? explode(',',$rule['rule']) : '';
		if(!empty($userRule)){
			foreach($allrule as $key => $item){
				if( in_array($item['id'],$userRule)){
					$allrule[$key]['check']= 1;
				}//不能else为0

			}
		}

		$result = getRuleParentChildrenArr($allrule);
		$parent = $result['parent'];
		$children = $result['children'];

		$DbFiledList       = getDbTablesInfo();
		$DbFiledListJson   = json_encode($DbFiledList);
		$userHasDbRule     = !empty($rule['db_rule']) ? json_decode($rule['db_rule'],true) : '';
		$userHasDbRuleJson = $rule['db_rule'];
	}
	include page('rule');
}

if ($operation == 'rule_field') {   //字段权限
	$id      = $_GP['id'];
	$rule    = mysqld_select('SELECT * FROM '.table('rolers')." WHERE  id={$id} and type=1");
	if(!empty($rule)){
		//插入新的
		$data = '';
		if(isset($_GP['shop_goods']) && !empty($_GP['shop_goods'])){
			$data['shop_goods'] = $_GP['shop_goods'];
		}
		if(isset($_GP['shop_dish']) && !empty($_GP['shop_dish'])){
			$data['shop_dish'] = $_GP['shop_dish'];
		}
		if(!empty($data)){
			$data = json_encode($data);
		}
		mysqld_update('rolers',array('db_rule'=>$data),array('id'=>$id));
		die(showAjaxMess('200','高级权限设置成功！'));
	}else{
		die(showAjaxMess('1002','对不起，该角户不存在！'));
	}
}

if ($operation == 'deleteuser') {
	//查找之前是否有关联过一些渠道商
	isRelationPurchase($_GP['id']);
	mysqld_delete('user', array('id'=>$_GP['id']));
	mysqld_delete('rolers_relation', array('uid'=> $_GP['id']));
	message('删除成功',refresh(),'success');
}
if ($operation == 'changepwduser') {

	$account = mysqld_select('SELECT * FROM '.table('user')." WHERE  id=:id" , array(':id'=> $_GP['id']));
	$username =$account['username'];
	$id =$account['id'];
	if (checksubmit('submit')) {
		if(!empty($account['id']))
		{
			if($_GP['newpassword']!=$_GP['confirmpassword'])
			{

				message('两次密码不一致！',refresh(),'error');
			}
			$data = array('mobile'=>$_GP['mobile']);
			if(!empty($_GP['newpassword'])){
				$data['password']  = md5($_GP['newpassword']);
			}
			mysqld_update('user', $data,array('id'=> $account['id']));
			message('资料修改成功！',create_url('site',array('name' => 'user','do' => 'user','op'=>'listuser')),'succes');
		}else
		{
			message($_GP['username'].'用户名已存在',refresh(),'error');
		}

	}
	include page('changepwd');
}

if ($operation == 'adduser') {
	if (checksubmit('submit')) {
		if(empty($_GP['username'])||empty($_GP['newpassword']))
		{
			message('用户名或密码不能为空',refresh(),'error');
		}
		$account = mysqld_select('SELECT * FROM '.table('user')." WHERE  username=:username" , array(':username'=> $_GP['username']));

		if(empty($account['id']))
		{
			if($_GP['newpassword']!=$_GP['confirmpassword'])
			{

				message('两次密码不一致！',refresh(),'error');

			}
			$data= array('username'=> $_GP['username'],'password'=> md5($_GP['newpassword']),'createtime'=>time());
			if(!empty($_GP['mobile'])){
				$data['mobile']  = $_GP['mobile'];
			}
			mysqld_insert('user', $data);
			message('新增用户成功！',web_url('user'),'succes');
		}else
		{
			message($_GP['username'].'用户名已存在',refresh(),'error');
		}
	}else{   //submit结束

		include page('adduser');
	}
}


if ($operation == 'menu') {  //菜单节点
	$act = $_GP['act'];
	switch($act){
		case 'post':  //添加编辑页面
			$parentMenu = $editMenu = array();
			$menu = mysqld_selectall("select moddescription,pid,id from ".table('rule') ." where pid=0");
			if(!empty($_GP['id'])){
				$editMenu = mysqld_select("select moddescription,moddo,modname,modop,pid,sort,id,cat_id,act_type from ".table('rule') ." where id={$_GP['id']}");
			}
			if(!empty($_GP['parent_id'])){
				$parentMenu = mysqld_select("select moddescription,moddo,modname,modop,pid,sort,id,cat_id,act_type from ".table('rule') ." where id={$_GP['parent_id']}");
			}
			include page('menuPost');
			break;

		case 'postData':  //提交表单
			$data = array(
				'moddescription' => $_GP['moddescription'],
				'moddo'    		=> $_GP['moddo'],
				'modname' 		=> $_GP['modname'],
				'modop'   		=> $_GP['modop'],
				'sort' 	   		=> $_GP['sort'],
				'act_type' 	   	=> $_GP['act_type'],
			);
			if(!empty($_GP['parent_id'])){
				$url  = web_url('user',array('op'=>'sonMenuList','id'=>$_GP['parent_id']));
			}else{
				$url  = web_url('user',array('op'=>'menudisplay'));
			}
			if(!empty($_GP['id'])){  //更新
				mysqld_update("rule",$data,array('id'=>$_GP['id']));
				cleanSystemRule();
				message('更新菜单成功！',$url,'succes');
			}else{  //添加
				if($_GP['cat_id'] == 0){
					message("对不起，请选择分类！",'','error');
				}
				$data['pid']     = $_GP['pid'];
				$data['cat_id']  = $_GP['cat_id'];
				mysqld_insert('rule',$data);
				cleanSystemRule();
				message('新增菜单成功！',$url,'succes');
			}
			break;

		case 'delete' :
			if(empty($_GP['id'])){
				message('对不起参数有误！','','error');
			}
			if(is_array($_GP['id'])){   //批量删除
				foreach($_GP['id'] as $id){
					$result  = mysqld_delete('rule',array('id'=>$id));
				}
			}else{   //单个删除
				$result  = mysqld_delete('rule',array('id'=>$_GP['id']));
			}

			if($result){
				cleanSystemRule();
				message("删除成功！");
			}
			break;

		default:

			break;
	}
}

if ($operation == 'sonMenuList') {//子节点
	$parentInfo = mysqld_select("select moddescription,id,cat_id from ". table('rule') ." where id={$_GP['id']}");
	$menu       = mysqld_selectall("select moddescription,concat(modname,'/',moddo,'/',modop) as url,pid,sort,id,cat_id,act_type from ".table('rule') ." where pid={$_GP['id']} order by sort asc,id asc");
	include page('sonMenuList');
}

if($operation == 'menudisplay'){
	$cat = MenuEnum::$getMenuEnumValues;
	$menu = mysqld_selectall("select moddescription,concat(modname,'/',moddo,'/',modop) as url,pid,sort,id,cat_id,top_menu from ".table('rule') ." where pid=0 order by cat_id asc,sort asc,id asc");
	$data = array();
	if(!empty($menu)){
		foreach($menu as $row){
			if(array_key_exists($row['cat_id'],$cat)){
				$row['cat_name']       = $cat[$row['cat_id']];
				$data[$row['cat_id']][] = $row;
			}
		}
	}

	include page('menuNode');
}

if($operation == 'cleanMenu'){
	cleanSystemRule();
	message('清除缓存成功',refresh(),'success');
}

if ($operation == 'getroler'){
	if ( !empty( $_GP['id'] ) ){
		$rolers = mysqld_select("SELECT * FROM ".table('rolers')." WHERE id = ".$_GP['id']);
		if ( $rolers['type'] == 3 ){
			die(showAjaxMess(200,$rolers['discount']));
		}else{
			die(showAjaxMess(1002,''));
		}
	}else{
		die(showAjaxMess(1002,''));
	}
}

if($operation == 'rolerlist'){
	$rolers = mysqld_selectall("select * from ".table('rolers')." where type=1");
	$users  = mysqld_selectall("select username,id from ".table('user'));
	//查找所有已经角色分配过的用户
	$rolers_relation = mysqld_selectall("select uid from ".table('rolers_relation'));
	if(!empty($rolers_relation)){
		$temp_data  = array();
		foreach($rolers_relation as $key=>$row){
			$temp_data[$row['uid']] = $row;
		}
		$rolers_relation = $temp_data;
	}

	//去除已经分配过的用户  这样避免每个用户被添加到多个角色里
	foreach($users as $key => $user){
		if(array_key_exists($user['id'],$rolers_relation)){
			unset($users[$key]);
		}
	}

	$purchase= mysqld_selectall("select * from ".table('rolers')." where type<>1 order by pid asc");
	if (! empty($purchase)) {
		$childrens = '';
		foreach ($purchase as $key => $item) {
			if (! empty($item['pid'])) {
				$childrens[$item['pid']][] = $item;
				unset($purchase[$key]);
			}
		}
	}

	//取出所有的品牌
	$shop_brand = mysqld_selectall("select id,brand from ".table('shop_brand')." where deleted=0");
	include page('rolerlist');
}

if($operation == 'deleterolers'){
	$rolers = mysqld_select("select id,pid,isdelete,type from ".table('rolers')." where id={$_GP['id']}");
	if($rolers['type'] == 1){
		//1代表后台管理员角色使用
		if($rolers['isdelete'] == 0){ //不可删除
			message('对不起，该角色不允许删除！',refresh(),'error');
		}
	}else{
		//2代表渠道商这边身份使用  后期可能会扩展其他身份
		if($rolers['pid'] == 0){ //不可删除
			message('对不起，该身份不允许删除！',refresh(),'error');
		}
	}

	mysqld_delete('rolers',array('id'=>$_GP['id']));
	message('删除成功！',refresh(),'success');
}
if($operation == 'changerolers'){
	if(empty($_GP['rolers_name']))
		message('对不起，名字不能为空！',refresh(),'error');

	$forbid_brand = empty($_GP['forbid_brand']) ? '' : serialize($_GP['forbid_brand']);
	$update_data = array(
		'name'		   => $_GP['rolers_name'],
		'description'  => $_GP['description'],
		'forbid_brand' => $forbid_brand
	);
	if(!empty($_GP['rolers_alls'])){
		$update_data['discount'] = $_GP['rolers_alls'];
	}
	mysqld_update('rolers',$update_data,array('id'=>$_GP['id']));
	$url = web_url('user',array('op'=>'rolerlist'));
	$url .= "#{$_GP['tab']}";
	message('修改成功！',$url,'success');
}
if($operation == 'addrolers'){
	if(empty($_GP['rolers_name']))
		message('对不起，名字不能为空！',refresh(),'error');
	mysqld_insert('rolers',array(
		'name'		=> $_GP['rolers_name'],
		'description' => $_GP['description'],
		'type'=>1,
		'createtime'=>time(),
		'modifiedtime'=>time()
	));
	message('添加成功！',refresh(),'success');
}

if($operation == 'add_purchase_rolers'){
	if($_GP['type'] == 0)
		message('对不起请选择身份类型！',refresh(),'error');
	if(empty($_GP['rolers_name']))
		message('对不起，名称不能为空！',refresh(),'error');

	mysqld_insert('rolers',array(
		'name'=>$_GP['rolers_name'],
		'pid' =>$_GP['pid'],
		'type'=>$_GP['type'],
		'createtime'=>time(),
		'modifiedtime'=>time()
	));
	$url = web_url('user',array('op'=>'rolerlist'));
	$url .= "#home";
	message('添加成功！',$url,'success');
}
if($operation == 'showuser'){
	$sql = "select u.id,u.username,r.rolers_id from ".table('rolers_relation')." as r left join ".table('user')." as u";
	$sql .= " on u.id=r.uid where r.rolers_id={$_GP['id']}";
	$users = mysqld_selectall($sql);
	die(showAjaxMess(200,$users));
}

if($operation == 'add_rolers_relation')
{
	//先删除再加入   id是rolers表中id
	mysqld_delete("rolers_relation",array('rolers_id'=>$_GP['id']));
	foreach($_GP['uids'] as $key => $uid){
		mysqld_insert('rolers_relation',array('uid'=>$uid,'rolers_id'=>$_GP['id'],'createtime'=>time()));
	}
	message('操作成功！',refresh(),'success');
}

if($operation == 'fenpei_rolers')
{
	if(empty($_GP['uid']))
		message('对不起，参数有误',refresh(),'error');

	//查找之前是否有关联过一些渠道商
	isRelationPurchase($_GP['uid']);
	//先删除之前分配的
	mysqld_delete('rolers_relation',array('uid'=>$_GP['uid']));
	mysqld_insert('rolers_relation',array(
		'rolers_id' => $_GP['rolers_id'],
		'uid'       => $_GP['uid'],
		'createtime'=> time(),
	));
	message('操作成功！',refresh(),'success');
}

if($operation == 'rolercate'){
	//根据type获取对应的顶级角色分类
	$roler = mysqld_select("select name,id,type from ".table('rolers')." where type={$_GP['type']} and pid=0");
	if(empty($roler)){
		//则前端需要创建一个顶级分类
		die(showAjaxMess(1002,'无分类'));
	}else{
		//则前端会显示该顶级分类
		die(showAjaxMess(200,$roler));
	}
}

if($operation == 'menusort'){
	//更新排序
	if(!empty($_GP['id']) && $_GP['sort'] !== NULL){
		mysqld_update('rule',array('sort'=>$_GP['sort']),array('id'=>$_GP['id']));
		die(showAjaxMess(200,'操作成功！'));
	}
	die(showAjaxMess(1002,'操作失败！'));
}

if($operation == 'top_nemu'){
	//设置快捷菜单
	if(!empty($_GP['id'])){
		mysqld_update('rule',array('top_menu'=>$_GP['topmenu']),array('id'=>$_GP['id']));
		die(showAjaxMess(200,'操作成功！'));
	}
	die(showAjaxMess(1002,'操作失败！'));
}

function isRelationPurchase($uid){
	$mobile = '';
	$purchase = mysqld_select("select mobile from ".table('member')." where relation_uid={$uid}");
	if(!empty($purchase))
		$mobile = $purchase['mobile'];

	if(!empty($mobile)){
		message("对不起，关联了用户{$mobile},请先去修改！");
	}
}