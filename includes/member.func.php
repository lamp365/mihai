<?php
/*
member操作
*/


function save_member_login($mobile = '', $openid = '')
{
    if (! empty($mobile)) {
        $member = mysqld_select("SELECT * FROM " . table('member') . " where mobile=:mobile limit 1", array(
            ':mobile' => $mobile
        ));
    }
    
    if (! empty($openid)) {
        $member = mysqld_select("SELECT * FROM " . table('member') . " where openid=:openid limit 1", array(
            ':openid' => $openid
        ));
    }

    //缓存用户信息
    $member_info = array();
    if (! empty($member['openid'])) {
        $_SESSION[MOBILE_ACCOUNT] = $member;
        //获取商铺信息  并缓存商铺的信息
        $member_info = set_store_logincache($member);

        //APP端将数据缓存一份到 memcache
        if ($_GET['name'] == 'api' && extension_loaded('Memcached')) {
            $mcache = new Mcache();
            // app登陆 初始化
            $app_key = $mcache->init_msession($_REQUEST['device_code'],$member_info);
            $member_info['app_key'] = $_SESSION[MOBILE_ACCOUNT]['app_key'] = $app_key;
        }
    }else{
        $_SESSION[MOBILE_ACCOUNT] = array();
    }

    return $member_info;
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
    $_SESSION = array();
    session_destroy();
    header("location:" . WEBSITE_ROOT);
    exit();
}

/**
 * 获取用户信息  最后返回空数组 或者返回用户数据
 * @param bool $create_weixin_account
 * @param bool $mustLogin   如果前端的 没有基类控制，需要判断后跳转登录的，加该参数为true
 * @return array|bool|int
 */
function get_member_account($create_weixin_account = true,$mustLogin = false)
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
        }else if($_GET['name']=='wapi'){  //小程序的
            $mAccount = $mcache->get($_REQUEST['device_code']);
            return $mAccount;
        } else{
            //非APP应用端请求  最后返回空数组 或者 weixin_openid
            $account_data = get_session_account($create_weixin_account);
            if(empty($account_data) && $mustLogin){
                tosaveloginfrom();
                header("location:".mobile_url('login'));
            }else{
                // 最后返回空数组 或者 weixin_openid
                return $account_data;
            }
        }
    }else{
        return $_SESSION[MOBILE_ACCOUNT];
    }
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
            'do' => 'index'
        ));
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

function member_store_getById($sts_id,$filed='*')
{
    if(empty($sts_id)){
        return array();
    }
    $store_info = mysqld_select("SELECT {$filed} FROM " . table('store_shop') . " where sts_id={$sts_id}");
    return $store_info;
}

/**
 * 获取用户的默认店铺  该方法用在登录的时候，登录后 就会缓存起来，所以之后需要获取用户信息，
 * 请使用以上方法  member_store_getById    从缓存中获取店铺id(store_sts_id)后丢进去
 * @param $member
 * @param string $filed
 * @return array|bool|mixed
 */
function member_store_get($member,$filed='*')
{
    if($member['member_type'] != 2) return array();
    //通过openid找到该用户当前默认的店铺id
    $default =  mysqld_select("select sts_id,is_admin from ".table('member_store_relation')." where openid='{$member['openid']}' and is_default=1 ");
    if(empty($default))  return array();

    $store_info = mysqld_select("SELECT {$filed} FROM " . table('store_shop') . " where sts_id={$default['sts_id']}");
    if(empty($store_info))  return array();
    $store_info['is_admin'] = $default['is_admin'];
    return $store_info;
}

/**
 * 返回用户的所有店铺  法人的所有店铺  或者 每个子用户能管理的所有店铺
 * @param $member
 * @param $is_admin   0或者1
 * @return array
 */
function member_allstore_get($member,$field='*',$is_admin = 1)
{
    if($is_admin == 1){
        $where = " and is_admin=1";
    }else{
        $where = " ";
    }
    if($member['member_type'] != 2) return array();
    $relation = mysqld_selectall("select sts_id from ".table('member_store_relation')." where openid='{$member['openid']}' {$where}");
    if(empty($relation))  return array();
    $store_info = array();
    foreach($relation as $item){
        $store_info[] = mysqld_select("SELECT {$field} FROM " . table('store_shop') . " where sts_id={$item['sts_id']}");
    }
    return $store_info;
}
/**
 * 设置店铺的缓存
 * @param $member
 */
function set_store_logincache($member,$store_info = array()){
    if(empty($store_info)){
        $store_info = member_store_get($member);
    }
    $member['store_sts_id']       = $_SESSION[MOBILE_ACCOUNT]['store_sts_id']   = $store_info['sts_id'];    //如果是空数组，则得到的store_sts_id为空，说明没有商铺
    $member['store_sts_name']     = $_SESSION[MOBILE_ACCOUNT]['store_sts_name'] = $store_info['sts_name'];
    $member['store_is_admin']     = $_SESSION[MOBILE_ACCOUNT]['store_is_admin'] = $store_info['is_admin'];
    $member['sts_category_p1_id'] = $_SESSION[MOBILE_ACCOUNT]['sts_category_p1_id'] = $store_info['sts_category_p1_id'];
    $member['sts_category_p2_id'] = $_SESSION[MOBILE_ACCOUNT]['sts_category_p2_id'] = $store_info['sts_category_p2_id'];
    return $member;
}

/**
 * 检验卖家的身份状态
 */
function checkSellerLoginStatus(){
    $memInfo    = get_member_account();
    //获取该用户的默认店铺
    $store_info = member_store_get($memInfo);
    if(empty($memInfo)){
        //判断用户是否有登录
        if( $_GET['name'] == 'api' ){
            //2表示 app需要跳转登录的标记
            ajaxReturnData(2,LANG('COMMON_PLEASE_LOGIN') );
        }else{
            message( LANG('COMMON_PLEASE_LOGIN'), WEBSITE_ROOT, 'error');
        }
    }else if($memInfo['member_type'] != 2 || empty($store_info)){
        //如果不是卖家 强行进来
        if( $_GET['name'] == 'api' ){
            ajaxReturnData(0,LANG('COMMON_NOTBE_SELLER') );
        }else{
            message( LANG('COMMON_NOTBE_SELLER'), WEBSITE_ROOT, 'error');
        }
    }
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
        $member = get_member_account();
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
 * @return int
 * @return int
 * 验证用户是否APP首次登录，并赠送积分
 * 
 */
function ifApp($openid=''){
    if ( !empty($openid) ){
        $member = mysqld_select("SELECT * FROM " . table('member') . " where ifapp = 0 and openid=:openid limit 1", array(
            ':openid' => $openid
        )); 
		if ($member){
			mysqld_update('member', array('ifapp'=>1), array('openid'=>$openid));
            member_credit($openid, 50, 'addcredit', '首次登陆APP积分赠送50');
		}
	}
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
        member_credit($openid,$shop_regcredit,"2",PayLogEnum::getLogTip('LOG_REGIST_JIFEN_TIP'));
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
                $name =  '小城市'.random(5);
                $face = '';
                $scan_openid = '';
            }else{
                $name =  $weixin['nickname'];
                $face =  $weixin['avatar'];
                $scan_openid = $weixin['scan_openid'];
            }
        }
    }else{
        $name =  '小城市'.random(5);
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

function update_member_info(){
    $member = get_member_account();
    if(empty($member))
        return $member;

    //获取用户的身份状态
    $type_info = member_get($member['openid'],'member_type');
    if($type_info['member_type'] != $member['member_type']){
        //说明有店铺审核成功了 重新设置缓存
        $member = save_member_login('',$member['openid']);
    }
    return $member;
}


