<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 17/7/15
 * Time: 上午11:45
 */
namespace service\shopwap;

class weixinloginService extends \service\publicService
{
    public $appid;
    public $secret;
    public $return_url;

    public function __construct($config){
        $this->appid  = $config['thirdlogin_weixin_appid'];
        $this->secret = $config['thirdlogin_weixin_appkey'];
        $this->return_url = "http://".$_SERVER['HTTP_HOST']."/thirdlogin/callback.html?oauth=weixin";

    }
    //构造要请求的参数数组，无需改动   snsapi_login 需要微信开放平台，开通该权限，才可以pc端扫码登录
    public function login(){
        $url = "https://open.weixin.qq.com/connect/qrconnect?appid={$this->appid}&redirect_uri=".urlencode($this->return_url)."&response_type=code&scope=snsapi_login&state=STATE#wechat_redirect";
        echo("<script> top.location.href='" . $url . "'</script>");
        exit;
    }

    public function respon(){
        //通过code换取 openi以及access_token
        $code = $_REQUEST['code'];
        $access_token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$this->appid.'&secret='.$this->secret.'&code='.$code.'&grant_type=authorization_code';
        if($code){
            $this->code = $_REQUEST['code'];
            $result = $this->get_wx_contents($access_token_url);

            $result = json_decode($result,true);
            $access_token = $result['access_token'];
            $openid       = $result['openid'];

            //全局的access_tiken
            $access_token = get_weixin_token();
            $user_info    = $this->GetUserInfo($access_token,$openid,1);
			return $user_info;
        }else{
            exit("No code");
        }
    }


    private function get_wx_contents($url){
        $ch = curl_init();
        $timeout = 5;
        curl_setopt ($ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $file_contents = curl_exec($ch);
        curl_close($ch);
        return $file_contents;
    }

    /**
     *
     * 通过access_token openid 从工作平台获取UserInfo
     * @return openid
     */
    public function GetUserInfo($access_token,$openid,$get_subscribe = 0)
    {
        // 获取用户 信息
        $urlObj["access_token"] = $access_token;
        $urlObj["openid"]       = $openid;
        $urlObj["lang"]          = 'zh_CN';
        $bizString = $this->ToUrlParams($urlObj);
        /**
         * sns/userinfo   与 cgi-bin/user/info  有区别
         * 当页面授权用的是 code换取 access_token那么，用sns/userinfo获取用户信息          得不到订阅状态
         * 当直接使用获取全局的access_token的话，那么要使用 cgi-bin/user/info 获取用户信息  会得到订阅状态
         */
        if($get_subscribe){
            $url     =  "https://api.weixin.qq.com/cgi-bin/user/info?".$bizString;
        }else{
            $url     =  "https://api.weixin.qq.com/sns/userinfo?".$bizString;
        }

        $content   = http_get($url);
        $data      = @json_decode($content, true);
        if (empty($data) || ! is_array($data) || empty($data['openid'])) {
            logRecord("获取微信公众号授权失败[无法取得info]",'weixin_auth');
            message('获取微信公众号授权失败[无法取得info], 请稍后重试');
            exit();
        }
        return $data;
    }
    /**
     *
     * 拼接签名字符串
     * @param array $urlObj
     *
     * @return 返回已经拼接好的字符串
     */
    private function ToUrlParams($urlObj)
    {
        $buff = "";
        foreach ($urlObj as $k => $v)
        {
            if($k != "sign"){
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");
        return $buff;
    }


    public function thirdUserLogin($userInfo)
    {
        $userInfo['weixin_openid'] = $userInfo['openid'];
        if(!empty($userInfo['unionid']))
            $info = mysqld_select("select * from ".table('weixin_wxfans')." where unionid='{$userInfo['unionid']}'");
        else
            $info = mysqld_select("select * from ".table('weixin_wxfans')." where weixin_openid='{$userInfo['weixin_openid']}'");

        if(empty($info)){
            //插入
            $mem_openid  = date("YmdH",time()).rand(100,999);
            $hasmember   = mysqld_select("SELECT openid FROM " . table('member') . " WHERE openid = :openid ", array(':openid' => $mem_openid));
            if(isset($hasmember['openid']) && !empty($hasmember['openid'])) {
                $mem_openid = date("YmdH",time()).rand(100,999);
            }
            $insert_data['openid'] = $mem_openid;
            $insert_data['weixin_openid'] = $userInfo['weixin_openid'];
            $insert_data['follow']        = $userInfo['subscribe'];
            $insert_data['nickname']      = $userInfo['nickname'];
            $insert_data['avatar']        = $userInfo['headimgurl'];
            $insert_data['gender']        = $userInfo['sex'];
            $insert_data['unionid']       = $userInfo['unionid'];
            $insert_data['createtime']    = time();
            //插入微信用户
            $res = mysqld_insert('weixin_wxfans',$insert_data);
            if($res){
                //插入用户 member
                $mem_data = array(
                    'nickname'	      => $userInfo['nickname'],
                    'realname'	      => $userInfo['nickname'],
                    'avatar'	      => $userInfo['headimgurl'],
                    'createtime'       => time(),
                    'status'           => 1,
                    'istemplate'       => 0,
                    'experience'       => 0 ,
                    'openid'           => $mem_openid,
                );
                mysqld_insert('member',$mem_data);
                //注册送积分
                register_credit('',$mem_openid);
            }else{
                logRecord("登录失败，刷亲页面再试！",'weixin_auth');
                message('登录失败，刷亲页面再试！');
                exit();
            }
        }else{
            $mem_data = member_get($info['openid']);
            $userInfo = $info;
        }

        $old_member   = get_session_account();
        $oldsessionid = $old_member['openid'] ?: get_sessionid();
        integration_session_account($mem_data['openid'],$oldsessionid);

        //完成登录
        $_SESSION[MOBILE_ACCOUNT]       = $mem_data;
        $_SESSION[MOBILE_WEIXIN_OPENID] = $userInfo['weixin_openid'];
        $sessionAccount = array(
            'openid'         => $mem_data['openid'],
            'weixin_openid'  => $userInfo['weixin_openid'],
            'unionid'        => $userInfo['unionid']
        );
        $_SESSION[MOBILE_SESSION_ACCOUNT] = $sessionAccount;
    }

}