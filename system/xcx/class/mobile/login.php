<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/4/20
 * Time: 18:39
 */

namespace xcx\controller;


class login extends \common\controller\basecontroller {

   public function index()
   {
       $_GP = $this->request;
       $code = $_GP['code'];
       $url = 'https://api.weixin.qq.com/sns/jscode2session?appid=wxea80facbec12df2d&secret=2f1e4a3fcb8620276bb8041cfbfe5b67&js_code='.$code.'&grant_type=authorization_code';
       $res = http_get($url);
       $res = json_decode($res,true);
       if(empty($res['openid']) || empty($res['session_key'])){
           ajaxReturnData(0,$res['errmsg']);
       }
       //否则的话记录缓存 和 过期时间
       $expires_in = $res['expires_in'];
       ajaxReturnData(1,'',$res);
   }

}