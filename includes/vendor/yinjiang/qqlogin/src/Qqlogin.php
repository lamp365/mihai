<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/28
 * Time: 10:30
 */

namespace Yinjiang\Qqlogin;


class Qqlogin
{
    protected $key;
    protected $secret;
    protected $callback;
    protected $state;
    protected $code;
    protected $access_token;

    public function __construct()
    {
        $config = require __DIR__ . '/config/config.php';
        $this->key = $config['key'];
        $this->secret = $config['secret'];
        $this->callback = $config['callback'];
        $this->state = md5(time());
    }

    public function setCode($code)
    {
        $this->code = $code;
    }

    public function setAccessToken($access_token)
    {
        $this->access_token = $access_token;
    }

    public function getCode()
    {
        $url = "https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id={$this->key}&redirect_uri={$this->callback}&state={$this->state}";
        header("location: $url");
        exit;
    }

    public function getAccessToken()
    {
        $url = "https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&client_id={$this->key}&client_secret={$this->secret}&code={$this->code}&redirect_uri={$this->callback}";
        $result = self::getCurl($url);
        parse_str($result, $datas);
        $this->setAccessToken($datas['access_token']);
    }

    public function getOpenId()
    {
        $this->getAccessToken();
        $url = "https://graph.qq.com/oauth2.0/me?access_token={$this->access_token}";
        $result = self::getCurl($url);
        if ($result) {
            if ($result) {
                $lpos = strpos($result, "(");
                $rpos = strrpos($result, ")");
                $result = substr($result, $lpos + 1, $rpos - $lpos - 1);
                $result = json_decode($result);
                return $result->openid;
            }
        } else {
            return false;
        }
    }

    public static function getCurl($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        return $result;
    }

}