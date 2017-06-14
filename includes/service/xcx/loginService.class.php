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
namespace service\xcx;

class loginService extends \service\publicService
{
    public function do_login($code)
    {
        $seting = globaSetting();
        $appid  = $seting['xcx_appid'];
        $secret = $seting['xcx_secret'];
        $url = 'https://api.weixin.qq.com/sns/jscode2session?appid=wxea80facbec12df2d&secret=2f1e4a3fcb8620276bb8041cfbfe5b67&js_code='.$code.'&grant_type=authorization_code';
        $res = http_get($url);
        $res = json_decode($res,true);
        if(empty($res['openid']) || empty($res['session_key'])){
            $this->error = $res['errmsg'];
            return false;
        }
        //否则的话记录缓存 和 过期时间
        $record = array();
        $record['xcx_openid']       = $res['openid'];
        $record['xcx_session_key']  = $res['session_key'];
        $record['xcx_expires_in']   = TIMESTAMP + $res['expires_in'];
        $seriaze_record             = serialize($record);
//       save_weixin_access_token($seriaze_access_token);

        /**
         * 生成第三方3rd_session，用于第三方服务器和小程序之间做登录态校验。为了保证安全性，3rd_session应该满足：
         * a.长度足够长。建议有2^128种组合，即长度为16B
         * b.避免使用rand（当前时间）然后rand()的方法，而是采用操作系统提供的真正随机数机制，比如Linux下面读取/dev/urandom设备
         * c.设置一定有效时间，对于过期的3rd_session视为不合法
         *
         * 以 $session3rd 为key，sessionKey+openId为value，写入memcached
         */
        $session3rd         = $this->session3rd(16);
        $res['session3rd']  = $session3rd;

        $this->set_session3rd_cache($session3rd,$res,$record['xcx_expires_in']);

        return $res;
    }

    public function session3rd($len)
    {
        $fp = @fopen('/dev/urandom','rb');
        $result = '';
        if ($fp !== FALSE) {
            $result .= @fread($fp, $len);
            @fclose($fp);
        }else{
            trigger_error('Can not open /dev/urandom.');
        }
        // convert from binary to string
        $result = base64_encode($result);
        // remove none url chars
        $result = strtr($result, '+/', '-_');
        return substr($result, 0, $len);
    }

    public function set_session3rd_cache($session3rd,$data,$expires_in)
    {
        $cache_val = serialize(array('openid'=>$data['opedid'],'session_key'=>$data['session_key']));
        if(class_exists('Memcached')){
            $memcache  = new \Mcache();
            $memcache->set($session3rd,$cache_val,$expires_in);
        }else {
            $_SESSION[$session3rd] = $cache_val;
        }
    }

    public function get_session3rd_cache($session3rd)
    {
        if(class_exists('Memcached')){
            $memcache  = new \Mcache();
            $data = $memcache->get($session3rd);
        }else {
            $data = $_SESSION[$session3rd];
        }
        if(empty($data)){
            return false;
        }
        return unserialize($data);
    }
}