<?php
$settings=globaSetting();
$is_login = is_vip_account();
if ( $is_login ){
    integration_session_account($loginid, $oldsessionid);
    header("location:".create_url('mobile', array('name' => 'shopwap','do' => 'purchase_order')));
}
// 获取品牌数据
$brand = mysqld_selectall("SELECT * FROM " . table('shop_brand') . "  where deleted=0 and recommend = 1 ");
if (checksubmit("submit")) {
    if (empty($_GP['mobile'])) {
        message("请输入手机号");
    }
    if (empty($_GP['pwd'])) {
        message("请输入密码");
    }
    $member = get_vip_session_account();
    $oldsessionid = $member['openid'];
    $unionid      = $member['unionid'];
    $loginid = vip_member_login($_GP['mobile'], $_GP['pwd']);
    if ($loginid == - 1) {
        message("账户已被禁用！");
    }
	if ($loginid == -2 ){
        message("账户身份错误!");
	}
	if ($loginid == -3 ){
        // 找出业务员的信息
        $relation  = mysqld_select("SELECT a.*,b.username,b.mobile FROM ".table('member')." as a LEFT JOIN ".table('user')." as b ON a.relation_uid = b.id WHERE a.mobile = ".$_GP['mobile']);
        message("尊敬的用户您好，您的账户长期未操作，已经被系统锁定，如需解锁请联系您的业务员!<br/>".$relation['username'].":".$relation['mobile']);
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