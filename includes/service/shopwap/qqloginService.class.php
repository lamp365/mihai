<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 17/7/15
 * Time: 上午10:43
 */
namespace service\shopwap;

class qqloginService extends \service\publicService
{
    //回调地址
    public $return_url;
    public $app_id;
    public $app_secret;
    public function __construct($config){

        //在腾讯那边如果回调域 不能带问号 直接去除问号 填写在腾讯那边
        $this->return_url = "http://".$_SERVER['HTTP_HOST']."/thirdlogin/callback.html?oauth=qq";
        $this->app_id     = $config['thirdlogin_qq_appid'];
        $this->app_secret = $config['thirdlogin_qq_appkey'];

    }
    //构造要请求的参数数组，无需改动
    public function login(){
        $state = $_SESSION['state'] = md5(time());
        $url = "https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id={$this->app_id}&redirect_uri={$this->return_url}&state={$state}";
        header("location: $url");
        exit;
    }

    public function respon(){
        if($_REQUEST['state'] == $_SESSION['state'])
        {
            $code = $_REQUEST["code"];
            //拼接URL
            $token_url = "https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&"
                . "client_id=" . $this->app_id . "&redirect_uri=" . urlencode($this->return_url)
                . "&client_secret=" . $this->app_secret . "&code=" . $code;

            $response = $this->get_contents($token_url);
            if (strpos($response, "callback") !== false)
            {
                $lpos = strpos($response, "(");
                $rpos = strrpos($response, ")");
                $response  = substr($response, $lpos + 1, $rpos - $lpos -1);
                $msg = json_decode($response);
                if (isset($msg->error))
                {
                    logRecord('QQ授权失败0','qq_login_error');
                    echo "<h3>error:</h3>" . $msg->error;
                    echo "<h3>msg  :</h3>" . $msg->error_description;
                    exit;
                }
            }

            //Step3：使用Access Token来获取用户的OpenID
            $params = array();
            parse_str($response, $params);

            $graph_url = "https://graph.qq.com/oauth2.0/me?access_token="
                .$params['access_token'];

            $str  = $this->get_contents($graph_url);
            if (strpos($str, "callback") !== false)
            {
                $lpos = strpos($str, "(");
                $rpos = strrpos($str, ")");
                $str  = substr($str, $lpos + 1, $rpos - $lpos -1);
            }
            $user = json_decode($str);
            if (isset($user->error))
            {
                logRecord('QQ授权失败1','qq_login_error');
                echo "<h3>error:</h3>" . $user->error;
                echo "<h3>msg  :</h3>" . $user->error_description;
                exit;
            }
            //获取到openid
            $openid = $user->openid;
            $_SESSION['state'] = null; // 验证SESSION
            return array(
                'openid'=>$openid,//支付宝用户号
                'oauth'=>'qq',
            );
        }else{
            return false;
        }
    }


    public function get_contents($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_URL, $url);
        $response =  curl_exec($ch);
        curl_close($ch);

        //-------请求为空
        if(empty($response)){
            logRecord('QQ授权失败2','qq_login_error');
            exit("登录失败:50001");
        }

        return $response;
    }

    public function thirdUserLogin($user_info)
    {
        $qq_openid = $user_info['openid'];
        if(empty($qq_openid)) {
            return '';
        }
        $qq_fans = mysqld_select("SELECT * FROM " . table('qq_qqfans') . " WHERE qq_openid=:qq_openid ", array(':qq_openid' =>$qq_openid));
        if(empty($qq_fans['qq_openid'])) {
            //插入
            $mem_openid  = date("YmdH",time()).rand(100,999);
            $hasmember   = mysqld_select("SELECT openid FROM " . table('member') . " WHERE openid = :openid ", array(':openid' => $mem_openid));
            if(isset($hasmember['openid']) && !empty($hasmember['openid'])) {
                $mem_openid = date("YmdH",time()).rand(100,999);
            }
            $row = array(
                'nickname'   => '会员'.mt_rand(100,999),
                'gender'     => 0,
                'qq_openid'  => $qq_openid,
                'openid'     => $mem_openid,
                'avatar'     => '',
                'createtime' => TIMESTAMP
            );
            $res = mysqld_insert('qq_qqfans', $row);
            if($res){
                //插入用户 member
                $mem_data = array(
                    'nickname'	      => $row['nickname'],
                    'realname'	      => $row['nickname'],
                    'avatar'	      => '',
                    'createtime'       => time(),
                    'status'           => 1,
                    'istemplate'       => 0,
                    'experience'       => 0 ,
                    'openid'           => $mem_openid,
                    'member_type'      => 1,
                );
                mysqld_insert('member',$mem_data);
                //注册送积分
                register_credit('',$mem_openid);
            }else{
                logRecord("登录失败，刷亲页面再试！",'qq_auth');
                message('登录失败，刷亲页面再试！');
                exit();
            }
        }else {
            $mem_data = member_get($qq_fans['openid']);
        }

        $old_member   = get_session_account();
        $oldsessionid = $old_member['openid'] ?: get_sessionid();
        integration_session_account($mem_data['openid'],$oldsessionid);

        //完成登录
        $_SESSION[MOBILE_ACCOUNT]       = $mem_data;
        $_SESSION[MOBILE_QQ_OPENID]     = $qq_openid;
        $sessionAccount = array(
            'openid'         => $mem_data['openid'],
            'qq_openid'      => $qq_openid,
            'unionid'        => ''
        );
        $_SESSION[MOBILE_SESSION_ACCOUNT] = $sessionAccount;
    }
}
