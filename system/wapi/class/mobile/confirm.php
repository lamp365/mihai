<?php
/**
 * Created by PhpStorm.
 * User: 刘建凡
 * Date: 2017/4/20
 * Time: 18:39
 */

namespace wapi\controller;

class confirm extends base
{
   public function index()
   {

   }

    public function topay()
    {
        $appid  = 'wx888888888';
        $openid = 'oCQwY0Q_pzrQpu8888888';
        $mch_id = '141388888';
        $key    = '9A0A86888888888';

        $weixinpay = new \service\wapi\wxpayService($appid,$openid,$mch_id,$key);
        $return    = $weixinpay->pay();
        ajaxReturnData(1,'操作成功!',$return);
    }
}