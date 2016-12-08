<?php
/*
session操作
*/

function create_sessionid()
{
    return '_t' . date("mdHis") . rand(10000000, 99999999);
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
            ':loginid' => $loginid,
            ':goodsid' => $cartitem['goodsid'],
            ':optionid' => $cartitem['optionid']
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
                'total' => $t,
                'optionid' => $optionid
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
    mysqld_update('shop_order_paylog', array(
        'openid' => $loginid
    ), array(
        'openid' => $oldsessionid
    ));
    mysqld_update('member_paylog', array(
        'openid' => $loginid
    ), array(
        'openid' => $oldsessionid
    ));
    
    /*
     * 可能出现刷分情况，屏蔽
     * if($sessionmember['credit']>0)
     * {
     * member_credit($loginid,intval($sessionmember['credit']),'addcredit','登陆后账户合并所得积分');
     * }
     */
    if ($sessionmember['gold'] > 0) {
        member_gold($loginid, intval($sessionmember['gold']), 'addgold', '登录后与临时账户合并所得余额');
    }
    
    mysqld_delete('member', array(
        'openid' => $oldsessionid
    ));
    $alipaythirdlogin = mysqld_select("SELECT * FROM " . table('thirdlogin') . " WHERE enabled=1 and `code`='alipay'");
    if (! empty($alipaythirdlogin) && ! empty($alipaythirdlogin['id'])) {
        $alipayfans = mysqld_select("SELECT * FROM " . table('alipay_alifans') . " WHERE alipay_openid=:alipay_openid ", array(
            ':alipay_openid' => $oldsessionid
        ));
        if (! empty($alipayfans['alipay_openid'])) {
            mysqld_update('alipay_alifans', array(
                'openid' => $loginid
            ), array(
                'alipay_openid' => $oldsessionid
            ));
        }
    }
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
        $weixinthirdlogin = mysqld_select("SELECT * FROM " . table('thirdlogin') . " WHERE enabled=1 and `code`='weixin'");
        if (! empty($weixinthirdlogin) && ! empty($weixinthirdlogin['id'])) {
            $weixinfans = mysqld_select("SELECT * FROM " . table('weixin_wxfans') . " WHERE weixin_openid=:weixin_openid ", array(
                ':weixin_openid' => $oldsessionid
            ));
            if (! empty($weixinfans['weixin_openid'])) {
                mysqld_update('weixin_wxfans', array(
                    'openid' => $loginid
                ), array(
                    'weixin_openid' => $oldsessionid
                ));
            }
        }
    }
    
    if (! empty($_SESSION[MOBILE_QQ_OPENID])) {
        $qqlogin = mysqld_select("SELECT * FROM " . table('thirdlogin') . " WHERE enabled=1 and `code`='qq'");
        if (! empty($qqlogin) && ! empty($qqlogin['id'])) {
            $qqfans = mysqld_select("SELECT * FROM " . table('qq_qqfans') . " WHERE qq_openid=:qq_openid", array(
                ':qq_openid' => $_SESSION[MOBILE_QQ_OPENID]
            ));
            
            if (! empty($qqfans['qq_openid'])) {
                mysqld_update('qq_qqfans', array(
                    'openid' => $loginid
                ), array(
                    'qq_openid' => $_SESSION[MOBILE_QQ_OPENID]
                ));
            }
        }
    }
    
    // unset($_SESSION[MOBILE_SESSION_ACCOUNT]);
}
function get_vip_session_account($useAccount = true, $mess_id = 0)
{
    $sessionAccount = "";
    if (! empty($_SESSION[VIP_MOBILE_SESSION_ACCOUNT]) && ! empty($_SESSION[VIP_MOBILE_SESSION_ACCOUNT]['openid'])) {
        $sessionAccount = $_SESSION[VIP_MOBILE_SESSION_ACCOUNT];
    } else {
        $sessionAccount = array(
            'openid' => create_sessionid()
        );
        $_SESSION[VIP_MOBILE_SESSION_ACCOUNT] = $sessionAccount;
    }
    
    if ($useAccount && ! empty($sessionAccount)) {
        $member = mysqld_select("SELECT * FROM " . table('member') . " where openid=:openid and istemplate=1 ", array(
            ':openid' => $sessionAccount['openid']
        ));
        if (empty($member['openid'])) {
            $data = array(
                'mobile' => "",
                'pwd' => md5(rand(10000, 99999)),
                'createtime' => time(),
                'status' => 1,
                'mess_id'=>$mess_id,
                'istemplate' => 1,
                'experience' => 0,
                'openid' => $sessionAccount['openid']
            );
            mysqld_insert('member', $data);
        }
    }
    return $sessionAccount;
}
function get_session_account($useAccount = true, $mess_id = 0)
{
    $sessionAccount = "";
    if (! empty($_SESSION[MOBILE_SESSION_ACCOUNT]) && ! empty($_SESSION[MOBILE_SESSION_ACCOUNT]['openid'])) {
        $sessionAccount = $_SESSION[MOBILE_SESSION_ACCOUNT];
    } else {
        $sessionAccount = array(
            'openid' => create_sessionid()
        );
        $_SESSION[MOBILE_SESSION_ACCOUNT] = $sessionAccount;
    }
    
    if ($useAccount && ! empty($sessionAccount)) {
        $member = mysqld_select("SELECT * FROM " . table('member') . " where openid=:openid and istemplate=1 ", array(
            ':openid' => $sessionAccount['openid']
        ));
        if (empty($member['openid'])) {
            $data = array(
                'mobile' => "",
                'pwd' => md5(rand(10000, 99999)),
                'createtime' => time(),
                'status' => 1,
                'mess_id'=>$mess_id,
                'istemplate' => 1,
                'experience' => 0,
                'openid' => $sessionAccount['openid']
            );
            mysqld_insert('member', $data);
        }
    }
    return $sessionAccount;
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
    if (! empty($_SESSION[VIP_MOBILE_ACCOUNT])) {
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