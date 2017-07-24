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
        //临时的用户不要注册 因为 第二天找不到这个临时用户，但是微信不一样，微信openid是唯一的
        //临时的用户注册没有一点意义
        $sessionAccount = array(
            'openid' => get_sessionid(),
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


function getTopOpenID()
{
    return $_SESSION[MOBILE_ACCOUNT]['top_open_id']?$_SESSION[MOBILE_ACCOUNT]['top_open_id']:$_SESSION[MOBILE_ACCOUNT]['openid'];
}

function getloginfrom($param = "")
{
    return $_SESSION["mobile_login_fromurl"] . $param;
}
