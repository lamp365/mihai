<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/4/20
 * Time: 18:29
 * demo
 * service 层 用于简化 我们的控制器，让控制器尽量再 简洁
 * 把一些业务提取出来，放在service层中去操作
$a = new \service\shopwap\loginService();
if($a->todo()){
    //操作成功 则继续业务
}else{
    message($a->getError());
}
 */
namespace service\shopwap;

class loginService extends \service\publicService
{
    public $error_location = 0;

    public function getErrorLocation()
    {
        return $this->error_location;
    }

    /*
     * 注册发送短信
     */
    public function do_regeditSms($telephone,$existMemberValidate=1)
    {
        //ppd($_SESSION);
        if (isset( $_SESSION['VerifyCode']['sms_code_expired']) && time()< $_SESSION['VerifyCode']['sms_code_expired'] ) {
            $this->error = LANG('COMMON_SMS_IS_ALREADY_SEND');
            $this->error_location = 1;//对应第一处输入框
            return false;
        }
       
        if(strlen($telephone) != 11 || !is_numeric($telephone)){
            $this->error = LANG('COMMON_PHONE_ERROR');
            $this->error_location = 1;//对应第一处输入框
            return false;
        }
        if( $existMemberValidate == 1){
            //验证手机号是否已经存在过
            $member = mysqld_select("select openid from ".table('member')." where mobile='{$telephone}'");
            if($member){
                $this->error = LANG('COMMON_PHONE_EXIST');
                $this->error_location = 1;//对应第一处输入框
                return false;
            }
        }
        
        date_default_timezone_set('Asia/Shanghai');
        if($existMemberValidate == 2){
            //修改密码的
            $code = set_sms_code($telephone,0,1);
        }else{
            //注册的
            $code = set_sms_code($telephone);
        }

        if($code){
            $_SESSION['VerifyCode'] 		 = array();//不设置会报错
            $_SESSION['VerifyCode'][$telephone] 		 = $code;
            $_SESSION['VerifyCode']['sms_code_expired'] = time()+120;		//短信的有效期,120s
            return true;
        }else{
            //发送失败
            $this->error = LANG('COMMON_SMS_SEND_FAIL');
            return true;
        }
    }

    /**
     * 提交表单时 进行注册 先检测数据验证 验证码
     * @param $_GP
     * @return bool
     */
    public function do_checksignin($_GP)
    {
        if(strlen($_GP['mobile']) != 11 || !is_numeric($_GP['mobile'])){
            $this->error = LANG('COMMON_PHONE_ERROR');
            $this->error_location = 1;
            return false;
        }

        //密码不为空
        if(empty($_GP['pwd'])){
            $this->error = LANG('COMMON_PWD_NOTNULL');
            $this->error_location = 2;
            return false;
        }
        //密码不一致
        if(isset($_GP['repwd']) && $_GP['pwd'] != $_GP['repwd']){
            $this->error = LANG('COMMON_PWD_NOTSAME');
            $this->error_location = 2;
            return false;
        }

        if(strtolower($_SESSION["VerifyCode"][$_GP['mobile']]) == strtolower($_GP['mobilecode'])) {
            unset($_SESSION["VerifyCode"]);
        }else{
            //验证码有误
            $this->error = LANG('COMMON_PHONECODE_ERROR');
            return false;
        }

        //验证手机号是否已经存在过
        $member = mysqld_select("select openid from ".table('member')." where mobile='{$_GP['mobile']}'");
        if(!empty($member['openid'])) {
            $this->error_location = 1;
            $this->error = LANG('COMMON_PHONE_EXIST');
            return false;
        }

        return true;

    }
    
    public function validateVerifyCode($phone,$code){
        
        if(strtolower($_SESSION["VerifyCode"]['sms_code_expired']) <  time() ) {
           $this->error = LANG('COMMON_PHONECODE_TIMEOOUT');
            return false;
        }    
            
        if(strtolower($_SESSION["VerifyCode"][$phone]) == strtolower($code)) {
            unset($_SESSION["VerifyCode"]);
        }else{
            //验证码有误
            $this->error = LANG('COMMON_PHONECODE_ERROR');
            return false;
        }
        return true;
    }
    
     /**
     * 提交表单时 进行注册 先检测数据验证 验证码
     * @param $_GP =array()   mobile  mobilecode pwd
     * @return bool
     */
    public function resetPasswordByPhone($_GP,$needVerify=1){
        $loginService = new \service\shopwap\loginService();

        $res = $loginService->validateVerifyCode($_GP['mobile'],$_GP['mobilecode']);
        if( !$res ){
            $this->error = '验证码有误';
            return false;
        }
        
        //更新member表
        $effect = mysqld_update("member",array('pwd'=>encryptPassword(trim($_GP['pwd']))),array('mobile'=>$_GP['mobile']));
        //更新user表（可选）
        mysqld_update("user",array('password'=>encryptPassword(trim($_GP['pwd']))),array('mobile'=>$_GP['mobile']));
        
        return $effect;
    }

    //开始注册
    public function do_signin($_GP,$needlogin=1)
    {
        if(empty($_GP['third_login'])) {
            $pwd = encryptPassword($_GP['pwd']);
        } else{
            //第三方注册
            $pwd = '';
        }
        $openid    = date("YmdH",time()).rand(100,999);
        $hasmember = mysqld_select("SELECT * FROM " . table('member') . " WHERE openid = :openid ", array(':openid' => $openid));
        if(isset($hasmember['openid']) && !empty($hasmember['openid']))
        {
            $openid=date("YmdH",time()).rand(100,999);
        }

        $wx_info          = get_weixininfo_from_regist();
        $recommend_openid = $wx_info['scan_openid'];
        $data = array(
            'mobile' 		  => $_GP['mobile'],
            'pwd'    		  => $pwd,
            'nickname'	      => $_GP['nickname']!=''?$_GP['nickname']:$wx_info['name'],
            'realname'	      => $wx_info['name'],
            'avatar'	      => $_GP['avatar']!=''?$_GP['avatar']:$wx_info['face'],
            'createtime'       => time(),
            'status'           => 1,
            'istemplate'       => 0,
            'experience'       => 0 ,
            'openid'           => $openid,
            'member_type'      => 1,
        );
        
        $result = mysqld_insert('member', $data);//表没有自增id,所以不能用insert_get id

        if($result){
            //注册送积分
            register_credit($_GP['mobile'],$openid);
            //觅友数 加1
            recommend_frend_count($recommend_openid);

            if($needlogin){
                $member  = get_session_account();
                $oldsessionid = $member['openid'] ?: get_sessionid();              //旧的openid
                $res_data     = save_member_login('',$openid);  //当前openid
                $loginid      = $res_data['openid'];
                integration_session_account($loginid,$oldsessionid);
            }
            return $data;
        }else{
            //注册失败
            $this->error = LANG('COMMON_SIGNIN_FAIL');
            return false;
        }
    }


    //登录操作
    public function do_login($_GP)
    {
        if (strlen($_GP['mobile']) != 11 && !is_numeric($_GP['mobile'])) {
           $this->error = LANG('COMMON_PHONE_ERROR');
           $this->error_location = 1;
            return false;
        }
        //密码不为空
        if(empty($_GP['pwd'])){
            $this->error = LANG('COMMON_PWD_NOTNULL');
            $this->error_location = 2;
            return false;
        }
        $member = get_session_account();
        //登录该用户
        $info   = member_login($_GP['mobile'], $_GP['pwd']);
        if ($info == - 1) {
            //账号被警用
            $this->error = LANG('COMMON_USER_FORBIDEN');
            $this->error_location = 1;
            return false;
        }else if ($info == - 2) {
            //密码有误
            $this->error = LANG('COMMON_USER_PWD_ERROR');
            $this->error_location =2;
            return false;
        }else if ($info == - 3) {
            //该用户不存在
            $this->error = LANG('COMMON_USER_NOT_EXIST');
            $this->error_location =1;
            return false;
        }

        $oldsessionid = $member['openid'] ?: get_sessionid();              //旧的openid(要门微信openid要么会话id)
        $loginid      = $info['openid'] ?: '';  //登录后的openid
        integration_session_account( $loginid, $oldsessionid);
        return $info;
    }

    /**
     * 获取登录后用户的 商铺信息 给APP作为本地缓存
     * @param $member_info
     * @return array
     */
    public function getStoreData(&$member_info)
    {
        if(empty($member_info['store_sts_id'])){
            //一个店铺都没有，查看是否有正在申请的店铺
            $store_info  = mysqld_select("select * from ".table('store_shop_apply')." where sts_openid={$member_info['openid']}");
            $store_id    = intval($store_info['sts_id']);
            $store_identity = mysqld_select("select * from ".table('store_shop_identity_apply')." where ssi_id={$store_id}");
            $member_info['store_sts_id'] = $store_info['sts_id'];
            $member_info['store_sts_name'] = $store_info['sts_name'];
            $member_info['store_sts_id'] = $store_info['sts_id'];
            $member_info['sts_category_p1_id'] = $store_info['sts_category_p1_id'];
            $member_info['sts_category_p2_id'] = $store_info['sts_category_p2_id'];
            if($member_info['openid'] == $store_info['sts_openid'])
                $member_info['store_is_admin'] = 1;
            else
                $member_info['store_is_admin'] = 0;
        }else{
            $store_info  = member_store_getById($member_info['store_sts_id']);
            $store_id    = intval($store_info['sts_id']);
            $store_identity = mysqld_select("select * from ".table('store_shop_identity')." where ssi_id={$store_id}");
        }
        //获取法人电话
        $admin_mobile = '';
        if($member_info['store_is_admin']){
            $admin_mobile = $member_info['mobile'];
        }else{
            if(!empty($store_info)){
                $memAdmin     = member_get($store_info['sts_openid'],'mobile');
                $admin_mobile = $memAdmin['mobile'];
            }
        }

        if(!empty($store_info)){
            $store_info['admin_mobile'] = $admin_mobile;
            //返回 行业的中文给app
            $industry1 = mysqld_select("select gc_name from ".table('industry')." where gc_id={$store_info['sts_category_p1_id']}");
            $industry2 = mysqld_select("select gc_name from ".table('industry')." where gc_id={$store_info['sts_category_p2_id']}");
            $store_info['sts_category_p1_id_text'] = $industry1['gc_name'];
            $store_info['sts_category_p2_id_text'] = $industry2['gc_name'];

            //返回城市 省  市 的中文给app   配送范围
            $sts_province = mysqld_select("select region_name from ".table('region')." where region_code={$store_info['sts_province']}");
            $sts_city     = mysqld_select("select region_name from ".table('region')." where region_code={$store_info['sts_city']}");
            $sts_region   = mysqld_select("select region_name from ".table('region')." where region_code={$store_info['sts_region']}");
            $store_info['sts_province_text'] = $sts_province['region_name'];
            $store_info['sts_city_text']     = $sts_city['region_name'];
            $store_info['sts_region_text']   = $sts_region['region_name'];

            //返回城市 省  市 的中文给app  商家位置
            $sts_locate_add_1 = mysqld_select("select region_name from ".table('region')." where region_code={$store_info['sts_locate_add_1']}");
            $sts_locate_add_2 = mysqld_select("select region_name from ".table('region')." where region_code={$store_info['sts_locate_add_2']}");
            $sts_locate_add_3 = mysqld_select("select region_name from ".table('region')." where region_code={$store_info['sts_locate_add_3']}");
            $store_info['sts_locate_add_1_text'] = $sts_locate_add_1['region_name'];
            $store_info['sts_locate_add_2_text'] = $sts_locate_add_2['region_name'];
            $store_info['sts_locate_add_3_text'] = $sts_locate_add_3['region_name'];
        }else{
            $store_info = new \stdClass();
        }

        if(!empty($store_identity)){
            //身份证解密
            $store_identity['ssi_owner_shenfenhao'] = empty($store_identity['ssi_owner_shenfenhao']) ? '' : cbd_decrypt($store_identity['ssi_owner_shenfenhao'],$member_info['openid']);
        }else{
            $store_identity = new \stdClass();
        }

        return array(
            'store_info'     => $store_info,
            'store_identity' => $store_identity,
        );
    }
}