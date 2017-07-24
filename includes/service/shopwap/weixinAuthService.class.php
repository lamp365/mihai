<?php

namespace service\shopwap;


class weixinAuthService extends \service\publicService
{
    private $appid   = '';
    private $secret  = '';

    public function __construct()
    {
        parent::__construct();
        if(strstr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')){
            $weixinthirdlogin = mysqld_select("SELECT * FROM " . table('thirdlogin') . " WHERE enabled=1 and `code`='weixin'");
            if(!empty($weixinthirdlogin['id'])){
                $settings       = globaSetting();
                $this->appid    = $settings['weixin_appId'];
                $this->secret   = $settings['weixin_appSecret'];
            }

        }
    }


    // 网页授权登录获取 OpendId
    public function GetOpenid()
    {
        if(strstr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger') == false || $this->appid == ''){
            return '';
        }
        if(!empty($_SESSION[MOBILE_WEIXIN_OPENID]) || !empty($_SESSION[MOBILE_SESSION_ACCOUNT]))
            return  $_SESSION[MOBILE_WEIXIN_OPENID];
        //通过code获得openid
        if (!isset($_GET['code'])){
            //触发微信返回code码
            if(is_https()){
                $baseUrl = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            }else{
                $baseUrl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            }

            $baseUrl = urlencode($baseUrl);
            $url = $this->__CreateOauthUrlForCode($baseUrl); // 获取 code地址
            Header("Location: $url"); // 跳转到微信授权页面 需要用户确认登录的页面
            exit();
        } else {
            //上面获取到code后这里跳转回来
            $code         = $_GET['code'];
            $data         = $this->__CreateOauthUrlForOpenid($code);//获取网页授权access_token和用户openid
            //全局的access_tiken
            $access_token = get_weixin_token();
            $userInfo = $this->GetUserInfo($access_token,$data['openid'],1);//获取微信用户信息
            $this->weixinUserLogin($userInfo);
            return $userInfo['openid'];
        }
    }

    /**
     *
     * 构造获取code的url连接
     * @param string $redirectUrl 微信服务器回跳的url，需要url编码
     *
     * @return 返回构造好的url
     */
    private function __CreateOauthUrlForCode($redirectUrl)
    {
        $urlObj["appid"]         = $this->appid;
        $urlObj["redirect_uri"]  = $redirectUrl;
        $urlObj["response_type"] = "code";
        $urlObj["scope"] = "snsapi_base";  //静默授权
//        $urlObj["scope"] = "snsapi_userinfo";
        $urlObj["state"] = "STATE"."#wechat_redirect";
        $bizString = $this->ToUrlParams($urlObj);
        return "https://open.weixin.qq.com/connect/oauth2/authorize?".$bizString;
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


    /**
     *
     * 构造获取open和access_toke的url地址
     * @param string $code，微信跳转带回的code
     * 通过code换取 openi以及access_token
     * @param string $code 微信跳转回来带上的code
     * @return
     * {
    "access_token": "OezXcEiiBSKSxW0eoylIeAsR0GmYd1awCffdHgb4fhS_KKf2CotGj2cBNUKQQvj-G0ZWEE5-uBjBz941EOPqDQy5sS_GCs2z40dnvU99Y5AI1bw2uqN--2jXoBLIM5d6L9RImvm8Vg8cBAiLpWA8Vw",
    "expires_in": 7200,
    "refresh_token": "OezXcEiiBSKSxW0eoylIeAsR0GmYd1awCffdHgb4fhS_KKf2CotGj2cBNUKQQvj-G0ZWEE5-uBjBz941EOPqDQy5sS_GCs2z40dnvU99Y5CZPAwZksiuz_6x_TfkLoXLU7kdKM2232WDXB3Msuzq1A",
    "openid": "oLVPpjqs9BhvzwPj5A-vTYAX3GLc",
    "scope": "snsapi_userinfo,"
    }
     */
    private function __CreateOauthUrlForOpenid($code)
    {
        //通过code获取网页授权access_token 和 openid 。网页授权access_token是一次性的，而基础支持的access_token的是有时间限制的：7200s。
        //1、微信网页授权是通过OAuth2.0机制实现的，在用户授权给公众号后，公众号可以获取到一个网页授权特有的接口调用凭证（网页授权access_token），通过网页授权access_token可以进行授权后接口调用，如获取用户基本信息；
        //2、其他微信接口，需要通过基础支持中的“获取access_token”接口来获取到的普通access_token调用。
        $urlObj["appid"]  = $this->appid;
        $urlObj["secret"] = $this->secret;
        $urlObj["code"]   = $code;
        $urlObj["grant_type"] = "authorization_code";
        $bizString = $this->ToUrlParams($urlObj);
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?".$bizString;
        $content = http_get($url);
        $token = @json_decode($content, true);
        if (empty($token) || ! is_array($token) || empty($token['access_token']) || empty($token['openid'])) {
            logRecord("微信授权失败啦！{$content}",'weixin_auth');
            message('获取微信公众号授权失败');
            exit();
        }
        return $token;
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

    public function weixinUserLogin($userInfo)
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
                    'member_type'      => 1,
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