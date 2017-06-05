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
    /*
     * 注册发送短信
     */
    public function do_regeditSms($telephone)
    {
        //ppd($_SESSION);
        if (isset( $_SESSION['VerifyCode']['sms_code_expired']) && time()< $_SESSION['VerifyCode']['sms_code_expired'] ) {
            $this->error = '请不要频繁发送短信！';
            $this->error_location = 1;//对应第一处输入框
            return false;
        }
       
        if(strlen($telephone) != 11 || !is_numeric($telephone)){
            $this->error = '手机号码格式有误！';
            $this->error_location = 1;//对应第一处输入框
            return false;
        }

        //验证手机号是否已经存在过
        $member = mysqld_select("select openid from ".table('member')." where mobile='{$telephone}'");
        if($member){
            $this->error = '手机号已经存在！';
            $this->error_location = 1;//对应第一处输入框
            return false;
        }

        
        date_default_timezone_set('Asia/Shanghai');
        $code = set_sms_code($telephone);
        if($code){
            $_SESSION['VerifyCode'] 		 = array();//不设置会报错
            $_SESSION['VerifyCode'][$telephone] 		 = $code;
            $_SESSION['VerifyCode']['sms_code_expired'] = time()+120;		//短信的有效期,120s
            return true;
        }else{
            //发送失败
            $this->error = '发送失败！';
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
            $this->error = '手机号格式有误！';
            $this->error_location = 1;
            return false;
        }

        //密码不为空
        if(empty($_GP['pwd'])){
            $this->error = '密码不为空！';
            $this->error_location = 2;
            return false;
        }
        //密码不一致
        if(isset($_GP['repwd']) && $_GP['pwd'] != $_GP['repwd']){
            $this->error = '密码不一致！';
            $this->error_location = 2;
            return false;
        }

        if(strtolower($_SESSION["VerifyCode"][$_GP['mobile']]) == strtolower($_GP['mobilecode'])) {
            unset($_SESSION["VerifyCode"]);
        }else{
            //验证码有误
            $this->error = '验证码有误！';
            return false;
        }

        //验证手机号是否已经存在过
        $member = mysqld_select("select openid from ".table('member')." where mobile='{$_GP['mobile']}'");
        if(!empty($member['openid'])) {
            $this->error_location = 1;
            $this->error = '手机号已经存在！';
            return false;
        }

        return true;

    }
    
    public function validateVerifyCode($phone,$code){
        
        if(strtolower($_SESSION["VerifyCode"]['sms_code_expired']) <  time() ) {
           $this->error = '验证码已经过期！';
            return false;
        }    
            
        if(strtolower($_SESSION["VerifyCode"][$phone]) == strtolower($code)) {
            unset($_SESSION["VerifyCode"]);
        }else{
            //验证码有误
            $this->error = '验证码有误！';
            return false;
        }
        return true;
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
            'nickname'	      => $wx_info['name'],
            'realname'	      => $wx_info['name'],
            'avatar'	      => $wx_info['face'],
            'createtime'       => time(),
            'status'           => 1,
            'istemplate'       => 0,
            'experience'       => 0 ,
            'openid'           => $openid,
        );
        
        $result = mysqld_insert('member', $data);//表没有自增id,所以不能用insert_get id

        if($result){
            //注册送积分
            register_credit($_GP['mobile'],$openid);
            //觅友数 加1
            recommend_frend_count($recommend_openid);

            if($needlogin){
                $member  = get_session_account();
                $oldsessionid = $member['openid'] ?: session_id();              //旧的openid
                $unionid      = $member['unionid'];             //用于绑定微信用户
                $res_data     = save_member_login('',$openid);  //当前openid
                $loginid      = $res_data['openid'];
                integration_session_account($loginid,$oldsessionid, $unionid);
            }
            return $data;
        }else{
            //注册失败
            $this->error = '注册失败！';
            return false;
        }
    }


    //登录操作
    public function do_login($_GP)
    {
        if (strlen($_GP['mobile']) != 11 && !is_numeric($_GP['mobile'])) {
           $this->error = '手机格式不对！';
           $this->error_location = 1;
            return false;
        }
        //密码不为空
        if(empty($_GP['pwd'])){
            $this->error = '密码不能为空！';
            $this->error_location = 2;
            return false;
        }
        $member = get_session_account();
        //登录该用户
        $info   = member_login($_GP['mobile'], $_GP['pwd']);
        if ($info == - 1) {
            //账号被警用
            $this->error = '账号被禁用！';
            return false;
        }else if ($info == - 2) {
            //密码有误
            $this->error = '密码有误！';
            return false;
        }else if ($info == - 3) {
            //该用户不存在
            $this->error = '该用户不存在！';
            return false;
        }

        $oldsessionid = $member['openid'] ?: session_id();              //旧的openid
        $unionid      = $member['unionid']; //用于微信绑定
        $loginid      = $info['openid'] ?: '';  //登录后的openid
        integration_session_account( $loginid, $oldsessionid,$unionid);
        return $info;
    }
}