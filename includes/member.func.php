<?php
/*
member操作
*/
function save_vip_member_login($mobile='', $openid ='' ){
     if (! empty($mobile)) {
        $member = mysqld_select("SELECT * FROM " . table('member') . " where mobile=:mobile limit 1", array(
            ':mobile' => $mobile
        ));
        if (! empty($member['openid'])) {
            $_SESSION[VIP_MOBILE_ACCOUNT] = $member;
            return $member['openid'];
        }
    }
    
    if (! empty($openid)) {
        $member = mysqld_select("SELECT * FROM " . table('member') . " where openid=:openid limit 1", array(
            ':openid' => $openid
        ));
        if (! empty($member['openid'])) {
            $_SESSION[VIP_MOBILE_ACCOUNT] = $member;
            return $member['openid'];
        }
    }
    return '';
}
function save_member_login($mobile = '', $openid = '')
{
    if (! empty($mobile)) {
        $member = mysqld_select("SELECT * FROM " . table('member') . " where mobile=:mobile limit 1", array(
            ':mobile' => $mobile
        ));
        if (! empty($member['openid'])) {
            $_SESSION[MOBILE_ACCOUNT] = $member;
            return $member['openid'];
        }
    }
    
    if (! empty($openid)) {
        $member = mysqld_select("SELECT * FROM " . table('member') . " where openid=:openid limit 1", array(
            ':openid' => $openid
        ));
        if (! empty($member['openid'])) {
            $_SESSION[MOBILE_ACCOUNT] = $member;
            return $member['openid'];
        }
    }
    return '';
}

function member_login_qq($qq_openid)
{
    if (! empty($qq_openid)) {
        $qq_fans = mysqld_select("SELECT * FROM " . table('qq_qqfans') . " WHERE qq_openid=:qq_openid ", array(
            ':qq_openid' => $qq_openid
        ));
        if (! empty($qq_fans['qq_openid'])) {
            $member = mysqld_select("SELECT * FROM " . table('member') . " where openid=:openid limit 1", array(
                ':openid' => $qq_fans['openid']
            ));
            if (! empty($member['openid'])) {
                $_SESSION[MOBILE_ACCOUNT] = $member;
            } else {
                clearloginfrom();
                header("location:" . create_url('mobile', array(
                    'name' => 'shopwap',
                    'do' => 'regedit',
                    'third_login' => 'true'
                )));
            }
        }
    }
}

function member_login_alipay($alipay_openid)
{
    if (! empty($weixin_openid)) {
        $alipay_alifans = mysqld_select("SELECT * FROM " . table('alipay_alifans') . " WHERE alipay_openid=:alipay_openid ", array(
            ':alipay_openid' => $alipay_openid
        ));
        if (! empty($alipay_alifans['openid'])) {
            $member = mysqld_select("SELECT * FROM " . table('member') . " where openid=:openid limit 1", array(
                ':openid' => $alipay_alifans['openid']
            ));
            if (! empty($member['openid'])) {
                $_SESSION[MOBILE_ACCOUNT] = $member;
            }
        }
    }
}

function member_login_weixin($weixin_openid)
{
    if (! empty($weixin_openid)) {
        $weixin_wxfans = mysqld_select("SELECT * FROM " . table('weixin_wxfans') . " WHERE weixin_openid=:weixin_openid ", array(
            ':weixin_openid' => $weixin_openid
        ));
        if (! empty($weixin_wxfans['openid'])) {
            $member = mysqld_select("SELECT * FROM " . table('member') . " where openid=:openid limit 1", array(
                ':openid' => $weixin_wxfans['openid']
            ));
            if (! empty($member['openid'])) {
                $_SESSION[MOBILE_ACCOUNT] = $member;
            }
        }
    }
}

function vip_member_login($mobile, $pwd)
{
    $member = mysqld_select("SELECT * FROM " . table('member') . " where mobile='{$mobile}' and parent_roler_id<>0  limit 1");
    
    if (!empty($member['openid'])) {
        if ($member['status'] != 1) {
            return - 1;
        }
        if ($member['pwd'] == md5($pwd)) {
            save_vip_member_login($mobile);
            return $member['openid'];
        }
    }
    return '';
}
function member_login($mobile, $pwd)
{
    $member = mysqld_select("SELECT * FROM " . table('member') . " where mobile=:mobile limit 1", array(
        ':mobile' => $mobile
    ));
    
    if (! empty($member['openid'])) {
        if ($member['status'] != 1) {
            return - 1;
        }
        if ($member['pwd'] == md5($pwd)) {
            save_member_login($mobile);
            return $member['openid'];
        }
    }
    return '';
}
function vip_member_logout(){
    unset($_SESSION["vip_mobile_login_fromurl"]);
    if (! empty($_SESSION[VIP_MOBILE_ACCOUNT])) {
        $openid = $_SESSION[VIP_MOBILE_ACCOUNT]['openid'];
        $weixinopenid = $_SESSION[VIP_MOBILE_SESSION_ACCOUNT]['openid'];
        if (! empty($openid) && ! empty($weixinopenid)) {
            mysqld_update('weixin_wxfans', array(
                'openid' => ''
            ), array(
                'openid' => $openid,
                'weixin_openid' => $weixinopenid
            ));
        }
        if (! empty($openid) && ! empty($weixinopenid)) {
            mysqld_update('alipay_alifans', array(
                'openid' => ''
            ), array(
                'openid' => $openid,
                'alipay_openid' => $weixinopenid
            ));
        }
        
        $openid = $_SESSION[VIP_MOBILE_ACCOUNT]['openid'];
        $qqopenid = "";
        if (! empty($_SESSION[MOBILE_QQ_OPENID])) {
            $qqopenid = $_SESSION[MOBILE_QQ_OPENID];
        } else {
            $qqopenid = $_SESSION[VIP_MOBILE_SESSION_ACCOUNT]['openid'];
        }
        
        if (! empty($openid) && ! empty($qqopenid)) {
            mysqld_update('qq_qqfans', array(
                'openid' => ''
            ), array(
                'openid' => $openid,
                'qq_openid' => $qqopenid
            ));
        }
    }
    
    unset($_SESSION[MOBILE_QQ_OPENID]);
    unset($_SESSION[VIP_MOBILE_ACCOUNT]);
    header("location:" . create_url('site', array(
        'name' => 'public',
		'do'=>'purchase'
    )));
    exit();

}
function member_logout()
{
    unset($_SESSION["mobile_login_fromurl"]);
    if (! empty($_SESSION[MOBILE_ACCOUNT])) {
        $openid = $_SESSION[MOBILE_ACCOUNT]['openid'];
        $weixinopenid = $_SESSION[MOBILE_SESSION_ACCOUNT]['openid'];
        if (! empty($openid) && ! empty($weixinopenid)) {
            mysqld_update('weixin_wxfans', array(
                'openid' => ''
            ), array(
                'openid' => $openid,
                'weixin_openid' => $weixinopenid
            ));
        }
        if (! empty($openid) && ! empty($weixinopenid)) {
            mysqld_update('alipay_alifans', array(
                'openid' => ''
            ), array(
                'openid' => $openid,
                'alipay_openid' => $weixinopenid
            ));
        }
        
        $openid = $_SESSION[MOBILE_ACCOUNT]['openid'];
        $qqopenid = "";
        if (! empty($_SESSION[MOBILE_QQ_OPENID])) {
            $qqopenid = $_SESSION[MOBILE_QQ_OPENID];
        } else {
            $qqopenid = $_SESSION[MOBILE_SESSION_ACCOUNT]['openid'];
        }
        
        if (! empty($openid) && ! empty($qqopenid)) {
            mysqld_update('qq_qqfans', array(
                'openid' => ''
            ), array(
                'openid' => $openid,
                'qq_openid' => $qqopenid
            ));
        }
    }
    
    unset($_SESSION[MOBILE_QQ_OPENID]);
    unset($_SESSION[MOBILE_ACCOUNT]);
    header("location:" . create_url('mobile', array(
        'name' => 'shopwap',
        'do' => 'index'
    )));
    exit();
}
function get_vip_member_account($useAccount = true, $mustlogin = false ){
    if (empty($_SESSION[VIP_MOBILE_ACCOUNT]) && $mustlogin) { 
		header("location:".create_url('site', array('name' => 'public','do' => 'purchase')));
    }
    if ($mustlogin == true) {
        return $_SESSION[VIP_MOBILE_ACCOUNT];
    }
    if (! empty($_SESSION[VIP_MOBILE_ACCOUNT])) {
        return $_SESSION[VIP_MOBILE_ACCOUNT];
    }
    return get_session_account($useAccount);
}
function get_member_account($useAccount = true, $mustlogin = false)
{
    if (extension_loaded('Memcached')) {
        $mcache = new Mcache();
    }
    if (empty($_SESSION[MOBILE_ACCOUNT]) && $mustlogin) {
        //如果是手机端请求
        if($_GET['name']=='api')
        {
            if (!extension_loaded('Memcached')) {
                return false;
            }
            $be_logout = $mcache->be_logout($_REQUEST['device_code']);
            if ($be_logout == 2) {
                $_SESSION[MOBILE_ACCOUNT] = NULL;
                return 3;
            }
            $mAccount = $mcache->get_msession($_REQUEST['device_code']);
            if (!empty($mAccount)) {
                return $mAccount;
            }else{
                return false;
            }
        }
        //非手机端请求，跳转到登陆页
        else{	
            header("location:" . create_url('mobile', array(
                    'name' => 'shopwap',
                    'do' => 'login'
            )));
        }
        
       
        exit();
    }

    if ($_GET['name']=='api' AND extension_loaded('Memcached')) {
        $be_logout = $mcache->be_logout($_REQUEST['device_code']);
        if ($be_logout == 2) {
            $_SESSION[MOBILE_ACCOUNT] = NULL;
            return 3;
        }
    }

    if ($mustlogin == true) {
        return $_SESSION[MOBILE_ACCOUNT];
    }
    
    if (! empty($_SESSION[MOBILE_ACCOUNT])) {
        return $_SESSION[MOBILE_ACCOUNT];
    }
    
    return get_session_account($useAccount);
}

function to_member_loginfromurl()
{   
	if (!empty($_SESSION["vip_mobile_login_fromurl"])){
        $fromurl = $_SESSION["vip_mobile_login_fromurl"];
        unset($_SESSION["vip_mobile_login_fromurl"]);
        return $fromurl;
	}
    if (!empty($_SESSION["mobile_login_fromurl"])) {
        $fromurl = $_SESSION["mobile_login_fromurl"];
        unset($_SESSION["mobile_login_fromurl"]);
        return $fromurl;
    } else {
		return create_url('mobile', array(
            'name' => 'shopwap',
            'do' => 'shopindex'
        ));
    }
}

function member_get($openid)
{
    $member = mysqld_select("SELECT * FROM " . table('member') . " where openid=:openid ", array(
        ':openid' => $openid
    ));
    
    return $member;
}

function member_credit($openid, $fee, $type, $remark)
{
    $member = member_get($openid);
    if (! empty($member['openid'])) {
        if (! is_numeric($fee) || $fee < 0) {
            message("输入数字非法，请重新输入");
        }
        if ($type == 'addcredit') {
            $data = array(
                'remark' => $remark,
                'type' => $type,
                'fee' => intval($fee),
                'account_fee' => $member['credit'] + $fee,
                'createtime' => TIMESTAMP,
                'openid' => $openid
            );
            mysqld_insert('member_paylog', $data);
            mysqld_update('member', array(
                'credit' => $member['credit'] + $fee,
                'experience' => $member['experience'] + $fee
            ), array(
                'openid' => $openid
            ));
            return true;
        }
        if ($type == 'usecredit') {
            if ($member['credit'] >= $fee) {
                $data = array(
                    'remark' => $remark,
                    'type' => $type,
                    'fee' => intval($fee),
                    'account_fee' => $member['credit'] - $fee,
                    'createtime' => TIMESTAMP,
                    'openid' => $openid
                );
                mysqld_insert('member_paylog', $data);
                mysqld_update('member', array(
                    'credit' => $member['credit'] - $fee
                ), array(
                    'openid' => $openid
                ));
                return true;
            }
        }
    }
    return false;
}

//该方法注意使用，因为资金不是退到用户上同时购买是，也不是扣该用户上的钱，都是通过微信或者支付宝第三方产生流动
function member_gold($openid, $fee, $type, $remark)
{
    $member = member_get($openid);
    if (! empty($member['openid'])) {
        if (! is_numeric($fee) || $fee < 0) {
            message("输入数字非法，请重新输入");
        }
        if ($type == 'addgold') {
            $data = array(
                'remark' => $remark,
                'type' => $type,
                'fee' => $fee,
                'account_fee' => $member['gold'] + $fee,
                'createtime' => TIMESTAMP,
                'openid' => $openid
            );
            mysqld_insert('member_paylog', $data);
            mysqld_update('member', array(
                'gold' => $member['gold'] + $fee
            ), array(
                'openid' => $openid
            ));
            return true;
        }
        if ($type == 'usegold') {
            if ($member['gold'] >= $fee) {
                $data = array(
                    'remark' => $remark,
                    'type' => $type,
                    'fee' => $fee,
                    'account_fee' => $member['gold'] - $fee,
                    'createtime' => TIMESTAMP,
                    'openid' => $openid
                );
                mysqld_insert('member_paylog', $data);
                mysqld_update('member', array(
                    'gold' => $member['gold'] - $fee
                ), array(
                    'openid' => $openid
                ));
                return true;
            }
        }
    }
    return false;
}

/**
 * @param $openid
 * @param $fee
 * @param $type
 * @param $remark
 * @param string $act_filed
 * @param string $updateGold
 * @return bool
 * @content 可能会操作冻结资金 freeze_gold  或者操作资金
 */
function member_goldinfo($openid, $fee, $type, $remark, $act_filed='gold',$updateGold = '')
{
    $member = member_get($openid);
    if (! empty($member['openid'])) {
        if (! is_numeric($fee) || $fee < 0) {
            message("输入数字非法，请重新输入");
        }
        if ($type == 'addgold') {
            $data = array(
                'remark' => $remark,
                'type' => $type,
                'fee' => $fee,
                'account_fee' => $member[$act_filed] + $fee,
                'createtime' => TIMESTAMP,
                'openid' => $openid
            );
            mysqld_insert('member_paylog', $data);
            if($updateGold){
                mysqld_update('member', array($act_filed => $member[$act_filed] + $fee), array('openid' => $openid));
            }

            return true;
        }
        if ($type == 'usegold') {
            if ($member[$act_filed] >= $fee) {
                $data = array(
                    'remark' => $remark,
                    'type' => $type,
                    'fee' => $fee,
                    'account_fee' => $member[$act_filed] - $fee,
                    'createtime' => TIMESTAMP,
                    'openid' => $openid
                );
                mysqld_insert('member_paylog', $data);
                if($updateGold){
                    mysqld_update('member', array($act_filed => $member[$act_filed] - $fee ), array('openid' => $openid));
                }

                return true;
            }
        }
    }
    return false;
}

/**
 * @param $openid
 * @param $name
 * @param $face
 * @return string
 * @content 有的评论来自后台录入的，有假的用户名和头像。
 */
function getUserFaceAndNameHtml($openid,$name,$isface = ''){
    $user= mysqld_select("select realname,avatar,mobile from ". table('member') ." where openid='{$openid}'");
    if(empty($name)){
        if(!empty($user['realname'])){
            $name = substr_cut($user['realname']);
        }else{
            $name = substr_cut($user['mobile']);
        }
    }else{
        $name = substr_cut($name);
    }

    if(empty($isface)){   //直接返回用户名
        return $name;
    }else{
        if(empty($user['avatar'])){  //返回头像和用户名
            $face = 'http://' . $_SERVER['HTTP_HOST']. "/themes/default/__RESOURCE__/recouse/images/userface.png";
        }else{
            $face = download_pic($user['avatar'],'40',40,1);
        }
        return "<img src='{$face}'/><p>{$name}</p>";
    }

}
//将用户名或者手机号进行处理，中间用星号表示
function substr_cut($str){
    //获取字符串长度
    $strlen = mb_strlen($str, 'utf-8');
    //如果字符创长度小于等于2
    if($strlen<=2){
        $firstStr = mb_substr($str, 0, 1, 'utf-8');
        $xing     = str_repeat("*",1);
        return $firstStr.$xing;
    }else{
        if(is_numeric($str)){
            //mb_substr — 获取字符串的部分
            $firstStr = mb_substr($str, 0, 2, 'utf-8');
            $lastStr = mb_substr($str, -2, 2, 'utf-8');
            $xing    = str_repeat("*",3);
        }else{
            //mb_substr — 获取字符串的部分
            $firstStr = mb_substr($str, 0, 1, 'utf-8');
            $lastStr = mb_substr($str, -1, 1, 'utf-8');
            $xing    = str_repeat("*",3);
        }
        return $firstStr.$xing.$lastStr;
    }
}

/**
 * @param $uid
 * @return string
 * @content 获取业务员分配的是谁
 */
function getAdminName($uid){
    $name = '';
    if(!empty($uid)){
        $users = mysqld_select("select username from ".table('user')." where id={$uid}");
        if(!empty($users))
            $name = $users['username'];
    }
    return $name;
}

/**
 * @param $uid
 * @content获取管理员被设置的角色是哪个
 */
function getAdminRolers($uid){
    $roler = '';
    if(!empty($uid)){
        $users = mysqld_select("select ro.name from ".table('rolers_relation')." as r left join ". table('rolers')." as ro on ro.id=r.rolers_id where r.uid={$uid}");
        if(!empty($users))
            $roler = $users['name'];
    }
    return $roler;
}

/**
 * 获取所有的业务员
 */
function getAllAgent(){
    //获取业务员角色
    $rolers = mysqld_select("select id from ".table('rolers')." where isdelete=0 and type=1");
    $users  = '';
    if(!empty($rolers)){
        $sql = "select u.id,u.username from ".table('rolers_relation')." as r left join ".table('user')." as u";
        $sql .= " on u.id=r.uid where r.rolers_id={$rolers['id']}";
        $users = mysqld_selectall($sql);
    }
    return $users;
}

/**
 * @return bool
 * 是否是一个业务员管理员
 */
function isAgentAdmin(){
    $amdin_uid = $_SESSION['account']['id'];
    $info = mysqld_select("select rolers_id from ".table('rolers_relation')." where uid={$amdin_uid}");
    if(empty($info)){  //说明还没分配过
        return false;
    }
    //获取业务员角色
    $rolers = mysqld_select("select id from ".table('rolers')." where isdelete=0 and type=1");
    if($rolers['id'] == $info['rolers_id']){
        //如果是该业务员
        return true;
    }else{
        //如果分配的不是业务员角色 则不算是业务员
        return false;
    }
}

/**
 * @param $str
 * @param $uid
 * @return string
 * 判断是否是自己所关联的渠道商，不是打星号不显示
 */
function isSelfAgent($str,$uid){
    $amdin_uid = $_SESSION['account']['id'];
    if(isAgentAdmin()){
        //是一个业余员
        if($amdin_uid != $uid){
            //并且关联的渠道商 不是自己的客户 打星号
            $str = substr_cut($str);
        }
    }
    return $str;
}