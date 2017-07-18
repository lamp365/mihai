<?php
/*
session操作
*/

function get_sessionid()
{
    return '_t' . session_id();
}

function integration_session_account($loginid, $oldsessionid)
{
    $member = mysqld_select("SELECT * FROM " . table('member') . " WHERE openid = :openid ", array(
        ':openid' => $loginid
    ));
    $sessionmember = mysqld_select("SELECT * FROM " . table('member') . " WHERE openid = :openid", array(
        ':openid' => $oldsessionid
    ));
    
    if (empty($member['openid']) || $sessionmember['istemplate'] != 1) {
        return;
    }
    $cartall = mysqld_selectall("SELECT * FROM " . table('shop_cart') . " WHERE session_id = :session_id ", array(
        ':session_id' => $oldsessionid
    ));
    
    foreach ($cartall as $cartitem) {
        $row = mysqld_select("SELECT * FROM " . table('shop_cart') . " WHERE session_id = :loginid  AND goodsid = :goodsid  and optionid=:optionid limit 1", array(
            ':loginid'  => $loginid,
            ':goodsid'  => $cartitem['goodsid'],
            ':spec_key' => $cartitem['spec_key']
        ));
        if (empty($row['id'])) {
            
            mysqld_update('shop_cart', array(
                'session_id' => $loginid
            ), array(
                'id' => $cartitem['id']
            ));
        } else {
            $t = $cartitem['total'] + $row['total'];
            
            $data = array(
                'marketprice' => $cartitem['marketprice'],
                'total'       => $t,
                'spec_key'    => $cartitem['spec_key']
            );
            mysqld_update('shop_cart', $data, array(
                'id' => $row['id']
            ));
            mysqld_delete('shop_cart', array(
                'id' => $cartitem['id']
            ));
        }
    }
    mysqld_update('shop_address', array(
        'openid' => $loginid
    ), array(
        'openid' => $oldsessionid
    ));
    mysqld_update('shop_order', array(
        'openid' => $loginid
    ), array(
        'openid' => $oldsessionid
    ));
    mysqld_update('shop_address', array(
        'openid' => $loginid
    ), array(
        'openid' => $oldsessionid
    ));
    mysqld_update('member_paylog', array(
        'openid' => $loginid
    ), array(
        'openid' => $oldsessionid
    ));

    if ($sessionmember['gold'] > 0) {
        member_gold($loginid, intval($sessionmember['gold']), 'addgold', PayLogEnum::getLogTip('LOG_LOGIN_TIP'));
    }
    
    mysqld_delete('member', array(
        'openid' => $oldsessionid
    ));

}

function get_session_account($useAccount = true)
{
    $sessionAccount = array();
    if (! empty($_SESSION[MOBILE_SESSION_ACCOUNT]) && ! empty($_SESSION[MOBILE_SESSION_ACCOUNT]['openid'])) {
        //微信端走这里
        $sessionAccount = $_SESSION[MOBILE_SESSION_ACCOUNT];
    } else {
         $sessionAccount = array(
             'openid'  => get_sessionid(),
             'unionid' => ''
         );
         $_SESSION[MOBILE_SESSION_ACCOUNT] = $sessionAccount;
    }
    return $sessionAccount;
}

/**
 * @content 微信打开时才会记录 MOBILE_SESSION_ACCOUNT
 * 存入值有 $sessionAccount = array(
                'openid'         => $from_user,
 *               'weixin_openid' => $from_user,
                'unionid'        => $_GP['unionid']
            );
 * @param string $key  可以是openid 或者  unionid  weixin_openid
 * @return string
 */
function get_weixin_session_account($key = ''){
    $weixin_info = '';
    if(!empty($_SESSION[MOBILE_SESSION_ACCOUNT])){
        $weixin_info = $_SESSION[MOBILE_SESSION_ACCOUNT];
        if(!empty($key) && array_key_exists($key,$weixin_info)){
            $weixin_info = $_SESSION[MOBILE_SESSION_ACCOUNT][$key];
        }
    }
    return $weixin_info;
}

function tosaveloginfrom()
{
    $_SESSION["mobile_login_fromurl"] = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
}

function clearloginfrom()
{
    $_SESSION["mobile_login_fromurl"] = "";
}

function is_login_account()
{
    if (! empty($_SESSION[MOBILE_ACCOUNT])) {
        return true;
    }
    return false;
}
function is_vip_account()
{
	// 判断是否登录，如果是，则更新登录时间
    if (! empty($_SESSION[VIP_MOBILE_ACCOUNT])) {
		$member = mysqld_select("SELECT * FROM " . table('member') . " where openid=:openid  limit 1", array(
            ':openid' => $_SESSION[VIP_MOBILE_ACCOUNT]['openid']
        ));
		$createtime = $member['createtime'];
	    $lastime    = $member['lastime'];
	    $timeend   = 30 * 24  * 60 * 60;
	    if ( ($createtime + $timeend) < time() ){
			  $paytime = mysqld_select("SELECT * FROM ".table('shop_order'). " WHERE  status >= 1 and openid = :openid ORDER BY paytime desc", array(':openid'=>$member['openid']) );
			  if (( $paytime['paytime'] + $lastime) < time() ){
                   return false;
			  }
	    }
		if ( !empty($_SESSION[VIP_MOBILE_ACCOUNT]['openid']) ){
               $data = array('lastime'=>time());
			   mysqld_update('member', $data, array('openid'=>$_SESSION[VIP_MOBILE_ACCOUNT]['openid']));
		}
        return true;
    }
    return false;
}
function getloginfrom($param = "")
{
    return $_SESSION["mobile_login_fromurl"] . $param;
}

function getOpenShopId($openid = '')
{
    if(!empty($_SESSION['mobile_account']['openshop_id'])){
        return $_SESSION['mobile_account']['openshop_id'];
    }else{
        if(!empty($openid)){
            $shop = mysqld_select("select id from ". table('openshop') ." where openid=:openid",array(
                'openid' => $openid
            ));
            return $shop['id'];
        }
    }
}