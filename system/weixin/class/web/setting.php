<?php
defined('SYSTEM_IN') or exit('Access Denied');
hasrule('weixin','weixin');
$op = empty($_GP['op'])? 'list' : $_GP['op'];
if($op == 'list'){
	$list      = mysqld_selectall("select * from ".table('weixin_config'));
	$thirdlogin = mysqld_select("SELECT * FROM " . table('thirdlogin') . " WHERE code = :code", array(':code' => 'weixin'));
	include page('setting_list');
}else if($op == 'add'){
	$isadd    = true;
	$settings = array();
	if(!empty($_GP['id']))
	{
		$isadd   = false;
		$settings = mysqld_select("select * from ".table('weixin_config')." where id={$_GP['id']}");
	}


	if (checksubmit()) {
		$domain = str_replace('http://','',$_GP['domain']);
		$domain = str_replace('https://','',$domain);
		$cfg = array(
			'domain'      => $domain,
			'weixinname'  => $_GP['weixinname'],
			'weixintoken' => $_GP['weixintoken'],
			'accesskey'   => $_GP['accesskey'],
			'appid'       => $_GP['appid'],
			'appsecret'   => $_GP['appsecret']
		);
		if(empty($_GP['id'])){
			$cfg['createtime'] = time();
			$cfg['modifytime'] = time();
			mysqld_insert('weixin_config',$cfg);
		}else{
			$cfg['modifytime'] = time();
			mysqld_update('weixin_config',$cfg,array('id'=>$_GP['id']));
		}

		$url = web_url('setting',array('op' =>'list','name'=>'weixin'));
		message('操作成功', $url, 'success');
	}

	include page('setting');

}else if($op == 'use_weixin'){
	mysqld_update('weixin_config',array('is_used'=>$_GP['is_used']),array('id'=>$_GP['id']));
	message('操作成功', refresh(), 'success');
}else if($op == 'isdefault'){
	if($_GP['is_default']==1){
		//先取消掉所有的默认，再重新设置一个默认
		mysqld_update('weixin_config',array('is_default'=>0));
	}
	mysqld_update('weixin_config',array('is_default'=>$_GP['is_default']),array('id'=>$_GP['id']));
	message('操作成功', refresh(), 'success');

}else if($op == 'loginStatus'){
		$thirdlogin = mysqld_select("SELECT * FROM " . table('thirdlogin') . " WHERE code = :code", array(':code' => 'weixin'));
		require WEB_ROOT.'/system/modules/plugin/thirdlogin/weixin/lang.php';

		if (empty($thirdlogin['id'])) {
			$data = array(
				'code' => 'weixin',
				'enabled' => intval($_GP['thirdlogin_weixin']),
				'name' => $_LANG['thirdlogin_weixin_name']
			);
			mysqld_insert('thirdlogin', $data);
		} else {
			$data = array(
				'enabled' => intval($_GP['thirdlogin_weixin']),
				'name' => $_LANG['thirdlogin_weixin_name'],
			);
			mysqld_update('thirdlogin',$data , array('code' =>'weixin'));
		}
		message('操作成功', refresh(), 'success');
}
