<?php

namespace service\shopwap;


class weixinpayService extends \service\publicService
{
    private $alipay_config = [
        'appid'         => '',
        'mch_id'        => '',
        'key'           => '',
    ];

    private $is_xcx     = 0;
    /**
     * 构造函数
     * @param type $data
     */
    public function __construct($is_xcx = 0) {
        parent::__construct();
        $payment = mysqld_select("SELECT * FROM " . table('payment') . " WHERE  enabled=1 and code='weixin' limit 1");
        $configs = unserialize($payment['configs']);
        $setting = globaSetting();

        $this->is_xcx = $is_xcx;   //是否是小程序 小程序
        if($is_xcx){
            $appid = $setting['xcx_appid'];
        }else{
            $appid = $setting['weixin_appId'];
        }
        $this->alipay_config['appid']     = $appid;
        $this->alipay_config['mch_id']    = $configs['weixin_pay_mchId'];
        $this->alipay_config['key']       = $configs['weixin_pay_paySignKey'];
    }

    /**
     * 支付宝支付
     * @param type [] 接口参数
     * @return type []
     */
    public function weixinpay($data = [])
    {
        if(empty($data['body'])){
            $this->error = '标题不能为空！';
            return false;
        }
        if(empty($data['out_trade_no']) || empty($data['total_fee'])){
            $this->error = '订单编号和金额不能为空！';
            return false;
        }
        //统一下单接口
        $unifiedorder = $this->unifiedorder($data);

        if(!$unifiedorder){
            return false;
        }

        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') || $this->is_xcx) {
            $config     = $this->alipay_config;
            $parameters = array(
                'appId'     => $config['appid'],//小程序ID
                'timeStamp' => ''.time().'',//时间戳
                'nonceStr'  => make_nonceStr(),//随机串
                'package'   => 'prepay_id='.$unifiedorder['prepay_id'],//数据包
                'signType'  => 'MD5'//签名方式
            );
            //签名
            $parameters['paySign'] = $this->getSign($parameters);
            //小程序直接返回给小晨旭   微信端的话，配合写一段js
            return $parameters;
        }else{
            if(empty($unifiedorder['code_url'])){
                $this->error = '无法发起二维码支付，请换一种付款方式';
                return false;
            }
            return $unifiedorder['code_url'];
        }

    }

    public function unifiedorder($data)
    {
        $pay_ordersn = $data['out_trade_no'];
        $body        = $data['body'];
        $total_fee   = $data['total_fee'];
        //微信接口
        if(is_array($pay_ordersn)){  //如果有多条订单的话  用下划线分隔
            $pay_ordersn = implode('_',$pay_ordersn);
        }

        $config = $this->alipay_config;

        $url    = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
        $parameters =array(
            'appid'     => $config['appid'],//小程序或者公众号ID
            'mch_id'    => $config['mch_id'],//商户号
            'nonce_str' => make_nonceStr(),//随机字符串()
            'body'      => $body,//商品描述
            'out_trade_no'     => $pay_ordersn,  //商户订单号  如果有多条订单的话  用下划线分隔
            'total_fee'        => $total_fee,//总金额 单位 分
            'spbill_create_ip' => $_SERVER['REMOTE_ADDR'],//终端IP
        );
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') || $this->is_xcx) {
            $parameters['notify_url'] =  WEBSITE_ROOT . 'notify/weixin_notify.php';   //异步通知
//            $parameters['notify_url'] =  mobile_url('weixinpay',array('name'=>'shopwap','op'=>'notifyurl'));  //异步通知
            $parameters['trade_type'] = 'JSAPI';

            $meminfo    = get_member_account();
            $weixinfans = mysqld_select("select weixin_openid from ".table('weixin_wxfans')." where openid='{$meminfo['openid']}'");
            $parameters['openid']     = $weixinfans['weixin_openid'];
        } else {
            $parameters['notify_url'] = WEBSITE_ROOT . 'notify/weixin_native_notify.php';  //同步通知
//            $parameters['notify_url'] = mobile_url('weixinpay',array('name'=>'shopwap','op'=>'native_notify'));  //同步通知
            $parameters['product_id'] = $pay_ordersn;
            $parameters['trade_type'] = 'NATIVE';
        }

        //统一下单签名
        $parameters['sign'] = $this->getSign($parameters);
        $xmlData            = $this->arrayToXml($parameters);
        $postXmlSSLCurl     = $this->postXmlSSLCurl($xmlData,$url,60);
        $return             = $this->xmlToArray($postXmlSSLCurl);

        if($return['return_code'] == 'FAIL'){
            $member  = get_member_account();
            $memInfo = member_get($member['openid'],'mobile');
            logRecord("{$memInfo['mobile']}用户支付错误---{$return['return_msg']}",'payError');
            $this->error = '出错了,请重新再试!';
            return false;
        }else if($return['result_code'] == 'FAIL'){
            $member  = get_member_account();
            $memInfo = member_get($member['openid'],'mobile');
            logRecord("{$memInfo['mobile']}用户支付错误---{$return['err_code_des']}",'payError');
            $this->error = '出错了,'.$return['err_code_des'];
            return false;
        }
        return $return;
    }

    //作用：生成签名
    private function getSign($Obj){
        $config = $this->alipay_config;
        foreach ($Obj as $k => $v){
            $Parameters[$k] = $v;
        }
        //签名步骤一：按字典序排序参数
        ksort($Parameters);
        $String = $this->formatBizQueryParaMap($Parameters, false);
        //签名步骤二：在string后加入KEY
        $String = $String."&key=".$config['key'];
        //签名步骤三：MD5加密
        $String = md5($String);
        //签名步骤四：所有字符转为大写
        $result_ = strtoupper($String);
        return $result_;
    }
    ///作用：格式化参数，签名过程需要使用
    private function formatBizQueryParaMap($paraMap, $urlencode){
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v){
            if($urlencode)
            {
                $v = urlencode($v);
            }
            $buff .= $k . "=" . $v . "&";
        }
        $reqPar = '';
        if (strlen($buff) > 0){
            $reqPar = substr($buff, 0, strlen($buff)-1);
        }
        return $reqPar;
    }

    //作用：array转xml
    public function arrayToXml($arr){
        $xml = "<xml>";
        foreach ($arr as $key=>$val){
            if (is_numeric($val))
                $xml.="<".$key.">".$val."</".$key.">";
            else
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
        }
        $xml.="</xml>";
        return $xml;
    }
    //作用：将xml转为array
    public function xmlToArray($xml){
        //将XML转为array
        $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $array_data;
    }
    //作用：使用证书，以post方式提交xml到对应的接口url
    public function postXmlSSLCurl($xml,$url,$second=30){
        $ch = curl_init();
        //超时时间
        curl_setopt($ch,CURLOPT_TIMEOUT,$second);
        //这里设置代理，如果有的话
        //curl_setopt($ch,CURLOPT_PROXY, '8.8.8.8');
        //curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
        //设置header
        curl_setopt($ch,CURLOPT_HEADER,FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
        //设置证书
        //使用证书：cert 与 key 分别属于两个.pem文件
        //默认格式为PEM，可以注释
        //curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
        //curl_setopt($ch,CURLOPT_SSLCERT, getcwd() . '/source/class/pay/Weixinnewpay/apiclient_cert.pem');
        //默认格式为PEM，可以注释
        //curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
        //curl_setopt($ch,CURLOPT_SSLKEY, getcwd() . '/source/class/pay/Weixinnewpay/apiclient_key.pem');
        //post提交方式
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$xml);
        $data = curl_exec($ch);
        //返回结果
        if($data){
            curl_close($ch);
            return $data;
        }else {
            $error = curl_errno($ch);
            $member  = get_member_account();
            $memInfo = member_get($member['openid'],'mobile');
            logRecord("{$memInfo['mobile']}用户支付错误!",'payError');
            curl_close($ch);
            ajaxReturnData(0,'出错了,请重新再试!');
        }
    }



    /**
     * 异步回调
     * @return string
     */
    public function notify_weixinpay()
    {
        $config       = $this->alipay_config;
        $alipayNotify = new \AlipayNotify($config); //计算得出通知验证结果
        if ($result = $alipayNotify->verifyNotify()) {
            //验签成功
            if ($_GET('trade_status') == 'TRADE_FINISHED' || $_GET('trade_status') == 'TRADE_SUCCESS') {
                $ordersn     = $_GET['out_trade_no'];
                $ordersn_arr = explode('_',$ordersn);   //多商家导致，可能有多个订单号
                //成功后的后续操作/**
                // 支付完毕 处理账单 佣金提成，卖家所得，平台费率
                $settings = globaSetting();
                foreach($ordersn_arr as $ordersn){
                    paySuccessProcess($ordersn,$settings);
                }
                return true;
            }else{
                $member  = get_member_account();
                $memInfo = member_get($member['openid'],'mobile');
                logRecord("{$memInfo['mobile']}用户支付业务错误",'payError');
                $this->error = '业务错误';
                return false;
            }
        }else{
            //验证失败  记录日志
            $member  = get_member_account();
            $memInfo = member_get($member['openid'],'mobile');
            logRecord("{$memInfo['mobile']}用户支付异步签名验证失败",'payError');
            $this->error = '支付失败';
            return false;
        }
    }

    /**
     * 同步回调  返回的数据
     *Array
    (
    [mod] => mobile
    [name] => shopwap
    [do] => alipay
    [op] => returnurl
    [body] => 测试商品
    [buyer_email] => 791845283@qq.com
    [buyer_id] => 2088802661101009
    [exterface] => create_direct_pay_by_user
    [is_success] => T
    [notify_id] => RqPnCoPT3K9%2Fvwbh3InYwe9UQaecKY9y3krILMLAzIFwEVVFOIAEcfZx4sSZwAlhKQTA
    [notify_time] => 2017-06-22 17:27:33
    [notify_type] => trade_status_sync
    [out_trade_no] => sn099239283879
    [payment_type] => 1
    [seller_email] => 33413434@qq.com
    [seller_id] => 2088321009666241
    [subject] => sn099239283879
    [total_fee] => 0.01
    [trade_no] => 2017062221001004000219681644
    [trade_status] => TRADE_SUCCESS
    [sign] => 280e4387a81f3e7cd2f28aa0f4203a12
    [sign_type] => MD5
    )
     */
    public function native_notify()
    {
        $config = $this->alipay_config;
        $alipayNotify  = new \AlipayNotify($config); //计算得出通知验证结果
        $verify_result = $alipayNotify->verifyReturn();
        if ($verify_result) {
            //验证成功
            if ($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
                $ordersn     = $_GET['out_trade_no'];
                $ordersn_arr = explode('_',$ordersn);   //多商家导致，可能有多个订单号
                //成功后的后续操作/**
                // 支付完毕 处理账单 佣金提成，卖家所得，平台费率
                $settings = globaSetting();
                foreach($ordersn_arr as $ordersn){
                    paySuccessProcess($ordersn,$settings);
                }
                return true;
            } else {
                $member  = get_member_account();
                $memInfo = member_get($member['openid'],'mobile');
                logRecord("{$memInfo['mobile']}用户支付业务错误",'payError');
                $this->error = '业务错误';
                return false;
            }
        } else {
            //验证失败  记录日志
            $member  = get_member_account();
            $memInfo = member_get($member['openid'],'mobile');
            logRecord("{$memInfo['mobile']}用户支付异步签名验证失败",'payError');
            $this->error = '支付失败';
            return false;
        }
    }


}