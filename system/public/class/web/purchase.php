<?php
$settings=globaSetting();
$is_login = is_vip_account();
if ( $is_login ){
    integration_session_account($loginid, $oldsessionid);
    header("location:".create_url('mobile', array('name' => 'shopwap','do' => 'purchase_order')));
}
if (checksubmit("submit")) {
    if (empty($_GP['mobile'])) {
        message("请输入手机号");
    }
    if (empty($_GP['pwd'])) {
        message("请输入密码");
    }
    $member = get_vip_session_account();
    $oldsessionid = $member['openid'];
    $loginid = vip_member_login($_GP['mobile'], $_GP['pwd']);
    if ($loginid == - 1) {
        message("账户已被禁用！");
    }
    if (empty($loginid)) {
        message("用户名或密码错误");
    } else {
        integration_session_account($loginid, $oldsessionid);
        header("location:".create_url('mobile', array('name' => 'shopwap','do' => 'purchase_order')));
    }
}		
	    //header("location:".create_url('site', array('name' => 'index','do' => 'main')));
		include themepage('purchase_login');