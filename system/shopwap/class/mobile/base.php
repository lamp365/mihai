<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/4/19
 * Time: 11:52
 * 基类，用于操作一些父类的东西 如登录授权  权限控制
 */
namespace shopwap\controller;


class base extends \common\controller\basecontroller{

    public $appid   = '';
    public $secret  = '';

    public function __construct()
    {
        parent::__construct();
        if(strstr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')){
            $weixinthirdlogin = mysqld_select("SELECT * FROM " . table('thirdlogin') . " WHERE enabled=1 and `code`='weixin'");
            if(!empty($weixinthirdlogin['id'])){
                $settings       = globaSetting();
                $this->appid    = $settings['weixin_appId'];
                $this->secret   = $settings['weixin_appSecret'];
                $user_wx_openid = $this->GetOpenid(); //授权获取openid以及微信用户信息
            }

        }
    }

    // 网页授权登录获取 OpendId
    public function GetOpenid()
    {
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
            $code     = $_GET['code'];
            $data     = $this->__CreateOauthUrlForOpenid($code);//获取网页授权access_token和用户openid
            $userInfo = $this->GetUserInfo($data['access_token'],$data['openid']);//获取微信用户信息

            $this->weixinUserLogin($userInfo);
            return $userInfo['openId'];
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
//        $urlObj["scope"] = "snsapi_base";
        $urlObj["scope"] = "snsapi_userinfo";
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
     * 通过code从工作平台获取openid机器access_token
     * @param string $code 微信跳转回来带上的code
     * @return 请求的url
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
    public function GetUserInfo($access_token,$openid)
    {
        // 获取用户 信息
        $urlObj["access_token"] = $access_token;
        $urlObj["openid"]       = $openid;
        $urlObj["lang"]          = 'zh_CN';
        $bizString = $this->ToUrlParams($urlObj);
//        $url       =  "https://api.weixin.qq.com/sns/userinfo?".$bizString;
        $url       =  "https://api.weixin.qq.com/cgi-bin/user/info?".$bizString;
        $content   = http_get($url);
        $data      = @json_decode($content, true);
        if (empty($data) || ! is_array($data) || empty($data['openid'])) {
            message('获取微信公众号授权失败[无法取得info], 请稍后重试');
            exit();
        }
        return $data;
    }

    public function weixinUserLogin($userInfo)
    {
        $info = mysqld_select("select * from ".table('weixin_wxfans')." where unionid='{$userInfo['unionId']}'");
        if(empty($info)){
            //插入
            $mem_openid  = date("YmdH",time()).rand(100,999);
            $hasmember   = mysqld_select("SELECT openid FROM " . table('member') . " WHERE openid = :openid ", array(':openid' => $mem_openid));
            if(isset($hasmember['openid']) && !empty($hasmember['openid'])) {
                $mem_openid = date("YmdH",time()).rand(100,999);
            }
            $insert_data['openid'] = $mem_openid;
            $insert_data['weixin_openid'] = $userInfo['openId'];
            $insert_data['nickname']      = $userInfo['nickName'];
            $insert_data['avatar']        = $userInfo['avatarUrl'];
            $insert_data['gender']        = $userInfo['gender'];
            $insert_data['unionid']       = $userInfo['unionId'];
            $insert_data['createtime']    = time();
            //插入微信用户
            $res = mysqld_insert('weixin_wxfans',$insert_data);
            if($res){
                //插入用户 member
                $mem_data = array(
                    'nickname'	      => $userInfo['nickName'],
                    'realname'	      => $userInfo['nickName'],
                    'avatar'	      => $userInfo['avatarUrl'],
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
                $this->error = '登录失败，刷亲页面再试！';
                return false;
            }
        }else{
            $mem_data = member_get($info['openid']);
            $userInfo = $info;
        }

        $_SESSION[MOBILE_ACCOUNT]       = $mem_data;
        $_SESSION[MOBILE_WEIXIN_OPENID] = $userInfo['openId'];
        $sessionAccount = array(
            'openid'         => $mem_data['openid'],
            'weixin_openid'  => $userInfo['openId'],
            'unionid'        => $userInfo['unionId']
        );
        $_SESSION[MOBILE_SESSION_ACCOUNT] = $sessionAccount;
    }

}
