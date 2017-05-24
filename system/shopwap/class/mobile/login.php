<?php
$is_login = is_login_account();
if ( $is_login ){
    //加入 $unionid 只为了微信绑定平台用户所做的
    integration_session_account($loginid, $oldsessionid,$unionid);
    header("location:" . to_member_loginfromurl());
}
if (checksubmit("submit")) {
    if (empty($_GP['mobile'])) {
        message("请输入手机号",refresh(),'error');
    }
    if (empty($_GP['pwd'])) {
        message("请输入密码",refresh(),'error');
    }
    $member = get_session_account();
    $oldsessionid = $member['openid'];
    $unionid      = $member['unionid'];
    $loginid = member_login($_GP['mobile'], $_GP['pwd']);
    if ($loginid == - 1) {
        message("账户已被禁用！",refresh(),'error');
    }
    if (empty($loginid)) {
        message("用户名或密码错误",refresh(),'error');
    } else {
        integration_session_account($loginid, $oldsessionid,$unionid);
        header("location:" . to_member_loginfromurl());
    }
}
$qqlogin = mysqld_select("SELECT * FROM " . table('thirdlogin') . " WHERE enabled=1 and `code`='qq'");
if (! empty($qqlogin) && ! empty($qqlogin['id'])) {
    $showqqlogin = true;
}
// 获取使用条款
        $use_page = getArticle(1,2);

		if ( !empty($use_page) ){
           $use_page = mobile_url('article',array('name'=>'addon8','id'=>$use_page[0]['id']));
		}else{
           $use_page = 'javascript:void(0)';
		}

		// 获取用户隐私
        $use_private = getArticle(1,3);
		if ( !empty($use_private) ){
           $use_private = mobile_url('article',array('name'=>'addon8','id'=>$use_private[0]['id']));
		}else{
           $use_private =  'javascript:void(0)';
		}

        //wap端关于我们
        $use_about = getArticle(1,5);
        if ( !empty($use_about) ){
            $use_about = mobile_url('article',array('name'=>'addon8','id'=>$use_about[0]['id']));
        }else{
            $use_about =  'javascript:void(0)';
        }
include themePage('login');