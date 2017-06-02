<?php
/*
member操作
*/

function save_member_login($mobile = '', $openid = '')
{
    $member = array();
    if (! empty($mobile)) {
        $member = mysqld_select("SELECT * FROM " . table('member') . " where mobile=:mobile limit 1", array(
            ':mobile' => $mobile
        ));
        if (! empty($member['openid'])) {
            $_SESSION[MOBILE_ACCOUNT] = $member;
        }
    }
    
    if (! empty($openid)) {
        $member = mysqld_select("SELECT * FROM " . table('member') . " where openid=:openid limit 1", array(
            ':openid' => $openid
        ));
        if (! empty($member['openid'])) {
            $_SESSION[MOBILE_ACCOUNT] = $member;
        }
    }
    return $member;
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

function member_login($mobile, $pwd)
{
    $member = mysqld_select("SELECT * FROM " . table('member') . " where mobile=:mobile limit 1", array(
        ':mobile' => $mobile
    ));
    if (! empty($member['openid'])) {
        if ($member['status'] != 1) {
            //用户被禁用
            return - 1;
        }
        if ($member['pwd'] == encryptPassword($pwd)) {
            $memberInfo = save_member_login($mobile);
            return $memberInfo;
        }else{
            //密码有误
            return - 2;
        }
    }else{
        //用户不存在
        return -3;
    }
}

function member_logout()
{
    unset($_SESSION["mobile_login_fromurl"]);
    unset($_SESSION[MOBILE_QQ_OPENID]);
    unset($_SESSION[MOBILE_ACCOUNT]);
    header("location:" . WEBSITE_ROOT);
    exit();
}

/**
 * 获取用户信息  最后返回空数组 或者返回用户数据
 * @param bool $create_weixin_account
 * @return array|bool|int
 */
function get_member_account($create_weixin_account = true)
{
    if (extension_loaded('Memcached')) {
        $mcache = new Mcache();
    }
    if (empty($_SESSION[MOBILE_ACCOUNT])) {
        //如果是APP端请求
        if($_GET['name']=='api' && is_mobile_request())
        {
            if (!extension_loaded('Memcached')) {
                ajaxReturnData('0','Memcached未启动，请检查！');
            }
            $be_logout = $mcache->be_logout($_REQUEST['device_code']);
            if ($be_logout == 2) {
                $_SESSION[MOBILE_ACCOUNT] = NULL;
                ajaxReturnData('3','您已在其他设备登录！');
            }
            $mAccount = $mcache->get_msession($_REQUEST['device_code']);
            if (!empty($mAccount)) {
                return $mAccount;
            }else{
                return array();
            }
        } else{
            //非APP应用端请求  最后返回空数组 或者 weixin_openid
            return get_session_account($create_weixin_account);
        }
    }else{
        return $_SESSION[MOBILE_ACCOUNT];
    }
}

function to_member_loginfromurl()
{
    if (!empty($_SESSION["mobile_login_fromurl"])) {
        $fromurl = $_SESSION["mobile_login_fromurl"];
        unset($_SESSION["mobile_login_fromurl"]);
        return $fromurl;
    } else {
		return WEBSITE_ROOT;
    }
}

function member_get($openid,$filed='*')
{
    if(empty($openid)) return array();
    $member = mysqld_select("SELECT {$filed} FROM " . table('member') . " where openid=:openid ", array(
        ':openid' => $openid
    ));
    return $member;
}
//通过手机号获取用户信息
function member_get_bymobile($mobile='',$field='mobile,openid'){
    if(empty($mobile)) return array();
    $memberData = mysqld_select("SELECT {$field} FROM ".table('member')." where mobile = {$mobile} limit 1");
    return $memberData;
}

/**
 * 更新 credit和experience字段  只对积分 和经验。。积分添加多少，经验就添加多少。
 * 积分操作请用 member_credit()该方法    金额操作请用member_gold()  邀请好友操作用 member_invitegold()
 * 佣金操作请用 member_commisiongold()  免单返现操作请用member_freegold()
 * @param $openid
 * @param $fee
 * @param $type  addcredit    usecredit
 * @param $remark
 * @return bool
 */
function member_credit($openid, $fee, $type, $remark)
{
    $add_arr = array('addcredit');
    $use_arr = array('usecredit');
    $member = member_get($openid);
    if (! empty($member['openid'])) {
        if (! is_numeric($fee) || $fee <= 0) {
            //这里直接返回，不进行更新操作 不要弹出错误信息
           return false;
        }
        if (!in_array($type,$add_arr) && !in_array($type,$use_arr) ) {
            return false;
        }else if(in_array($type,$use_arr)){
            //积分为负
            $fee = -1*floor($fee);
        }
        $data = array(
            'remark' => $remark,
            'type'   => $type,
            'fee'    =>  $fee,
            'account_fee' => $member['credit'] + $fee,
            'createtime' => TIMESTAMP,
            'openid' => $openid
        );
        mysqld_insert('member_paylog', $data);
        $credit     = max(0,$member['credit']+$fee);
        $experience = max(0,$member['experience']+$fee);
        $update     = array('credit'=>$credit);
        if($fee > 0){
            //经验不扣除，一般兑换礼品只扣积分不扣经验
            $update['experience'] = $experience;
        }

        mysqld_update('member', $update, array(
            'openid' => $openid
        ));
        return true;
    }
    return false;
}

/**
 * 更新免单返现的字段  freeorder_gold
 * 只对 免单金额操作，  注意免单金额的使用，有一个免额过期时间，那么时间的判断是否可以使用免额，要在外部判断，正常是下单后判断免额没过期，订单总额扣除免额，再产生免额log
 * 积分操作请用 member_credit()该方法    金额操作请用member_gold()  邀请好友操作用 member_invitegold()
 * 佣金操作请用 member_commisiongold()  免单返现操作请用member_freegold()
 * @param $openid
 * @param $fee
 * @param $type  addgold or usegold
 * @param $remark
 * @return bool
 */
function member_freegold($openid, $fee, $type, $remark,$ordersn='')
{
    $add_arr = array('addgold');
    $use_arr = array('usegold');
    $member = member_get($openid);
    if (! empty($member['openid'])) {
        if (! is_numeric($fee) || $fee <= 0) {
           return false;
        }
        if (!in_array($type,$add_arr) && !in_array($type,$use_arr) ) {
            return false;
        }else if(in_array($type,$use_arr)){
            //金额为负
            $fee = -1*$fee;
        }
        $data = array(
            'remark' => $remark,
            'type' => $type,
            'fee' => $fee,
            'account_fee' => $member['freeorder_gold'] + $fee,
            'createtime' => TIMESTAMP,
            'openid'  => $openid,
            'ordersn' => $ordersn
        );
        //以免扣掉时为负数
        $gold  = max(0,$member['freeorder_gold'] + $fee);
        mysqld_insert('member_paylog', $data);
        mysqld_update('member', array('freeorder_gold' => $gold ), array(
            'openid' => $openid
        ));
        return true;
    }
    return false;
}
/**
 * 更新gold字段   只对金额操作
 * 积分操作请用 member_credit()该方法    金额操作请用member_gold()  邀请好友操作用 member_invitegold()
 * 佣金操作请用 member_commisiongold()  免单返现操作请用member_freegold()
 * @param $openid
 * @param $fee
 * @param $type     addgold  usegold
 * @param $remark
 * @param $update 请用bool型，true  or  false
 * 有些地方不一定要更新gold，只需要有记录。如下单后，钱是第三方的，但是会记录一个paylog,这时候不是扣除余额，不能进行更新
 * @return bool
 */
function member_gold($openid, $fee, $type, $remark,$update=true,$ordersn='')
{
    $add_arr = array('addgold');
    $use_arr = array('usegold');
    $member = member_get($openid);
    if (! empty($member['openid'])) {
        if (! is_numeric($fee) || $fee <= 0) {
            return false;
        }
        if (!in_array($type,$add_arr) && !in_array($type,$use_arr) ) {
            return false;
        }else if(in_array($type,$use_arr)){
            //金额为负
            $fee = -1*$fee;
        }
        $data = array(
            'remark' => $remark,
            'type' => $type,
            'fee' => $fee,
            'account_fee' => $member['gold'] + $fee,
            'createtime' => TIMESTAMP,
            'openid'  => $openid,
            'ordersn' => $ordersn,
        );
        $gold  = max(0,$member['gold'] + $fee);
        mysqld_insert('member_paylog', $data);
        if($update){
            mysqld_update('member', array( 'gold' => $gold), array(
                'openid' => $openid
            ));
        }
        return true;
    }
    return false;
}

/**
 * 更新 gold   只对佣金操作  会给卖家计算佣金
 * 积分操作请用 member_credit()该方法    金额操作请用member_gold()  邀请好友操作用 member_invitegold()
 * 佣金操作请用 member_commisiongold()  免单返现操作请用member_freegold()
 * @param $openid          卖家openid
 * @param $friend_openid   买家openid
 * @param $fee
 * @param $type  addgold_byorder or usegold_byorder
 * @param $remark
 * @return bool
 */
function member_commisiongold($openid, $friend_openid,$fee, $type, $ordersn='',$remark='')
{
    $add_arr = array('addgold_byorder');
    $use_arr = array('usegold_byorder');
    $member = member_get($openid);

    if(empty($remark)){
        $friend_member =  member_get($friend_openid);
        $name   = getNameByMemberInfo($friend_member);
        $remark = PayLogEnum::getLogTip('LOG_BUYORDER_TIP',$name);
    }

    if (! empty($member['openid'])) {
        if (! is_numeric($fee) || $fee <= 0) {
            return false;
        }
        if (!in_array($type,$add_arr) && !in_array($type,$use_arr) ) {
            return false;
        }else if(in_array($type,$use_arr)){
            //金额为负
            $fee = -1*$fee;
        }
        $data = array(
            'remark' => $remark,
            'type' => $type,
            'fee' => $fee,
            'account_fee'   => $member['gold'] + $fee,
            'createtime'    => TIMESTAMP,
            'openid'        => $openid,
            'friend_openid' => $friend_openid,
            'ordersn'       => $ordersn,
        );
        //以免扣掉时为负数
        $freeze_gold  = max(0,$member['gold'] + $fee);
        mysqld_insert('member_paylog', $data);
        mysqld_update('member', array( 'gold' => $freeze_gold), array(
            'openid' => $openid
        ));
        return true;
    }
    return false;
}

/**
 * 更新gold字段  只对邀请好友时操作
 * 积分操作请用 member_credit()该方法    金额操作请用member_gold()  邀请好友操作用 member_invitegold()
 * 佣金操作请用 member_commisiongold()  免单返现操作请用member_freegold()
 * @param $openid          邀请人openid
 * @param $friend_openid   当前被邀请openid
 * @param $fee
 * @param $type  addgold_byinvite  usegold_byinvite
 * @param $remark
 * @param $ordersn:订单编号,  可选
 * 
 * @return bool
 */
function member_invitegold($openid,$friend_openid, $fee, $type,$remark='',$ordersn='')
{
    $add_arr = array('addgold_byinvite');
    $use_arr = array('usegold_byinvite');
    $member  = member_get($openid);

    if(empty($remark)){
        $friend_member =  member_get($friend_openid);
        $name   = getNameByMemberInfo($friend_member);
        $remark = PayLogEnum::getLogTip('LOG_REGISTER_TIP',$name);
    }


    if (! empty($member['openid'])) {
        if (! is_numeric($fee) || $fee <= 0) {
            //这里直接返回，不进行更新操作 不要弹出错误信息
            return false;
        }
        if (!in_array($type,$add_arr) && !in_array($type,$use_arr) ) {
            return false;
        }else if(in_array($type,$use_arr)){
            //金额为负
            $fee = -1*$fee;
        }
        $data = array(
            'remark' => $remark,
            'type' => $type,
            'fee' => $fee,
            'account_fee'   => $member['gold'] + $fee,
            'createtime'    => TIMESTAMP,
            'openid'        => $openid,
            'friend_openid' => $friend_openid,
        	'ordersn'		=> $ordersn
        );
        //以免扣掉时为负数
        $gold  = max(0,$member['gold'] + $fee);
        mysqld_insert('member_paylog', $data);
        mysqld_update('member', array( 'gold' => $gold), array(
            'openid' => $openid
        ));
        return true;
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
function getUserFaceAndName($openid,$name,$face){
    if(empty($name)){
        //名字是空的 头像也一定是空，说明该用户是真实用户评论
        $user = mysqld_select("select realname,avatar,mobile from ". table('member') ." where openid='{$openid}'");
        if(!empty($user['realname'])){
            $name = substr_cut($user['realname']);
        }else{
            $name = substr_cut($user['mobile']);
        }
        if(empty($user['avatar'])){  //返回头像和用户名
            $face = WEBSITE_ROOT. "themes/default/__RESOURCE__/recouse/images/userface.png";
        }else{
            $face = download_pic($user['avatar'],'40',40,1);
        }
    }else{
        $name = substr_cut($name);
        if(empty($face)){
            $face = WEBSITE_ROOT. "themes/default/__RESOURCE__/recouse/images/userface.png";
        }else{
            $face = download_pic($face,'40',40,2);
        }
    }
    return array('face'=>$face,'username'=>$name);
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
 * @content 获取管理员名字
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

/**
 * @return int
 * @return int
 * 验证用户是否登录
 * get_member_account 该方法可以进行获取用户是否登录，但是很多时候会自动跳转到登录，
 * 一些场合，不需要跳转故再加一个方法
 */
function checkIsLogin(){
    //微信端 如果从 get_member_account 中获取 得到的openid就是weixin_openid
    //还不能判断就是登陆了
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {
        //微信端存的 key 是 MOBILE_SESSION_ACCOUNT  登录后存的key 是 MOBILE_ACCOUNT
        if (! empty($_SESSION[MOBILE_ACCOUNT])) {
            return $_SESSION[MOBILE_ACCOUNT]['openid'];
        }else{
            return '';
        }
    }else{
        //非微信端存的 可能有临时的 前面带有 _t
        $member = get_member_account(false);
        if(empty($member)){
            return '';
        }else{
            $openid     = $member['openid'];
            $openid_arr = explode('_t', $openid);
            if(count($openid_arr) == 2){
                //是临时用户
                return '';
            }else{
                return $openid;
            }
        }
    }
}



/**
 * 该方法少用，尽量使用以下
 * 积分操作请用 member_credit()该方法    金额操作请用member_gold()  邀请好友操作用 member_invitegold()
 * 佣金操作请用 member_commisiongold()  免单返现操作请用member_freegold()
 *
 * 如果使用，请小心type类型，type的值，参照以上五个方法，并且金额fee支出(use)  要记为负数
 * 同时还要注意有friend_openid这个字段，如佣金 与邀请的 就必须存值，故该方法要小心使用
 *
 * 记录用户账单的收支情况
 * @param $openid :用户ID
 * @param $fee:收支费用
 * @param $account_fee:用户账号剩余金额
 * @param unknown $type:收支类型
 * @param unknown $remark :收支备注
 * @param $ordersn:订单编号 ，可选
 */
function insertMemberPaylog($openid, $fee,$account_fee, $type, $remark,$ordersn='')
{
	$data = array('remark' 			=> $remark,
					'type' 			=> $type,
					'fee' 			=> $fee,
					'account_fee' 	=> $account_fee,
					'createtime' 	=> TIMESTAMP,
					'openid' 		=> $openid,
					'ordersn'		=> $ordersn
	);

	return mysqld_insert('member_paylog', $data);
}

/**
 * @content 获得用户的余额
 * @param $gold  余额
 * @param $free_gold  免单返现金额
 * @param $free_time  免单使用期限
 * @return mixed
 */
function getMemberBalance($gold,$free_gold,$free_time){
    if(time()>$free_time){
        return $gold;
    }else{
        $total = $gold+$free_gold;
        return $total;
    }
}

/**
 * 当有人注册的时候，分享者的觅友统计数要累加
 * @param $recommend_openid
 */
function recommend_frend_count($recommend_openid){
    if($recommend_openid){
        $sql = "update ".table('member')." set `friend_count`=friend_count+1 where openid={$recommend_openid}";
        mysqld_query($sql);
    }
}

/**
 * 注册送积分，这一块 提取出来，方便其他地方 注册 积分的分配一样，对于新老客户的处理一样
 * @param $mobile
 * @param $openid
 */
function register_credit($mobile,$openid){
    $cfg = globaSetting();
    $shop_regcredit=intval($cfg['shop_regcredit']);
    if(!empty($shop_regcredit))
    {
        member_credit($openid,$shop_regcredit,"addcredit",PayLogEnum::getLogTip('LOG_REGIST_JIFEN_TIP'));
    }
}

/**
 * pc 没有nickname  app没有realname  故获取名字，优先nickname 在获取realname  最后获取mobile mobile中间打星星
 * 可以扩展，比如后续需要微信名字   以后期望pc废弃掉realname，后台有查询的地方，都换掉 换为nickname
 * @param $member
 * @return string
 */
function getNameByMemberInfo($member){
    if(empty($member)){
        return '';
    }
    if(!empty($member['nickname']))
        $name = $member['nickname'];
    else if(!empty($member['realname']))
        $name = $member['realname'];
    else
        $name = substr_cut($member['mobile']);

    return $name;
}

/**
 * 获取微信的名字 以及头像 当注册的时候  不是微信进入的则随机返回nickname
 * @return string
 */
function get_weixininfo_from_regist(){
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {
        if(!empty( $_SESSION[MOBILE_SESSION_ACCOUNT]['unionid'])){
            $unionid =  $_SESSION[MOBILE_SESSION_ACCOUNT]['unionid'];
            $weixin  =  mysqld_select("select avatar,nickname,scan_openid from ".table('weixin_wxfans')." where unionid='{$unionid}'");
            if(empty($weixin)){
                $name =  '会员'.random(5);
                $face = '';
                $scan_openid = '';
            }else{
                $name =  $weixin['nickname'];
                $face =  $weixin['avatar'];
                $scan_openid = $weixin['scan_openid'];
            }
        }
    }else{
        $name =  '会员'.random(5);
        $face = '';
        $scan_openid = '';
    }
    return array('name'=>$name,'face'=>$face,'scan_openid'=>$scan_openid);
}

/**
 * @param $experience
 * @return array|bool|mixed
 */
function member_rank_model($experience)
{
    $rank = mysqld_select("SELECT * FROM " . table('rank_model')." where experience<='".intval($experience)."' order by rank_level desc limit 1 " );
    if(empty($rank))
    {
        // 扩展下一级需要
        $rank['rank_level']  = 1;
        $rank['rank_name']   = '普通会员';
        $rank['experience']  = 0;
        $rank['privile']     = '';
    }
    $rank = member_rank_next($rank);
    return $rank;
}
function member_rank_next($rank=array()){
    if ( empty( $rank ) or empty($rank['rank_level']) ){
        $rank['rank_level'] = 1;
    }
    $rank['rank_level']  = intval($rank['rank_level']);
    $rank['rank_next']  = mysqld_select('SELECT * FROM '.table('rank_model')." where rank_level > {$rank['rank_level']} order by rank_level asc limit 1");
    return $rank;
}


/** 用户加密
 * @param $length
 * @return  code
 */
function encryptPassword($password) {
    $result =  hash("sha256",md5($password));
    return $result;
}